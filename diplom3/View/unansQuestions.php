<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
</head>
<body>
    <fieldset>
        <legend>Вопросы без ответа во всех темах</legend>
        <table width="" border="1" cellpadding="4" cellspacing="0">
            <tr>
                <th>Дата создания</th>
                <th>Тема</th>
                <th>Вопрос</th>
                <th>Ответ</th>
                <th>Опубликован</th>
                <th>Автор</th>
                <th>E-mail автора</th>
            </tr>
            <?php foreach ($allUnansQuestions as $unansQuestion) : ?>
                <tr>
                    <td><?= $unansQuestion['date_added'] ?></td>
                    <td>
                        <form action="index.php?showQuestionId=<?=$unansQuestion['id']?>&unansQuestions=unansQuestions" method="POST">
                            <select name="changeThemeId">
                                <?php foreach ($themes as $theme) : ?>
                                    <option <?php if ($unansQuestion['theme_id'] == $theme['id']):?>
                                        selected<?php endif ?> value="<?= $theme['id'] ?>">
                                        <?= $theme['theme'] ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <button type="submit">Изменить</button>
                        </form>
                    </td>
                    <td>
                        <form action="index.php?showQuestionId=<?=$unansQuestion['id']?>&unansQuestions=unansQuestions" method="POST">
                            <input type="text" size="" name="changeQuestion" value="<?= $unansQuestion['question'] ?>">
                            <input type="submit" value="Изменить">
                        </form>
                     </td>
                    <td>
                        <form action="index.php?showQuestionId=<?=$unansQuestion['id']?>&unansQuestions=unansQuestions" method="POST">
                            <textarea type="text" size="" name="changeAnswer" value=""><?= $unansQuestion['answer'] ?></textarea>
                            <p><input type="checkbox" name="publish" value="<?=$unansQuestion['id']?>">опубликовать</p>
                            <input type="submit" value="Ответить">
                        </form>
                    </td>
                    <td>
                        <?php if ($unansQuestion['published'] == 1) : ?>
                            <p>ДА - <a href="index.php?publishedOnOff=0&showQuestionId=<?=$unansQuestion['id']?>&unansQuestions=unansQuestions">скрыть</a></p>
                        <?php else : ?>
                            <p>НЕТ - <a href="index.php?publishedOnOff=1&showQuestionId=<?=$unansQuestion['id']?>&unansQuestions=unansQuestions">опубликовать</a></p>
                        <?php endif ?>
                    </td>
                    <td>
                        <form action="index.php?showQuestionId=<?=$unansQuestion['id']?>&unansQuestions=unansQuestions" method="POST">
                            <input type="text" size="" name="changeAuthorName" value="<?= $unansQuestion['author_name'] ?>">
                            <input type="submit" value="Изменить">
                        </form>
                    </td>
                    <td><?= $unansQuestion['e-mail'] ?></td>
                </tr>
            <?php endforeach ?>
        </table>
    </fieldset>
</body>
</html>