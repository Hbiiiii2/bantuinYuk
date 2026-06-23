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

    public function markAllAsRead()
    {
        $userId = auth()->id();
        $this->notificationModel->markAllAsRead($userId);

        return redirect()->back()->with('message', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
