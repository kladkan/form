<?php
session_start();
if(@$_GET['exit'] == 'exit') {
    session_destroy();
    header('Location: ./index.php');
}
echo '<a href="index.php?exit=exit">Выход</a>';

if (!isset($_SESSION['name']) && !empty($_POST['user']) && !empty($_POST['pass'])) {
    if (!file_exists(__DIR__ . 'core/users/'.$_POST['user'].'.json')) {
    //echo 'Файл учетной записи не обнаружен';
    header('Location: ./index.php');
    } else {
        //echo 'Файл учетной записи есть';
        $json = file_get_contents(__DIR__ . 'core/users/'.$_POST['user'].'.json');
        $user = json_decode($json, true);
    }
    if ($_POST['pass'] === $user[$_POST['user']]) {
        $_SESSION['name'] = $_POST['user'];
        $_SESSION['role'] = 'admin';
    }
}

if (!isset($_SESSION['name']) && !empty($_POST['user'])) {
    $_SESSION['name'] = $_POST['user'];
    $_SESSION['role'] ='guest';
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Привет</title>
</head>
<body>
<?php if (!isset($_SESSION['name'])) : ?>
    <form action="index.php" method="post">
        <p>Логин: <input type="text" name="user"></p>
        <p>Пароль: <input type="password" name="pass"></p>
        <input type="submit" value="Войти">
    </form>
<?php endif ?>
<?php if (isset($_SESSION['name'])) : ?>
    <p>Вы авторизованы, ваше имя: <?php echo $_SESSION['name']; ?></p>
    <?php if ($_SESSION['role'] == 'guest') : ?> 
        <p>Ваш статус {Гость}.</p>
    <?php endif; ?>
    <?php if ($_SESSION['role'] == 'admin') : ?> 
        <p>Ваш статус {администратор}.</p>
    <?php endif; ?>
    <p>Перейти к <a href="listV3.php">списку тестов</a></p>
<?php endif ?>
    
</body>
</html>