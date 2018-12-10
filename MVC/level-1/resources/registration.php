<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Регистрация</title>
</head>
<body>
    <?php if (!empty($errors)) : ?>
        <p><?= 'Ошибка регистрации:' ?>
        <?php foreach ($errors as $errorName => $errorValue) : ?>
            <p><?= $errorValue?></p>
        <?php endforeach ?>
    <?php endif?>
    <form action="index2.php?reg=reg" method="POST">
        <fieldset>
        <legend>РЕГИСТРАЦИЯ</legend>
            <p>Для регистрации, введите логин и пароль. Если вы зарегистрированы, выполните <a href="index2.php">вход.</a></p>
            <p>Логин: <input type="text" name="regname"></p>
            <p>Пароль: <input type="password" name="regpass"></p>
            <input type="submit" value="Зарегистрироваться">
        </fieldset>
    </form>
</body>
</html>