<?php
if (!file_exists('./tests/')) {
    mkdir('./tests/');
}
if (!empty($_FILES['test']['name'])) {
    move_uploaded_file($_FILES['test']['tmp_name'], './tests/onserver'.$_FILES['test']['name']);
    echo "Тест успешно загружен.<br>";
    header('Location: ./listV2.php');

   // echo '<a href="listV2.php">Перейти к списку тестов прямо сейчас</a>';
} else {
    echo '<br>Ошибка загрузки теста! Вы не выбрали файл.';
}

?>

<html>
<head>
    <title>Загрузка тестов</title>
</head>
<body>

<form action="adminV2.php" method="POST" enctype = "multipart/form-data">
    
    <p>Загрузите файл с тестом:</p>
    <p><input name="test" type="file"></p>
    <p><input type="submit" value="Загрузить"></p>
</form>
<p><a href="listV2.php">Посмотреть список доступных тестов</a></p>
</body>
</html>

