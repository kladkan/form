<?php
session_start();
if (empty($_SESSION)) {
    //echo 'никто не авторизован';
    header('Location: ./index.php');
}

if (!file_exists('./downloadedtests/')) {
    mkdir('./downloadedtests/');
}

$list = scandir('./downloadedtests');

if (empty($list['2'])) {
    if ($_SESSION['role'] == 'guest') {
        echo 'Тесты не загружены! Обратитесь к администратору';
        exit;
    }
    if ($_SESSION['role'] == 'admin') {
        echo 'Тесты не загружены! <a href="adminV3.php">Добавить тест</a>';
        exit;
    }
}

if (key($_GET) == 'del') {
    @unlink(__DIR__ . '/downloadedtests/' . $_GET['del']);
}

echo 'Список доступных тестов:';

?>

<html>
<head>
    <title>Список тестов</title>
</head>
<body>
    <ul>
    <?php $list = scandir('./downloadedtests'); for ($i=2; $i < count($list); $i++) : ?>   
        <li><a href="testV3.php?testnumber=<?php echo $list[$i] ?>">Тест №<?php echo $i-1 ?></a>
            <?php
                $json = file_get_contents(__DIR__ . '/downloadedtests/' . $list[$i]);
                $test = json_decode($json, true);
                echo ($test['0']['title']);
            ?>
            <?php if ($_SESSION['role'] == 'admin') : ?> 
                <a href="listV3.php?del=<?php echo $list[$i] ?>"> Удалить тест</a>
            <?php endif; ?>
        </li>
    <?php endfor; ?>
    </ul>

<?php if ($_SESSION['role'] == 'guest') : ?> 
   <p>Ваш статус {Гость}. У вас не прав добавлять/удалять тесты!</p>
   <p>Перейти на <a href="index.php">главную страницу.</a></p>
<?php endif; ?>
<?php if ($_SESSION['role'] == 'admin') : ?> 
   <p>Ваш статус {администратор}.</p>
    <?php echo '<a href="adminV3.php">Добавить тест</a>'; ?>
    <p>Перейти на <a href="index.php">главную страницу.</a></p>
<?php endif; ?>
    

</body>
</html>