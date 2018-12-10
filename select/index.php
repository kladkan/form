<?php
session_start();
if (isset($_GET['exit'])) {
    session_destroy();
    header('Location: ./index.php');
}
echo '<a href="index.php?exit=exit">Выход</a><br>';
if (isset($_SESSION['user_login'])) {
    echo '<br>'.$_SESSION['user_login'].'<br><hr>';
}
/*
echo '<pre>массив POST старт страницы:<br>';
print_r($_POST);
echo '</pre>';

echo '<pre>массив SESSION старт страницы:<br>';
print_r($_SESSION);
echo '</pre>';
*/
$pdo = new PDO("mysql:host=localhost; dbname=netology01; charset=utf8","root","fg2018start");
if (!isset($_SESSION['user_id'])) {
    if (!empty($_POST['regname']) && !empty($_POST['regpass'])) {
        $sql = "SELECT id FROM user WHERE login='{$_POST['regname']}'"; //ищем существует ли id который равен введенному в POST имени
        foreach ($pdo->query($sql) as $row) {
            }
        if (!empty($row['id'])) { //если существует то
            //echo $row['id'];
            echo 'Логин: '.$_POST['regname'].' - занят! Зарегистрируйтесь под другим именем.<br><a href="index.php?reg=reg">Зарегистрироваться</a>';
            //unset($_POST);
        } else {
            $stmt = $pdo->prepare("INSERT INTO user (login, password) VALUES (?, ?)");
            $stmt->bindParam(1, $_POST['regname']);
            $stmt->bindParam(2, $_POST['regpass']);
            $stmt->execute();
            echo '<br>Вы успешно зарегистрированы!';
            /*echo '<pre>массив POST: после регистрации<br>';
            print_r($_POST);
            echo '</pre>';*/
            echo '<br><a href="index.php">Войти</a>';
        }
    }
    if (empty($_POST['authname']) && empty($_POST['authpass'])) {
        if (empty($_POST['regname']) or empty($_POST['regpass'])) {
            echo '<br>Оба поля обязательны для заполнения!<br><a href="index.php?reg=reg">Зарегистрироваться</a><br><br>';

        }
    }

    if (!empty($_POST['authname']) && !empty($_POST['authpass'])) {
        $sql = "SELECT id FROM user WHERE login='{$_POST['authname']}' AND password='{$_POST['authpass']}'";
        foreach ($pdo->query($sql) as $row) {
        }
        if (!empty($row['id'])) { //если существует то
            //echo $row['id'];
            echo '<br>Вы вошли как: '.$_POST['authname'];
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_login'] = $_POST['authname'];
        } else {
            echo 'Пользователь не найден или неправильно введён логин/пароль!
            <br><a href="index.php">Войти заново </a> или
            <br><a href="index.php?reg=reg">Зарегистрироваться</a>';
            exit;
    
        }
    }
}

//добавление дела
if (isset($_SESSION['user_id']) && isset($_GET['add_your_task']) && !empty($_POST['description'])) {
    $stmt = $pdo->prepare("INSERT INTO task (user_id, assigned_user_id, description, date_added) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $_SESSION['user_id']);
    $stmt->bindParam(2, $_SESSION['user_id']);
    $stmt->bindParam(3, $_POST['description']);
    $date = date('Y-m-d H:i:s');
    $stmt->bindParam(4, $date);
    $stmt->execute();
}

//получение массива ваших дел
if (isset($_SESSION['user_id']) && isset($_GET['echo_your_tasklist'])) {
    $sql = "SELECT task.description as 'Дела', task.date_added as 'Дата', task.is_done as 'Выполнено/Невыполнено', user.login as 'Автор', u.login as 'Исполнитель', task.id as 'Удаление дела'
    FROM task JOIN user ON user.id=task.user_id JOIN user u ON u.id=task.assigned_user_id
    WHERE user_id='$_SESSION[user_id]' ORDER BY date_added ASC";

    $all = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    /*echo '<pre>Вывод массива ваших дел:<br>';
    print_r($all);
    echo '</pre>';*/
    if (empty($all)) {
        header('Location: ./index.php?count_task=count_task');
        exit;
    }
    $sqluserlist = "SELECT id, login FROM user";
    $assignedUserList = $pdo->query($sqluserlist)->fetchAll(PDO::FETCH_ASSOC);
    /*echo '<pre>Массив всех пользователей:<br>';
    print_r($assignedUserList);
    echo '</pre>';*/
}

//получение массива делегированных дел
if (isset($_SESSION['user_id']) && isset($_GET['echo_assigned_list'])) {
    $sql = "SELECT t.description as Дела, t.date_added as Дата, u.login as Исполнитель
    FROM task t INNER JOIN user u ON u.id=t.assigned_user_id
    WHERE t.user_id=$_SESSION[user_id] OR t.assigned_user_id=$_SESSION[user_id]";
    //SELECT ... FROM task t INNER JOIN user u ON u.id=t.assigned_user_id WHERE t.user_id = ... OR t.assigned_user_id = ...
    $all = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    /*echo '<pre>Вывод массива делегированных дел:<br>';
    print_r($all);
    echo '</pre>';*/
    if (empty($all)) {
        header('Location: ./index.php?count_task=count_task');
        exit;
    }
    $sqluserlist = "SELECT id, login FROM user";
    $assignedUserList = $pdo->query($sqluserlist)->fetchAll(PDO::FETCH_ASSOC);
    //echo '<pre>Массив всех пользователей:<br>';
    //print_r($assignedUserList);
    //echo '</pre>';
}

//удаление дела
if (isset($_SESSION['user_id']) && key($_GET) == 'del_task_namder_id') {
    $stmt = $pdo->prepare("DELETE FROM task WHERE user_id=$_SESSION[user_id] AND id=$_GET[del_task_namder_id] LIMIT 1");
    $stmt->execute();
    header('Location: ./index.php?echo_your_tasklist=echo_your_tasklist');
}

//Переключатель выполнено/невыполнено
if (isset($_SESSION['user_id']) && isset($_GET['done_on_off'])) {
    //print_r($_GET);
    $stmt = $pdo->prepare("UPDATE task SET is_done=$_GET[done_on_off] WHERE user_id=$_SESSION[user_id] AND id=$_GET[task_id] LIMIT 1");
    $stmt->execute();
    header('Location: ./index.php?echo_your_tasklist=echo_your_tasklist');
}

//Делегирование - смена ответственного
if (isset($_SESSION['user_id']) && isset($_POST['assigned_user_id'])) {
    /*echo '<pre>массив POST перед делегированием:<br>';
    print_r($_POST);
    echo '</pre>';
    echo '<pre>массив GET перед делегированием:<br>';
    print_r($_GET);
    echo '</pre>';*/
    $stmt = $pdo->prepare("UPDATE task SET assigned_user_id=$_POST[assigned_user_id] WHERE id=$_POST[task_id] AND user_id=$_SESSION[user_id] LIMIT 1");
    $stmt->execute();
    header('Location: ./index.php?echo_your_tasklist=echo_your_tasklist');
}

//Подсчет количества дел
if (isset($_SESSION['user_id']) && isset($_GET['count_task'])) {
    //print_r($_GET);
    $sql = "SELECT count(*) FROM task t WHERE t.user_id=$_SESSION[user_id] OR t.assigned_user_id=$_SESSION[user_id]";
    $counttask = $pdo->query($sql)->fetch();
    echo 'Подсчет количества дел: '.$counttask[0];
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Вход (регистрация)</title>
</head>
<body>
<?php if (isset($_GET['reg'])) : ?>
    <?php if (empty($_POST['regname']) && empty($_POST['regpass'])) : ?>
        <form action="index.php" method="POST">
        <fieldset>
        <legend>РЕГИСТРАЦИЯ</legend>
            <p>Для регистрации, введите логин и пароль. Если вы зарегистрированы, выполните <a href="index.php">вход.</a></p>
            <p>Логин: <input type="text" name="regname"></p>
            <p>Пароль: <input type="password" name="regpass"></p>
            <input type="submit" value="Зарегистрироваться">
        </fieldset>
        </form>
    <?php endif ?>
<?php endif ?>

<?php if (!isset($_GET['reg']) && !isset($_POST['regname']) && !isset($_POST['regpass']))  : ?>
    <?php if (!isset($_SESSION['user_id'])) : ?>
        <form action="index.php" method="POST">
        <fieldset>
        <legend>ФАРМА ДЛЯ ВХОДА</legend>
            <p>Чтобы войти, введите логин и пароль или пройдите<a href="index.php?reg=reg"> Регистрацию</a></p>
            <p>Логин: <input type="text" name="authname"></p>
            <p>Пароль: <input type="password" name="authpass"></p>
            <p><input type="submit" value="Войти"> 
        </fieldset>
        </form>
    <?php endif ?>
<?php endif ?>

<?php if (isset($_SESSION['user_id'])) : ?>
    <p><a href="index.php?add_your_task=add_your_task">Добавить дело</a> || <a href="index.php?echo_your_tasklist=echo_your_tasklist">Вывод списка ваших дел(отсортированных по дате)</a> || <a href="index.php?echo_assigned_list=echo_assigned_list">Показать делегированные дела</a> || <a href="index.php?count_task=count_task">Вывести количество дел</a></p>

    <?php if (isset($_GET['add_your_task'])) : ?>
    <form action="index.php?add_your_task=add_your_task" method="POST">
        <fieldset>
        <legend>Форма для добавления нового вашего дела</legend>    
            <p>Описание: <input type="text" size="100" name="description"></p>
            <p><input type="submit" value="Добавить"></p> 
        </fieldset>
    </form>
    <?php if (!empty($_POST['description'])) {echo 'Задача "'.$_POST['description'].'" добавлена.';} ?>
    <?php endif ?>

    <?php if (isset($_GET['echo_your_tasklist']) OR isset($_GET['echo_assigned_list'])) : ?>
        <table width="" border="1" cellpadding="4" cellspacing="0">
        <tr>
            <?php foreach ($all['0'] as $colname => $var) : ?>
                <th><?= $colname?></th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($all as $line => $row) : ?>    
            <tr>
                <?php foreach ($row as $key => $value) : ?>
                    <td><?php
                        if ($key == 'Выполнено/Невыполнено') {
                            if ($value == 0) {
                                echo '<a href="index.php?done_on_off=1&task_id='.$row['Удаление дела'].'">Невыполнено</a>';
                            } else {
                                echo '<a href="index.php?done_on_off=0&task_id='.$row['Удаление дела'].'">Выполнено</a>';
                            }
                        }                        
                        if ($key == 'Удаление дела') {
                            echo '<a href="index.php?del_task_namder_id='.$value.'">Удалить</a>';
                        }

                        if (isset($_GET['echo_your_tasklist']) or isset($_GET['echo_assigned_list'])) {
                            if ($key !== 'Выполнено/Невыполнено' &&  $key !== 'Удаление дела') {
                                echo $value;
                            }
                        }

                        ?>
                        <?php if ($key == 'Автор') : ?>
                            <form action="index.php?echo_your_tasklist=echo_your_tasklist" method="POST">
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
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php endif ?>

<?php endif ?>

<!--пример из задания проверить что он делает(даёт)-->
    
</body>
</html>