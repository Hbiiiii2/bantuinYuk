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

    public function disputeDetail($id)
    {
        $disputeModel = new \App\Models\DisputeModel();
        $taskModel = new \App\Models\TaskModel();

        // Get single dispute using the existing method query logic by creating a custom one or just simple fetch
        $builder = $disputeModel->builder();
        $builder->select('disputes.*, tasks.title as task_title, users.name as creator_name, helpers.name as helper_name');
        $builder->join('tasks', 'tasks.id = disputes.task_id', 'left');
        $builder->join('users', 'users.id = disputes.user_id', 'left');
        $builder->join('users as helpers', 'helpers.id = disputes.helper_id', 'left');
        $builder->where('disputes.id', $id);
        
        $dispute = $builder->get()->getRowArray();

        if (!$dispute) {
            return redirect()->back()->with('error', 'Komplain tidak ditemukan.');
        }

        $task = $taskModel->find($dispute['task_id']);

        $helperProfileModel = new \App\Models\HelperProfileModel();
        $helperProfile = null;
        if ($dispute['helper_id']) {
            $helperProfile = $helperProfileModel->where('user_id', $dispute['helper_id'])->first();
        }

        // Fetch user info for phone numbers if not in profile
        $userModel = new \App\Models\UserModel();
        $helperUser = $dispute['helper_id'] ? $userModel->find($dispute['helper_id']) : null;

        return view('admin/dispute_detail', [
            'title'   => 'Detail Komplain - Bantuin Yuk',
            'dispute' => $dispute,
            'task'    => $task,
            'helperProfile' => $helperProfile,
            'helperUser' => $helperUser
        ]);
    }

    public function resolveDispute($id)
    {
        $disputeModel = new \App\Models\DisputeModel();
        $taskModel = new \App\Models\TaskModel();
        $walletModel = new \App\Models\WalletModel();

        $dispute = $disputeModel->find($id);
        if (!$dispute) {
            return redirect()->back()->with('error', 'Komplain tidak ditemukan.');
        }

        $task = $taskModel->find($dispute['task_id']);
        $adminNote = $this->request->getPost('admin_note');
        $fundAction = $this->request->getPost('fund_action'); // 'refund' or 'release'
        $suspendHelper = $this->request->getPost('suspend_helper');
        
        $db = \Config\Database::connect();
        $db->transStart();

        if ($fundAction === 'refund') {
            // Refund: uang kembali ke user, pekerjaan dianggap batal.
            $walletModel->releaseHeldBalance($dispute['user_id'], $task['price']);
            $taskModel->update($task['id'], ['status' => 'cancelled']);
        } elseif ($fundAction === 'release') {
            // Release: uang dicairkan ke helper, pekerjaan dianggap selesai.
            $walletModel->confirmHeldBalance($task['user_id'], $task['helper_id'], $task['price'], $task['id']);
            $taskModel->update($task['id'], ['status' => 'completed']);
        }

        // Suspend Helper jika dicentang
        if ($suspendHelper == '1' && $dispute['helper_id']) {
            $userModel = new \App\Models\UserModel();
            $userModel->update($dispute['helper_id'], ['active' => 0]);
        }

        $disputeModel->update($id, [
            'status' => 'resolved',
            'admin_note' => $adminNote,
            'resolved_by' => auth()->id(),
            'resolved_at' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses resolusi.');
        }

        return redirect()->to('/admin/disputes')->with('message', 'Komplain berhasil diselesaikan dan dana telah diproses.');
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
