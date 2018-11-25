<html lang="ru">
<head>
    <meta charset="utf-8" />
</head>
<body>
    <?php if (!isset($_SESSION['adminId']) && !isset($_GET['admin'])) : ?>
        <p><a href="index.php?admin=admin">Вход для администраторов</a></p>
    <?php endif ?>
</body>
</html>