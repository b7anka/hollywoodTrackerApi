<?php
require('connectDB.php');
    require('libraries/omdb/omdb.class.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'libraries/phpmailer/src/Exception.php';
    require 'libraries/phpmailer/src/PHPMailer.php';
    require 'libraries/phpmailer/src/SMTP.php';

    $defaultURL = "https://api.hollywoodtracker.eu";

    define("DEFAULT_IMAGE","$defaultURL/photos/default.png");
    define("BOOKS","books");
    define("MOVIES","movies");
    define("TVSHOWS","tvshows");
    define("RECENTLY_WATCHED","recentlywatched");
    define("PROFILE","profile");
    define("BOOKS_DB","ht_books");
    define("MOVIES_DB","ht_movies");
    define("TVSHOWS_DB","ht_tvshows");
    define("RECENTLY_WATCHED_DB","ht_movies");
    define("PROFILE_DB","ht_users");
    define("DEFAULT_EMAIL_TO","admin@hollywoodtracker.eu");
    define("DEFAULT_EMAIL_FROM","donot-reply@hollywoodtracker.eu");
    define("DEFAULT_NAME_FROM","Tiago Moreira");
    define("DEFAULT_EMAIL_USERNAME","YOUR_EMAIL_USERNAME");
    define("DEFAULT_EMAIL_PASSWORD","YOUR_EMAIL_PASSWORD");
    define("DEFAULT_EMAIL_HOST","YOUR_MAIL_SERVER");
    define("DEFAULT_EMAIL_PORT",587);
    define("TOTAL_VIDEOS_TO_WIN",200);

    function registerUser($fullname, $username, $email, $password, $thumbnail, $status='R')
    {
        global $con;
        global $defaultURL;
    
        $options = ['cost' => 15,];
        $temp = password_hash($password, PASSWORD_BCRYPT, $options);

        if(empty($thumbnail))
        {
            $thumbnail = DEFAULT_IMAGE;
        }
        else
        {
            $imageDecodedSuccessfully = decodeAndStoreImage($thumbnail);

            if(!empty($imageDecodedSuccessfully))
            {
                $thumbnail = "$defaultURL/photos/$imageDecodedSuccessfully";
            }
            else
            {
                $thumbnail = DEFAULT_IMAGE;
            }
        }
          
        $comando = "INSERT INTO ht_users (fullname, username, email, password, thumbnail, status) 
        VALUES ('$fullname', '$username', '$email', '$temp', '$thumbnail', '$status');";
        $query 	= mysqli_query($con, $comando);       

        if($query)
        { 
            return true;
        }
        return false;
    }

function registerUserWithGoogle($fullname, $username, $email, $password, $thumbnail, $status='V')
{
    global $con;

    $options = ['cost' => 15,];
    $temp = password_hash($password, PASSWORD_BCRYPT, $options);

    if(empty($thumbnail))
    {
        $thumbnail = DEFAULT_IMAGE;
    }

    $comando = "INSERT INTO ht_users (fullname, username, email, password, thumbnail, status, google_account) 
        VALUES ('$fullname', '$username', '$email', '$temp', '$thumbnail', '$status', 1);";
    $query 	= mysqli_query($con, $comando);

    if($query)
    {
        return true;
    }
    return false;
}

    function updateLastActivity($idUser, $timeStamp)
    {
        global $con;

        $sql = "UPDATE ht_users SET last_activity=$timeStamp WHERE id_user=$idUser";

        $query 	= mysqli_query($con, $sql);

        if($query)
        {
            return true;
        }

        return false;
    }

    function updateRewardedVideos($idUser, $value)
    {
        global $con;

        $sql = "UPDATE ht_users SET videos_watched=$value WHERE id_user=$idUser";

        $query 	= mysqli_query($con, $sql);

        if($query)
        {
            return true;
        }

        return false;
    }

    function getLastActivity($username)
    {
        global $con;
        $sql = "SELECT last_activity FROM ht_users WHERE username='" .$username. "'";
        $query = mysqli_query($con, $sql);
        $value = mysqli_fetch_array($query);
        $lastActivity = $value[0];

        return $lastActivity;
    }

    function getAppVersion()
    {
        global $con;
        $sql = "SELECT app_version, version_code, app_version_ios, build_version  FROM ht_version WHERE id=1";
        $query = mysqli_query($con, $sql);
        $value = mysqli_fetch_array($query);

        return $value;
    }

    function getAppApprovedForAppStoreStatus()
    {
        global $con;
        $sql = "SELECT was_approved_for_app_store FROM ht_version WHERE id=1";
        $query = mysqli_query($con, $sql);
        $value = mysqli_fetch_array($query);

        return $value[0];
    }

function updateAppVersion($version, $versionCode)
{
    global $con;

    $sql = "UPDATE ht_version SET app_version=$version, version_code=$versionCode WHERE id=1";

    $query 	= mysqli_query($con, $sql);

    if($query)
    {
        return true;
    }

    return false;
}

    function getRewardedVideos($idUser)
    {
        global $con;
        $sql = "SELECT videos_watched FROM ht_users WHERE id_user=$idUser";
        $query = mysqli_query($con, $sql);
        $value = mysqli_fetch_array($query);
        $rewardedVideos = $value[0];

        return $rewardedVideos;
    }

    function testInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function verifyUser($user,$email = '')
    {
        global $con;

        if (strpos($user, '@') !== false) 
        {           
            $sql = "SELECT email FROM ht_users WHERE email='" .$user. "'";
        }
        else
        {
            $sql = "SELECT username FROM ht_users WHERE username='" .$user. "'";
        }
        
        $query 	= mysqli_query($con, $sql);

        if(mysqli_num_rows($query)===1)
        {
            return true;
        }
        else
        {       
            if(!empty($email))
            {
                $sql = "SELECT email FROM ht_users WHERE email='" .$email. "'";
                $query 	= mysqli_query($con, $sql);
                if(mysqli_num_rows($query)===1)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
                                        
            return false;            
        } 
    }
    
    function deleteUser($token)
    {
        global $con;    
        global $defaultURL;    
        
        $sql = "SELECT thumbnail FROM ht_users WHERE token = '$token'";                
        $query = mysqli_query($con, $sql);
        $value = mysqli_fetch_array($query);
    
        $imageToRemove = $value[0];
        $imageToRemove = str_replace("$defaultURL/photos/","",$imageToRemove);        
        
        $sql = "DELETE FROM ht_users WHERE token = '$token'";
                
        $query = mysqli_query($con, $sql);
                
        if($query)
        { 
            if(removePhoto($imageToRemove))     
            {
                return true;
            }                  
        }                                       
        return false;                    

    }
    
    function deleteUserAccount($id)
    {
        global $con;
        global $defaultURL;

        $userData = fetchInfo($id);
        $imageToRemove = $userData[5];
        $imageToRemove = str_replace("$defaultURL/photos/","",$imageToRemove);
        removePhoto($imageToRemove);
        $sql = "DELETE FROM ht_users WHERE id_user = $id";                
        $query = mysqli_query($con, $sql);                                     
                
        if($query)
        { 
            $sql = "DELETE FROM ht_movies WHERE idUser=$id";                
            $query 	= mysqli_query($con, $sql);           
            
            if($query)
            {
                $sql = "DELETE FROM ht_tvshows WHERE idUser=$id";                
                $query 	= mysqli_query($con, $sql); 

                if($query)
                {
                    return true;
                }
            }
        }                                       
        return false;                    
    }

    function login($user, $pass)
    {
        global $con;

        if (strpos($user, '@') !== false) 
        {
            
            $sql 	= "SELECT id_user, fullname, username, email, password, premium, status FROM ht_users WHERE email='" .$user. "'";
            
            $userSwitcher = 'email';
        }
        else
        {
           
            $sql 	= "SELECT id_user, fullname, username, email, password, premium, status FROM ht_users WHERE username='" .$user. "'";
           
            $userSwitcher = 'username';
        }


        
        $query 	= mysqli_query($con, $sql);
        
        $value = mysqli_fetch_array($query);
        
        $tempUser = $value[$userSwitcher];        
        $id = $value['id_user'];  
        $hashed = $value['password'];  
        $status = $value['status'];
                
        if($user === $tempUser && password_verify($pass, $hashed))
        {
           if($status === 'R')
           {
                return 'not-verified';
           }else
           {                       
               return getUserProfile($id);
           }
        }
        else
        {            
            return 'not-ok';
        }	

    }

    function linkGoogleAccount($mail, $value)
    {
        global $con;

        $sql = "UPDATE ht_users SET google_account=$value WHERE email='$mail'";

        $query 	= mysqli_query($con, $sql);

        if($query)
        {
            return true;
        }

        return false;
    }

    function fetchInfo($id,$email="")
    {
        global $con;
      
        $sql = (empty($email) ? "SELECT * FROM ht_users WHERE id_user=$id" : "SELECT * FROM ht_users WHERE email='$email'");
               
        $query 	= mysqli_query($con, $sql);           
      
        return mysqli_fetch_array($query);
    }

    function getUserProfile($idUser)
    {
        global $con;        

        $results = [];
      
        $sql = "SELECT * FROM ht_users WHERE id_user = $idUser";
        
        
        $query 	= mysqli_query($con, $sql);
              
        while($data = mysqli_fetch_assoc($query))
        {
            $thumbnail = (!empty($data['thumbnail']) ? $data['thumbnail'] : DEFAULT_IMAGE);

            $tempResult = [
                    'id'=>$data['id_user'],
                    'username'=>$data['username'],
                    'fullname'=>$data['fullname'],
                    'email'=>$data['email'],
                    'thumbnail'=>$thumbnail,
                    'premium'=>$data['premium'],
                    'recentlywatched'=>getWatching($idUser,1,RECENTLY_WATCHED),
                    'movies'=>getWatching($idUser,0,MOVIES),
                    'tvshows'=>getWatching($idUser,0,TVSHOWS),
                    'total'=>getWatching($idUser,1,RECENTLY_WATCHED)+getWatching($idUser,0,MOVIES)+getWatching($idUser,0,TVSHOWS)           
            ];
            

            array_push($results,$tempResult);
        }

        return $results;
    }

    function generateCode($limit)
    {
        return strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit));
    }

    function getWatching($idUser,$condition,$type)
    {
        global $con;              

        switch($type)
        {
            case MOVIES:
                $dataToRetrieve = MOVIES_DB;                
            break;
            case TVSHOWS:
                $dataToRetrieve = TVSHOWS_DB;                
            break;
            case BOOKS:
                $dataToRetrieve = BOOKS_DB;               
            break;       
            break;
            default:
                $dataToRetrieve = RECENTLY_WATCHED_DB;
                
        }
        
        $sql = "SELECT COUNT(*) FROM $dataToRetrieve WHERE idUser=$idUser AND completed=$condition";
        
        $query = mysqli_query($con, $sql);  
        $row = mysqli_fetch_array($query); 
        $total = $row[0];     

        if($type == RECENTLY_WATCHED)
        {
            $dataToRetrieve = TVSHOWS_DB;  
            $sql = "SELECT COUNT(*) FROM $dataToRetrieve WHERE idUser=$idUser AND completed=$condition";
            $query = mysqli_query($con, $sql);  
            $row = mysqli_fetch_array($query);

            $total += $row[0];
        }

        return $total;
    }

    function getUserData($idUser,$type)
    {
        global $con;        

        $results = [];
        $dataToRetrieve = '';
        $orderById = "";
        $column = "idUser";

        switch($type)
        {
            case MOVIES:
                $dataToRetrieve = MOVIES_DB;
                $orderById = MOVIES;
            break;
            case TVSHOWS:
                $dataToRetrieve = TVSHOWS_DB;
                $orderById = TVSHOWS;
            break;
            case BOOKS:
                $dataToRetrieve = BOOKS_DB;
                $orderById = BOOKS;
            break;
            case PROFILE:
                $dataToRetrieve = PROFILE_DB;
                $column = "id_user";
                $orderById = "user";
            break;
            default:
                $dataToRetrieve = RECENTLY_WATCHED_DB;
                $orderById = MOVIES;
        }
      
        $sql = "SELECT * FROM $dataToRetrieve WHERE $column=$idUser ORDER bY id_$orderById DESC";
        
        
        $query 	= mysqli_query($con, $sql);
              
        while($data = mysqli_fetch_assoc($query))
        {
            $completed = $data['completed'];

            if($completed == '0' && $type !== RECENTLY_WATCHED)
            {
                switch($type)
                {
                    case MOVIES:
                        $tempResult = [
                                    'id'=>$data['id_movies'],
                                    'userId'=>$data['idUser'],
                                    'title'=>$data['title'],
                                    'timewatched'=>$data['timewatched'],
                                    'type'=>'movie',
                                    'completed'=>false,
                                    'thumbnail'=>$data['thumbnail']               
                            ];
                    break;
                    case TVSHOWS:
                        $tempResult = [
                                    'id'=>$data['id_tvshows'],
                                    'userId'=>$data['idUser'],
                                    'title'=>$data['title'],
                                    'season'=>$data['season'],
                                    'type'=>'tvshow',
                                    'episode'=>$data['episode'],
                                    'timewatched'=>$data['timewatched'],
                                    'completed'=>false,
                                    'thumbnail'=>$data['thumbnail']               
                            ];
                    break;
                    case BOOKS:
                        $tempResult = [
                                    'id'=>$data['id_books'],
                                    'title'=>$data['title'],
                                    'page'=>$data['page'],
                                    'completed'=>false,
                                    'thumbnail'=>$data['thumbnail']               
                            ];
                }
            }
            elseif($completed == '1' && $type == RECENTLY_WATCHED)
            {
                $tempResult = [
                            'id'=>$data['id_movies'],
                            'userId'=>$data['idUser'],
                            'title'=>$data['title'],
                            'type'=>'movie',
                            'timewatched'=>$data['timewatched'],
                            'completed'=>true,
                            'thumbnail'=>$data['thumbnail']               
                    ];
            }
            elseif($type == PROFILE)
            {                

                $thumbnail = (!empty($data['thumbnail']) ? $data['thumbnail'] : DEFAULT_IMAGE);

                $tempResult = [
                        'id'=>$data['id_user'],
                        'username'=>$data['username'],
                        'fullname'=>$data['fullname'],
                        'email'=>$data['email'],
                        'thumbnail'=>$thumbnail,
                        'premium'=>$data['premium'],
                        'recentlywatched'=>getWatching($idUser,1,RECENTLY_WATCHED),
                        'movies'=>getWatching($idUser,0,MOVIES),
                        'tvshows'=>getWatching($idUser,0,TVSHOWS),
                        'total'=>getWatching($idUser,1,RECENTLY_WATCHED)+getWatching($idUser,0,MOVIES)+getWatching($idUser,0,TVSHOWS)           
                ];
            }
            else
            {
                continue;
            }

            

            array_push($results,$tempResult);
        }

        if($type == RECENTLY_WATCHED)
        {
            $dataToRetrieve = TVSHOWS_DB;
            $orderById = TVSHOWS;
            $sql = "SELECT * FROM $dataToRetrieve WHERE $column=$idUser ORDER BY id_$orderById DESC";
            $query 	= mysqli_query($con, $sql);

            while($data = mysqli_fetch_assoc($query))
            {
                $completed = $data['completed'];

                if($completed == '0')
                {
                    continue;
                }
                else
                {
                    $tempResult = [
                                'id'=>$data['id_tvshows'],
                                'title'=>$data['title'],
                                'season'=>$data['season'],
                                'type'=>'tvshow',
                                'episode'=>$data['episode'],
                                'timewatched'=>$data['timewatched'],
                                'completed'=>true,
                                'thumbnail'=>$data['thumbnail']               
                        ];
                }
                array_push($results,$tempResult);
            }
        }

        return $results;
    }

    function saveUserData($idUser,$type,$title,$watchedtime,$page,$season,$episode,$completed,$lang)
    {
        global $con;        
        
        $dataToRetrieve = '';

        $thumbnail = getDataThumbnail($title,$lang);
        

        switch($type)
        {
            case MOVIES:
                $dataToRetrieve = MOVIES_DB;
                $sql = "INSERT INTO $dataToRetrieve (idUser, title, timewatched, thumbnail,completed) 
                VALUES ($idUser, '$title', '$watchedtime', '$thumbnail', $completed);";
            break;
            case TVSHOWS:
                $dataToRetrieve = TVSHOWS_DB;
                $sql = "INSERT INTO $dataToRetrieve (title, idUser, season, episode, timewatched, thumbnail, completed) 
                VALUES ('$title', $idUser, $season, $episode, '$watchedtime', '$thumbnail', $completed);";
            break;
            case BOOKS:
                $dataToRetrieve = BOOKS_DB;
            break;
        }
                
        $query 	= mysqli_query($con, $sql);             

        if($query)
        {
            return true;
        }

        return false;
    }

    function savePremiumTransaction($email)
    {
        global $con;        

        $sql = "INSERT INTO ht_transactions (email) 
                VALUES ('$email');";

        $query 	= mysqli_query($con, $sql);             

        if($query)
        {
            return true;
        }

        return false;
    }

    function CheckPreviousTransaction($email)
    {
        global $con;

        $sql = "SELECT email FROM ht_transactions WHERE email='$email'";

        $query 	= mysqli_query($con, $sql);

        if(mysqli_num_rows($query)===1)
        {
            return true;
        }
        return false;
    }

    function checkForPremium($idUser, $type)
    {
        global $con;           

        $sql = "SELECT premium FROM ht_users WHERE id_user=$idUser";
        
        $query 	= mysqli_query($con, $sql);

        $data = mysqli_fetch_array($query);

        $premium = $data[0];

        if($premium == 0)
        {
            switch($type)
            {
                case MOVIES:
                    $dataToRetrieve = MOVIES_DB;
                break;
                
                case TVSHOWS:
                    $dataToRetrieve = TVSHOWS_DB;
                break;                    
            }

            $sql = "SELECT COUNT(*) FROM $dataToRetrieve WHERE idUser=$idUser and completed=0";
            $query = mysqli_query($con, $sql);  
            $row = mysqli_fetch_array($query);

            $numberOfRecords = $row[0];

            if($numberOfRecords >= 2)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
       
        return true;                       
    }

    function verifyThumbnail($title)
    {
        global $con;

        $sql = "SELECT thumbnail FROM ht_thumbnails WHERE title LIKE '$title'";

        $query 	= mysqli_query($con, $sql);

        if(mysqli_num_rows($query)===1)
        {
            return true;
        }
        return false;
    }

    function getDataThumbnail($title,$lang)
    {
        global $con;
        global $defaultURL;

        $defaultImage = "$defaultURL/images/$lang[74].png";        

        $sql = "SELECT thumbnail FROM ht_thumbnails WHERE title LIKE '$title'";
               
        $query 	= mysqli_query($con, $sql);        
        $value = mysqli_fetch_array($query);

        $thumbnail = $value[0];

        if(empty($thumbnail))
        {
            $omdb = new OMDb( ['tomatoes' => false, 'apikey' => '41e8863'] );
            $result = $omdb->get_by_title($title);
            $thumbnail = $result['Poster'];
            if(!empty($thumbnail))
            {
                setDataThumbnail($title,$thumbnail);
                return $thumbnail;
            }

            return $defaultImage;
        }
        
        return $thumbnail;
    }

    function setDataThumbnail($title,$thumbnail)
    {
        global $con;

      
        $sql = "INSERT INTO ht_thumbnails (title,thumbnail) 
        VALUES ('$title','$thumbnail');";
        
        $query 	= mysqli_query($con, $sql);

        if($query)
        {
            return true;
        }
        
        return false;
    }

        function updateData($id,$type, $content)
        {
            global $con;               
            global $defaultURL;

            switch($type)
            {
                case MOVIES:
                    $dataToRetrieve = MOVIES_DB;
                    if(count($content)<=2)
                    {
                        $sql = "UPDATE $dataToRetrieve SET completed=1 WHERE id_movies=$id";
                    }
                    else
                    {
                        $sql = "UPDATE $dataToRetrieve SET title='$content[0]', timewatched='$content[1]', completed=$content[2] WHERE id_movies=$id";
                    }                
                break;
                case TVSHOWS:
                    $dataToRetrieve = TVSHOWS_DB;
                    if(count($content)<=2)
                    {
                        $sql = "UPDATE $dataToRetrieve SET completed=1 WHERE id_tvshows=$id";
                    }
                    else
                    {
                        $sql = "UPDATE $dataToRetrieve SET title='$content[0]', season=$content[1], episode=$content[2], timewatched='$content[3]', completed=$content[4] WHERE id_tvshows=$id";
                    }
                break;
                case PROFILE:
                    $dataToRetrieve = PROFILE_DB;
                    if(count($content)<=2)
                    {
                        $options = ['cost' => 15,];
                        $temp = password_hash($content[0], PASSWORD_BCRYPT, $options);
                        $sql = "UPDATE $dataToRetrieve SET password='$temp' WHERE id_user=$id";
                    }
                    else
                    {
                        if(!empty($content[3]))
                        {
                            $imageDecodedSuccessfully = decodeAndStoreImage($content[3]);

                            $fetchInfo = fetchInfo($id);

                            $imageToRemove = $fetchInfo[5];

                            $imageToRemove = str_replace("$defaultURL/photos/","",$imageToRemove);                        

                            $thumbnail = "$defaultURL/photos/$imageDecodedSuccessfully";

                            $sql = "UPDATE $dataToRetrieve SET username='$content[0]', fullname='$content[1]', email='$content[2]', thumbnail='$thumbnail' WHERE id_user=$id";
                            
                            removePhoto($imageToRemove);
                            
                        }
                        else
                        {
                            $sql = "UPDATE $dataToRetrieve SET username='$content[0]', fullname='$content[1]', email='$content[2]' WHERE id_user=$id";
                        }
                    }
                break;
                case BOOKS:
                    $dataToRetrieve = BOOKS_DB;
                break;
            }        
        
            $query 	= mysqli_query($con, $sql);

            if($query)
            { 
                return true;
            }

            return false;  
        }

    function updatePremium($id)
    {
        global $con; 

        $sql = "UPDATE ht_users SET premium=1 WHERE id_user=$id";

        $query 	= mysqli_query($con, $sql);

        if($query)
        { 
            return true;
        }

        return false;  
    }

    function removePhoto($photo)
    {
        if($photo == "default.png" || empty($photo))
        {
            return true;
        }
        else
        {
            if(unlink("photos/$photo"))
            {
                return true;
            }
        }
        return false;
    }

    function decodeAndStoreImage($thumbnail,$location="photos")
    {
        $type_temp = explode('{', $thumbnail);
                    
            $data = explode(',', $type_temp[1]);  
            $type = explode('/', $type_temp[0]);                                                                     
        
            $data = $data[1];
            $type = strtolower($type[1]); // jpg, png, gif
        
            if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) 
            {
                return false;
            }
            else
            {
                $data = base64_decode($data);
            }
                                    
            if ($data === false) 
            {
                return false;
            }
            else
            {
                $aleatorio=mt_rand();
                $nome_temp=date('YmdHis').$aleatorio;							       
                $nome_ficheiro=$nome_temp.'.'.$type;
                
                file_put_contents("$location/{$nome_ficheiro}", $data);
            }

            return $nome_ficheiro;
    }

    function checkIfUserHasPreviouslyPurchasedPremium($email)
    {
        global $con;
                
        $sql = "SELECT email FROM ht_transactions WHERE email='" .$email. "'";
        
        $query 	= mysqli_query($con, $sql);

        if(mysqli_num_rows($query)===1)
        {
            return 1;
        }              
        return 0;            
    }

    function deleteData($id,$type)
    {
        global $con;        

        $dataToRetrieve = "";
        $idFieldDB = "";

        switch($type)
        {
            case MOVIES:
                $dataToRetrieve = MOVIES_DB;
                $idFieldDB = MOVIES;                
            break;
            case TVSHOWS:
                $dataToRetrieve = TVSHOWS_DB;  
                $idFieldDB = TVSHOWS;              
            break;
            case BOOKS:
                $dataToRetrieve = BOOKS_DB;
                $idFieldDB = BOOKS;
            break;
        }        
        
        $sql = "DELETE FROM $dataToRetrieve WHERE id_$idFieldDB=$id";
                
        $query = mysqli_query($con, $sql);
                
        if($query)
        {          
            return true;
        }
                                 
        return false;            
    }   

    function insertToken($email,$token)
    {
        global $con;                      

        $comando = "UPDATE ht_users SET token='$token' WHERE email='$email'";
       
        $query = mysqli_query($con, $comando);

        if($query)
        { 
            return true;
        }
        
        return false;
    }

    function updateUserToken($token)
    {
        global $con;                      

        $comando = "UPDATE ht_users SET status='V' WHERE token='$token'";       
        $query 	= mysqli_query($con, $comando);
        
        $comando = "UPDATE ht_users SET token='' WHERE token='$token'";    
        $query 	= mysqli_query($con, $comando);
            
        if($query)
        {
            return true;
        }
                                               
        return false;
    }

    function verifyToken($token)
    {
        global $con;                      

        $comando = "SELECT token FROM ht_users WHERE token='$token'";
       
        $query 	= mysqli_query($con, $comando);

        if(mysqli_num_rows($query)===1)
        { 
            return true;
        }
        
        return false;
    }

    function removeToken($token)
    {
        global $con;
        global $defaultURL;                       
        
        $comando = "SELECT * from ht_users WHERE token='$token'";
       
        $query 	= mysqli_query($con, $comando);

        $fetchInfo = mysqli_fetch_array($query);

        $imageToRemove = $fetchInfo[5];

        $imageToRemove = str_replace("$defaultURL/photos/","",$imageToRemove); 

        $comando = "DELETE from ht_users WHERE token='$token'";
       
        $query 	= mysqli_query($con, $comando);

        removePhoto($imageToRemove);

        if($query)
        { 
            return true;
        }      
        return false;               
    }

    function body($name,$email,$token,$lang)
    {
        global $defaultURL;
        $bdy = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
                            <html lang="pt">
                            <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1">
                            <meta http-equiv="X-UA-Compatible" content="IE=edge">
                            
                            <title>'.$lang[35].'</title>
                            
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
                                                        <p style="color:green; font-size:1.5rem; font-weight:800;">'.$lang[36].$name.$lang[37].'</p>
                                                        <p style="color:green; font-size:1.5rem; font-weight:800;">'.$lang[38].' <a href="'.$defaultURL.'/verify.php?token='.$token.'">link</a> '.$lang[39].'</p>
                                                        <p style="font-size:1rem; font-weight:800;">'.$lang[40].$name.'</p>
                                                        <p style="font-size:1rem; font-weight:800;">'.$lang[41].$email.'</p>                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="center" valign="top">                                                                                                        
                                                        <p style="color:red; font-size:1rem; font-weight:800;">'.$lang[42].'<a href="'.$defaultURL.'/deleteuser.php?token='.$token.'">'.$lang[43].'</a>.</p>
                                                    </td>
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
                        
                        $altbdy = $lang[36].$name.$lang[37].$lang[44].$defaultURL.'/verify.php?token='.$token.$lang[45].$lang[48].$lang[46].$defaultURL.'/deleteuser.php?token='.$token.$lang[47];
                
                return array($bdy,$altbdy);
    }

    function verifyInAppPlayStore($signed_data, $signature, $public_key_base64) 
    {
        $key =	"-----BEGIN PUBLIC KEY-----\n".
            chunk_split($public_key_base64, 64,"\n").
            '-----END PUBLIC KEY-----';   
        
        $key = openssl_get_publickey($key);
       
        $signature = base64_decode($signature);   
       
        $result = openssl_verify(
                $signed_data,
                $signature,
                $key,
                OPENSSL_ALGO_SHA1);
        if (0 === $result) 
        {
            return false;
        }
        else if (1 !== $result)
        {
            return false;
        }
        else 
        {
            return true;
        }
    }

    function sendEmail($from,$nomeFrom,$to,$nome,$ass,$token="",$lang,$bdy="",$altbdy="",$html=true,$atfile="")
    {
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try 
        {
            //Server settings
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = 0;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = DEFAULT_EMAIL_HOST;  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = DEFAULT_EMAIL_USERNAME;                 // SMTP username
            $mail->Password = DEFAULT_EMAIL_PASSWORD;                           // SMTP password
            //$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = DEFAULT_EMAIL_PORT;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom($from, $nomeFrom);
            $mail->addAddress($to, $nome);     // Add a recipient
            // $mail->addAddress('ellen@example.com');               // Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            if(!empty($atfile))
            {
                //Attachments
                $mail->addAttachment($atfile);         // Add attachments
            }              

            if(empty($bdy))
            {
                $temp = body($nome,$to,$token,$lang);
                $bdy = $temp[0];
            }
            
            if(empty($altbdy))
            {
                $temp = body($nome,$to,$token,$lang);
                $altbdy = $temp[1];
            }
            
            //Content
            $mail->isHTML($html);                                  // Set email format to HTML
            $mail->Subject = $ass;
            $mail->Body    = $bdy;
            $mail->AltBody = $altbdy;

            $mail->send();
            return true;
        } 
        catch (Exception $e)
        {
            // echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;            
            $_SESSION['erro'] = $mail->ErrorInfo;
            return false;
        }
    }

    function doLog($text)
    {
        $filename = "logs/info.log";
        $fh = fopen($filename, "a") or die("Could not open log file.");
        fwrite($fh, "$text - ") or die("Could not write file!");
        fclose($fh);
    }
?>