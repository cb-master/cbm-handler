<?php
/**
 * Project: Framework Handler
 * Author Name: Showket Ahmed
 * Author Email: riyadhtayf@gmail.com
 */

// Namespace
namespace CBM\Handler\Error;

class Error Extends \Exception
{
    // Error Display
    public static bool $display = true;

    // Errors
    private static $errors = [];

    // Set Error
    public static function set(string $message, int|string $code, string $file, int|string $line)
    {
        self::$errors[] = [
            'message'   =>  trim($message),
            'code'      =>  trim($code ?: 1000),
            'file'      =>  trim($file),
            'line'      =>  trim($line)
        ];
    }

    // Throw New Error
    public static function throw(object $e)
    {
        if($e instanceof \PDOException){
            $trace = $e->getTrace();
            $count = count($trace) - 1;
            for ($i=$count; $i >= 0 ; $i--){
                self::$errors[] = [
                    'message'   =>  $e->errorInfo[2] ?? 'Issues in Database!',
                    'code'      =>  'PDO9009',
                    'file'      =>  $trace[$i]['file'] ?? "Class: {$trace[$i]['class']}",
                    'line'      =>  $trace[$i]['line'] ?? 0
                ];
            }
        }else{
            self::$errors[] = [
                'message'   =>  $e->getMessage(),
                'code'      =>  $e->getCode() ?: 1000,
                'file'      =>  $e->getFile(),
                'line'      =>  $e->getLine()
            ];
        }
    }

    // Exception Message
    public function message():string
    {
        return "[<b>{$this->getCode()}</b>] - {$this->getMessage()}. In {$this->getFile()}:{$this->getLine()}<br>";
    }

    // Error Handler
    public static function errorHandler($severity, $message, $filename, $lineno)
    {
        if($severity){
            Error::set($message, 1000, $filename, $lineno);
            error_log("[1000]: {$message} in {$filename}>>{$lineno}.");
            // die;
        }
    }

    // Exception Handler
    public static function exceptionHandler(\Throwable $e)
    {
        self::throw($e);
        error_log("[{$e->getCode()}]: {$e->getMessage()} in {$e->getFile()}>>{$e->getLine()}.");
    }

    // Shutdown Function
    public static function shutdownHandler()
    {
        $e = error_get_last();
        $types = [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING, E_RECOVERABLE_ERROR];
        if($e && in_array($e['type'], $types)){
            Error::set($e['message'], 1000, $e['file'], $e['line']);
            error_log("[1000]: {$e['message']} in {$e['file']}>>{$e['line']}.");
        }

        if(self::$errors){
            http_response_code(500);
            // Display Errors
            call_user_func([__NAMESPACE__.'\Display', 'message'], self::$display, self::$errors);
            die;
        }
    }

    // Display Error & Store in File
    /**
     * @param $file - Optional Argument. Default is 
     */
    public static function registerErrorHandlers(bool $throw = true, ?string $file = null, bool $log = true)
    {
        $file = $file ?: __DIR__.'/../../../../../error.log';
        // Display Errors
        self::$display = $throw;
        ini_set('display_errors', $throw);
        ini_set('error_reporting', ($throw ? E_ALL : $throw));
        // Log Errors
        if($log){
            ini_set("log_errors", $throw);
            if(!file_exists($file)){
                file_put_contents($file, '');
            }
            ini_set('error_log', $file);
        }

        set_error_handler([Error::class, 'errorHandler']);
        set_exception_handler([Error::class, 'exceptionHandler']);
        register_shutdown_function([Error::class, 'shutdownHandler']);
    }
}