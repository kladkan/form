<?php
$pdo = new PDO("mysql:host=localhost; dbname=netology01; charset=utf8","root","fg2018start");

if (!empty($_GET)) {
    //echo 'Ест запрос по фильтру<br>';
    if (!empty($_GET['isbn'])) {
        //echo 'Есть фильтр по isbn<br>';
        $sql = "SELECT * FROM books WHERE isbn like '%{$_GET['isbn']}%'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_GET['isbn']]);
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if (!empty($_GET['name'])) {
        //echo 'Есть фильтр по name<br>';
        $sql = "SELECT * FROM books WHERE name like '%{$_GET['name']}%'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_GET['name']]);
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if (!empty($_GET['author'])) {
        //echo 'Есть фильтр по author<br>';
        $sql = "SELECT * FROM books WHERE author like '%{$_GET['author']}%'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_GET['author']]);
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} else {
    //echo 'Выводим всю таблицу';
    $sql = "SELECT * FROM books";
    $all = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Список полезных книг</title>
</head>
<body>
    <h1>Библиотека успешного человека</h1>
    <form action="pdo-sort.php" method="GET">
        <input type="text" name="isbn" placeholder="ISBN" value="" />
        <input type="text" name="name" placeholder="Название книги" value="" />
        <input type="text" name="author" placeholder="Автор книги" value="" />
        <input type="submit" value="Поиск" />
    </form>
    <p><a href="pdo-sort.php">Сбросить фильтр</a></p>
    <table width="" border="1" cellpadding="4" cellspacing="0">
    <tr>
        <?php if (!empty($all)) : ?>
            <?php foreach ($all['0'] as $colname => $var) : ?>
                <th><?php echo $colname?></th>
            <?php endforeach; ?>
        <?php else : ?>
        <p>Нет данных по указанному отбору!</p>
            <?php exit ?>
        <?php endif ?>

   </tr>
    <?php foreach ($all as $line => $row) : ?>    
        <tr>
            <?php foreach ($row as $key => $value) : ?>
                <td><?php echo $value ?></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </table>
</body>
</html>