<?php 
if(!defined('__API')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}
$app['db-config'] = array(
    "localhost", "root", "pw", "database-name"
);
$app['kakao'] = array(
    'client_id' => '',
    'redirect_uri' => 'http://localhost/oauth/kakao',
    'logout_redirect_uri' => 'http://localhost/oauth/kakao_logout'
);
$app['naver'] = array(
    'client_id' => '',
    'client_secret' => '',
    'redirect_uri' => 'http://localhost/oauth/naver',
);
$app['google'] = array(
    'client_id' => '',
    'client_secret' => '',
    'redirect_uri' => 'http://localhost/oauth/google',
);
?>