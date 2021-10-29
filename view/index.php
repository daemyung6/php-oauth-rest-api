<?php 
if(!defined('__API')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}
session_start();
?>



<?php if(isset($_SESSION['user_info'])): ?>
<?php
    $userInfo = $_SESSION['user_info'];
    
    $kakao_logout_url = (function($app) {
        $REST_API_KEY = $app['kakao']['client_id'];
        $LOGOUT_REDIRECT_URI = $app['kakao']['logout_redirect_uri'];
        return "https://kauth.kakao.com/oauth/logout?client_id=$REST_API_KEY&logout_redirect_uri=$LOGOUT_REDIRECT_URI";
    })($app)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    유저 정보 : <br />
    <pre>
<?php print_r($_SESSION['user_info']) ?>
    </pre>
    <br />
    <br />

    <a href="<?php echo $kakao_logout_url ?>">
        카카오톡 로그아웃
    </a><br />
    <a href="http://localhost/logout">
        로그아웃
    </a><br />
</body>
</html>


<?php else: ?>
<?php 

$kakao_login_url = "https://kauth.kakao.com/oauth/authorize?";
$kakao_login_url .= "client_id=".$app['kakao']['client_id']."&";
$kakao_login_url .= "redirect_uri=".$app['kakao']['redirect_uri']."&";
$kakao_login_url .= "response_type=code";

$naver_login_url = "https://nid.naver.com/oauth2.0/authorize?";
$naver_login_url .= "response_type=code&client_id=".$app['naver']['client_id']."&";
$naver_login_url .= "redirect_uri=".$app['naver']['redirect_uri']."&";
$naver_login_url .= "state=RAMDOM_STATE";

$google_login_url = "https://accounts.google.com/o/oauth2/v2/auth?";
$google_login_url .= "scope=".urlencode("https://www.googleapis.com/auth/userinfo.profile")."&";
$google_login_url .= "access_type=offline&";
$google_login_url .= "include_granted_scopes=true&";
$google_login_url .= "response_type=code&";
$google_login_url .= "redirect_uri=".$app["google"]["redirect_uri"]."&";
$google_login_url .= "client_id=".$app["google"]["client_id"];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="<?php echo $kakao_login_url ?>">
        카카오톡 로그인
    </a><br />
    <a href="<?php echo $naver_login_url ?>">
        네이버 로그인
    </a><br />
    <a href="<?php echo $google_login_url ?>">
        구글 로그인
    </a><br />
</body>
</html>

<?php endif ?>