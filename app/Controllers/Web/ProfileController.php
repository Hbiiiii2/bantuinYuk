<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use CodeIgniter\Files\File;

class ProfileController extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        
        return view('profile/index', [
            'title' => 'Profil Saya - Bantuin Yuk',
            'user'  => $user
        ]);
    }

    public function edit()
    {
        $user = auth()->user();
        
        return view('profile/edit', [
            'title' => 'Edit Profil - Bantuin Yuk',
            'user'  => $user
        ]);
    }

    public function update()
    {
        $user = auth()->user();
        $users = auth()->getProvider();

        $rules = [
            'name'  => 'required|min_length[3]|max_length[100]',
            'phone' => 'required|min_length[10]|max_length[15]',
        ];

        // Handle photo upload if exists
        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && ! $photo->hasMoved()) {
            $rules['photo'] = 'is_image[photo]|mime_in[photo,image/jpg,image/jpeg,image/png]|max_size[photo,2048]';
        }

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update basic info
        $user->fill([
            'name'  => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
        ]);

        // Process photo
        if ($photo && $photo->isValid() && ! $photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move(FCPATH . 'uploads/profiles', $newName);
            
            // Delete old photo if exists
            if (!empty($user->photo) && file_exists(FCPATH . 'uploads/profiles/' . $user->photo)) {
                unlink(FCPATH . 'uploads/profiles/' . $user->photo);
            }
            
            $user->photo = $newName;
        }

        $users->save($user);

        return redirect()->to('/profile')->with('message', 'Profil berhasil diperbarui!');
    }
}
