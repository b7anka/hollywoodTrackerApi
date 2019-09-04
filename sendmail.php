<?php
    require_once('functions.php');
    require_once('langs.php');    
    
    $msg = '';
    $error = '';
    $success = false;

    $lang = 'en_US';

    if(!empty($_GET['lang']))
    {
        $lang = $_GET['lang'];
    }

    $langToUse = setLangToUse($lang);

    if(empty($_GET['id']))
    {
        echo '<center><h2>'.$langToUse[20].'</h2></center>';
    }
    else
    {                                       
        $fetchData = fetchInfo($_GET['id']);

        $nome = $fetchData['fullname'];
        $mail = $fetchData['email'];
        $token = $fetchData['token'];

        $from = DEFAULT_EMAIL_FROM;
        $nomeFrom = DEFAULT_NAME_FROM;
        $to = $mail;
        $name = $nome;
        $ass = $langToUse[7] . $name; 

        if(sendEmail($from,$nomeFrom,$to,$name,$ass,$token))
        {
            $msg = $langToUse[18];
            $error = 0;
            $success = true;
        }
        else
        {
            $msg = $langToUse[19];
            $error = 403;
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