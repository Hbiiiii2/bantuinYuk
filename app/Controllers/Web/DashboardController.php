<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\TaskModel;
use App\Models\WalletModel;

class DashboardController extends BaseController
{
    protected $taskModel;
    protected $walletModel;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->walletModel = new WalletModel();
    }

    public function user()
    {
        $userId = auth()->id();
        
        // Fetch tasks
        $tasks = $this->taskModel->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll(5);
        $totalTasks = $this->taskModel->where('user_id', $userId)->countAllResults();
        $activeTasks = $this->taskModel->where('user_id', $userId)
            ->whereIn('status', ['open', 'accepted', 'in_progress'])
            ->countAllResults();
            
        // Fetch wallet
        $wallet = $this->walletModel->where('user_id', $userId)->first();
        if (!$wallet) {
            $wallet = ['balance' => 0, 'pending_balance' => 0];
        }

        return view('dashboard/user', [
            'title'       => 'Dashboard User - Bantuin Yuk',
            'tasks'       => $tasks,
            'totalTasks'  => $totalTasks,
            'activeTasks' => $activeTasks,
            'wallet'      => $wallet
        ]);
    }

    public function helper()
    {
        $userId = auth()->id();
        
        // Fetch tasks
        $tasks = $this->taskModel->where('helper_id', $userId)->orderBy('created_at', 'DESC')->findAll(5);
        $totalTasks = $this->taskModel->where('helper_id', $userId)->countAllResults();
        $activeTasks = $this->taskModel->where('helper_id', $userId)
            ->whereIn('status', ['accepted', 'in_progress'])
            ->countAllResults();
            
        // Fetch wallet
        $wallet = $this->walletModel->where('user_id', $userId)->first();
        if (!$wallet) {
            $wallet = ['balance' => 0, 'pending_balance' => 0];
        }

        // Fetch open tasks for the dashboard
        $openTasks = $this->taskModel
            ->select('tasks.*, categories.name as category_name, users.name as user_name')
            ->join('categories', 'categories.id = tasks.category_id', 'left')
            ->join('users', 'users.id = tasks.user_id', 'left')
            ->where('tasks.status', 'open')
            ->orderBy('tasks.created_at', 'DESC')
            ->findAll(5);

        $completedTasks = $this->taskModel->where('helper_id', $userId)
            ->where('status', 'completed')
            ->countAllResults();

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        $rating = $user->rating ?? 0;

        return view('dashboard/helper', [
            'title'          => 'Dashboard Helper - Bantuin Yuk',
            'tasks'          => $tasks,
            'totalTasks'     => $totalTasks,
            'activeTasks'    => $activeTasks,
            'completedTasks' => $completedTasks,
            'rating'         => $rating,
            'wallet'         => $wallet,
            'openTasks'      => $openTasks
        ]);
    }
}
