<?php
session_start();
if (isset($_GET['exit'])) {
    session_destroy();
    header('Location: ./index2.php');
}

function db()
{
    static $db = null;
    if ($db === null) {
        $config = [
            'host' => 'localhost',
            'dbname' => 'ayakovlev',
            'user' => 'ayakovlev',
            'pass' => 'neto1880',
        ];
        try {
            $db = new PDO(
                'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8',
                $config['user'],
                $config['pass']
            );
        } catch (PDOException $e) {
            die('Database error: ' . $e->getMessage() . '<br/>');
        }
    }
    return $db;
}

function render($template)
{
    include $template;
}

// КОНТРОЛЛЕР


if (!isset($_SESSION['user_id'])) {
    if (!empty($_POST['regname']) && !empty($_POST['regpass'])) {
        search_new_user();
        if (!empty($reg_user['id'])) {
            echo 'Логин: '.$_POST['regname'].' - занят! Зарегистрируйтесь под другим именем.<br><a href="index2.php?reg=reg">Зарегистрироваться</a>';
        } else {
            add_user();
            include 'resources/success_reg.php';
        }
    }
    if (empty($_POST['authname']) or empty($_POST['authpass'])) {
        if (empty($_POST['regname']) or empty($_POST['regpass'])) {
            echo '<br>Оба поля обязательны для заполнения!<br><a href="index2.php?reg=reg">Зарегистрироваться</a><br><br>';
        }
    }
    if (!empty($_POST['authname']) && !empty($_POST['authpass'])) {
        search_auth_user();
        if (!empty($auth_user['id'])) { //если существует то
            //echo $user['id'];
            //Вы вошли как: '.$_POST['authname'];
            $_SESSION['user_id'] = $auth_user['id'];
            $_SESSION['user_login'] = $_POST['authname'];
        } else {
            include 'resources/empty_user.php';
            exit;    
        }
    }
}

if (isset($_GET['reg'])) {
    if (empty($_POST['regname']) && empty($_POST['regpass'])) {
        render('resources/registration.php');
    }
}

if (!isset($_GET['reg']) && !isset($_POST['regname']) && !isset($_POST['regpass'])) {
    if (!isset($_SESSION['user_id'])) {
        render('resources/enter.php');
    }
}


if (isset($_SESSION['user_id'])) {
    
    //добавление дела
    if (isset($_GET['add_your_task'])) {
        render('resources/add_task.php');

        if (!empty($_POST['description'])) {
            add_task();
        }
    }
    //Вывод списка ваших дел
    if (isset($_GET['echo_your_tasklist'])) {
        tasklist();
        
        if (empty($all)) {
            header('Location: ./index2.php?count_task=count_task');
            exit;
        }
        userlist();

    }
    //получение массива делегированных дел
    if (isset($_GET['echo_assigned_list'])) {
        assigned_list();
        if (empty($all)) {
            header('Location: ./index2.php?count_task=count_task');
            exit;
        }
        userlist();


    }


    //удаление дела
    if (key($_GET) == 'del_task_namder_id') {
        del_task();
        header('Location: ./index2.php?echo_your_tasklist=echo_your_tasklist');
    }
    //Переключатель выполнено/невыполнено
    if (isset($_GET['done_on_off'])) {
        //print_r($_GET);
        done_on_off();
        header('Location: ./index2.php?echo_your_tasklist=echo_your_tasklist');
    }
    //Делегирование - смена ответственного
    if (isset($_POST['assigned_user_id'])) {
        assign();
        header('Location: ./index2.php?echo_your_tasklist=echo_your_tasklist');
    }
    //Подсчет количества дел
    if (isset($_GET['count_task'])) {
        count_task();
    }
}

// МОДЕЛЬ

//Проверка существования
function search_new_user()
{
    $reg_user = null;
    global $reg_user;
    $sql = "SELECT id FROM user WHERE login='{$_POST['regname']}'"; //ищем существует ли id который равен введенному в POST имени
        foreach (db()->query($sql) as $reg_user) {
            }
}

//Добавление пользователя
function add_user()
{
    $stmt = db()->prepare("INSERT INTO user (login, password) VALUES (?, ?)");
    $stmt->bindParam(1, $_POST['regname']);
    $stmt->bindParam(2, $_POST['regpass']);
    return $stmt->execute();
}

//Поиск пользователя
function search_auth_user()
{
    $auth_user = null;
    global $auth_user;
    $sql = "SELECT id FROM user WHERE login='{$_POST['authname']}' AND password='{$_POST['authpass']}'";
        foreach (db()->query($sql) as $auth_user) {
            }
}

//добавление дела
function add_task()
{
    $stmt =db()->prepare("INSERT INTO task (user_id, assigned_user_id, description, date_added) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $_SESSION['user_id']);
    $stmt->bindParam(2, $_SESSION['user_id']);
    $stmt->bindParam(3, $_POST['description']);
    $date = date('Y-m-d H:i:s');
    $stmt->bindParam(4, $date);
    return $stmt->execute();
}

//Получение списка дел
function tasklist()
{
    $all = null;
    global $all;
    $sql = "SELECT task.description as 'Дела', task.date_added as 'Дата', task.is_done as 'Выполнено/Невыполнено', user.login as 'Автор', u.login as 'Исполнитель', task.id as 'Удаление дела'
    FROM task JOIN user ON user.id=task.user_id JOIN user u ON u.id=task.assigned_user_id
    WHERE user_id='$_SESSION[user_id]' ORDER BY date_added ASC";
    $all = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    return $all;
}

//Получение списка пользователей
function userlist()
{
    $assignedUserList = null;
    global $assignedUserList;
    $sqluserlist = "SELECT id, login FROM user";
    $assignedUserList = db()->query($sqluserlist)->fetchAll(PDO::FETCH_ASSOC);
    return $assignedUserList;
}

//получение массива делегированных дел
function assigned_list()
{
    $all = null;
    global $all;
    $sql = "SELECT t.description as Дела, t.date_added as Дата, u.login as Исполнитель
    FROM task t INNER JOIN user u ON u.id=t.assigned_user_id
    WHERE t.user_id=$_SESSION[user_id] OR t.assigned_user_id=$_SESSION[user_id]";
    $all = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    return $all;
}

//удаление дела
function del_task()
{
    $stmt = db()->prepare("DELETE FROM task WHERE user_id=$_SESSION[user_id] AND id=$_GET[del_task_namder_id] LIMIT 1");
    $stmt->execute();
    return $stmt->execute();
}

//Переключатель выполнено/невыполнено
function done_on_off()
{
    $stmt = db()->prepare("UPDATE task SET is_done=$_GET[done_on_off] WHERE user_id=$_SESSION[user_id] AND id=$_GET[task_id] LIMIT 1");
    $stmt->execute();
    return $stmt->execute();
}

//Делегирование - смена ответственного
function assign()
{
    $stmt = db()->prepare("UPDATE task SET assigned_user_id=$_POST[assigned_user_id] WHERE id=$_POST[task_id] AND user_id=$_SESSION[user_id] LIMIT 1");
    $stmt->execute();
    return $stmt->execute();
}

//Подсчет количества дел
function count_task()
{
    $counttask = null;
    global $counttask;
    $sql = "SELECT count(*) FROM task t WHERE t.user_id=$_SESSION[user_id] OR t.assigned_user_id=$_SESSION[user_id]";
    $counttask = db()->query($sql)->fetch();
    return $counttask;
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body>

<?php if (isset($_SESSION['user_id'])) : ?>

<!-- можно убрать позже это меню т.к. оно реализовано в файле resources/menu_for_user.php -->
    <p><a href="index2.php?exit=exit">Выход</a></p>
    <p><?= $_SESSION['user_login'] ?></p>
    <hr>
    <p><a href="index2.php?add_your_task=add_your_task">Добавить дело</a> || <a href="index2.php?echo_your_tasklist=echo_your_tasklist">Вывод списка ваших дел(отсортированных по дате)</a> || <a href="index2.php?echo_assigned_list=echo_assigned_list">Показать делегированные дела</a> || <a href="index2.php?count_task=count_task">Вывести количество дел</a></p>

    <?php if (isset($_GET['count_task'])) :?>
        <p>Подсчет количества дел: <?= $counttask[0]?></p>
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
<?php endif ?>
</body>
</html>