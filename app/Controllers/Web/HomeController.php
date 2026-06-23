<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        // If logged in, redirect to dashboard
        if (auth('session')->loggedIn()) {
            $user = auth('session')->user();
            $role = $user->getGroups()[0] ?? 'user';
            
            if ($role === 'admin') {
                return redirect()->to('/admin/dashboard');
            } elseif ($role === 'helper') {
                return redirect()->to('/helper/dashboard');
            } else {
                return redirect()->to('/user/dashboard');
            }
        }

        return view('landing/index', [
            'title' => 'Bantuin Yuk - Solusi Jasa Harian Cepat & Terpercaya'
        ]);
    }
}
