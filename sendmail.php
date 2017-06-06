<?php
define('WP_USE_THEMES', false);
require( dirname( __FILE__ ) . '/../../../wp-blog-header.php' );

//$mail_address = get_option('admin_email');

$common_site_settings = get_option('common_site_settings');
$mail_address = $common_site_settings['notification_email'];

$mail_subject = 'Запрос с сайта '.$_SERVER['SERVER_NAME'];

$mail_text = '';
function add_to_mail_text($title, $value){
    global $mail_text;
    $mail_text .= $title.': '.nl2br(htmlspecialchars(trim($value))).'<br>';
}

$inputs = array(
    array('name', 'Имя'),
    array('tel', 'Номер телефона'),
    array('email', 'E-mail'),
    array('topic', 'Тема'),
    array('message', 'Сообщение'),
);
foreach ($inputs as $input){
    if(isset($_POST[$input[0]])){
        add_to_mail_text($input[1], $_POST[$input[0]]);
    }
}

$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=utf-8\r\n";
$headers .= 'From: no-reply@'.$_SERVER['SERVER_NAME']."\r\n";

$mail = mail($mail_address, $mail_subject, $mail_text, $headers);

// если запрос пришел не через AJAX то делаем перенаправление назад на страницу
if(isset($_POST['ajax'])){
    header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
    if($mail){
        echo json_encode(array("status" => "success"));
    }else{
        echo json_encode(array("status" => "error"));
    }
}else{
    $redirect_url = isset($_POST['redirect']) ? $_POST['redirect'] : 'http://'.$_SERVER['SERVER_NAME'];
    header('Location: '.$redirect_url);
}

?>