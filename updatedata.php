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

    if(empty($_POST['id']) || empty($_POST['type']) || empty($_POST['content']))
    {
        $msg= $langToUse[5];
        $error = 403;
        $success = false;
    }
    else
    {        
        $id = $_POST['id'];
        $type = $_POST['type'];
        $idUser = ($type == PROFILE ? $id : $_POST['idUser']);
        $timeStamp = $_POST['time'];
        $content = explode(";",$_POST['content']);
        updateLastActivity($idUser, $timeStamp);

        if($type == PROFILE && count($content)>2)
        {
            $fetchInfo = fetchInfo($id);

            if($fetchInfo[2] != $content[0] && !verifyUser($content[0])) 
            {                
                $result = updateData($id,$type,$content);
                if($result)
                {
                    $msg= $langToUse[33];
                    $error = 0;
                    $success = true;
                } 
                else
                {
                    $msg= $langToUse[34];
                    $error = 403;
                    $success = false;            
                }                         
            }
            elseif($fetchInfo[3] != $content[2] && !verifyUser("",$content[2]))
            {        
                $result = updateData($id,$type,$content);
                if($result)
                {
                    $msg= $langToUse[33];
                    $error = 0;
                    $success = true;
                } 
                else
                {
                    $msg= $langToUse[34];
                    $error = 403;
                    $success = false;            
                }                                 
            }
            elseif($fetchInfo[2] == $content[0] && $fetchInfo[3] == $content[2] && verifyUser($content[0],$content[2]))
            {
                 $result = updateData($id,$type,$content);
                 if($result)
                {
                    $msg= $langToUse[33];
                    $error = 0;
                    $success = true;
                } 
                else
                {
                    $msg= $langToUse[34];
                    $error = 403;
                    $success = false;            
                }                                 
            }
            else
            {
                    $msg= $langToUse[6];
                    $error = 403;
                    $success = false; 
            }
        }
        else
        {
            $result = updateData($id,$type,$content);

            if($result)
            {
                $msg= $langToUse[29];
                $error = 0;
                $success = true;
            } 
            else
            {
                $msg= $langToUse[30];
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