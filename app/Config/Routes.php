<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Home
$routes->get('/', 'Home::index');

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
    
    // Categories
    $routes->get('categories', 'AdminController::categories');
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
