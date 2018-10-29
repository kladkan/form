<?php

$list = scandir('./tests');

if (empty($list['2'])) {
    echo 'Тесты не загружены! Вернитесь на главную старницу для загрузки';
}
echo 'Список доступных тестов:';

?>

<html>
<head>
    <title>Список тестов</title>
</head>
<body>
    <ul>
    <?php for ($i=2; $i < count($list); $i++) : ?>   
        <li><a href="test.php?testnumber=<?php echo $list[$i] ?>">Тест №<?php echo $i-1 ?></a></li>
    <?php endfor; ?>
    </ul>
    
</body>
</html>