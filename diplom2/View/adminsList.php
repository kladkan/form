<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
</head>
<body>
    <fieldset>
    <legend>Список администраторов:</legend>
    <table width="" border="1" cellpadding="4" cellspacing="0">
        <tr>
            <th>Логин</th>
            <th>Пароль</th>
            <th>Удаление</th>
        </tr>
        <?php foreach ($admins as $row) : ?>    
        <tr>
            <?php foreach ($row as $key => $value) : ?>
                <td>
                    <?= $value?>
                    <?php if ($key == 'password') : ?>
                    <form action="index.php?login=<?=$row['login']?>" method="POST">
                        <input type="text" size="20" name="changePassword">
                        <input type="submit" value="Изменить">
                    </form>
                    <?php endif ?>
                </td>                
            <?php endforeach ?>
                <td><a href="index.php?delAdmin=<?=$row['login']?>">Удалить</a></td>
        </tr>
        <?php endforeach ?>
    </table>
    <p><a href="index.php?listAdmin=listAdmin&addAdmin=addAdmin">Добавить администратора</a></p>
    </fieldset>
</body>
</html>