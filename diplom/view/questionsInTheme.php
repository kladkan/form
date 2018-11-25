<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
</head>
<body>
    <?php if (isset($_SESSION['adminLogin'])) : ?><!--Для админов-->
        <fieldset>
            <legend>Список всех вопросов в теме:</legend>
            <?php if (empty($questions)) : ?>
                <p>В этой теме вопросов нет.</p>
            <?php else : ?>
                <table width="" border="1" cellpadding="4" cellspacing="0">
                    <tr>
                        <th>Тема</th>
                        <th>Вопрос</th>
                        <th>Дата создания</th>
                        <th>Ожидает ответа</th>
                        <th>Опубликован</th>
                        <th>Удалить вопрос</th>
                    </tr>
                    <?php foreach ($questions as $question) : ?>
                        <tr>
                            <td><?=$question['theme']?></td>
                            <td><a href="index.php?showQuestionId=<?=$question['id']?>&showQuestionsTheme=<?=$question['theme_id']?>"><?=$question['question']?></a></td>
                            <td><?=$question['date_added']?></td>
                            <td>
                                <?php if ($question['answer'] === NULL OR $question['answer'] ==''): ?>
                                    <?='Да'?>
                                <?php else : ?>
                                    <?='Нет'?>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php if ($question['published'] == 1) : ?>
                                    <p>ДА - <a href="index.php?publishedOnOff=0&showQuestionId=<?=$question['id']?>&showQuestionsTheme=<?=$question['theme_id']?>">скрыть</a></p>
                                <?php else : ?>
                                    <p>НЕТ - <a href="index.php?publishedOnOff=1&showQuestionId=<?=$question['id']?>&showQuestionsTheme=<?=$question['theme_id']?>">опубликовать</a></p>
                                <?php endif ?>
                            </td>
                            <td><a href="index.php?delQuestionId=<?=$question['id']?>&questionsThemeId=<?=$question['theme_id']?>">Удалить</a></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            <?php endif ?>
        </fieldset>
    <?php else : ?><!--Для пользователей-->
        <fieldset>
            <legend>Список всех вопросов в теме:</legend>
            <table width="" border="1" cellpadding="4" cellspacing="0">
                <tr>
                    <th>Тема</th>
                    <th>Вопрос</th>
                </tr>
                <?php foreach ($questions as $question) : ?>
                    <tr>
                        <?php if ($question['answer'] !== NULL && $question['answer'] !='' && $question['published'] != 0) : ?>
                            <td><?=$question['theme']?></td>
                            <td><a href="index.php?showQuestionId=<?=$question['id']?>&showQuestionsTheme=<?=$question['theme_id']?>"><?=$question['question']?></a></td>
                        <?php endif ?>
                    </tr>
                <?php endforeach ?>
            </table>
        </fieldset>
    <?php endif ?>
</body>
</html>