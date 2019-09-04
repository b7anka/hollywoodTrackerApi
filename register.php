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

        if(verifyUser($username,$mail))
        {
            $msg = $langToUse[6];
            $error = 403;
            $success = false;
        }
        else
        {            
            if(registerUser($nome,$username,$mail,$pass,$thumbnail))
            {                                                
                $token = sha1(uniqid($mail, true));
                $from = DEFAULT_EMAIL_FROM;
                $nomeFrom = DEFAULT_NAME_FROM;
                $to = $mail;
                $name = $nome;
                $ass = $langToUse[7] . $name; 

                if(insertToken($mail, $token))
                {
                    if(sendEmail($from,$nomeFrom,$to,$name,$ass,$token,$langToUse))
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
                        $msg = $langToUse[10];
                        $error = 3;
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