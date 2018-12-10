<html lang="ru">
<head>
    <meta charset="utf-8" />
</head>
<body>
    <fieldset>
        <legend>Список тем для администраторов с дополнительной информацией:</legend>
        <table width="" border="1" cellpadding="4" cellspacing="0">
            <tr>
                <th>Тема</th>
                <th>Удаление темы со всеми вопросами</th>
                <th>Вопросов в теме</th>
                <th>Опубликовано вопросов</th>
                <th>Вопросов без ответа</th>
            </tr>
            <?php foreach ($themesMoreInfo as $theme) : ?>    
            <tr>
                <td><a href="index.php?showQuestionsTheme=<?=$theme['id']?>"><?= $theme['theme']?></a></td>
                <td><a href="index.php?delTheme=<?=$theme['id']?>">Удалить</a></td>
                <td><?= $theme['Вопросов в теме']?></td>
                <td><?= $theme['Опубликовано вопросов']?></td>
                <td><?= $theme['Вопросов без ответа']?></td>
            </tr>
            <?php endforeach ?>
        </table>
    </fieldset>
</body>
</html>