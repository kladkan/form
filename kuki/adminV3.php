<?php
session_start();

include 'resources/include/forunauthor.php';

if ($_SESSION['role'] == 'guest') {
    http_response_code(403);
    echo 'У Вас нет доступа к этой странице, вы не можете добавлять тесты!<br>';
    exit;
  }

if (!file_exists('./downloadedtests/')) {
    mkdir('./downloadedtests/');
}
if (!empty($_FILES['test']['name'])) {
    move_uploaded_file($_FILES['test']['tmp_name'], './downloadedtests/onserver'.$_FILES['test']['name']);
    echo "Тест успешно загружен.<br>";
    header('Location: ./listV3.php');
}

if (isset($_FILES['test']['name']) && empty($_FILES['test']['name'])) {
    echo '<br>Ошибка загрузки теста! Вы не выбрали файл.';
}

?>

<html>
<head>
    <title>Загрузка тестов</title>
</head>
<body>

<form action="adminV3.php" method="POST" enctype = "multipart/form-data">
    
    <p>Загрузите файл с тестом:</p>
    <p><input name="test" type="file"></p>
    <p><input type="submit" value="Загрузить"></p>
</form>
<p><a href="listV3.php">Посмотреть список доступных тестов</a></p>
</body>
</html>

