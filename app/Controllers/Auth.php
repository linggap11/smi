<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel = "";
    
    public function __construct()
    {
        $user = session()->get('role');
        if ($user) {            
            if ($user == "administrator") {
                header('Location: '.base_url('/admin/dashboard'));
                exit;
            } else {
                header('Location: '.base_url('/gudang/dashboard'));
                exit;
            }
        }
        $this->userModel = new UserModel();
    }

    public function index() {
        return view('login');
    }

    public function loginProccess() {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $user = $this->userModel->getWhere(['username' => $username])->getRow();
        if ($user) {
            if (password_verify($password, $user->password)) {
                $params = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role
                ];
                session()->set($params);
                if ($user->role == "administrator") {
                    return redirect()->to(base_url('admin/dashboard'))->with('message', 'Login Successful!');
                    // if ($currentPage == base_url()) {
                    //     return redirect()->to(base_url('admin/dashboard'))->with('message', 'Login Successful!');
                    // } else {
                    //     return redirect()->to($currentPage)->with('message', 'Login Successful!');
                    // }
                } elseif ($user->role == "gudang_1" || $user->role == "gudang_2") {
                    return redirect()->to(base_url('gudang/dashboard'))->with('message', 'Login Successful!');
                }
            } else {
                return redirect()->back()->with('error', 'Incorrect Password!');
            }
        } else {
            return redirect()->back()->with('error', 'Username Not Found!');
        }
    }

    public function logout() {
        $data = array(
            'user_id', 'name', 'role'
        );
        session()->remove($data);
        return redirect()->to(base_url('/'));
    }
}
