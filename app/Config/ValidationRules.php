<?php
namespace App\Config;

use Respect\Validation\Validator as V;

class ValidationRules {
    function common() {
        return [
            'username' => V::length(3, 25)->alnum('-')->noWhitespace(),
            'password' => V::length(3, 25)->alnum('-')->noWhitespace()
        ];
    }

    // POST /users
    function usersPost() {
        return [
            'username' => self::common()['username'],
            'password' => self::common()['password']
        ];
    }

    // POST /auth
    function authPost() {
        return [
            'username' => self::common()['username'],
            'password' => self::common()['password']
        ];
    }
}
?>