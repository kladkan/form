<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
</head>
<body>
    <?php if (isset($_SESSION['adminLogin'])) : ?><!--Для админов-->
        <fieldset>
            <legend>Редактирование вопроса</legend>
            <table width="" border="1" cellpadding="4" cellspacing="0">
                <tr>
                    <th>Дата создания</th>
                    <th>Тема</th>
                    <th>Автор</th>
                    <th>Вопрос</th>
                    <th>Ответ</th>
                    <th>Ожидает ответа</th>
                    <th>Опубликован</th>
                </tr>
                <?php foreach ($showQuestion as $questionInfo) : ?>
                    <tr>
                        <td><?= $questionInfo['date_added'] ?></td>
                        <td>
                            <form action="index.php?showQuestionId=<?=$questionInfo['id']?>&showQuestionsTheme=<?=$questionInfo['theme_id']?>" method="POST">
                                <select name="changeThemeId">
                                    <?php foreach ($themes as $theme) : ?>
                                        <option <?php if ($questionInfo['theme_id'] == $theme['id']):?>
                                            selected<?php endif ?> value="<?= $theme['id'] ?>">
                                            <?= $theme['theme'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <button type="submit">Изменить</button>
                            </form>
                        </td>
                        <td>
                            <form action="index.php?showQuestionId=<?=$questionInfo['id']?>&showQuestionsTheme=<?=$questionInfo['theme_id']?>" method="POST">
                                <input type="text" size="" name="changeAuthorName" value="<?= $questionInfo['author_name'] ?>">
                                <input type="submit" value="Изменить">
                            </form>
                        </td>
                        <td>
                            <form action="index.php?showQuestionId=<?=$questionInfo['id']?>&showQuestionsTheme=<?=$questionInfo['theme_id']?>" method="POST">
                                <input type="text" size="" name="changeQuestion" value="<?= $questionInfo['question'] ?>">
                                <input type="submit" value="Изменить">
                            </form>
                        </td>
                        <td>
                            <form action="index.php?showQuestionId=<?=$questionInfo['id']?>&showQuestionsTheme=<?=$questionInfo['theme_id']?>" method="POST">
                                <textarea type="text" size="" name="changeAnswer" value=""><?= $questionInfo['answer'] ?></textarea>
                                <p><input type="checkbox" name="publish" value="<?=$questionInfo['id']?>">опубликовать</p>
                                <input type="submit" value="Ответить">
                            </form>
                        </td>
                        <td>
                            <?php if ($questionInfo['answer'] === NULL OR $questionInfo['answer'] ==''): ?>
                                <?='Да'?>
                            <?php else : ?>
                                <?='Нет'?>
                            <?php endif ?>
                        </td>
                        <td>
                        <?php if ($questionInfo['published'] == 1) : ?>
                                <p>ДА<!-- - <a href="index.php?publishedOnOff=0&showQuestionId=<?=$questionInfo['id']?>&showQuestionsTheme=<?=$questionInfo['theme_id']?>">скрыть</a>--></p>
                            <?php else : ?>
                                <p>НЕТ<!-- - <a href="index.php?publishedOnOff=1&showQuestionId=<?=$questionInfo['id']?>&showQuestionsTheme=<?=$questionInfo['theme_id']?>">опубликовать</a>--></p>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
        </fieldset>
    <?php else : ?><!--Для пользователей-->
        <fieldset>
            <legend>Ответ</legend>
            <table width="" border="1" cellpadding="4" cellspacing="0">
                <tr>
                    <th>Вопрос</th>
                    <th>Ответ</th>
                </tr>
                <?php foreach ($showQuestion as $questionInfo) : ?>
                    <tr>
                        <td><?= $questionInfo['question'] ?></td>
                        <td><?= $questionInfo['answer'] ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </fieldset>
    <?php endif ?>
</body>
</html>