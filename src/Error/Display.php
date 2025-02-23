<?php
/**
 * Project: Framework Handler
 * Author Name: Showket Ahmed
 * Author Email: riyadhtayf@gmail.com
 */

// Namespace
namespace CBM\Handler\Error;

class Display
{
    // Diplay Error Message
    /**
     * @param bool $display - Optional Argument. Message Will Display if true.
     * @param array $errors - Optional Argument. Error Messages.
     */
    public static function message(bool $display = false, array $errors = []):void
    {
        $html = "<html>\n<head>\n<title>Application Error</title>\n</head>\n<body style=\"margin:0;\">\n<div style=\"height:100vh;position:relative;\">\n<h1 style=\"text-align:center;color:#ec8e8e; font-size:3rem; position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);margin:0;\">!! 500 Internal Server Error !!</h1>\n</div>\n</body></html>";
        if($display){
            $html = "<html>
            <head>
                <title>Application Error</title>
            <style>
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
            </head>
            <body>
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
                                foreach($errors as $error):
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
                </div>
            </body>
        </html>";
        }
        echo $html;
    }
}