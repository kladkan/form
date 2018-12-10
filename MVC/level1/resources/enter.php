<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Авторизация</title>
</head>
<body>
    <?php if (!empty($errors)) : ?>
        <p><?= 'Ошибка авторизации:' ?>
        <?php foreach ($errors as $errorName => $errorValue) : ?>
            <p><?= $errorValue?></p>
        <?php endforeach ?>
    <?php endif?>
    <form action="index2.php" method="POST">
        <fieldset>
        <legend>ФАРМА ДЛЯ ВХОДА</legend>
            <p>Чтобы войти, введите логин и пароль или пройдите<a href="index2.php?reg=reg"> Регистрацию</a></p>
            <p>Логин: <input type="text" name="authname"></p>
            <p>Пароль: <input type="password" name="authpass"></p>
            <p><input type="submit" value="Войти"> 
        </fieldset>
    </form>
</body>
</html>