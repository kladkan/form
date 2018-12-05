<?php
class User
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function askButton()
    {
        $controller = new Controller($this->db);
        $theme = $controller -> themesForAll();
        if (isset($theme)) {//если темы существуют то показываем кнопку "задать вопрос"
            if (!isset($_GET['askQuestion'])) {
                include_once 'View/buttonForAsk.php';
            }
        } else {
            include_once 'View/noAsk.php';
        }
    }

    public function askQuestion()
    {
        $questionsTable = new QuestionsTable($this->db);
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
                $questionsTable -> newQuestion([//Добавление вопроса
                    'author_name' => $_POST['author_name'],
                    'e-mail' => $_POST['e-mail'],
                    'theme_id' => $_POST['theme_id'],
                    'question' => $_POST['question']
                ]);
                include_once 'View/thanksForQuestion.php';
                include_once 'View/buttonForAsk.php';
                exit;
            }
        }
        $controller = new Controller($this->db);
        $themes = $controller -> themesForAll();
        include_once 'View/formForQuestion.php';
    }
}