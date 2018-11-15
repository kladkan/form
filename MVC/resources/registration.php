<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Регистрация</title>
</head>
<body>
    <form action="index2.php" method="POST">
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