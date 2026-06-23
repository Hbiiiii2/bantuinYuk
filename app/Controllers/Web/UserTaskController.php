<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\TaskModel;
use App\Models\CategoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class UserTaskController extends BaseController
{
    protected $taskModel;
    protected $categoryModel;
    protected $walletModel;
    protected $transactionModel;
    protected $notificationService;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->categoryModel = new CategoryModel();
        $this->walletModel = new \App\Models\WalletModel();
        $this->transactionModel = new \App\Models\TransactionModel();
        $this->notificationService = new \App\Services\NotificationService();
    }

    public function index()
    {
        $userId = auth()->id();
        $tasks = $this->taskModel
                      ->select('tasks.*, categories.name as category_name')
                      ->join('categories', 'categories.id = tasks.category_id', 'left')
                      ->where('user_id', $userId)
                      ->orderBy('created_at', 'DESC')
                      ->findAll();

        return view('tasks/user/index', [
            'title' => 'Task Saya - Bantuin Yuk',
            'tasks' => $tasks
        ]);
    }

    public function create()
    {
        $categories = $this->categoryModel->findAll();
        $userId = auth()->id();
        $wallet = $this->walletModel->where('user_id', $userId)->first();

        return view('tasks/user/create', [
            'title'      => 'Buat Task Baru - Bantuin Yuk',
            'categories' => $categories,
            'wallet'     => $wallet
        ]);
    }

    public function store()
    {
        $rules = [
            'title'       => 'required|min_length[5]',
            'description' => 'required',
            'category_id' => 'required|numeric',
            'price'       => 'required|numeric|greater_than_equal_to[10000]',
            'location'    => 'required',
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId = auth()->id();
        $price = $this->request->getPost('price');

        // Check if user has sufficient balance
        if (!$this->walletModel->hasSufficientBalance($userId, $price)) {
            return redirect()->back()->withInput()->with('error', 'Saldo Wallet tidak mencukupi untuk membuat pekerjaan ini. Harap Top Up terlebih dahulu.');
        }

        // Hold balance (Escrow)
        $this->walletModel->holdBalance($userId, $price);

        $taskId = $this->taskModel->insert([
            'user_id'     => $userId,
            'category_id' => $this->request->getPost('category_id'),
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'price'       => $price,
            'location'    => $this->request->getPost('location'),
            'status'      => 'open',
        ]);

        // Record pending transaction
        $this->transactionModel->insert([
            'user_id' => $userId,
            'task_id' => $taskId,
            'amount'  => $price,
            'type'    => \App\Models\TransactionModel::TYPE_TASK_PAYMENT,
            'status'  => \App\Models\TransactionModel::STATUS_PENDING,
            'description' => 'Pembayaran ditahan untuk pekerjaan: ' . $this->request->getPost('title')
        ]);

        $task = $this->taskModel->find($taskId);
        $this->notificationService->notifyTaskCreated($userId, $task);

        return redirect()->to('/user/tasks')->with('message', 'Task baru berhasil dibuat!');
    }

    public function detail($id)
    {
        $userId = auth()->id();
        
        $task = $this->taskModel
                     ->select('tasks.*, categories.name as category_name, users.name as helper_name')
                     ->join('categories', 'categories.id = tasks.category_id', 'left')
                     ->join('users', 'users.id = tasks.helper_id', 'left')
                     ->where('tasks.id', $id)
                     ->where('tasks.user_id', $userId)
                     ->first();

        if (!$task) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('tasks/user/detail', [
            'title' => 'Detail Task - Bantuin Yuk',
            'task'  => $task
        ]);
    }

    public function acceptHelper($taskId)
    {
        // For simplicity, let's assume we're just marking the task as "accepted" if a helper had bid on it.
        // In a real flow, a helper applies, and the user selects. 
        // We will just allow marking it complete for now.
        
        return redirect()->back()->with('message', 'Helper diterima.');
    }

    public function cancelTask($id)
    {
        $userId = auth()->id();
        $task = $this->taskModel->find($id);

        if (!$task || $task['user_id'] != $userId) {
            return redirect()->back()->with('error', 'Pekerjaan tidak ditemukan.');
        }

        if ($task['status'] !== 'open') {
            return redirect()->back()->with('error', 'Hanya pekerjaan dengan status Open yang dapat dibatalkan.');
        }

        $cancelReason = $this->request->getPost('cancel_reason');
        if (empty(trim($cancelReason))) {
            return redirect()->back()->with('error', 'Alasan pembatalan harus diisi.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Kembalikan saldo yang ditahan
        $this->walletModel->releaseHeldBalance($userId, $task['price']);

        // Ubah status dan simpan alasan
        $this->taskModel->update($id, [
            'status' => 'cancelled',
            'cancel_reason' => $cancelReason
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal membatalkan pekerjaan. Silakan coba lagi.');
        }

        return redirect()->back()->with('message', 'Pekerjaan berhasil dibatalkan dan saldo telah dikembalikan.');
    }

    public function complete($taskId)
    {
        $userId = auth()->id();
        $task = $this->taskModel->where('id', $taskId)->where('user_id', $userId)->first();
        
        if ($task && $task['status'] === 'in_progress') {
            if (empty($task['photo_start']) || empty($task['photo_end'])) {
                return redirect()->back()->with('error', 'Tidak dapat menyelesaikan pekerjaan. Helper belum mengunggah foto progress secara lengkap.');
            }

            // Release escrow: confirm held balance for user and increment helper's balance
            $this->walletModel->confirmHeldBalance($userId, $task['price']);
            $this->walletModel->incrementBalance($task['helper_id'], $task['price']);

            // Update transaction status for user
            $transaction = $this->transactionModel->getByTaskAndType($taskId, \App\Models\TransactionModel::TYPE_TASK_PAYMENT);
            if ($transaction) {
                $this->transactionModel->update($transaction['id'], ['status' => \App\Models\TransactionModel::STATUS_COMPLETED]);
            }

            // Create credit transaction for helper
            $this->transactionModel->insert([
                'user_id' => $task['helper_id'],
                'task_id' => $taskId,
                'amount'  => $task['price'],
                'type'    => \App\Models\TransactionModel::TYPE_TASK_PAYMENT,
                'status'  => \App\Models\TransactionModel::STATUS_COMPLETED,
                'description' => 'Pendapatan dari penyelesaian pekerjaan: ' . $task['title']
            ]);

            $this->taskModel->update($taskId, ['status' => 'completed']);
            
            $user = auth()->user();
            $this->notificationService->notifyTaskCompleted($taskId, $task['title'], $task['helper_id'], $userId, $user->name ?? 'User');
            $this->notificationService->notifyPaymentReleased($task['helper_id'], $task['title'], $task['price'], $taskId);

            return redirect()->back()->with('message', 'Task diselesaikan. Pembayaran diteruskan ke Helper.');
        }
        
        return redirect()->back()->with('error', 'Task tidak dapat diselesaikan.');
    }
    public function rateHelper($taskId)
    {
        $userId = auth()->id();
        $task = $this->taskModel->where('id', $taskId)->where('user_id', $userId)->first();
        
        if (!$task || $task['status'] !== 'completed') {
            return redirect()->back()->with('error', 'Pekerjaan belum selesai atau tidak ditemukan.');
        }

        $rating = $this->request->getPost('rating');
        $review = $this->request->getPost('review');

        if (!$rating || $rating < 1 || $rating > 5) {
            return redirect()->back()->with('error', 'Rating harus antara 1 sampai 5 bintang.');
        }

        $reviewModel = new \App\Models\TaskReviewModel();

        // Check if already rated
        if ($reviewModel->hasReview($taskId, $userId)) {
            return redirect()->back()->with('error', 'Anda sudah memberikan ulasan untuk pekerjaan ini.');
        }

        $reviewModel->insert([
            'task_id'     => $taskId,
            'reviewer_id' => $userId,
            'reviewee_id' => $task['helper_id'],
            'rating'      => $rating,
            'review'      => $review
        ]);

        $userModel = new \App\Models\UserModel();
        $averageRating = $reviewModel->getAverageRating($task['helper_id']);
        $userModel->update($task['helper_id'], ['rating' => $averageRating]);

        $user = auth()->user();
        $this->notificationService->notifyReviewReceived($taskId, $task['title'], $task['helper_id'], $userId, $user->name ?? 'User', $rating);

        return redirect()->back()->with('message', 'Ulasan berhasil dikirim. Terima kasih!');
    }
}
