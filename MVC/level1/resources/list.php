<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Список дел</title>
</head>
<body>
<p><a href="index2.php?exit=exit">Выход</a></p>
    <p><?= $_SESSION['user_login'].'+++' ?></p>
    <hr>
    <p><a href="index2.php?add_your_task=add_your_task">Добавить дело</a> || <a href="index2.php?echo_your_tasklist=echo_your_tasklist">Вывод списка ваших дел(отсортированных по дате)</a> || <a href="index2.php?echo_assigned_list=echo_assigned_list">Показать делегированные дела</a> || <a href="index2.php?count_task=count_task">Вывести количество дел</a></p>
    <?php if (isset($_GET['echo_your_tasklist']) OR isset($_GET['echo_assigned_list'])) :?>
        <table width="" border="1" cellpadding="4" cellspacing="0">
        <tr>
            <?php foreach ($all['0'] as $colname => $var) : ?>
                <th><?= $colname?></th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($all as $line => $row) : ?>    
            <tr>
                <?php foreach ($row as $key => $value) : ?>
                    <td>
                        <?php if ($key == 'Выполнено/Невыполнено') : ?>
                            <?php if ($value == 0) : ?>
                                <a href="index2.php?done_on_off=1&task_id=<?= $row['Удаление дела'] ?>">Невыполнено</a>
                            <?php else : ?>
                                <a href="index2.php?done_on_off=0&task_id=<?= $row['Удаление дела'] ?>">Выполнено</a>
                            <?php endif ?>
                        <?php endif ?>

                        <?php if ($key == 'Удаление дела') :?>
                            <a href="index2.php?del_task_namder_id=<?= $value ?>">Удалить</a>
                        <?php endif ?>

                        <?php if (isset($_GET['echo_your_tasklist']) or isset($_GET['echo_assigned_list'])) : ?>
                            <?php if ($key !== 'Выполнено/Невыполнено' &&  $key !== 'Удаление дела') :?>
                                <?= $value?>
                            <?php endif ?>
                        <?php endif ?>
                        
                        <?php if ($key == 'Автор') : ?>
                            <form action="index2.php?echo_your_tasklist=echo_your_tasklist" method="POST">
                                <input name="task_id" type="hidden" value="<?= $row['Удаление дела'] ?>"> 
                                <select name="assigned_user_id">
                                    <?php foreach ($assignedUserList as $assignedUser): ?>
                                        <option <?php if ($row['Исполнитель'] == $assignedUser['id']):?>
                                            selected<?php endif; ?> value="<?= $assignedUser['id'] ?>">
                                            <?= $assignedUser['login'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit">Делегировать</button>
                            </form>
                        <?php endif ?>
                    </td>
                <?php endforeach ?>
            </tr>
        <?php endforeach ?>
        </table>
    <?php endif ?>
</body>
</html>