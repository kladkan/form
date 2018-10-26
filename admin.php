<html>
<head>
    <title>Загрузка тестов</title>
</head>
<body>
<p><a href="list.php">Посмотреть список доступных тестов</a></p>
<form action="admin.php" method="POST" enctype = "multipart/form-data">
    
    <p>Загрузите файл с тестом:</p>
    <p><input name="test" type="file"></p>
    <p><input type="submit" value="Загрузить"></p>
</form>
</body>
</html>

<?php

if (!empty($_FILES['test']['name'])) {
    move_uploaded_file($_FILES['test']['tmp_name'], './tests/onserver'.$_FILES['test']['name']);
    echo "Тест успешно загружен.<br>";
    echo '<a href="list.php">Перейти к списку тестов</a>';
} else {
    echo 'Ошибка загрузки! Вы не выбрали файл.';
}

?>