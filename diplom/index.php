<?php
session_start();
if (isset($_GET['exit'])) {
    session_destroy();
    header('Location: ./index.php');
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

//db() = new PDO("mysql:host=localhost; dbname=netology01; charset=utf8","root","fg2018start");

//создаем Три таблицы для работы программы `admins`, `questions`, `themes`
//Проверка существования таблицы
$get_table = "describe `admins`";
if (db()->query($get_table) == FALSE) { //если таблицы с админами нет, то создаем её и еще две таблицы
    $stmt = db()->prepare("CREATE TABLE `admins` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `login` varchar(50) NOT NULL,
        `password` varchar(150) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $stmt->execute();

    //Создаем администратора по умолчанию
    $stmt = db()->prepare("INSERT INTO `admins`(`login`, `password`) VALUES (?, ?)");
    $x = 'admin';
    $stmt->bindParam(1, $x);
    $stmt->bindParam(2, $x);
    $stmt->execute();

    //создаем таблицу где будут храниться вопросы и ответы
    $stmt = db()->prepare("CREATE TABLE `questions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `theme_id` int(11) NOT NULL,
        `question` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `answer` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
        `published` int(11) NOT NULL DEFAULT '0',
        `author_name` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `e-mail` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $stmt->execute();

    //создаем таблицу с темами
    $stmt = db()->prepare("CREATE TABLE `themes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `theme` varchar(100) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    $stmt->execute();
}

//кнопка вызова формы входа для админов
if (!isset($_SESSION['adminLogin']) && !isset($_GET['admin'])) {
    include_once 'view/enterBattonForAdmin.php';
}

//Авторизация
if (!isset($_SESSION['adminLogin'])) {
    if (count($_POST) > 0) {
        $errors = [];
        if (empty($_POST['authname'])) {
            $errors['authname'] = 'Вы не ввели логин';
        }
        if (empty($_POST['authpass'])) {
            $errors['authpass'] = 'Вы не ввели пароль';
        }
        if (count($errors) == 0) {
            $sql = "SELECT `id` FROM `admins` WHERE `login`='{$_POST['authname']}' AND `password`='{$_POST['authpass']}'";
            foreach (db()->query($sql) as $admin) {
            }
            if (!empty($admin['id'])) {
                $_SESSION['adminId'] = $admin['id'];
                $_SESSION['adminLogin'] = $_POST['authname'];
                header('Location: index.php');
            } else {
                include_once 'view/emptyAdmin.php';
                exit;    
            }
        }
    }
    if (isset($_GET['admin'])) {
        include_once 'view/enterForAdmin.php';
    }
}

//Для авторизованного администратора
if (isset($_SESSION['adminLogin'])) {

    //Панель администратора
    include_once 'view/menuForAdmin.php';
    
    //Работа с администраторами - начало
    //Вывод списка администраторов
    if (isset($_GET['listAdmin'])) {
        //Получение списка администраторов
        $sql = "SELECT `login`, `password` FROM `admins`";
        $admins = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        include_once 'view/adminsList.php';
    }

    //Добавление администратора
    if (isset($_GET['addAdmin'])) {
        if (count($_POST) > 0) {
            $errors = [];
            if (empty($_POST['newLogin'])) {
                $errors['newLogin'] = 'Вы не ввели логин';
            }
            if (empty($_POST['newPassword'])) {
                $errors['newPassword'] = 'Вы не указали пароль';
            }
            if (count($errors) == 0) {
                $sql = "SELECT `id` FROM `admins` WHERE `login`='{$_POST['newLogin']}'";
                foreach (db()->query($sql) as $admin) {
                }
                if (!empty($admin['id'])) {
                    include_once 'view/loginForNewAdminNotFree.php';//Логин занят
                } else {//Добавляем админа
                    $stmt = db()->prepare("INSERT INTO `admins`(`login`, `password`) VALUES (?, ?)");
                    $stmt->bindParam(1, $_POST['newLogin']);
                    $stmt->bindParam(2, $_POST['newPassword']);
                    $stmt->execute();
                    header('Location: ./index.php?listAdmin=listAdmin');
                }
            }
        }
        include_once 'view/addAdmin.php';
    }

    //Изменение пароля администратора
    if (isset($_POST['changePassword'])) {
        $stmt = db()->prepare("UPDATE `admins` SET `password`='{$_POST['changePassword']}' WHERE `login`='{$_GET['login']}' LIMIT 1");
        $stmt->execute();
        header('Location: ./index.php?listAdmin=listAdmin');
    }

    //Удаление администратора
    if (isset($_GET['delAdmin'])) {
        $stmt = db()->prepare("DELETE FROM `admins` WHERE `login`='{$_GET['delAdmin']}' LIMIT 1");
        $stmt->execute();
        header('Location: ./index.php?listAdmin=listAdmin');
    }
    //работа с администраторами - конец

    //Работа с вопросами
    //Добавление новой темы
    if (isset($_GET['addTheme'])) {
        if (count($_POST) > 0) {
            if (empty($_POST['newTheme'])) {
                $error = 'Тема не должна быть пустой!';
            }
            if (empty($error)) {
                $stmt = db()->prepare("INSERT INTO `themes` (`theme`) VALUES (?)");
                $stmt->bindParam(1, $_POST['newTheme']);
                $stmt->execute();
                header('Location: ./index.php');
            } 
        }
        include_once 'view/addTheme.php';
    }

    //Удаление темы со всеми вопросами
    if (isset($_GET['delTheme'])) {
        $stmt = db()->prepare("DELETE FROM `questions` WHERE `theme_id`='{$_GET['delTheme']}';
        DELETE FROM `themes` WHERE `id`='{$_GET['delTheme']}'");
        $stmt->execute();
        header('Location: ./index.php');
    }
    
    //Переключатель опубликован/неопубликован
    if (isset($_GET['publishedOnOff']) OR isset($_POST['changeAnswer'])) {
        if (isset($_POST['changeAnswer']) && !isset($_POST['publish'])) {
            $_GET['publishedOnOff'] = 0;
            //$_GET['questionId'] = $_GET['showQuestionId'];
        }
        if (isset($_POST['publish'])) {
            $_GET['publishedOnOff'] = 1;
            //$_GET['questionId'] = $_GET['showQuestionId'];
        }
        $stmt = db()->prepare("UPDATE `questions` SET `published`='{$_GET['publishedOnOff']}' WHERE `id`='{$_GET['showQuestionId']}' LIMIT 1");
        $stmt->execute();
        //header('Location: ./index.php?showQuestionsTheme='.$_GET['questionsThemeId']);
    }

    //Удаление вопроса из темы
    if (isset($_GET['delQuestionId'])) {
        $stmt = db()->prepare("DELETE FROM `questions` WHERE `id`='{$_GET['delQuestionId']}'");
        $stmt->execute();
        header('Location: ./index.php?showQuestionsTheme='.$_GET['questionsThemeId']);
    }

    //Изменение автора
    if (isset($_POST['changeAuthorName'])) {
        $stmt = db()->prepare("UPDATE `questions` SET `author_name`='{$_POST['changeAuthorName']}' WHERE `id`='{$_GET['showQuestionId']}' LIMIT 1");
        $stmt->execute();
    }

    //Изменение вопроса
    if (isset($_POST['changeQuestion'])) {
        $stmt = db()->prepare("UPDATE `questions` SET `question`='{$_POST['changeQuestion']}' WHERE `id`='{$_GET['showQuestionId']}' LIMIT 1");
        $stmt->execute();
    }

    //Изменение ответа
    if (isset($_POST['changeAnswer'])) {
        $stmt = db()->prepare("UPDATE `questions` SET `answer`='{$_POST['changeAnswer']}' WHERE `id`='{$_GET['showQuestionId']}' LIMIT 1");
        $stmt->execute();
    }

    //Изменение темы
    if (isset($_POST['changeThemeId'])) {
        if (!isset($_GET['unansQuestions'])) {
            $_GET['showQuestionsTheme'] = $_POST['changeThemeId'];
        }
        $stmt = db()->prepare("UPDATE `questions` SET `theme_id`='{$_POST['changeThemeId']}' WHERE `id`='{$_GET['showQuestionId']}' LIMIT 1");
        $stmt->execute();
    }

    //Получение всех вопросов без ответа во всех темах в порядке их добавления
    if (isset($_GET['unansQuestions'])) {
        $sql = "SELECT `questions`.`id`, `theme_id`, `theme`, `question`, `answer`, `published`, `author_name`, `e-mail`, `date_added` FROM `questions` JOIN `themes` ON `themes`.`id`=`questions`.`theme_id` WHERE `answer` IS NULL OR `answer`='' ORDER BY `date_added` ASC";
        $allUnansQuestions = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        //echo '<pre>'; print_r($allUnansQuestions); echo '</pre>';
        include_once 'view/unansQuestions.php';
    }
}//конец условия если админ авторизован.


//Получение списка тем (для всех)
$themes = getThemes();

if (!empty($themes)) {
    foreach ($themes as $theme) {

        //подсчет вопросов в теме
        $countAllQuesInThemeArray = countAllQuesInThemeArray($theme['id']);
        if ($countAllQuesInThemeArray) {
            foreach ($countAllQuesInThemeArray as $countAllQues) {
            }
        } else {
            $countAllQues['Вопросов в теме'] = 0;
        }

        //подсчет опубликованных вопросов в теме
        $countPublishedQuesInThemeArray = countPublishedQuesInThemeArray($theme['id']);
        if ($countPublishedQuesInThemeArray) {
            foreach ($countPublishedQuesInThemeArray as $countPublishedQues) {
            }
        } else {
            $countPublishedQues['Опубликовано вопросов'] = 0;
        }
        
        //подсчет вопросов без ответа в теме
        $countUnansweredQuesInThemeArray = countUnansweredQuesInThemeArray($theme['id']);
        if ($countUnansweredQuesInThemeArray) {
            foreach ($countUnansweredQuesInThemeArray as $countUnansweredQues) {
            }
        } else {
            $countUnansweredQues['Вопросов без ответа'] = 0;
        }

        //Создаем новый массив и добавляем данные из запросов
        $themesMoreInfo[] = array('id' => $theme['id'], 'theme' => $theme['theme'], 'Вопросов в теме' => $countAllQues['Вопросов в теме'], 'Опубликовано вопросов' => $countPublishedQues['Опубликовано вопросов'], 'Вопросов без ответа' => $countUnansweredQues['Вопросов без ответа']);
    }
} else {
    $themesMoreInfo = 0;
}

//выводим темы для пользователей и для админов
include_once 'view/themesList.php';

if (isset($_GET['showQuestionsTheme'])) {//Получение списка вопросов в выбранной теме
    $questions = questions($_GET['showQuestionsTheme']);

    //Выводим вопросы для пользователей и для админов
    include_once 'view/questionsInTheme.php';

    if (isset($_GET['showQuestionId'])) {//Получение данных вопроса
    $showQuestion = showQuestion($_GET['showQuestionId']);

    //Выводим данные вопроса
    include_once 'view/showQuestionInfo.php';
    }
}

if (!isset($_SESSION['adminLogin'])) {//Для пользователей
    if (isset($theme)) {//если темы существуют то показываем кнопку "задать вопрос"
        if (!isset($_GET['ask_question'])) {
            include_once 'view/buttonForAsk.php';
        }
    } else {
        include_once 'view/noAsk.php';
    }

    if (isset($_GET['ask_question'])) {
        if (count($_POST) > 0) {
            $errors = [];
            if (empty($_POST['author_name'])) {
                $errors['author_name'] = 'Вы не указали имя';
            }
            if (empty($_POST['e-mail'])) {
                $errors['e-mail'] = 'Вы не указали e-mail';
            }
            if (empty($_POST['theme_id'])) {
                $errors['theme_id'] = 'Вы не выбрали тему';
            }
            if (empty($_POST['question'])) {
                $errors['question'] = 'Вы не написали вопрос';
            }
            if (count($errors) == 0) {
                newQuestion([
                    'author_name' => $_POST['author_name'],
                    'e-mail' => $_POST['e-mail'],
                    'theme_id' => $_POST['theme_id'],
                    'question' => $_POST['question']
                ]);
                include_once 'view/thanksForQuestion.php';
                include_once 'view/buttonForAsk.php';
                exit;
            }
        }
        include_once 'view/formForQuestion.php';
    }
}

//функции

//Получение списка тем (для всех)
function getThemes()
{
    $sql = "SELECT * FROM `themes`";
    $themes = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    return $themes;
}

//подсчет вопросов в теме
function countAllQuesInThemeArray($param)
{
    $sql = "SELECT COUNT(*) as 'Вопросов в теме' FROM `questions` WHERE `theme_id`='$param' GROUP BY `theme_id`";
    $countAllQuesInThemeArray = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    return $countAllQuesInThemeArray;
}

//подсчет опубликованных вопросов в теме
function countPublishedQuesInThemeArray($param)
{
    $sql = "SELECT COUNT(*) AS 'Опубликовано вопросов' FROM `questions` WHERE `theme_id`='$param' AND `published`=1 GROUP BY `theme_id`";
    $countPublishedQuesInThemeArray = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    return $countPublishedQuesInThemeArray;
}

//подсчет вопросов без ответа в теме
function countUnansweredQuesInThemeArray($param)
{
    $sql = "SELECT COUNT(*) AS 'Вопросов без ответа' FROM `questions` WHERE `theme_id`='$param' AND (`answer` IS NULL OR `answer`='') GROUP BY `theme_id`";
    $countUnansweredQuesInThemeArray = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    return $countUnansweredQuesInThemeArray;
}

//Получение списка вопросов в выбранной теме
function questions($param)
{
    $sql = "SELECT `questions`.`id`,  `questions`.`theme_id`, `theme`, `question`, `answer`, `date_added`, `published` FROM `questions` JOIN `themes` ON `themes`.`id`=`questions`.`theme_id` WHERE `theme_id`='$param'";
    $questions = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    return $questions;
}

//Получение данных вопроса
function showQuestion($param)
{
    $sql = "SELECT `questions`.`id`,  `questions`.`theme_id`, `date_added`, `author_name`, `theme`, `question`, `answer`, `published` FROM `questions` JOIN `themes` ON `themes`.`id`=`questions`.`theme_id` WHERE `questions`.`id`='$param'";
    $showQuestion = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    return $showQuestion;
}

//Добавление вопроса
function newQuestion($param)
{
    $stmt = db()->prepare("INSERT INTO `questions`(`author_name`, `e-mail`, `theme_id`, `question`) VALUES (?, ?, ?, ?)");
    $stmt->bindParam(1, $params['author_name']);
    $stmt->bindParam(2, $params['e-mail']);
    $stmt->bindParam(3, $params['theme_id']);
    $stmt->bindParam(4, $params['question']);
    return $stmt->execute();
}