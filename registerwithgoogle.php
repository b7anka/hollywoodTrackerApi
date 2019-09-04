<?php
    require_once('functions.php');    
    require_once('langs.php');

    header("Access-Control-Allow-Origin: *");

    $msg = '';
    $error = '';
    $success = false;

    $data = json_decode(file_get_contents('php://input'), true);    

    if(is_array($data))
    {
        $_POST = $data;
    }

    $lang = (!empty($_POST['lang']) ? $_POST['lang'] : 'en_US');

    $langToUse = setLangToUse($lang);

    if(empty($_POST['fullname']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['pass']))
    {        
        $msg = $langToUse[5];
        $error = 403;
        $success = false;
    }
    else
    {
        $nome = testInput($_POST['fullname']);
        $username = testInput($_POST['username']);
        $mail = testInput($_POST['email']);   
        $pass = $_POST['pass'];
        $thumbnail = $_POST['thumbnail'];

        $bodyToSend = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
            <html lang="pt">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            
            <title>'.$langToUse[35].'</title>
            
            <style type="text/css">
            </style>    
            </head>
            <body style="margin:0; padding:0; background-color:#F2F2F2;">
            <center>
                <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                    <tr>
                        <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                
                            <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                                <tr>
                                <td align="center" valign="top">
                                <img src="'.$defaultURL.'/images/logo.png" style="width:50%; height:auto;">
                                </td>
                                </tr>                                                
                                <tr>
                                    <td align="center" valign="top">
                                        <p style="color:green; font-size:1.5rem; font-weight:800;">'.$langToUse[36].$langToUse[85].'</p>  
                                        <p style="font-size:1.5rem; font-weight:800;">'.$langToUse[86].'</p>
                                        <p style="font-size:1rem; font-weight:800;">'.$langToUse[87].$username.'</p>
                                        <p style="font-size:1rem; font-weight:800;">'.$langToUse[41].$mail.'</p>
                                        <p style="font-size:1rem; font-weight:800;">'.$langToUse[88].$pass.'</p>                                                                                                                                                                  
                                    </td>
                                </tr>
                                <tr>                               
                                </tr>
                            </table>
                
                        </td>
                    </tr>
                    <tr>
                        <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                    </tr>
                </table>  
            </center>
            </body>
            </html>';

        $altbdy = $lang[36].$lang[84].' '.$lang[85].' '.$lang[86].$username.' '.$lang[41].$email.' '.$lang[87].$pass;

        if(verifyUser($username,$mail))
        {
            $msg = $langToUse[6];
            $error = 403;
            $success = false;
        }
        else
        {            
            if(registerUserWithGoogle($nome,$username,$mail,$pass,$thumbnail))
            {
                $from = DEFAULT_EMAIL_FROM;
                $nomeFrom = DEFAULT_NAME_FROM;
                $to = $mail;
                $name = $nome;
                $ass = $langToUse[7] . $name; 

                if(sendEmail($from,$nomeFrom,$to,$name,$ass,"",$langToUse, $bodyToSend, $altbdy))
                {
                    $msg = $langToUse[8];
                    $error = 0;
                    $success = true;
                }
                else
                {
                    $msg = $langToUse[9];
                    $error = 2;
                    $success = true;
                }
            }
            else
            {
                $msg = $langToUse[11];
                $error = 403;
                $success = false;
            }
        }
    }

    $response = [
        'msg'=>$msg,
        'error'=>$error,
        'success'=>$success
    ];

    echo json_encode($response);
?>