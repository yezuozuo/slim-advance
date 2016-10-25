<?php

namespace App\Auth;

use App\Models\User;

/**
 * Class Auth
 *
 * @package App\Auth
 */
class Auth {

    /**
     * @return mixed
     */
    public function user() {
        return User::find(isset($_SESSION['user']) ? $_SESSION['user'] : '');
    }

    /**
     * @return bool
     */
    public function check() {
        return isset($_SESSION['user']);
    }

    /**
     * @param $email
     * @param $password
     * @return bool
     */
    public function attempt($email, $password) {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            $_SESSION['user'] = $user->id;

            return true;
        }

        return false;
    }

    public function logout() {
        unset($_SESSION['user']);
    }
}