<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ============================================================
// WEB ROUTES (Pure PHP Views + Tailwind)
// ============================================================
$routes->get('/', '\App\Controllers\Web\HomeController::index');

// Auth Web Routes
$routes->get('login', '\App\Controllers\Web\AuthController::login', ['as' => 'login']);
$routes->post('login', '\App\Controllers\Web\AuthController::loginAction');
$routes->get('register', '\App\Controllers\Web\AuthController::register', ['as' => 'register']);
$routes->post('register', '\App\Controllers\Web\AuthController::registerAction');
$routes->post('logout', '\App\Controllers\Web\AuthController::logoutAction', ['as' => 'logout']);

// Dashboard Web Routes (Protected by Session)
$routes->group('', ['filter' => 'session'], function ($routes) {
    $routes->get('user/dashboard', '\App\Controllers\Web\DashboardController::user');
    $routes->get('helper/dashboard', '\App\Controllers\Web\DashboardController::helper');
    
    // Web Profile
    $routes->get('profile', '\App\Controllers\Web\ProfileController::index');
    $routes->get('profile/edit', '\App\Controllers\Web\ProfileController::edit');
    $routes->post('profile/update', '\App\Controllers\Web\ProfileController::update');
    
    // Web User Tasks
    $routes->get('user/tasks', '\App\Controllers\Web\UserTaskController::index');
    $routes->get('user/tasks/create', '\App\Controllers\Web\UserTaskController::create');
    $routes->post('user/tasks/store', '\App\Controllers\Web\UserTaskController::store');
    $routes->get('user/tasks/(:num)', '\App\Controllers\Web\UserTaskController::detail/$1');
    $routes->post('user/tasks/(:num)/complete', '\App\Controllers\Web\UserTaskController::complete/$1');
    $routes->post('user/tasks/(:num)/rate', '\App\Controllers\Web\UserTaskController::rateHelper/$1');
    
    // Web Helper Tasks
    $routes->get('helper/tasks/explore', '\App\Controllers\Web\HelperTaskController::explore');
    $routes->get('helper/tasks/my-tasks', '\App\Controllers\Web\HelperTaskController::myTasks');
    $routes->get('helper/tasks/(:num)', '\App\Controllers\Web\HelperTaskController::detail/$1');
    $routes->post('helper/tasks/(:num)/take', '\App\Controllers\Web\HelperTaskController::take/$1');
    $routes->post('helper/tasks/(:num)/upload-progress', '\App\Controllers\Web\HelperTaskController::uploadProgress/$1');
    $routes->post('helper/tasks/(:num)/rate', '\App\Controllers\Web\HelperTaskController::rateUser/$1');
    
    // Web Wallet
    $routes->get('wallet', '\App\Controllers\Web\WalletController::index');
    $routes->post('wallet/topup', '\App\Controllers\Web\WalletController::topup');

    // Web Admin Routes
    $routes->get('admin/dashboard', '\App\Controllers\Web\AdminController::dashboard');
    $routes->get('admin/users', '\App\Controllers\Web\AdminController::users');
    $routes->get('admin/helpers', '\App\Controllers\Web\AdminController::helpers');
    $routes->get('admin/tasks', '\App\Controllers\Web\AdminController::tasks');
    $routes->get('admin/disputes', '\App\Controllers\Web\AdminController::disputes');
    $routes->post('admin/disputes/(:num)/resolve', '\App\Controllers\Web\AdminController::resolveDispute/$1');
    $routes->post('admin/disputes/(:num)/reject', '\App\Controllers\Web\AdminController::rejectDispute/$1');
    $routes->post('admin/toggle-user/(:num)', '\App\Controllers\Web\AdminController::toggleUserStatus/$1');

    // Web Notifications
    $routes->get('notifications', '\App\Controllers\Web\NotificationController::index');
    $routes->post('notifications/(:num)/read', '\App\Controllers\Web\NotificationController::markAsRead/$1');
    $routes->post('notifications/mark-all-read', '\App\Controllers\Web\NotificationController::markAllAsRead');

    // Web Pusat Resolusi (Disputes)
    $routes->get('disputes', '\App\Controllers\Web\DisputeController::index');
    $routes->get('disputes/create/(:num)', '\App\Controllers\Web\DisputeController::create/$1');
    $routes->post('disputes/store/(:num)', '\App\Controllers\Web\DisputeController::store/$1');
    $routes->get('disputes/(:num)', '\App\Controllers\Web\DisputeController::detail/$1');
});

// ============================================================
// AUTH ROUTES (Public - No Auth Required + Rate Limiting)
// ============================================================
$routes->post('api/v1/auth/register', 'AuthController::register');
$routes->post('api/v1/auth/login', 'AuthController::login', ['filter' => 'authRateLimit']);

// ============================================================
// AUTH ROUTES (Protected - Token Required)
// ============================================================
$routes->group('api/v1/auth', ['filter' => 'tokens'], function ($routes) {
    $routes->post('logout', 'AuthController::logout');
    $routes->get('me', 'AuthController::me');
    $routes->put('me', 'AuthController::updateProfile');
});

// ============================================================
// USER ROUTES (Protected - Token Required)
// ============================================================
$routes->group('api/v1/user', ['filter' => 'tokens'], function ($routes) {
    $routes->get('profile', 'AuthController::me');
    $routes->put('profile', 'AuthController::updateProfile');
});

// ============================================================
// TASK ROUTES (Protected - Token Required)
// ============================================================
$routes->group('api/v1/tasks', ['filter' => 'tokens'], function ($routes) {
    $routes->get('/', 'TaskController::index');
    $routes->get('my', 'TaskController::myTasks');
    $routes->get('(:num)', 'TaskController::show/$1');
    $routes->post('/', 'TaskController::store');
    $routes->put('(:num)', 'TaskController::update/$1');
    $routes->delete('(:num)', 'TaskController::delete/$1');
    $routes->post('(:num)/complete', 'TaskController::complete/$1');
    
    // Attachments
    $routes->get('(:num)/attachments', 'TaskController::getAttachments/$1');
    $routes->post('(:num)/attachments', 'TaskController::uploadAttachment/$1');
    $routes->delete('(:num)/attachments/(:num)', 'TaskController::deleteAttachment/$1/$2');
    
    // Reviews
    $routes->post('(:num)/review', 'TaskController::createReview/$1');
    $routes->get('(:num)/review', 'TaskController::getReview/$1');
});

// ============================================================
// HELPER ROUTES (Protected - Token Required + Helper Role Only)
// ============================================================
$routes->group('api/v1/helpers', ['filter' => 'tokens', 'filter' => 'role:helper'], function ($routes) {
    $routes->get('/', 'HelperController::list');
    $routes->get('available-tasks', 'HelperController::availableTasks');
    $routes->get('my-tasks', 'HelperController::myTasks');
    $routes->get('profile', 'HelperController::profile');
    $routes->put('profile', 'HelperController::updateProfile');
    $routes->put('location', 'HelperController::updateLocation');
    $routes->post('verification', 'HelperController::submitVerification');
    $routes->get('stats', 'HelperController::stats');
    $routes->get('(:num)', 'HelperController::show/$1');

    // Task actions (helper only)
    $routes->post('tasks/(:num)/accept', 'HelperController::acceptTask/$1');
    $routes->post('tasks/(:num)/start', 'HelperController::startTask/$1');
    $routes->post('(:num)/submit', 'HelperController::submitTask/$1');
    
    // Progress
    $routes->post('tasks/(:num)/progress', 'HelperController::createProgress/$1');
    $routes->get('tasks/(:num)/progress', 'HelperController::getProgress/$1');
    
    // Attachments (helper)
    $routes->post('tasks/(:num)/attachments', 'HelperController::uploadAttachment/$1');
    
    // Reviews (helper can view)
    $routes->get('reviews', 'HelperController::getReviews');
    $routes->get('rating-summary', 'HelperController::getRatingSummary');
});

// ============================================================
// WALLET ROUTES (Protected - Token Required)
// ============================================================
$routes->group('api/v1/wallet', ['filter' => 'tokens'], function ($routes) {
    $routes->get('/', 'WalletController::index');
    $routes->get('transactions', 'WalletController::transactions');
    $routes->get('transactions/(:num)', 'WalletController::show/$1');
    $routes->post('release-payment/(:num)', 'WalletController::releasePayment/$1');
    $routes->post('withdraw', 'WalletController::withdraw');
});

// ============================================================
// NOTIFICATION ROUTES (Protected - Token Required)
// ============================================================
$routes->group('api/v1/notifications', ['filter' => 'tokens'], function ($routes) {
    $routes->get('/', 'NotificationController::index');
    $routes->get('unread-count', 'NotificationController::unreadCount');
    $routes->get('(:num)', 'NotificationController::show/$1');
    $routes->post('(:num)/read', 'NotificationController::markAsRead/$1');
    $routes->post('read-all', 'NotificationController::markAllAsRead');
});

// ============================================================
// CATEGORY ROUTES (Public - Get Categories)
// ============================================================
$routes->get('api/v1/categories', 'AdminController::categories', ['filter' => 'cors']);

// ============================================================
// DISPUTE ROUTES (Protected - Token Required)
// ============================================================
$routes->group('api/v1/disputes', ['filter' => 'tokens'], function ($routes) {
    $routes->get('/', 'DisputeController::index');
    $routes->post('/', 'DisputeController::create');
    $routes->get('(:num)', 'DisputeController::show/$1');
});

// ============================================================
// ADMIN ROUTES (Protected - Token Required + Admin Role)
// ============================================================
$routes->group('api/v1/admin', ['filter' => 'tokens', 'filter' => 'role:admin'], function ($routes) {
    // Dashboard & Analytics
    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->get('analytics', 'AdminController::analytics');
    
    // User Management
    $routes->get('users', 'AdminController::users');
    $routes->get('users/(:num)', 'AdminController::userDetail/$1');
    $routes->put('users/(:num)/status', 'AdminController::updateUserStatus/$1');
    
    // Helper Management
    $routes->get('helpers', 'AdminController::helpers');
    $routes->get('helpers/(:num)', 'AdminController::helperDetail/$1');
    $routes->post('helpers/(:num)/verify', 'AdminController::verifyHelper/$1');
    $routes->post('helpers/(:num)/reject', 'AdminController::rejectHelper/$1');
    
    // Task Management
    $routes->get('tasks', 'AdminController::tasks');
    $routes->get('tasks/(:num)', 'AdminController::taskDetail/$1');
    
    // Transaction Management
    $routes->get('transactions', 'AdminController::transactions');
    $routes->get('transactions/(:num)', 'AdminController::transactionDetail/$1');
    
    // Wallet Monitoring
    $routes->get('wallets', 'AdminController::wallets');
    
    // Categories (admin only)
    $routes->post('categories', 'AdminController::createCategory');
    $routes->put('categories/(:num)', 'AdminController::updateCategory/$1');
    $routes->delete('categories/(:num)', 'AdminController::deleteCategory/$1');
    
    // Reviews
    $routes->get('reviews', 'AdminController::getReviews');
    
    // Wallet & Transactions (existing from Wallet domain)
    $routes->get('withdrawals', 'AdminWalletController::pendingWithdrawals');
    $routes->post('withdrawals/(:num)/approve', 'AdminWalletController::approveWithdraw/$1');
    $routes->post('withdrawals/(:num)/reject', 'AdminWalletController::rejectWithdraw/$1');
    
    // Disputes (existing from Dispute domain)
    $routes->get('disputes', 'DisputeController::adminIndex');
    $routes->post('disputes/(:num)/review', 'DisputeController::review/$1');
    $routes->post('disputes/(:num)/resolve', 'DisputeController::resolve/$1');
    $routes->post('disputes/(:num)/reject', 'DisputeController::reject/$1');
});