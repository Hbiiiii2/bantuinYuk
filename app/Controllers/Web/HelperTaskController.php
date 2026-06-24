<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\TaskModel;
use App\Models\CategoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class HelperTaskController extends BaseController
{
    protected $taskModel;
    protected $categoryModel;
    protected $notificationService;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->categoryModel = new CategoryModel();
        $this->notificationService = new \App\Services\NotificationService();
    }

    public function explore()
    {
        $keyword = $this->request->getGet('keyword');
        $categoryId = $this->request->getGet('category_id');

        $query = $this->taskModel
                      ->select('tasks.*, categories.name as category_name, users.name as user_name')
                      ->join('categories', 'categories.id = tasks.category_id', 'left')
                      ->join('users', 'users.id = tasks.user_id', 'left')
                      ->where('tasks.status', 'open');

        if (!empty($keyword)) {
            $query->groupStart()
                  ->like('tasks.title', $keyword)
                  ->orLike('tasks.description', $keyword)
                  ->orLike('tasks.location', $keyword)
                  ->groupEnd();
        }

        if (!empty($categoryId)) {
            $query->where('tasks.category_id', $categoryId);
        }

        $tasks = $query->orderBy('tasks.created_at', 'DESC')->findAll();
        $categories = $this->categoryModel->findAll();

        return view('tasks/helper/explore', [
            'title'      => 'Eksplor Pekerjaan - Bantuin Yuk',
            'tasks'      => $tasks,
            'categories' => $categories
        ]);
    }

    public function myTasks()
    {
        $userId = auth()->id();
        $tasks = $this->taskModel
                      ->select('tasks.*, categories.name as category_name, users.name as user_name')
                      ->join('categories', 'categories.id = tasks.category_id', 'left')
                      ->join('users', 'users.id = tasks.user_id', 'left')
                      ->where('tasks.helper_id', $userId)
                      ->orderBy('tasks.updated_at', 'DESC')
                      ->findAll();

        return view('tasks/helper/my_tasks', [
            'title' => 'Pekerjaan Saya - Bantuin Yuk',
            'tasks' => $tasks
        ]);
    }

    public function detail($id)
    {
        $task = $this->taskModel
                     ->select('tasks.*, categories.name as category_name, users.name as user_name, users.phone as user_phone')
                     ->join('categories', 'categories.id = tasks.category_id', 'left')
                     ->join('users', 'users.id = tasks.user_id', 'left')
                     ->where('tasks.id', $id)
                     ->first();

        if (!$task) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('tasks/helper/detail', [
            'title' => 'Detail Pekerjaan - Bantuin Yuk',
            'task'  => $task
        ]);
    }

    public function take($id)
    {
        $userId = auth()->id();

        // Cek verifikasi KYC
        $helperProfileModel = new \App\Models\HelperProfileModel();
        $profile = $helperProfileModel->where('user_id', $userId)->first();
        
        if (!$profile || $profile['verification_status'] !== 'verified') {
            return redirect()->to('/helper/kyc')->with('error', 'Anda harus melengkapi verifikasi identitas (KYC) terlebih dahulu sebelum dapat mengambil pekerjaan.');
        }

        $task = $this->taskModel->find($id);

        if (!$task || $task['status'] !== 'open') {
            return redirect()->back()->with('error', 'Pekerjaan tidak tersedia.');
        }

        // Update task status and helper_id
        $this->taskModel->update($id, [
            'status'    => 'in_progress',
            'helper_id' => $userId
        ]);

        $task = $this->taskModel->find($id);
        $user = auth()->user();
        $this->notificationService->notifyTaskAccepted($id, $task['title'], $task['user_id'], $userId, $user->name ?? 'Helper');

        return redirect()->to('/helper/tasks/my-tasks')->with('message', 'Pekerjaan berhasil diambil! Silakan hubungi pembuat task.');
    }

    public function uploadProgress($id)
    {
        $userId = auth()->id();
        $task = $this->taskModel->find($id);

        if (!$task || $task['helper_id'] != $userId) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $type = $this->request->getPost('type'); // 'start' or 'end'
        if (!in_array($type, ['start', 'end'])) {
            return redirect()->back()->with('error', 'Tipe upload tidak valid.');
        }

        $validationRule = [
            'photo' => [
                'label' => 'Foto Progress',
                'rules' => 'uploaded[photo]|ext_in[photo,jpg,jpeg,png,pdf]|max_size[photo,5120]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()->with('error', $this->validator->getErrors()['photo'] ?? 'Gagal mengupload foto.');
        }

        $img = $this->request->getFile('photo');

        if (!$img->hasMoved()) {
            $newName = $img->getRandomName();
            $img->move(FCPATH . 'uploads/tasks', $newName);
            
            $column = 'photo_' . $type;
            $this->taskModel->update($id, [
                $column => 'uploads/tasks/' . $newName
            ]);

            $user = auth()->user();
            if ($type === 'start') {
                $this->notificationService->notifyTaskStarted($id, $task['title'], $task['user_id'], $userId, $user->name ?? 'Helper');
            } else {
                $this->notificationService->notifyTaskProgress($id, $task['title'], $task['user_id'], $userId, $user->name ?? 'Helper');
            }

            $updatedTask = $this->taskModel->find($id);
            if (!empty($updatedTask['photo_start']) && !empty($updatedTask['photo_end'])) {
                
                if ($updatedTask['status'] === 'in_progress') {
                    $this->taskModel->update($id, ['status' => 'waiting_approval']);
                    
                    // Log status history
                    $historyModel = new \App\Models\TaskStatusHistoryModel();
                    $historyModel->insert([
                        'task_id' => $id,
                        'status' => 'waiting_approval',
                        'created_by' => $userId,
                        'note' => 'Pekerjaan selesai, foto lengkap diunggah'
                    ]);
                }

                // Notify User
                $this->notificationService->notifyTaskSubmitted($id, $updatedTask['title'], $updatedTask['user_id'], $userId, $user->name ?? 'Helper');
                
                // Notify Helper
                $this->notificationService->create(
                    $userId,
                    'task_submitted',
                    'Pekerjaan Selesai',
                    "Anda telah berhasil mengirimkan bukti penyelesaian untuk pekerjaan \"{$updatedTask['title']}\". Menunggu persetujuan User.",
                    ['task_id' => $id]
                );
            }

            return redirect()->back()->with('message', 'Foto progress berhasil diunggah.');
        }

        return redirect()->back()->with('error', 'Gagal memindahkan file foto.');
    }
    public function rateUser($taskId)
    {
        $userId = auth()->id();
        $task = $this->taskModel->where('id', $taskId)->where('helper_id', $userId)->first();
        
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
            'reviewee_id' => $task['user_id'],
            'rating'      => $rating,
            'review'      => $review
        ]);

        $userModel = new \App\Models\UserModel();
        $averageRating = $reviewModel->getAverageRating($task['user_id']);
        $userModel->update($task['user_id'], ['rating' => $averageRating]);

        $user = auth()->user();
        $this->notificationService->notifyReviewReceived($taskId, $task['title'], $task['user_id'], $userId, $user->name ?? 'Helper', $rating);

        return redirect()->back()->with('message', 'Ulasan berhasil dikirim. Terima kasih!');
    }
}
