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
    ($app['url'][1] === 'kakao')
) {
    if(
        ($_SERVER["REQUEST_METHOD"] == "GET")
    ) { 
        //getToken
        $userCode = $_GET['code'];

        $access_token = (function($app, $userCode) {
            $post_quary = "grant_type=authorization_code&";
            $post_quary .= "client_id=".$app['kakao']['client_id']."&";
            $post_quary .= "redirect_uri=".urlencode($app['kakao']['redirect_uri'])."&";
            $post_quary .= "code=$userCode";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://kauth.kakao.com/oauth/token");
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
        
        //get userinfo
        $userInfo = (function($access_token) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://kapi.kakao.com/v2/user/me");
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer $access_token"
            ));
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
        $userInfo['type'] = 'kakao';
        $_SESSION['user_info'] = $userInfo;

        //home redirect
        echo '<meta http-equiv="refresh" content="0;url=/">';

        die();
    }
}

if(
    (count($app['url']) === 2) &&
    ($app['url'][0] === 'oauth') &&
    ($app['url'][1] === 'kakao_logout')
) {
    if(
        ($_SERVER["REQUEST_METHOD"] == "GET")
    ) {
        session_start();
        session_destroy();

        //home redirect
        echo '<meta http-equiv="refresh" content="0;url=/">';
        die();
    }
}


?>