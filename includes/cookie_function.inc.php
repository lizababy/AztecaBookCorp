<?php

function page_counter($cookie_name,$path){

    if (!isset($_COOKIE[$cookie_name]))
        { 
            $cookie = 1;
            setcookie($cookie_name, $cookie,  time()+ 60*60*24,$path);
            $msg = '<p align = "right" class="text-info">Welcome! This is the first time you visiting this page in 24 hrs.</p>'; 
        }
        else
        {
            $cookie = ++$_COOKIE[$cookie_name];
            setcookie($cookie_name, $cookie, time()+ 60*60*24,$path);

            $msg = '<p align = "right" class="text-info">Hi,You have viewed this page '. $_COOKIE[$cookie_name] .' times in 24 hrs.!</p>';
        }
        
      return $msg;
}

function last_login_info(){
   
    $msg = '<p align="right" class="text-info">'.'Last log in :'. $_COOKIE['last_loggedin'] . '</p>'
                    . '<p align="right" class="text-info">'.'Last IP  :'.$_COOKIE['last_ip']. '</p>';
    
    return $msg;
        
}
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
function del_sess_if_exp(){
        //subtract new timestamp from the old one
        //Expiration time set to 3 minitues
        if((time() - $_SESSION['last_timestamp'] ) > 180) {         

            // Cancel the session: Log out the user.
            $_SESSION = array(); // Clear the variables.
            session_destroy(); // Destroy the session itself.
            setcookie (session_name(), '', time()-3600); // Destroy the cookie.
            
        	ob_end_clean(); // Delete the buffer.
            // Print a customized message:
            $msg = '<h4 align = "right" class="text-warning"> Time expired! You are now Logged Out!</h4>';
            
            
           
        }else{
            $msg = '<h4 align = "right" class="text-info">Hi,' . $_SESSION['first_name'] . '</h4>';
            
            if(isset($_COOKIE['last_loggedin']) && isset($_COOKIE['last_ip'])){
                $msg .= '<p align="right" class="text-info">'.'Current log in :'. $_COOKIE['last_loggedin'] . '</p>'
                    . '<p align="right" class="text-info">'.'Current IP  :'.$_COOKIE['last_ip']. '</p>';
            } 
              
    		$_SESSION['last_timestamp'] = time(); //set new timestamp
        }
  
    
    return $msg;
}


