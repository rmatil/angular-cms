<?php

namespace rmatil\cms\Utils;

class PasswordUtils {

    private static $HASH_ALGORITHM = 'sha512';
    private static $HASH_SALT = 'thisIsNotSafeChangeIt';

    public static function hash($token) {
        return hash(self::$HASH_ALGORITHM, $token.self::$HASH_SALT);
    }

    public static function isEqual($plainToken, $hash) {
        return (hash(self::$HASH_ALGORITHM, $plainToken.self::$HASH_SALT) === $hash);
    }
}