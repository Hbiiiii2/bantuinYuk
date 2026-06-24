<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Controllers\LoginController as ShieldLogin;
use CodeIgniter\Shield\Controllers\RegisterController as ShieldRegister;

class AuthController extends BaseController
{
    public function login()
    {
        if (auth('session')->loggedIn()) {
            return redirect()->to('/');
        }
        
        return view('auth/login', ['title' => 'Masuk - Bantuin Yuk']);
    }

    public function loginAction()
    {
        if (auth('session')->loggedIn()) {
            return redirect()->to('/');
        }

        $credentials = $this->request->getPost(setting('Auth.validFields'));
        $credentials = array_filter($credentials);
        $credentials['password'] = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        $attempt = auth('session')->attempt($credentials, $remember);

        if (! $attempt->isOK()) {
            return redirect()->route('login')->withInput()->with('error', $attempt->reason());
        }

        $user = auth()->user();
        
        // Check if user is blocked/suspended
        if ($user->status === 'suspended' || empty($user->active)) {
            auth('session')->logout();
            return redirect()->route('login')->withInput()->with('error', 'Akun Anda telah diblokir. Silakan hubungi admin.');
        }

        $role = $user->getGroups()[0] ?? 'user';
        
        if ($role === 'admin') {
            return redirect()->to('/admin/dashboard');
        } elseif ($role === 'helper') {
            return redirect()->to('/helper/dashboard');
        } else {
            return redirect()->to('/user/dashboard');
        }
    }

    public function register()
    {
        if (auth('session')->loggedIn()) {
            return redirect()->to('/');
        }
        
        return view('auth/register', ['title' => 'Daftar - Bantuin Yuk']);
    }
    
    public function registerAction()
    {
        if (auth('session')->loggedIn()) {
            return redirect()->to('/');
        }

        $users = auth()->getProvider();
        
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[auth_identities.secret]',
            'phone'    => 'required|min_length[10]|max_length[15]',
            'password' => 'required|min_length[8]',
            'role'     => 'required|in_list[user,helper]'
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = new \CodeIgniter\Shield\Entities\User([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'phone'    => $this->request->getPost('phone'),
            'password' => $this->request->getPost('password'),
            'role'     => $this->request->getPost('role'),
        ]);

        $users->save($user);
        $user = $users->findById($users->getInsertID());
        $user->addGroup($this->request->getPost('role'));

        // Activate immediately for simplicity
        $user->activate();

        // Login automatically
        auth('session')->login($user);

        $role = $this->request->getPost('role');
        return redirect()->to($role === 'helper' ? '/helper/dashboard' : '/user/dashboard');
    }

    public function logoutAction()
    {
        auth('session')->logout();
        return redirect()->to('/');
    }
}
