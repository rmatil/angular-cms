<?php

namespace rmatil\cms\Utils;

class FileUtils {

    /**
     * Returns the maximum value of post_max_size and upload_max_filesize
     * as set in php.ini
     * @return string The max file size on which the upload will not fail
     */
    public static function getUploadMaxFileSize() {
        return min(self::getFileSizeInBytes(ini_get('post_max_size')), 
                   self::getFileSizeInBytes(ini_get('upload_max_filesize')));
    }

    /**
     * Converts the php.ini notation for number (like 2M) to 
     * an integer representation.
     * 
     * @param  string $size String to transform
     * @return integer      An integer representation in bytes
     */
    public static function getFileSizeInBytes($size)  {  
        $length = substr($size, -1);  
        $ret = substr($size, 0, -1);  
        
        switch(strtoupper($length)) {  
            case 'P':  
                $ret *= 1024;  
            case 'T':  
                $ret *= 1024;  
            case 'G':  
                $ret *= 1024;  
            case 'M':  
                $ret *= 1024;  
            case 'K':  
                $ret *= 1024;  
                break;  
        }

        return $ret;  
    }

    /**
     * Returns a given filesize in a human readable format.
     * 
     * @param  String $bytes    Number of bytes to convert
     * @return String           Filesize with KB, MB, ...
     */
    public static function getFileSizeHuman($bytes) {
        $output = $bytes." B"; 

        if ($bytes>=1024) {
            // output in kb with one decimal place
            $kb     = sprintf("%01.1f", $bytes/1024);
            $output = "$kb KB";
        }

        // bigger than 100kb
        if ($bytes>=100*1024) {
            $kb     = round($bytes/1024);
            $output = "$kb KB";
        }

        // bigger than 1024 KB
        if ($bytes>=1024*1024) {
            $mb     = sprintf("%01.1f", $bytes/1048576);
            $output = "$mb MB";
        }

        // bigger than 1024 MB
        if ($bytes>=1024*1024*1024) {
            $gb     = sprintf("%01.1f", $bytes/1073741824);
            $output = "$gb GB";
        }
    
        return $output;
     }


    /**
    * Replaces whitespaces with a dash and removes 
    * german umlauts. Additionally converts the 
    * string to lowercase.
    * 
    * @param string $string The string to apply this functionality on.
    * 
    * @return string The edited string
    */
    public static function replaceWhitespacesFromString($string) {
        // lower case everything
        $string    = strtolower($string);
        // clean up multiple dashes or whitespaces
        $string    = preg_replace("/[\s-]+/", " ", $string);
        // convert dots to dashes
        $string    = preg_replace("/\./", "-", $string);
        // convert whitespaces and underscore to dash
        $string    = preg_replace("/[\s_]/", "-", $string);
        // removes german umlauts
        $string    = preg_replace("/[\x7f-\xff]/", "", $string);

        return $string;
     }
}