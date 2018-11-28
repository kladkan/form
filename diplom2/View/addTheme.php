<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
</head>
<body>
    <?php if (!empty($error)) : ?>
        <p>Ошибка добавления темы!</p>
        <p><?= $error?></p>
    <?php endif?>
    <form action="index.php?addTheme=addTheme" method="POST">
        <fieldset>
            <legend>Новая тема:</legend>
            <p><input type="text" size="70" name="newTheme"></p>
            <p><input type="submit" value="Добавить"></p>
        </fieldset>
    </form>
</body>
</html>