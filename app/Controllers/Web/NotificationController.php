<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function index()
    {
        $userId = auth()->id();
        
        $page = $this->request->getGet('page') ?? 1;
        $result = $this->notificationModel->getByUserId($userId, $page, 50);

        return view('notifications/index', [
            'title'         => 'Notifikasi - Bantuin Yuk',
            'notifications' => $result['data'],
            'total'         => $result['total'],
            'currentPage'   => $result['page']
        ]);
    }

    public function markAsRead($id)
    {
        $userId = auth()->id();
        
        if ($this->notificationModel->belongsToUser($id, $userId)) {
            $this->notificationModel->markAsRead($id, $userId);
        }

        return redirect()->back();
    }

    public function readAndRedirect($id)
    {
        $userId = auth()->id();
        
        $notification = $this->notificationModel->where('id', $id)->where('user_id', $userId)->first();
        if (!$notification) {
            return redirect()->to('/notifications')->with('error', 'Notifikasi tidak ditemukan.');
        }

        // Mark as read
        if ($notification['is_read'] == 0) {
            $this->notificationModel->markAsRead($id, $userId);
        }

        // Parse data to find redirect URL
        $data = !empty($notification['data']) ? json_decode($notification['data'], true) : [];
        $role = auth()->user()->getGroups()[0] ?? 'user';

        if (isset($data['task_id'])) {
            $taskId = $data['task_id'];
            if ($role === 'helper') {
                return redirect()->to("/helper/tasks/{$taskId}");
            } else {
                return redirect()->to("/user/tasks/{$taskId}");
            }
        } elseif (isset($data['transaction_id'])) {
            return redirect()->to('/wallet');
        }

        return redirect()->to('/notifications');
    }

    public function markAllAsRead()
    {
        $userId = auth()->id();
        $this->notificationModel->markAllAsRead($userId);

        return redirect()->back()->with('message', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function deleteRead()
    {
        $userId = auth()->id();
        $this->notificationModel->deleteRead($userId);

        return redirect()->back()->with('message', 'Notifikasi yang sudah dibaca berhasil dihapus.');
    }
}
