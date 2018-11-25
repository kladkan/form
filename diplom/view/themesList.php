<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
</head>
<body>
    <?php if ($themesMoreInfo != 0) : ?>
        <?php if (isset($_SESSION['adminLogin'])) : ?><!--Для админов-->
            <fieldset>
                <legend>Список тем для администраторов с дополнительной информацией:</legend>
                <table width="" border="1" cellpadding="4" cellspacing="0">
                    <tr>
                        <th>Тема</th>
                        <?php if (isset($_SESSION['adminLogin'])) : ?>
                            <th>Удаление темы со всеми вопросами</th>
                            <th>Вопросов в теме</th>
                            <th>Опубликовано вопросов</th>
                            <th>Вопросов без ответа</th>
                        <?php endif ?>
                    </tr>
                    
                    <?php foreach ($themesMoreInfo as $theme) : ?>    
                    <tr>
                        <td><a href="index.php?showQuestionsTheme=<?=$theme['id']?>"><?= $theme['theme']?></a></td>
                        <?php if (isset($_SESSION['adminLogin'])) : ?>
                            <td><a href="index.php?delTheme=<?=$theme['id']?>">Удалить</a></td>
                            <td><?= $theme['Вопросов в теме']?></td>
                            <td><?= $theme['Опубликовано вопросов']?></td>
                            <td><?= $theme['Вопросов без ответа']?></td>
                        <?php endif ?>
                    </tr>
                    <?php endforeach ?>
                </table>
            </fieldset>
        <?php else : ?> <!--Для пользователей-->
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
        <?php endif ?>
    <?php else :?>
        <p>Список тем пуст! Только администраторы могу добавлять темы.</p>
    <?php endif ?>
</body>
</html>