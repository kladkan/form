<?php
$pdo = new PDO("mysql:host=localhost; dbname=global; charset=utf8","ayakovlev","neto1880");

$sql = "SELECT * FROM books";

$all = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
    <title>Список полезных книг</title>
</head>
<body>
    <h1>Библиотека успешного человека</h1>
    <table border="1" cellpadding="4" cellspacing="0">
        <tr>
            <?php foreach ($all['0'] as $colname => $var) : ?>
                <th><?php echo $colname?></th>
            <?php endforeach; ?>
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