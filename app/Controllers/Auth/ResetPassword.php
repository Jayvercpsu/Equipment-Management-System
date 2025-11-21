<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class ResetPassword extends BaseController
{
    public function index($token)
    {
        $userModel = new UserModel();
        $user = $userModel->where('password_reset_token', $token)
            ->where('password_reset_expires >', date('Y-m-d H:i:s'))
            ->first();
        
        if (!$user) {
            return redirect()->to('/auth/forgot-password')->with('error', 'Invalid or expired reset token');
        }
        
        return view('auth/reset', ['title' => 'Reset Password', 'token' => $token]);
    }
    
    public function reset()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'token' => 'required',
            'password' => 'required|min_length[8]|regex_match[/^(?=.*[A-Z])(?=.*\d).+$/]',
            'password_confirm' => 'required|matches[password]'
        ], [
            'password' => [
                'regex_match' => 'Password must contain at least one uppercase letter and one number'
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $userModel = new UserModel();
        $user = $userModel->where('password_reset_token', $this->request->getPost('token'))
            ->where('password_reset_expires >', date('Y-m-d H:i:s'))
            ->first();
        
        if (!$user) {
            return redirect()->to('/auth/forgot-password')->with('error', 'Invalid or expired reset token');
        }
        
        $userModel->update($user['id'], [
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'password_reset_token' => null,
            'password_reset_expires' => null
        ]);
        
        return redirect()->to('/auth/login')->with('success', 'Password reset successfully! You can now login with your new password.');
    }
}