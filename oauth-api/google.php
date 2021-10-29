<?php
error_reporting( E_ALL );
ini_set( "display_errors", 1 ); 

if(!defined('__API')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

if(
    (count($app['url']) === 2) &&
    ($app['url'][0] === 'oauth') &&
    ($app['url'][1] === 'google')
) {
    if(
        ($_SERVER["REQUEST_METHOD"] == "GET")
    ) { 
        $userCode = $_GET['code'];

        $access_token = (function($app, $userCode) {
            $client_id = $app['google']['client_id'];
            $client_secret = $app['google']['client_secret'];
            $redirect_uri = urlencode($app['google']['redirect_uri']);

            $post_quary = "code=$userCode&";
            $post_quary .= "client_id=$client_id&";
            $post_quary .= "client_secret=$client_secret&";
            $post_quary .= "redirect_uri=$redirect_uri&";
            $post_quary .= "grant_type=authorization_code";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://oauth2.googleapis.com/token");
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/x-www-form-urlencoded"
            ));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_quary);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            if($response === false)
            {
                echo 'Curl error: ' . curl_error($ch);
                die();
            }
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);

            $body = json_decode($body, true);
            $access_token = $body["access_token"];
            curl_close ($ch);
            return $access_token;
        })($app, $userCode);

        echo $access_token;

        
        //get userinfo
        $userInfo = (function($access_token) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=$access_token");
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            if($response === false)
            {
                echo 'Curl error: ' . curl_error($ch);
                die();
            }
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);
            // $body = json_decode($body, true);
            curl_close ($ch);
            return json_decode($body, true);;
        })($access_token);
        
        session_start();
        $userInfo['type'] = 'google';
        $_SESSION['user_info'] = $userInfo;

        //home redirect
        echo '<meta http-equiv="refresh" content="0;url=/">';

        die();
    }
}
?>