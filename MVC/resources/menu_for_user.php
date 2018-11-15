<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
</head>
<body>
    <p><a href="index2.php?exit=exit">Выход</a></p>
    <p><?= $_SESSION['user_login'] ?></p>
    <hr>
    <p><a href="index2.php?add_your_task=add_your_task">Добавить дело</a> || <a href="index2.php?echo_your_tasklist=echo_your_tasklist">Вывод списка ваших дел(отсортированных по дате)</a> || <a href="index2.php?echo_assigned_list=echo_assigned_list">Показать делегированные дела</a> || <a href="index2.php?count_task=count_task">Вывести количество дел</a></p>
</body>
</html>