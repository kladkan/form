<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Добавление дела</title>
</head>
<body>



    <form action="index2.php?add_your_task=add_your_task" method="POST">
        <fieldset>
            <legend>Форма для добавления нового дела</legend>    
            <p>Описание: <input type="text" size="100" name="description"></p>
            <p><input type="submit" value="Добавить"></p> 
        </fieldset>
    </form>



    <?php if (!empty($_POST['description'])) : ?>
            <p>Задача "<?= $_POST['description'] ?>" добавлена.</p>
        <?php endif ?>
</body>
</html>