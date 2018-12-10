<?php
$pdo = new PDO("mysql:host=localhost; dbname=netology01; charset=utf8","root","fg2018start");

$sql = "SELECT * FROM books";

$all = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

 //   echo '<pre>';
   // print_r($all);
 //   echo '</pre>';


//foreach ($all as $line => $row) {
    /*echo '<pre>';
    print_r($row);
    echo '</pre>'; exit;*/
//    foreach ($row as $key => $value) {
//        echo $key.' - '.$value.'<br>';
//    }
//echo key($row)."<br />";


//}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Библиотека успешного человека</title>
</head>
<body>
    <h1>Библиотека успешного человека</h1>
    <table width="" border="1" cellpadding="4" cellspacing="0">
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