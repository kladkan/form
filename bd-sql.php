<?php
$pdo = new PDO("mysql:host=localhost;dbname=global, charset=utf8","ayakovlev","neto1880");

$sql = "SELECT name FROM students";

foreach ($pdo->query($sql) as $row) {
echo $row['name']."<br />";
}

$sth = $dbh->prepare("SELECT fio, age FROM students");
$sth->execute();
/* Извлечение всех оставшихся строк результирующего набора */
print("Извлечение всех оставшихся строк результирующего набора:\n");
$result = $sth->fetchAll(PDO::FETCH_ASSOC);
print_r($result);