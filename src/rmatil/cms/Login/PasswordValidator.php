<?php


namespace rmatil\cms\Login;


use rmatil\cms\Exceptions\PasswordInvalidException;

/**
 * Utilities for password validation
 *
 * @package rmatil\cms\Login
 */
class PasswordValidator {

    /**
     * @param $password string The password to validate
     *
     * @throws \rmatil\cms\Exceptions\PasswordInvalidException If the password contains less than 8 characters
     */
    public static function validatePassword($password) {
        if (0 === strlen($password)) {
            throw new PasswordInvalidException('Password can not be empty');
        }

        if (8 > strlen($password)) {
            throw new PasswordInvalidException('Password must be at least 8 characters long');
        }
    }
}