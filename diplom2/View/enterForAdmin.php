<html lang="ru">
<head>
    <meta charset="utf-8" />
</head>
<body>
    <?php if (!empty($errors)) : ?>
        <p>Ошибка авторизации:</p>
        <?php foreach ($errors as $errorName => $errorValue) : ?>
            <p><?= $errorValue?></p>
        <?php endforeach ?>
    <?php endif?>
    <form action="index.php?admin=admin" method="POST">
        <fieldset>
            <legend>Вход для администратора</legend>
            <p>Логин: <input type="text" name="authname"></p>
            <p>Пароль: <input type="password" name="authpass"></p>
            <p><input type="submit" value="Войти">
        </fieldset>
    </form>
</body>
</html>