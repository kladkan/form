<?php
session_start();
if (isset($_GET['exit'])) {
    session_destroy();
    header('Location: ./index.php');
}

$pdo = new PDO("mysql:host=localhost; dbname=netology01; charset=utf8","root","fg2018start");

//Авторизация для администраторов
if (!empty($_POST['authname']) && !empty($_POST['authpass'])) {
    $sql = "SELECT `id` FROM admins WHERE `login`='{$_POST['authname']}' AND `password`='{$_POST['authpass']}'";
    foreach ($pdo->query($sql) as $admin) {
    }

    if (!empty($admin['id'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_login'] = $_POST['authname'];

    } else {
        //include 'includes/empty_admin.php';
        echo 'Администратор не найден или неправильно введён логин/пароль!<br>';
        echo '<a href="index.php?admin=admin">Войдите заново</a> или обратитесь к главному Администратору</a>';
        exit;    
    }
}


if (isset($_SESSION['admin_login'])) {
    //Получение списка администраторов
    if (isset($_GET['list_admin'])) {
        $sql = "SELECT `login`, `password` FROM `admins`";
        $admins = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        //echo '<pre>'; print_r($admins); echo '</pre>';
    }

    //Добавление администратора
    if (isset($_POST['new_login']) && isset($_POST['new_password'])) {
        if (empty($_POST['new_login']) or empty($_POST['new_password'])) {
            echo 'Вы пропустили пароль или логин! Вернитесь назад.';
            exit;
        } else {
            //echo '<pre>'; print_r($_POST); echo '</pre>';
            $sql = "SELECT `id` FROM `admins` WHERE `login`='{$_POST['new_login']}'";
            foreach ($pdo->query($sql) as $admin) {
            }

            if (!empty($admin['id'])) {
                echo 'Логин: '.$_POST['new_login'].' - занят! Придумайте уникальное имя.<br>';
            } else {
                $stmt = $pdo->prepare("INSERT INTO `admins`(`login`, `password`) VALUES (?, ?)");
                $stmt->bindParam(1, $_POST['new_login']);
                $stmt->bindParam(2, $_POST['new_password']);
                $stmt->execute();
                header('Location: ./index.php?list_admin=list_admin');
            }
        }
    }

    //Изменение пароля администратора
    if (isset($_POST['change_password'])) {
        $stmt = $pdo->prepare("UPDATE `admins` SET `password`='{$_POST['change_password']}' WHERE `login`='{$_GET['login']}' LIMIT 1");
        $stmt->execute();
        header('Location: ./index.php?list_admin=list_admin');
    }

    //Удаление администратора
    if (isset($_GET['del_admin'])) {
        $stmt = $pdo->prepare("DELETE FROM `admins` WHERE `login`='{$_GET['del_admin']}' LIMIT 1");
        $stmt->execute();
        header('Location: ./index.php?list_admin=list_admin');
    }


    //Создание новой темы
    if (isset($_POST['new_theme'])) {
        $stmt = $pdo->prepare("INSERT INTO `themes` (`theme`) VALUES (?)");
        $stmt->bindParam(1, $_POST['new_theme']);
        $stmt->execute();
        header('Location: ./index.php');
    }


}

//Получение списка тем (для всех)
$sql = "SELECT * FROM `themes`";
$themes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['question'])) {
    $stmt = $pdo->prepare("INSERT INTO `questions`(`author_name`, `e-mail`, `theme_id`, `question`) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $_POST['author_name']);
    $stmt->bindParam(2, $_POST['e-mail']);
    $stmt->bindParam(3, $_POST['theme_id']);
    $stmt->bindParam(4, $_POST['question']);
    $stmt->execute();
}

?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    
<!--Панель администратора-->
<?php if (isset($_SESSION['admin_login'])) : ?>
    <p>Вы вошли как: <?=$_SESSION['admin_login']?></p>
    <p><a href="index.php?exit=exit">Выход (прекратить администрирование)</a></p>
    <fieldset>
        <legend>Меню администратора:</legend>
        <ul>
            <li><a href="index.php?list_admin=list_admin">Список админов</a></li>
            <li><a href="index.php?unans_questions=unans_questions">Список вопросов без ответов</a></li>
        </ul>
    </fieldset>
<?php endif ?>


<!--Для авторизации-->
<?php if (!isset($_SESSION['admin_id']) && !isset($_GET['admin'])) : ?>
    <p><a href="index.php?admin=admin">Вход для администраторов</a></p>
<?php endif ?>

<?php if (isset($_GET['admin'])) : ?>
    <form action="index.php" method="POST">
        <fieldset>
        <legend>Вход для администратора</legend>
            <p>Логин: <input type="text" name="authname"></p>
            <p>Пароль: <input type="password" name="authpass"></p>
            <p><input type="submit" value="Войти">
        </fieldset>
    </form>
<?php endif ?>

<!---->

<!--Для авторизованного администратора-->
<?php if (isset($_SESSION['admin_login'])) : ?>
    <!--Вывод списка администраторов-->
    <?php if (isset($_GET['list_admin'])) : ?>
    <p>Список администраторов:</p>
    <table width="" border="1" cellpadding="4" cellspacing="0">
        <tr>
            <th>Логин</th>
            <th>Пароль</th>
            <th>Удаление</th>
        </tr>
        <?php foreach ($admins as $row) : ?>    
        <tr>
            <?php foreach ($row as $key => $value) : ?>
                <td>
                    <?= $value?>
                    <?php if ($key == 'password') : ?>
                    <form action="index.php?login=<?=$row['login']?>" method="POST">
                        <input type="text" size="20" name="change_password">
                        <input type="submit" value="Изменить">
                    </form>
                    <?php endif ?>
                </td>                
            <?php endforeach ?>
                <td><a href="index.php?del_admin=<?=$row['login']?>">Удалить</a></td>
        </tr>
        <?php endforeach ?>
    </table>
    <p><a href="index.php?list_admin=list_admin&add_admin=add_admin">Добавить администратора</a></p>
    <?php endif ?>

    <!--Добавление администратора-->
    <?php if (isset($_GET['add_admin'])) : ?>
        <form action="index.php" method="POST">
            <fieldset>
            <legend>Новый администратор</legend>
                <p>Логин: <input type="text" size="50" name="new_login"></p>
                <p>Пароль: <input type="text" size="50" name="new_password"></p>
                <p><input type="submit" value="Добавить"></p>
            </fieldset>
        </form>
    <?php endif ?>

    <!--Форма добавление темы-->
    <?php if (isset($_GET['add_theme'])) : ?>
        <form action="index.php" method="POST">
            <fieldset>
            <legend>Новая тема</legend>
                <p><input type="text" size="70" name="new_theme"></p>
                <p><input type="submit" value="Добавить"></p>
            </fieldset>
        </form>
    <?php endif ?>

    <!--Выводим темы-->
    <fieldset>
        <legend>Список тем:</legend>
        <table width="" border="1" cellpadding="4" cellspacing="0">
            <tr>
                <th>Тема</th>
                <th>Удаление</th>
            </tr>
            <?php foreach ($themes as $theme) : ?>    
            <tr>
                <td><?= $theme['theme']?></td>
                <td><a href="index.php?del_theme=<?=$theme['theme']?>">Удалить</a></td>
            </tr>
            <?php endforeach ?>
        </table>
        <a href="index.php?add_theme=add_theme">Добавить тему...</a>
    </fieldset>
<?php endif ?>


<!--Добавить вопрос (пока для всех, возможно надо оставить только для пользователей)-->
<?php if (!isset($_GET['ask_question'])) : ?>
    <p><a href="index.php?ask_question=ask_question">Задать вопрос</a></p>
<?php endif ?>
<!--Форма для нового вопроса-->
<?php if (isset($_GET['ask_question'])) : ?>
    <form action="index.php" method="POST">
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
<?php endif ?>

<!--Вывод тем для пользователей-->
<fieldset>
    <legend>Список тем:</legend>
    <table width="" border="1" cellpadding="4" cellspacing="0">
        <tr>
            <th>Тема</th>
        </tr>
        <?php foreach ($themes as $theme) : ?>    
        <tr>
            <td><?= $theme['theme']?></td>
        </tr>
        <?php endforeach ?>
    </table>
</fieldset>

</body>
</html>