<?php
class Controller
{
    function controllerForStart()
    {   $tablesForStart = new TablesForStart();
        //Проверка существования таблицы
        if (db()->query("describe `admins`") == FALSE) {//если таблицы с админами нет, то создаем её и еще две таблицы
            $tablesForStart -> createAdminsTable();
        }
        if (db()->query("describe `questions`") == FALSE) {
            $tablesForStart -> createQuestionsAnswersTable();
        }
        if (db()->query("describe `themes`") == FALSE) {
            $tablesForStart -> createThemesTable();
        }
    }

    function controllerAuthorizationForAdmin()
    {
        $adminsTable = new AdminsTable();
        //кнопка вызова формы входа для админов
        if (!isset($_SESSION['adminLogin']) && !isset($_GET['admin'])) {
            include_once 'View/enterBattonForAdmin.php';
        }
        //Авторизация
        //работа с администраторами - начало
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
                    $adminAuth = $adminsTable -> getAdminForAuth([
                        'authname' => $_POST['authname'],
                        'authpass' => $_POST['authpass']
                    ]);
                    if (!empty($adminAuth['id'])) {
                        $_SESSION['adminId'] = $adminAuth['id'];
                        $_SESSION['adminLogin'] = $_POST['authname'];
                        header('Location: index.php');
                    } else {
                        include_once 'View/emptyAdmin.php';
                        exit;    
                    }
                }
            }
            if (isset($_GET['admin'])) {
                include_once 'View/enterForAdmin.php';
            }
        }
    }

    public function themesForAll()
    {
        $themesTable = new ThemesTable();
        //Получение списка тем (для всех)
        $themes = $themesTable -> getThemes();
        if (!empty($themes)) {
            foreach ($themes as $theme) {
                $questionsTable = new QuestionsTable();
                //подсчет вопросов в теме
                $countAllQuesInThemeArray = $questionsTable -> countAllQuesInThemeArray($theme['id']);
                if ($countAllQuesInThemeArray) {
                    foreach ($countAllQuesInThemeArray as $countAllQues) {
                    }
                } else {
                    $countAllQues['Вопросов в теме'] = 0;
                }
                //подсчет опубликованных вопросов в теме
                $countPublishedQuesInThemeArray = $questionsTable -> countPublishedQuesInThemeArray($theme['id']);
                if ($countPublishedQuesInThemeArray) {
                    foreach ($countPublishedQuesInThemeArray as $countPublishedQues) {
                    }
                } else {
                    $countPublishedQues['Опубликовано вопросов'] = 0;
                }
                //подсчет вопросов без ответа в теме
                $countUnansweredQuesInThemeArray = $questionsTable -> countUnansweredQuesInThemeArray($theme['id']);
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
        if ($themesMoreInfo != 0) {
            if (isset($_SESSION['adminLogin'])) {
                include_once 'View/themesListForAdmins.php';
            } else {
                include_once 'View/themesListForUsers.php';
            }
        } else {
            include_once 'View/themesListEmpty.php';
        }
        return $themes;
    }

    function showQuestionsTheme()
    {
        $questionsTable = new QuestionsTable();
        $questions = $questionsTable -> questions($_GET['showQuestionsTheme']);
        //Выводим вопросы для пользователей и для админов
        include_once 'View/questionsInTheme.php';
        if (isset($_GET['showQuestionId'])) {//Получение данных вопроса
        $showQuestion = $questionsTable -> showQuestion($_GET['showQuestionId']);
        //Выводим данные вопроса
        $controller = new Controller();
        $themes = $controller -> themesForAll();
        include_once 'View/showQuestionInfo.php';
        }
    }
}