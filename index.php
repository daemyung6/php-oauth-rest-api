<?php
define('__API', true);
$app = array();
$app['url'] = explode('/', $_GET['params']);
// header('Content-Type: application/json; charset=utf-8');
header('Content-Type: text/html; charset=utf-8');

$httpOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
if (in_array(
        $httpOrigin, 
        [
            'http://localhost',
        ]
    )
) {
    header("Access-Control-Allow-Origin: ${httpOrigin}");
}

include realpath(__DIR__).'/config.php';

include realpath(__DIR__).'/oauth-api/kakao.php';
include realpath(__DIR__).'/oauth-api/naver.php';
include realpath(__DIR__).'/oauth-api/google.php';

if(
    (count($app['url']) === 1) &&
    ($app['url'][0] === "")
) {
    if(
        ($_SERVER["REQUEST_METHOD"] == "GET")
    ) {
        (function($app) {
            include realpath(__DIR__).'/view/index.php';
        })($app);
        die();
    }
}
if(
    (count($app['url']) === 1) &&
    ($app['url'][0] === 'logout')
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


header('HTTP/1.1 404 not found');
die();
?>