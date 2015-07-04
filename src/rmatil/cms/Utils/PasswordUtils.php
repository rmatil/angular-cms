<?php

namespace rmatil\cms\Utils;

/**
 * Utilities to handle hashes and strings
 * 
 * @author Raphael Matile <raphael.matile@gmail.com>
 */
abstract class PasswordUtils {

    /**
     * The hash algorithm used to hash strings
     * @var string
     */
    private static $hashAlgorithm = 'sha512';
    
    /**
     * The salt used to hash strings
     * @var string
     */
    private static $hashSalt = 'thisIsNotSafeChangeIt';

    /**
     * Hashes the given token using the hash algorithm and salt 
     * specified in this class.
     * 
     * @param string $token The token to hash
     * @return string The hashed token
     */
    public static function hash($token) {
        return hash(self::$hashAlgorithm, $token.self::$hashSalt);
    }

    /**
     * Checks whether the given plainToken is equal to the given hash
     * using the hash algorithm and hash salt specified in this class.
     * 
     * @param string $plainToken Unhashed token to check for equality
     * @param string $hash Hashed string used to compare $plainToken
     * @return boolean True, if the hash of $plainToken is equal to the given hash
     */
    public static function isEqual($plainToken, $hash) {
        return (hash(self::$hashAlgorithm, $plainToken.self::$hashSalt) === $hash);
    }
}