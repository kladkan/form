<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
</head>
<body>
    <?php if (!empty($errors)) : ?>
        <p><?= 'Ошибка заполнения формы вопроса:' ?>
        <?php foreach ($errors as $errorName => $errorValue) : ?>
            <p><?= $errorValue?></p>
        <?php endforeach ?>
    <?php endif?>
    <form action="index.php?askQuestion=askQuestion" method="POST">
        <fieldset>
            <legend>Новый вопрос</legend>
            <p>Все поля обязательны для заполнения</p>
            <p>Ваше имя: <input type="text" size="50" name="author_name"></p>
            <p>E-mail: <input type="text" size="50" name="e-mail"></p>
            <p>Выберите тему:
                <select name="theme_id">
                    <?php foreach ($themes as $theme) : ?>
                        <option value="<?= $theme['id'] ?>"><?= $theme['theme'] ?></option>
                    <?php endforeach ?>
                </select>
            </p>
            <p>Ваш вопрос: <textarea type="text" cols="50" rows="5" name="question"></textarea></p>
            <button type="submit">Задать</button>
        </fieldset>
    </form>
</body>
</html>