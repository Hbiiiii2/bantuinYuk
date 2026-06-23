<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\TaskModel;
use App\Models\TransactionModel;

class AdminController extends BaseController
{
    protected $userModel;
    protected $taskModel;
    protected $transactionModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->taskModel = new TaskModel();
        $this->transactionModel = new TransactionModel();
    }

    public function dashboard()
    {
        // Statistik Cepat
        $totalUsers = $this->userModel->where('active', 1)->countAllResults();
        $totalTasks = $this->taskModel->countAllResults();
        $totalTransactions = $this->transactionModel->countAllResults();
        
        // Pendapatan Platform (misal 10% dari setiap transaksi success - mock logic)
        $transactions = $this->transactionModel->where('status', 'completed')->findAll();
        $platformRevenue = 0;
        foreach ($transactions as $t) {
            if ($t['type'] === 'task_payment') {
                $platformRevenue += $t['amount'] * 0.10;
            }
        }

        // Ambil beberapa task terakhir untuk ditampilkan di dashboard
        $recentTasks = $this->taskModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

        return view('admin/dashboard', [
            'title'           => 'Admin Dashboard - Bantuin Yuk',
            'totalUsers'      => $totalUsers,
            'totalTasks'      => $totalTasks,
            'totalTransactions' => $totalTransactions,
            'platformRevenue' => $platformRevenue,
            'recentTasks'     => $recentTasks
        ]);
    }

    public function users()
    {
        // Ambil data users dengan peran "user"
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('users.*, auth_groups_users.group');
        $builder->join('auth_groups_users', 'auth_groups_users.user_id = users.id');
        $builder->where('auth_groups_users.group', 'user');
        
        $users = $builder->get()->getResultArray();

        return view('admin/users', [
            'title' => 'Manajemen User - Bantuin Yuk',
            'users' => $users
        ]);
    }

    public function helpers()
    {
        // Ambil data users dengan peran "helper"
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('users.*, auth_groups_users.group');
        $builder->join('auth_groups_users', 'auth_groups_users.user_id = users.id');
        $builder->where('auth_groups_users.group', 'helper');
        
        $helpers = $builder->get()->getResultArray();

        return view('admin/helpers', [
            'title'   => 'Manajemen Helper - Bantuin Yuk',
            'helpers' => $helpers
        ]);
    }

    public function tasks()
    {
        // Ambil semua task beserta info pembuat dan helper
        $tasks = $this->taskModel
                      ->select('tasks.*, u1.name as user_name, u2.name as helper_name')
                      ->join('users as u1', 'u1.id = tasks.user_id', 'left')
                      ->join('users as u2', 'u2.id = tasks.helper_id', 'left')
                      ->orderBy('created_at', 'DESC')
                      ->findAll();

        return view('admin/tasks', [
            'title' => 'Manajemen Pekerjaan - Bantuin Yuk',
            'tasks' => $tasks
        ]);
    }

    public function toggleUserStatus($id)
    {
        $user = $this->userModel->find($id);
        if ($user) {
            $newStatus = $user->active == 1 ? 0 : 1;
            $this->userModel->update($id, ['active' => $newStatus]);
            $msg = $newStatus == 1 ? 'User diaktifkan.' : 'User diblokir.';
            return redirect()->back()->with('message', $msg);
        }
        return redirect()->back()->with('error', 'User tidak ditemukan.');
    }

    public function disputes()
    {
        $disputeModel = new \App\Models\DisputeModel();
        
        $page = $this->request->getGet('page') ?? 1;
        $status = $this->request->getGet('status');

        $result = $disputeModel->getAllDisputes($page, 20, $status);

        return view('admin/disputes', [
            'title'    => 'Manajemen Komplain - Bantuin Yuk',
            'disputes' => $result['data'],
            'total'    => $result['total'],
            'page'     => $result['page'],
            'status'   => $status
        ]);
    }

    public function resolveDispute($id)
    {
        $disputeModel = new \App\Models\DisputeModel();
        $adminNote = $this->request->getPost('admin_note');
        
        $disputeModel->update($id, [
            'status'      => \App\Models\DisputeModel::STATUS_RESOLVED,
            'admin_note'  => $adminNote,
            'resolved_by' => auth()->id(),
            'resolved_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('message', 'Komplain berhasil diselesaikan.');
    }

    public function rejectDispute($id)
    {
        $disputeModel = new \App\Models\DisputeModel();
        $adminNote = $this->request->getPost('admin_note');
        
        $disputeModel->update($id, [
            'status'      => \App\Models\DisputeModel::STATUS_REJECTED,
            'admin_note'  => $adminNote,
            'resolved_by' => auth()->id(),
            'resolved_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('message', 'Komplain ditolak.');
    }
}
