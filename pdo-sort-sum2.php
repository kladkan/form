<?php
$pdo = new PDO("mysql:host=localhost; dbname=global; charset=utf8","ayakovlev","neto1880");

if (!empty($_GET)) {
    //echo 'Ест запрос по фильтру<br>';
    
    if (!empty($_GET['isbn'])) {
        //echo 'Есть фильтр по isbn<br>';
        $sql = "SELECT * FROM books WHERE isbn like '%{$_GET['isbn']}%' and name like '%{$_GET['name']}%' and author like '%{$_GET['author']}%'";
        $stmt = $pdo->prepare($sql);
        @$stmt->execute([$_GET['isbn'], ['name'], ['author']]);
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if (!empty($_GET['name'])) {
        //echo 'Есть фильтр по name<br>';
        $sql = "SELECT * FROM books WHERE name like '%{$_GET['name']}%' and isbn like '%{$_GET['isbn']}%' and author like '%{$_GET['author']}%'";
        $stmt = $pdo->prepare($sql);
        @$stmt->execute([$_GET['name'], ['author'], ['isbn']]);
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if (!empty($_GET['author'])) {
        //echo 'Есть фильтр по author<br>';
        $sql = "SELECT * FROM books WHERE author like '%{$_GET['author']}%' and isbn like '%{$_GET['isbn']}%' and name like '%{$_GET['name']}%'";
        $stmt = $pdo->prepare($sql);
        @$stmt->execute([$_GET['author'], ['isbn'], ['name']]);
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
    <form action="pdo-sort-sum2.php" method="GET">
        <input type="text" name="isbn" placeholder="ISBN" value="<?php if (!empty($_GET['isbn'])) {echo $_GET['isbn'];} ?>" />
        <input type="text" name="name" placeholder="Название книги" value="<?php if (!empty($_GET['name'])) {echo $_GET['name'];} ?>" />
        <input type="text" name="author" placeholder="Автор книги" value="<?php if (!empty($_GET['author'])) {echo $_GET['author'];} ?>" />
        <input type="submit" value="Поиск" />
    </form>
    <p><a href="pdo-sort-sum2.php">Сбросить фильтр</a></p>
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