<?php
session_start();
// level понимания 0
// оставшуюся часть представления нужно присоединять include
// СПРАКВА если присоединять через функции,то переменные которые вызываются в инклуд части
// не имеют незаданные значения, т.к. они не были переданы через функцию.

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
            'dbname' => 'netology01',
            'user' => 'root',
            'pass' => 'fg2018start',
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
/*
function render($link)
{
    include 'resources/'.$link;   // выяснилось не работает такой подход сказал Алексей Дацков.
}// надо обычным include делать
*/

// КОНТРОЛЛЕР
//Для авторизации
if (!isset($_SESSION['user_id']) && !isset($_GET['reg'])) {
    //print_r($_SESSION);
    if (count($_POST) > 0) {
        $errors = [];
        if (empty($_POST['authname'])) {
            $errors['authname'] = 'Вы не ввели логин';
        }
        if (empty($_POST['authpass'])) {
            $errors['authpass'] = 'Вы не ввели пароль';
        }
        if (count($errors) == 0) {
            $auth_user = search_auth_user([
                'login' => $_POST['authname'],
                'password' => $_POST['authpass']
            ]);
            if (!empty($auth_user['id'])) { //если существует то
                //echo $user['id'];
                //echo 'Вы вошли как: '.$_POST['authname'];
                $_SESSION['user_id'] = $auth_user['id'];
                $_SESSION['user_login'] = $_POST['authname'];
                header('Location: index2.php');
            } else {
                include_once 'resources/empty_user.php';
                exit;    
            }
        }
    }
    //print_r($errors);
    include_once 'resources/enter.php';
    //render('enter.php'); выяснилось не работает такой подход сказал Алексей Дацков.
}

//Для регистрации
if (isset($_GET['reg'])) {
    if (count($_POST) > 0) {
        $errors = [];
        if (empty($_POST['regname'])) {
            $errors['regname'] = 'Вы не указали логин';
        }
        if (empty($_POST['regpass'])) {
            $errors['regpass'] = 'Вы не указали пароль';
        }
        if (count($errors) == 0) {
            $reg_user = search_new_user($_POST['regname']);
            if (!empty($reg_user['id'])) {
                include_once 'resources/login_for_reg_not_free.php';
            } else {
                add_user([
                    'login' => $_POST['regname'],
                    'password' => $_POST['regpass']
                ]);
                include_once 'resources/success_reg.php';
                exit;
            }
        }
    }
    include_once 'resources/registration.php';
}


//для авторизованного пользователя
if (isset($_SESSION['user_id'])) {
    
    //добавление дела
    if (isset($_GET['add_your_task'])) {
        include_once 'resources/add_task.php';
        if (!empty($_POST['description'])) {
            add_task([
                'user_id' => $_SESSION['user_id'],
                'assigned_user_id' => $_SESSION['user_id'],
                'description' => $_POST['description'],
                'date_added' => date('Y-m-d H:i:s')
            ]);
        } else {
            include_once 'resources/empty_description.php';
        }
    }
    //Вывод списка ваших дел
    if (isset($_GET['echo_your_tasklist'])) {
        $all = tasklist($_SESSION['user_id']);
        
        if (empty($all)) {
            header('Location: ./index2.php?count_task=count_task');
            exit;
        }
        $assignedUserList = userlist();
    }
    //получение массива делегированных дел
    if (isset($_GET['echo_assigned_list'])) {
        $all = assigned_list($_SESSION['user_id']);
        if (empty($all)) {
            header('Location: ./index2.php?count_task=count_task');
            exit;
        }
        $assignedUserList = userlist();
    }
    //удаление дела
    if (key($_GET) == 'del_task_namder_id') {
        del_task([
            'user_id' => $_SESSION['user_id'],
            'id' => $_GET['del_task_namder_id']
        ]);
        header('Location: ./index2.php?echo_your_tasklist=echo_your_tasklist');
    }
    //Переключатель выполнено/невыполнено
    if (isset($_GET['done_on_off'])) {
        //print_r($_GET);
        done_on_off([
            'is_done' => $_GET['done_on_off'],
            'user_id' => $_SESSION['user_id'],
            'id' => $_GET['task_id']
        ]);
        header('Location: ./index2.php?echo_your_tasklist=echo_your_tasklist');
    }
    //Делегирование - смена ответственного
    if (isset($_POST['assigned_user_id'])) {
        assign([
            'assigned_user_id' => $_POST['assigned_user_id'],
            'id' => $_POST['task_id'],
            'user_id' => $_SESSION['user_id']
        ]);
        header('Location: ./index2.php?echo_your_tasklist=echo_your_tasklist');
    }
    //Подсчет количества дел
    if (isset($_GET['count_task'])) {
        $counttask = count_task($_SESSION['user_id']);

        include_once 'resources/count_task.php';
    }

    include_once 'resources/list.php';
}
// МОДЕЛЬ
//Проверка существования
function search_new_user($param)
{
    $reg_user = null;
    $sql = "SELECT id FROM user WHERE login='$param'"; //ищем существует ли id который равен введенному в POST имени
    foreach (db()->query($sql) as $reg_user) {
    }
    return $reg_user;
}
//Добавление пользователя
function add_user($params)
{
    $stmt = db()->prepare("INSERT INTO user (login, password) VALUES (?, ?)");
    $stmt->bindParam(1, $params['login']);
    $stmt->bindParam(2, $params['password']);
    return $stmt->execute();
}
//Поиск пользователя
function search_auth_user($params)
{
    $auth_user = null;
    $sql = "SELECT `id` FROM `user` WHERE `login`='{$params['login']}' AND password='{$params['password']}'";
    foreach (db()->query($sql) as $auth_user) {
    }
    return $auth_user;
}
//добавление дела
function add_task($params)
{
    $stmt =db()->prepare("INSERT INTO task (user_id, assigned_user_id, description, date_added) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $params['user_id']);
    $stmt->bindParam(2, $params['assigned_user_id']);
    $stmt->bindParam(3, $params['description']);
    $stmt->bindParam(4, $params['date_added']);
    return $stmt->execute();
}
//Получение списка дел
function tasklist($param)
{
    $all = null;
    $sql = "SELECT task.description as 'Дела', task.date_added as 'Дата', task.is_done as 'Выполнено/Невыполнено', user.login as 'Автор', u.login as 'Исполнитель', task.id as 'Удаление дела'
    FROM task JOIN user ON user.id=task.user_id JOIN user u ON u.id=task.assigned_user_id
    WHERE user_id='$param' ORDER BY date_added ASC";
    $all = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    return $all;
}
//Получение списка пользователей
function userlist()
{
    $assignedUserList = null;
    $sqluserlist = "SELECT id, login FROM user";
    $assignedUserList = db()->query($sqluserlist)->fetchAll(PDO::FETCH_ASSOC);
    return $assignedUserList;
}
//получение массива делегированных дел
function assigned_list($param)
{
    $all = null;
    $sql = "SELECT t.description as Дела, t.date_added as Дата, u.login as Исполнитель
    FROM task t INNER JOIN user u ON u.id=t.assigned_user_id
    WHERE t.user_id='$param' OR t.assigned_user_id='$param'";
    $all = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    return $all;
}
//удаление дела
function del_task($params)
{
    $stmt = db()->prepare("DELETE FROM task WHERE `user_id`='{$params['user_id']}' AND `id`='{$params['id']}' LIMIT 1");
    $stmt->execute();
    return $stmt->execute();
}
//Переключатель выполнено/невыполнено
function done_on_off($params)
{
    $stmt = db()->prepare("UPDATE task SET `is_done`='{$params['is_done']}' WHERE `user_id`='{$params['user_id']}' AND `id`='{$params['id']}' LIMIT 1");
    $stmt->execute();
    return $stmt->execute();
}
//Делегирование - смена ответственного
function assign($params)
{
    $stmt = db()->prepare("UPDATE task SET `assigned_user_id`='{$params['assigned_user_id']}' WHERE `id`='{$params['id']}' AND `user_id`='{$params['user_id']}' LIMIT 1");
    $stmt->execute();
    return $stmt->execute();
}
//Подсчет количества дел
function count_task($param)
{
    $counttask = null;
    $sql = "SELECT count(*) FROM task t WHERE t.user_id='$param' OR t.assigned_user_id='$param'";
    $counttask = db()->query($sql)->fetch();
    return $counttask;
}