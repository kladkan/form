<html lang="ru">
<head>
    <meta charset="utf-8" />
</head>
<body>
    <fieldset>
        <legend>Список тем с вопросами и ответами:</legend>
        <table width="" border="1" cellpadding="4" cellspacing="0">
            <tr>
                <th>Тема</th>
            </tr>
            <?php foreach ($themesMoreInfo as $theme) : ?>    
            <tr>
                <?php if ($theme['Вопросов в теме'] <> $theme['Вопросов без ответа'] OR $theme['Опубликовано вопросов'] <> 0) : ?>
                    <td><a href="index.php?showQuestionsTheme=<?=$theme['id']?>"><?= $theme['theme']?></a></td>
                <?php endif ?>
            </tr>
            <?php endforeach ?>
        </table>
    </fieldset>
</body>
</html>