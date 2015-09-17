<?php


namespace rmatil\cms\Login;


class PasswordValidator {

    public static function validatePassword($password) {
        if (0 === strlen($password)) {
            return false;
        }

        if (8 > strlen($password)) {
            return false;
        }

        return true;
    }
}