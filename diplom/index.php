<?php
session_start();
if (isset($_GET['exit'])) {
    session_destroy();
    header('Location: ./index.php');
}

include_once 'config.php';//Подключение к базе данных функция db()

spl_autoload_register(function ($className) {
    $file = 'Model/'.strtolower($className) .'.php';
    if (file_exists($file)) {
        require_once ($file);
    }
  });

spl_autoload_register(function ($className) {
    $file = 'Controller/'.strtolower($className) .'.php';
    if (file_exists($file)) {
        require_once ($file);
    }
  });

$controller = new Controller();
$controller -> controllerForStart();
$controller -> controllerAuthorizationForAdmin();

//Для авторизованного администратора
if (isset($_SESSION['adminLogin'])) {
    include_once 'View/menuForAdmin.php';//Панель администратора
    $administration = new Administration();
    if (isset($_GET['listAdmin'])) {//Вывод списка администраторов
        $administration -> listAdmin();
    }
    if (isset($_GET['addAdmin'])) {//Добавление администратора
        $administration -> addAdmin();
    }

    //Изменение пароля администратора
    if (isset($_POST['changePassword'])) {
        $administration -> changePassword();
    }

    //Удаление администратора
    if (isset($_GET['delAdmin'])) {
        $administration -> delAdmin();
    }//работа с администраторами - конец

    //Работа с вопросами
    if (isset($_GET['addTheme'])) {//Добавление новой темы
        $administration -> addTheme();
    }

    //Удаление темы со всеми вопросами
    if (isset($_GET['delTheme'])) {
        $administration -> delTheme();
    }
    
    //Переключатель опубликован/скрыт
    if (isset($_GET['publishedOnOff']) OR isset($_POST['changeAnswer'])) {
        $administration -> publishedOnOff();
    }

    //Удаление вопроса из темы
    if (isset($_GET['delQuestionId'])) {
        $administration -> delQuestionId();
    }

    //Изменение автора
    if (isset($_POST['changeAuthorName'])) {
        $administration -> changeAuthorName();
    }

    //Изменение вопроса
    if (isset($_POST['changeQuestion'])) {
        $administration -> changeQuestion();
    }

    //Изменение ответа
    if (isset($_POST['changeAnswer'])) {
        $administration -> changeAnswer();
    }

    //Изменение темы
    if (isset($_POST['changeThemeId'])) {
        $administration -> changeThemeId();
    }

    //Получение всех вопросов без ответа во всех темах в порядке их добавления
    if (isset($_GET['unansQuestions'])) {
        $administration -> unansQuestions();
    }
}//конец условия если админ авторизован.

$controller -> themesForAll();

if (isset($_GET['showQuestionsTheme'])) {//Получение списка вопросов в выбранной теме
    $controller -> showQuestionsTheme();
}

$user = new User;
if (!isset($_SESSION['adminLogin'])) {//Для пользователей Задать вопрос
    $user -> askButton();
    if (isset($_GET['askQuestion'])) {
        $user -> askQuestion();
    }
}

