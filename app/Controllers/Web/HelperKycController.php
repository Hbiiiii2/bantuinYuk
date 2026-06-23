<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\HelperProfileModel;

class HelperKycController extends BaseController
{
    protected $helperProfileModel;

    public function __construct()
    {
        $this->helperProfileModel = new HelperProfileModel();
    }

    public function index()
    {
        $userId = auth()->id();
        $profile = $this->helperProfileModel->where('user_id', $userId)->first();

        // Jika tidak ada profil, buatkan
        if (!$profile) {
            $this->helperProfileModel->insert([
                'user_id' => $userId,
                'verification_status' => 'pending'
            ]);
            $profile = $this->helperProfileModel->where('user_id', $userId)->first();
        }

        // Jika sudah diverifikasi, tidak perlu isi ulang
        if ($profile['verification_status'] === 'verified') {
            return redirect()->to('/helper/dashboard')->with('message', 'Akun Anda sudah diverifikasi.');
        }

        return view('helper/kyc', [
            'title' => 'Verifikasi Identitas Mitra - Bantuin Yuk',
            'profile' => $profile
        ]);
    }

    public function submit()
    {
        $userId = auth()->id();
        $profile = $this->helperProfileModel->where('user_id', $userId)->first();

        if (!$profile) {
            return redirect()->back()->with('error', 'Profil tidak ditemukan.');
        }

        $rules = [
            'ktp_name' => 'required',
            'ktp_number' => 'required|min_length[16]|max_length[20]',
            'address' => 'required',
            'ktp_photo' => 'uploaded[ktp_photo]|max_size[ktp_photo,2048]|ext_in[ktp_photo,png,jpg,jpeg]',
            'selfie_photo' => 'uploaded[selfie_photo]|max_size[selfie_photo,2048]|ext_in[selfie_photo,png,jpg,jpeg]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Pastikan semua file berupa foto (max 2MB).');
        }

        $ktpPhotoFile = $this->request->getFile('ktp_photo');
        $selfiePhotoFile = $this->request->getFile('selfie_photo');

        $ktpPhotoName = $ktpPhotoFile->getRandomName();
        $selfiePhotoName = $selfiePhotoFile->getRandomName();

        $ktpPhotoFile->move('uploads/kyc', $ktpPhotoName);
        $selfiePhotoFile->move('uploads/kyc', $selfiePhotoName);

        $this->helperProfileModel->update($profile['id'], [
            'ktp_name' => $this->request->getPost('ktp_name'),
            'ktp_number' => $this->request->getPost('ktp_number'),
            'address' => $this->request->getPost('address'),
            'ktp_photo' => 'uploads/kyc/' . $ktpPhotoName,
            'selfie_photo' => 'uploads/kyc/' . $selfiePhotoName,
            'verification_status' => 'verified' // Auto-verified for now (can be pending if admin approval is needed)
        ]);

        return redirect()->to('/helper/dashboard')->with('message', 'Verifikasi KYC berhasil! Anda sekarang dapat mengambil pekerjaan.');
    }
}
