<?php
/**
 * Project: Laika MVC Framework
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

        if(self::$errors && self::$display){
            http_response_code(500);
            $html = "<style>
            body{
                position:relative;
                margin:0;
            }
                div{
                    text-align:left!important;
                }
                /* PHP Error CSS Start */
                .err-box{
                    display:block;
                    overflow-x:auto;
                    background:#c6c6c6!important;
                    position:absolute!important;
                    top:0px!important;
                    width:100%;
                    height:100vh;
                    left:0px!important;
                    z-index:9999999!important;
                }
                .err-contents{
                    margin:auto!important;
                    width:80%!important;
                }
                .err-box h2{
                    font-size:3rem;
                    text-transform:uppercase;
                    text-align:center;
                    color:#9e6a6a!important;
                    margin-top:1rem;
                }
                .table{
                    width:100%;
                }
                table{
                    color:#580a0a!important;
                    text-align:left;
                    width:100%;
                }
                th{
                    font-size:15px;
                    padding: 10px 5px;
                    font-weight:bold;
                    text-transform:uppercase;
                }
                td{
                    min-width:20%;
                    max-width:100%;
                    font-size:14px!important;
                    margin-bottom:5px;
                    padding:5px;
                }
                table,th,td{
                    border:1px solid #9e6a6a;
                    border-collapse: collapse;
                }
                /* PHP Error CSS End */
            </style>
            <div class=\"err-box\">
                <div class=\"err-contents\">
                    <h2>SYSTEM ERROR!</h2>
                    <div class='table'>
                        <table>
                            <tr>
                                <th>Code</th>
                                <th>Messages</th>
                                <th>File</th>
                                <th>Line</th>
                            </tr>\n";
                            foreach(self::$errors as $error):                                
                                $html .= "<tr>
                                <td>{$error['code']}</td>
                                <td>{$error['message']}</td>
                                <td>{$error['file']}</td>
                                <td>{$error['line']}</td>
                            </tr>\n";
                            endforeach;                        
                        $html .= "</table>
                    </div>
                </div>
            </div>";
        echo $html;
        die;
        }elseif(self::$errors && !self::$display){
            http_response_code(500);
            die;
        }
    }
}