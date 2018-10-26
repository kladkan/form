<?php
echo 'Список доступных тестов:<br><br>';

if (@file_get_contents(__DIR__ . '/tests/onservertest1.json') == false) {
    echo 'Тест 1 - не найден или не загружен!<br>';
} else {
    echo 'Тест 1 - доступен: <a href="test.php?testnumber=t1">приступить к тесту.</a><br>';
}

if (@file_get_contents(__DIR__ . '/tests/onservertest2.json') == false) {
    echo 'Тест 2 - не найден или не загружен!<br>';
} else {
    echo 'Тест 2 - доступен: <a href="test.php?testnumber=t2">приступить к тесту.</a><br>';
}

echo '<br>Для загрузки нового теста вернитесь на <a href="admin.php">главную страницу</a><br>';

?>