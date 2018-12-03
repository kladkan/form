<?php
class Administration
{
    function listAdmin()
    {
        $adminsTable = new AdminsTable();
        //Получение списка администраторов
        $admins = $adminsTable -> adminsList();
        include_once 'View/adminsList.php';
    }

    function addAdmin()
    {
        $adminsTable = new AdminsTable();
        if (count($_POST) > 0) {
            $errors = [];
            if (empty($_POST['newLogin'])) {
                $errors['newLogin'] = 'Вы не ввели логин';
            }
            if (empty($_POST['newPassword'])) {
                $errors['newPassword'] = 'Вы не указали пароль';
            }
            if (count($errors) == 0) {
                //Поиск администратора перед регистрацией нового
                $adminOk = $adminsTable -> getAdminControl($_POST['newLogin']);
                if (!empty($adminOk['id'])) {
                    include_once 'View/loginForNewAdminNotFree.php';//Логин занят
                } else {//Добавляем админа
                    $adminsTable -> addAdmin([
                        'newLogin' => $_POST['newLogin'],
                        'newPassword' => $_POST['newPassword']
                    ]);
                    header('Location: ./index.php?listAdmin=listAdmin');
                }
            }
        }
        include_once 'View/addAdmin.php';
    }

    function changePassword()
    {
        $adminsTable = new AdminsTable();
        $adminsTable -> changePassword([
            'changePassword' => $_POST['changePassword'],
            'login' => $_GET['login']
        ]);
        header('Location: ./index.php?listAdmin=listAdmin');
    }

    function delAdmin()
    {
        $adminsTable = new AdminsTable();
        $adminsTable -> delAdmin($_GET['delAdmin']);
        header('Location: ./index.php?listAdmin=listAdmin');
    }

    function addTheme()
    {
        $themesTable = new ThemesTable();
        if (count($_POST) > 0) {
            if (empty($_POST['newTheme'])) {
                $error = 'Тема не должна быть пустой!';
            }
            if (empty($error)) {
                $themesTable -> addTheme($_POST['newTheme']);
                header('Location: ./index.php');
            } 
        }
        include_once 'View/addTheme.php';
    }

    function delTheme()
    {
        $themesTable = new ThemesTable();
        $themesTable -> delTheme($_GET['delTheme']);
        $questionsTable = new QuestionsTable();
        $questionsTable -> delQuestionsInTheme($_GET['delTheme']);
        header('Location: ./index.php');
    }

    function publishedOnOff()
    {
        $questionsTable = new QuestionsTable();
        if (isset($_POST['changeAnswer']) && !isset($_POST['publish'])) {
            $_GET['publishedOnOff'] = 0;
            //$_GET['questionId'] = $_GET['showQuestionId'];
        }
        if (isset($_POST['publish'])) {
            $_GET['publishedOnOff'] = 1;
            //$_GET['questionId'] = $_GET['showQuestionId'];
        }
        $questionsTable -> publishedOnOff([
            'publishedOnOff' => $_GET['publishedOnOff'],
            'showQuestionId' => $_GET['showQuestionId']
        ]);
    }

    function delQuestionId()
    {
        $questionsTable = new QuestionsTable();
        $questionsTable -> delQuestion($_GET['delQuestionId']);
        header('Location: ./index.php?showQuestionsTheme='.$_GET['questionsThemeId']);
    }

    function changeAuthorName()
    {
        $questionsTable = new QuestionsTable();
        $questionsTable -> changeAuthorName([
            'changeAuthorName' => $_POST['changeAuthorName'],
            'showQuestionId' => $_GET['showQuestionId']
        ]);
    }

    function changeQuestion()
    {
        $questionsTable = new QuestionsTable();
        $questionsTable -> changeQuestion([
            'changeQuestion' => $_POST['changeQuestion'],
            'showQuestionId' => $_GET['showQuestionId']
        ]);
    }

    function changeAnswer()
    {
        $questionsTable = new QuestionsTable();
        $questionsTable -> changeAnswer([
            'changeAnswer' => $_POST['changeAnswer'],
            'showQuestionId' => $_GET['showQuestionId']
        ]);
    }

    function changeThemeId()
    {
        $questionsTable = new QuestionsTable();
        if (!isset($_GET['unansQuestions'])) {
            $_GET['showQuestionsTheme'] = $_POST['changeThemeId'];
        }
        $questionsTable -> changeTheme([
            'changeThemeId' => $_POST['changeThemeId'],
            'showQuestionId' => $_GET['showQuestionId']
        ]);
    }

    function unansQuestions()
    {
        $questionsTable = new QuestionsTable();
        $allUnansQuestions = $questionsTable -> unansQuestions();
        $themesTable = new ThemesTable();
        $themes = $themesTable -> getThemes();
        include_once 'View/unansQuestions.php';
    }
}