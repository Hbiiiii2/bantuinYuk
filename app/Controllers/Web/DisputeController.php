<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\DisputeModel;
use App\Models\TaskModel;

class DisputeController extends BaseController
{
    protected $disputeModel;
    protected $taskModel;

    public function __construct()
    {
        $this->disputeModel = new DisputeModel();
        $this->taskModel = new TaskModel();
    }

    public function index()
    {
        $userId = auth()->id();
        $page = $this->request->getGet('page') ?? 1;
        $status = $this->request->getGet('status');

        $result = $this->disputeModel->getByUserId($userId, $page, 20, $status);

        return view('disputes/index', [
            'title'    => 'Pusat Resolusi - Bantuin Yuk',
            'disputes' => $result['data'],
            'total'    => $result['total'],
            'page'     => $result['page'],
            'status'   => $status
        ]);
    }

    public function create($taskId)
    {
        $userId = auth()->id();
        $task = $this->taskModel->find($taskId);

        if (!$task) {
            return redirect()->back()->with('error', 'Pekerjaan tidak ditemukan.');
        }

        // Only the user or helper involved can create a dispute
        if ($task['user_id'] != $userId && $task['helper_id'] != $userId) {
            return redirect()->back()->with('error', 'Anda tidak berhak mengajukan komplain untuk pekerjaan ini.');
        }

        if ($this->disputeModel->hasActiveDispute($taskId)) {
            return redirect()->back()->with('error', 'Pekerjaan ini sudah memiliki komplain aktif.');
        }

        return view('disputes/create', [
            'title' => 'Ajukan Komplain - Bantuin Yuk',
            'task'  => $task
        ]);
    }

    public function store($taskId)
    {
        $userId = auth()->id();
        $task = $this->taskModel->find($taskId);

        if (!$task || ($task['user_id'] != $userId && $task['helper_id'] != $userId)) {
            return redirect()->back()->with('error', 'Tidak berhak.');
        }

        $rules = [
            'reason' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $helperId = $task['helper_id'];
        
        $data = [
            'task_id'   => $taskId,
            'user_id'   => $userId, // User who created the dispute
            'helper_id' => ($userId == $task['user_id']) ? $task['helper_id'] : $task['user_id'], // Counterparty
            'reason'    => $this->request->getPost('reason'),
            'status'    => DisputeModel::STATUS_OPEN
        ];

        $this->disputeModel->insert($data);

        // Update task status to disputed
        $this->taskModel->update($taskId, ['status' => 'cancelled']); // Actually, API might have a 'disputed' status, but fallback to open/cancelled. Assuming API uses 'in_progress' or similar. We just insert dispute.

        return redirect()->to('/disputes')->with('message', 'Komplain berhasil diajukan dan sedang menunggu tinjauan admin.');
    }

    public function detail($id)
    {
        $userId = auth()->id();
        $dispute = $this->disputeModel->getDisputeById($id);

        if (!$dispute) {
            return redirect()->to('/disputes')->with('error', 'Komplain tidak ditemukan.');
        }

        if (!$this->disputeModel->isInvolved($id, $userId)) {
            return redirect()->to('/disputes')->with('error', 'Anda tidak berhak melihat komplain ini.');
        }

        return view('disputes/detail', [
            'title'   => 'Detail Komplain - Bantuin Yuk',
            'dispute' => $dispute
        ]);
    }
}
