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
    ($app['url'][1] === 'naver')
) {
    if(
        ($_SERVER["REQUEST_METHOD"] == "GET")
    ) { 
        //getToken
        $userCode = $_GET['code'];

        $access_token = (function($app, $userCode) {
            // 네이버 로그인 콜백 예제
            $client_id = $app['naver']['client_id'];
            $client_secret = $app['naver']['client_secret'];
            $code = $userCode;
            $state = $_GET["state"];;
            $redirectURI = urlencode("YOUR_CALLBACK_URL");
            $url = "https://nid.naver.com/oauth2.0/token?grant_type=authorization_code&client_id=".$client_id."&client_secret=".$client_secret."&redirect_uri=".$redirectURI."&code=".$code."&state=".$state;
            $is_post = false;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, $is_post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if($response === false)
            {
                echo 'Curl error: ' . curl_error($ch);
                die();
            }
        
            curl_close ($ch);
            $response = json_decode($response, true);
            $access_token = $response['access_token'];
            return $access_token;

        })($app, $userCode);
        
        $userInfo = (function($access_token) {
            $token = $access_token;
            $header = "Bearer ".$token; // Bearer 다음에 공백 추가
            $url = "https://openapi.naver.com/v1/nid/me";
            $is_post = false;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, $is_post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $headers = array();
            $headers[] = "Authorization: ".$header;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec ($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            echo "status_code:".$status_code."<br>";
            curl_close ($ch);
            if($status_code == 200) {
            } else {
                echo "Error 내용:".$response;
                die();
            }
            return json_decode($response, true);

        })($access_token);

        session_start();
        $userInfo['type'] = 'naver';
        $_SESSION['user_info'] = $userInfo;

        //home redirect
        echo '<meta http-equiv="refresh" content="0;url=/">';

        die();
    }
}
?>