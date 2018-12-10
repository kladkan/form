<?php
/*echo '<pre>';
print_r($_POST);
echo '</pre>';*/
//if (isset($_POST['table_name'])) {
    $pdo = new PDO("mysql:host=localhost; dbname=ayakovlev; charset=utf8","ayakovlev","neto1880");
    $stmt = $pdo->prepare("CREATE TABLE `user` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `login` varchar(50) NOT NULL,
        `password` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $stmt->execute();
//}

if (isset($_GET['show_tables'])) {
    $pdo = new PDO("mysql:host=localhost; dbname=ayakovlev; charset=utf8","ayakovlev","neto1880");
    $sql = "show tables";
    $tables = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
/*echo '<pre>';
print_r($tables);
echo '</pre>';*/
}

if (isset($_GET['describe_table'])) {
    $pdo = new PDO("mysql:host=localhost; dbname=ayakovlev; charset=utf8","ayakovlev","neto1880");
    $sql_table = "describe {$_GET['describe_table']}";
    $table_info = $pdo->query($sql_table)->fetchAll(PDO::FETCH_ASSOC);
/*echo '<pre>';
print_r($table_info);
echo '</pre>';*/
}

if (isset($_GET['del_field'])) {
    $pdo = new PDO("mysql:host=localhost; dbname=ayakovlev; charset=utf8","ayakovlev","neto1880");
    $sql = $pdo->prepare("ALTER TABLE {$_GET['table_name']} DROP COLUMN {$_GET['del_field']}");
    $sql->execute();
    header('Location: ./forCreatTables.php?describe_table='.$_GET['table_name']);
}

if (isset($_POST['new_type'])) {
    $pdo = new PDO("mysql:host=localhost; dbname=ayakovlev; charset=utf8","ayakovlev","neto1880");
    $sql = $pdo->prepare("ALTER TABLE {$_GET['table_name']} MODIFY {$_GET['field_name']} {$_POST['new_type']}");
    $sql->execute();
    header('Location: ./forCreatTables.php?describe_table='.$_GET['table_name']);
}

if (isset($_POST['new_name'])) {
    $pdo = new PDO("mysql:host=localhost; dbname=ayakovlev; charset=utf8","ayakovlev","neto1880");
    $sql = $pdo->prepare("ALTER TABLE {$_GET['table_name']} CHANGE {$_GET['field_name']} {$_POST['new_name']}");
    $sql->execute();
    header('Location: ./forCreatTables.php?describe_table='.$_GET['table_name']);
}

/*
$sql = "SELECT t.description as Дела, t.date_added as Дата, u.login as Исполнитель
    FROM task t INNER JOIN user u ON u.id=t.assigned_user_id
    WHERE t.user_id=$_SESSION[user_id] OR t.assigned_user_id=$_SESSION[user_id]";
    $all = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    */
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Управление таблицами</title>
</head>
<body>
    <p><a href="forCreatTables.php?create_table=create_table">Создать таблицу</a> | <a href="forCreatTables.php?show_tables=show_tables">Показать все таблицы в базе данных</a></p>

    <?php if (isset($_GET['create_table'])) : ?>
        <form action="forCreatTables.php" method="POST">
            <fieldset>
            <legend>Создание таблицы</legend>    
                <p>Введите название: <input type="text" size="50" name="table_name"></p>
                <p><input type="submit" value="Создать"></p> 
            </fieldset>
        </form>
    <?php endif ?>

    <?php if (isset($_GET['show_tables'])) : ?>
        <table width="" border="1" cellpadding="4" cellspacing="0">
            <tr>
                <th>Названия таблиц<br>(для подробной информации выберите таблицу)</th>
            </tr>
            <?php foreach ($tables as $table) : ?>    
            <tr>
                <?php foreach ($table as $key => $value) : ?>
                    <td>
                        <a href="forCreatTables.php?describe_table=<?= $value ?>"><?= $value ?></a>
                    </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif ?>

    <?php if (isset($_GET['describe_table']) or isset($_GET['del_field']) or isset($_GET['field_name']))  : ?>
        <p>Информация о таблице - "<?= $_GET['describe_table'] ?>":</p>
        <table width="" border="1" cellpadding="4" cellspacing="0">
            <tr>
                <?php foreach ($table_info['0'] as $colname => $var) : ?>
                    <th><?php echo $colname?></th>
                <?php endforeach; ?>
                <th>Удаление поля</th>
            </tr>
            <?php foreach ($table_info as $row) : ?>    
            <tr>
                <?php foreach ($row as $col_name => $value) : ?>
                    <td><?= $value ?>
                    <?php if ($col_name == 'Type') : ?>
                        <form action="forCreatTables.php?field_name=<?= $row['Field'] ?>&table_name=<?= $_GET['describe_table'] ?>" method="POST">
                            <p>Для изменения введите новый тип:<br><input type="text" size="30" name="new_type"></p>
                            <input type="submit" value="Изменить">
                        </form>
                    <?php endif ?>

                    <?php if ($col_name == 'Field') : ?>
                        <form action="forCreatTables.php?field_name=<?= $row['Field'] ?>&table_name=<?= $_GET['describe_table'] ?>" method="POST">
                            <p> Для изменения введите новое название и тип через пробел:<br><input type="text" size="40" name="new_name"></p>
                            <input type="submit" value="Изменить">
                        </form>
                    <?php endif ?>
                    </td>
                <?php endforeach; ?>
                <td><a href="forCreatTables.php?del_field=<?= $row['Field'] ?>&table_name=<?= $_GET['describe_table'] ?>">Удалить</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif ?>
</body>
</html>