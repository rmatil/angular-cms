<?php

namespace rmatil\cms\Login;

/**
 * Utilities to handle hashes and strings
 * 
 * @author Raphael Matile <raphael.matile@gmail.com>
 */
abstract class PasswordHandler {

    /**
     * Hashes the given token using the hash algorithm and salt 
     * specified in this class. Uses a default salt, stored in the hash
     * 
     * @param string $token The token to hash
     * @return string The hashed token
     */
    public static function hash($token) {
        return password_hash($token, PASSWORD_BCRYPT);
    }

    /**
     * Checks whether the given plainToken is equal to the given hash
     * using the hash algorithm and hash salt specified in this class.
     * 
     * @param string $plainToken Unhashed token to check for equality
     * @param string $hash Hashed string used to compare $plainToken
     * @return boolean True, if the hash of $plainToken is equal to the given hash
     */
    public static function isEqual($plainToken,  $hash) {
        return password_verify($plainToken, $hash);
    }
}