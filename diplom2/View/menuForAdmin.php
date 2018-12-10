<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
</head>
<body>
    <p>Вы вошли как: <?=$_SESSION['adminLogin']?></p>
    <p><a href="index.php?exit=exit">Выход (прекратить администрирование)</a></p>
    <fieldset>
        <legend>Меню администратора:</legend>
        <ul>
            <li><a href="index.php?listAdmin=listAdmin">Список админов</a></li>
            <li><a href="index.php?unansQuestions=unansQuestions">Список вопросов без ответов</a></li>
            <li><a href="index.php?addTheme=addTheme">Добавить тему...</a></li>
        </ul>
    </fieldset>
</body>
</html>