    <?php
    require_once('functions.php');    
    require_once('langs.php');

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");

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

    if(empty($_POST['title']) || empty($_POST['name']) || empty($_POST['email']) || empty($_POST['body']))
    {
        $msg = $langToUse[61];
        $error = 403;
        $success = false;
    }
    else
    {        
        $title = testInput($_POST['title']);
        $name = testInput($_POST['name']);  
        $mail = testInput($_POST['email']);
        $body = testInput($_POST['body']);

        $from = DEFAULT_EMAIL_FROM;
        $nomeFrom = $name;
        $to = DEFAULT_EMAIL_TO;
        $ass = $langToUse[62];
        
        $bodyToSend = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
        <html lang="pt">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        
        <title>'.$langToUse[62].'</title>
        
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
                                    <p style="color:green; font-size:1.5rem; font-weight:800;">'.$langToUse[62].$langToUse[63].$name.'</p>  
                                    <p style="color:green; font-size:1.5rem; font-weight:800;">'.$mail.'</p>
                                    <p style="font-size:1.5rem; font-weight:800;">'.$title.'</p>                                  
                                    <p style="font-size:1rem; font-weight:800;">'.$body.'</p>                                                                                        
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

        $altbdy = "$langToUse[62]$langToUse[63]$name, $mail, $title, $body";
        
        if(sendEmail($from,$nomeFrom,$to,$name,$ass,$token="",$langToUse,$bodyToSend,$altbdy))
        {                        
            $msg = $langToUse[64];
            $error = 0;
            $success = true; 
        }
        else
        {     
            $msg = $langToUse[65];
            $error = 2;
            $success = false;                                          
        }
        
    }

    $response = [
        'msg'=>$msg,
        'error'=>$error,
        'success'=>$success               
    ];

    echo json_encode($response);
?>