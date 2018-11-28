<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
</head>
<body>
    <?php if (!empty($errors)) : ?>
        <p>Ошибка регистрации нового администратора!</p>
        <?php foreach ($errors as $errorName => $errorValue) : ?>
            <p><?= $errorValue?></p>
        <?php endforeach ?>
    <?php endif?>
    <form action="index.php?listAdmin=listAdmin&addAdmin=addAdmin" method="POST">
        <fieldset>
            <legend>Новый администратор</legend>
            <p>Логин: <input type="text" size="50" name="newLogin"></p>
            <p>Пароль: <input type="text" size="50" name="newPassword"></p>
            <p><input type="submit" value="Добавить"></p>
        </fieldset>
    </form>
</body>
</html>