<?php
session_start();
if(@$_GET['exit'] == 'exit') {
    session_destroy();
    header('Location: ./index.php');
}
echo '<a href="index.php?exit=exit">Выход</a>';

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
            echo 'Логин: '.$_POST['regname'].' - занят! Зарегистрируйтесь под другим именем.<br><a href="index.php?param=reg">Зарегистрироваться</a>';
            //unset($_POST);
        } else {
            $stmt = $pdo->prepare("INSERT INTO user (login, password) VALUES (?, ?)");
            $stmt->bindParam(1, $_POST['regname']);
            $stmt->bindParam(2, $_POST['regpass']);
            $stmt->execute();
            echo 'Вы успешно зарегистрированы!';
            /*echo '<pre>массив POST: после регистрации<br>';
            print_r($_POST);
            echo '</pre>';*/
            echo '<br><a href="index.php">Войти</a>';
        }
    }
    if (empty($_POST['authname']) && empty($_POST['authpass'])) {
        if (empty($_POST['regname']) or empty($_POST['regpass'])) {
            echo 'Оба поля обязательны для заполнения!<br><a href="index.php?param=reg">Зарегистрироваться</a>';
            //unset($_POST);
        }
    }

    if (!empty($_POST['authname']) && !empty($_POST['authpass'])) {
        $sql = "SELECT id FROM user WHERE login='{$_POST['authname']}' AND password='{$_POST['authpass']}'";
        foreach ($pdo->query($sql) as $row) {
        }
        if (!empty($row['id'])) { //если существует то
            //echo $row['id'];
            echo 'Пользователь найден и авторизован<br>';
            /*$_SESSION['user_id'] = $row['id'];
            echo($_SESSION['user_id']);
            echo '<pre>массив SESSION: после авторизации<br>';
            print_r($_SESSION);
            echo '</pre>';*/
        } else {
            echo 'Пользователь не найден или неправильно введён логин/пароль!
            <br><a href="index.php">Войти заново </a> или
            <br><a href="index.php?param=reg">Зарегистрироваться</a>';
            exit;
    
        }
    }
}

if (isset($_SESSION['user_id']) && $_GET['param'] == 'add_your_task' && !empty($_POST['description'])) {
    $stmt = $pdo->prepare("INSERT INTO task (user_id, assigned_user_id, description, date_added) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $_SESSION['user_id']);
    $stmt->bindParam(2, $_SESSION['user_id']);
    $stmt->bindParam(3, $_POST['description']);
    $stmt->bindParam(4, date('Y-m-d H:i:s'));
    $stmt->execute();
}

if (isset($_SESSION['user_id']) && $_GET['param'] == 'echo_your_tasklist') {
    $sql = "SELECT task.description as Дела, task.date_added as Дата, task.is_done as 'Выполнено/Невыполнено', user.login as Исполнитель, task.assigned_user_id, task.id as 'Удаление дела'
    FROM task JOIN user ON user.id=task.user_id
    WHERE user_id=$_SESSION[user_id] ORDER BY date_added ASC";
    $all = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    /*echo '<pre>Вывод списка ваших дел:<br>';
    print_r($all);
    echo '</pre>';*/
    if (empty($all)) {
        echo 'Список ваших дел пуст.';
    }

    $sqluserlist = "SELECT id, login FROM user";

    $assignedUserList = $pdo->query($sqluserlist)->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_SESSION['user_id']) && key($_GET) == 'del_task_namder_id') {
    $stmt = $pdo->prepare("DELETE FROM task WHERE user_id=$_SESSION[user_id] AND id=$_GET[del_task_namder_id] LIMIT 1");
    $stmt->execute();
    //echo 'Дело успешно удалено!';
    header('Location: ./index.php?param=echo_your_tasklist');
}

if (isset($_SESSION['user_id']) && isset($_GET['done_on_off'])) {
    echo $_GET['task_id'];
    $stmt = $pdo->prepare("UPDATE task SET is_done=$_GET[done_on_off] WHERE user_id=$_SESSION[user_id] AND id=$_GET[task_id] LIMIT 1");
    $stmt->execute();
    //echo 'Дело отмечено как выполнено/невыполнено.';
    header('Location: ./index.php?param=echo_your_tasklist');
}


/*
$sql = "SELECT * FROM shop";
$all = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
*/

/*
$sql = "SELECT * FROM books WHERE isbn like '%{$_GET['isbn']}%'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_GET['isbn']]);
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
        */
/*
$all = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    echo '<pre>';
    print_r($all);
    echo '</pre>';
*/
    
//foreach ($all as $line => $row) {
    /*echo '<pre>';
    print_r($row);
    echo '</pre>'; exit;*/
//    foreach ($row as $key => $value) {
//        echo $key.' - '.$value.'<br>';
//    }
//echo key($row)."<br />";
//}

/*
session_start();
if(@$_GET['exit'] == 'exit') {
    session_destroy();
    header('Location: ./index.php');
}
echo '<a href="index.php?exit=exit">Выход</a>';

if (!isset($_SESSION['name']) && !empty($_POST['user']) && !empty($_POST['pass'])) {
    if (!file_exists(__DIR__ . '/core/users/'.$_POST['user'].'.json')) {
    //echo 'Файл учетной записи не обнаружен';
    header('Location: ./index.php');
    } else {
        //echo 'Файл учетной записи есть';
        $json = file_get_contents(__DIR__ . '/core/users/'.$_POST['user'].'.json');
        $user = json_decode($json, true);
    }
    if ($_POST['pass'] === $user[$_POST['user']]) {
        $_SESSION['name'] = $_POST['user'];
        $_SESSION['role'] = 'admin';
    }
}

if (!isset($_SESSION['name']) && !empty($_POST['user'])) {
    $_SESSION['name'] = $_POST['user'];
    $_SESSION['role'] ='guest';
}
*/

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Вход (регистрация)</title>
</head>
<body>
<?php if ($_GET['param'] == 'reg') : ?>
    <?php if (empty($_POST['regname']) && empty($_POST['regpass'])) : ?>
        <form action="index.php" method="POST">
        <fieldset>
        <legend>Форма для регистрации</legend>
            <p>Логин: <input type="text" name="regname"></p>
            <p>Пароль: <input type="password" name="regpass"></p>
            <input type="submit" value="Зарегистрироваться">
        </fieldset>
        </form>
    <?php endif ?>
<?php endif ?>

<?php if ($_GET['param'] !== 'reg' && !isset($_POST['regname']) && !isset($_POST['regpass']))  : ?>
    <?php if (!isset($_SESSION['user_id'])) : ?>
        <form action="index.php" method="POST">
        <fieldset>
        <legend>Форма для входа</legend>
            <p>Чтобы войти, введите логин и пароль или пройдите<a href="index.php?param=reg"> Регистрацию</a></p>
            <p>Логин: <input type="text" name="authname"></p>
            <p>Пароль: <input type="password" name="authpass"></p>
            <p><input type="submit" value="Войти"> 
        </fieldset>
        </form>
    <?php endif ?>
<?php endif ?>

<?php if (isset($_SESSION['user_id'])) : ?>
    <p><a href="index.php?param=add_your_task">Добавить дело</a> || 
    <a href="index.php?param=echo_your_tasklist">Вывод списка ваших дел(отсортированных по дате)</a> || 
    <a href="index.php?param=assign">Добавить возможность делегирования</a></p>

    <?php if ($_GET['param'] == 'add_your_task') : ?>
    <form action="index.php?param=add_your_task" method="POST">
        <fieldset>
        <legend>Форма для добавления нового вашего дела</legend>    
            <p>Описание: <input type="text" size="100" name="description"></p>
            <p><input type="submit" value="Добавить"></p> 
        </fieldset>
    </form>
    <?php echo 'Задача "'.$_POST['description'].'" добавлена.'; ?>
    <?php endif ?>

    <?php if ($_GET['param'] == 'echo_your_tasklist') : ?>
        <table width="" border="1" cellpadding="4" cellspacing="0">
        <tr>
            <?php foreach ($all['0'] as $colname => $var) : ?>
                <th><?php echo $colname?></th>
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
                        if ($key !== 'Выполнено/Невыполнено' && $key !== 'Удаление дела') {
                            echo $value;
                        }
                        ?>
                        <?php if ($key == 'Исполнитель') : ?>
                            <form action="index.php?param=echo_your_tasklist" method="POST">
                            <input name="task_id" type="hidden" value="<?= $row['Удаление дела'] ?>"> 
                            <select name="assigned_user_id">
                            <?php foreach ($assignedUserList as $assignedUser): ?>
                            <option <?php if ($row['assigned_user_id'] == $assignedUser['id']):?>
                                selected<?php endif; ?> value="<?= assignedUser['id'] ?>">
                                <?= assignedUser['login'] ?>
                            </option>
                            <?php endforeach; ?>
                            </select>
                            <buttom type="submit">Делегировать</buttom>
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