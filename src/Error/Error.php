<?php
/**
 * Project: Laika MVC Framework
 * Author Name: Showket Ahmed
 * Author Email: riyadhtayf@gmail.com
 */

// Namespace
namespace CBM\Handler;

class Error Extends \Exception
{
    // Errors
    private static $errors = [];

    // Set Error
    public static function set(string $message, int|string $code, string $file, int|string $line):bool
    {
        self::$errors[] = [
            'message'   =>  trim($message),
            'code'      =>  trim($code ?: 1000),
            'file'      =>  trim($file),
            'line'      =>  trim($line)
        ];
        return true;
    }

    // Throw New Error
    public static function throw(object $e)
    {
        self::$errors[] = [
            'message'   =>  $e->message,
            'code'      =>  $e->code ?: 1000,
            'file'      =>  $e->file,
            'line'      =>  $e->line
        ];
    }

    // Get Errors
    public static function getErrors():array
    {
        return self::$errors;
    }

    // Exception Message
    public function message():string
    {
        return "[<b>{$this->getCode()}</b>] - {$this->getMessage()}. In {$this->getFile()}:{$this->getLine()}<br>";
    }
}