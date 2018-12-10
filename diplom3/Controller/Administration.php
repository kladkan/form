<?php
class Administration
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function listAdmin()
    {
        $adminsTable = new AdminsTable($this->db);
        //Получение списка администраторов
        $admins = $adminsTable -> adminsList();
        include_once 'View/adminsList.php';
    }

    public function addAdmin()
    {
        $adminsTable = new AdminsTable($this->db);
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

    public function changePassword()
    {
        $adminsTable = new AdminsTable($this->db);
        $adminsTable -> changePassword([
            'changePassword' => $_POST['changePassword'],
            'login' => $_GET['login']
        ]);
        header('Location: ./index.php?listAdmin=listAdmin');
    }

    public function delAdmin()
    {
        $adminsTable = new AdminsTable($this->db);
        $adminsTable -> delAdmin($_GET['delAdmin']);
        header('Location: ./index.php?listAdmin=listAdmin');
    }

    public function addTheme()
    {
        $themesTable = new ThemesTable($this->db);
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

    public function delTheme()
    {
        $themesTable = new ThemesTable($this->db);
        $themesTable -> delTheme($_GET['delTheme']);
        $questionsTable = new QuestionsTable($this->db);
        $questionsTable -> delQuestionsInTheme($_GET['delTheme']);
        header('Location: ./index.php');
    }

    public function publishedOnOff()
    {
        $questionsTable = new QuestionsTable($this->db);
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

    public function delQuestionId()
    {
        $questionsTable = new QuestionsTable($this->db);
        $questionsTable -> delQuestion($_GET['delQuestionId']);
        header('Location: ./index.php?showQuestionsTheme='.$_GET['questionsThemeId']);
    }

    public function changeAuthorName()
    {
        $questionsTable = new QuestionsTable($this->db);
        $questionsTable -> changeAuthorName([
            'changeAuthorName' => $_POST['changeAuthorName'],
            'showQuestionId' => $_GET['showQuestionId']
        ]);
    }

    public function changeQuestion()
    {
        $questionsTable = new QuestionsTable($this->db);
        $questionsTable -> changeQuestion([
            'changeQuestion' => $_POST['changeQuestion'],
            'showQuestionId' => $_GET['showQuestionId']
        ]);
    }

    public function changeAnswer()
    {
        $questionsTable = new QuestionsTable($this->db);
        $questionsTable -> changeAnswer([
            'changeAnswer' => $_POST['changeAnswer'],
            'showQuestionId' => $_GET['showQuestionId']
        ]);
    }

    public function changeThemeId()
    {
        $questionsTable = new QuestionsTable($this->db);
        if (!isset($_GET['unansQuestions'])) {
            $_GET['showQuestionsTheme'] = $_POST['changeThemeId'];
        }
        $questionsTable -> changeTheme([
            'changeThemeId' => $_POST['changeThemeId'],
            'showQuestionId' => $_GET['showQuestionId']
        ]);
    }

    public function unansQuestions()
    {
        $questionsTable = new QuestionsTable($this->db);
        $allUnansQuestions = $questionsTable -> unansQuestions();
        $themesTable = new ThemesTable($this->db);
        $themes = $themesTable -> getThemes();
        include_once 'View/unansQuestions.php';
    }
}