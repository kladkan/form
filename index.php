<?php
session_start();

if($_GET['exit'] == 'exit') {
    echo 'работает';
    //unset($_SESSION['name']);
    unset($_SERVER['PHP_AUTH_USER']); unset($_GET['exit']);
   

}
echo '<a href="index.php?exit=exit">Выход</a>';


//для очистки значения закомментировать раскомментировать следующую строку
//unset($_SESSION['name']); unset($_SERVER['PHP_AUTH_USER']);


if (!file_exists(__DIR__ . '/login.json')) {
    echo 'Файл учетной записи не обнаружен';
    exit;
}

$json = file_get_contents(__DIR__ . '/login.json');
$user = json_decode($json, true);
/*echo '<pre>';
print_r($user);
echo '</pre>';*/

if (!isset($_SESSION['name']) && isset($_SERVER['PHP_AUTH_USER'])) {//если имя пользователя нет в сессии (известно), но при этом от пользователя пришел заголовок с авторизации (пришло имя пользователя)
    if ($_SERVER['PHP_AUTH_PW'] === $user['admin']) {// проверка пороля и имени пользователя, если имя и пароль совпадают, то
        $_SESSION['name'] = $_SERVER['PHP_AUTH_USER'];
        $_SESSION['role'] = 'admin';// запишем в сессию имя пользователя, который авторизовался
        /*echo '<pre>';
        print_r($_SESSION);
        echo '</pre>';
        echo 'Вы админ';*/
    } else {
        $_SESSION['name'] = $_SERVER['PHP_AUTH_USER'];
        $_SESSION['role'] ='guest';
        /*echo '<pre>';
        print_r($_SESSION);
        echo '</pre>';
        echo 'Вы гость';*/
    }
}

if (!isset($_SESSION['name'])) { //проверим если ползователя не сохранено в сессии,
    header('WWW-Authenticate: Basic realm="Admin"');//то попросим авторизоваться (выводим окно для авторизации)
    http_response_code(401);

    exit;   
} // только после этого отображаем содержимое страницы html:

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Привет</title>
</head>
<body>

    Здравствуйте, <?php echo $_SESSION['name']; ?>
  
    <?php if ($_SESSION['role'] == 'guest') : ?> 
        <p>Ваш статус {Гость}.</p>
    <?php endif; ?>
    <?php if ($_SESSION['role'] == 'admin') : ?> 
        <p>Ваш статус {администратор}.</p>
    <?php endif; ?>
    <p>Перейти к <a href="listV3.php">списку тестов</a></p>
    

</body>
</html>