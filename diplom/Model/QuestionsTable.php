<?php
class QuestionsTable
{
    //подсчет вопросов в теме
    public function countAllQuesInThemeArray($param)//9
    {
        $sql = "SELECT COUNT(*) as 'Вопросов в теме' FROM `questions` WHERE `theme_id`='$param' GROUP BY `theme_id`";
        $countAllQuesInThemeArray = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $countAllQuesInThemeArray;
    }

    //подсчет опубликованных вопросов в теме
    public function countPublishedQuesInThemeArray($param)//10
    {
        $sql = "SELECT COUNT(*) AS 'Опубликовано вопросов' FROM `questions` WHERE `theme_id`='$param' AND `published`=1 GROUP BY `theme_id`";
        $countPublishedQuesInThemeArray = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $countPublishedQuesInThemeArray;
    }

    //подсчет вопросов без ответа в теме
    public function countUnansweredQuesInThemeArray($param)//11
    {
        $sql = "SELECT COUNT(*) AS 'Вопросов без ответа' FROM `questions` WHERE `theme_id`='$param' AND (`answer` IS NULL OR `answer`='') GROUP BY `theme_id`";
        $countUnansweredQuesInThemeArray = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $countUnansweredQuesInThemeArray;
    }

    //Получение списка вопросов в выбранной теме
    public function questions($param)//12
    {
        $sql = "SELECT `questions`.`id`,  `questions`.`theme_id`, `theme`, `question`, `answer`, `date_added`, `published` FROM `questions` JOIN `themes` ON `themes`.`id`=`questions`.`theme_id` WHERE `theme_id`='$param'";
        $questions = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $questions;
    }

    //Получение данных вопроса
    public function showQuestion($param)//13
    {
        $sql = "SELECT `questions`.`id`,  `questions`.`theme_id`, `date_added`, `author_name`, `theme`, `question`, `answer`, `published` FROM `questions` JOIN `themes` ON `themes`.`id`=`questions`.`theme_id` WHERE `questions`.`id`='$param'";
        $showQuestion = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $showQuestion;
    }

    //Добавление вопроса
    public function newQuestion($params)//14
    {
        $stmt = db()->prepare("INSERT INTO `questions`(`author_name`, `e-mail`, `theme_id`, `question`) VALUES (?, ?, ?, ?)");
        $stmt->bindParam(1, $params['author_name']);
        $stmt->bindParam(2, $params['e-mail']);
        $stmt->bindParam(3, $params['theme_id']);
        $stmt->bindParam(4, $params['question']);
        $stmt->execute();
    }

    //Удаление вопросов одной темы
    public function delQuestionsInTheme($param)//1
    {
        $stmt = db()->prepare("DELETE FROM `questions` WHERE `theme_id`='$param'");
        $stmt->execute();
    }

    //Переключатель опубликован/скрыт
    public function publishedOnOff($params)//2
    {
        $stmt = db()->prepare("UPDATE `questions` SET `published`='{$params['publishedOnOff']}' WHERE `id`='{$params['showQuestionId']}' LIMIT 1");
        $stmt->execute();
    }

    //Удаление вопроса из темы
    public function delQuestion($param)//3
    {
        $stmt = db()->prepare("DELETE FROM `questions` WHERE `id`='{$param}'");
        $stmt->execute();
    }

    //Изменение автора
    public function changeAuthorName($params)//4
    {
        $stmt = db()->prepare("UPDATE `questions` SET `author_name`='{$params['changeAuthorName']}' WHERE `id`='{$params['showQuestionId']}' LIMIT 1");
        $stmt->execute();
    }

    //Изменение вопроса
    public function changeQuestion($params)//5
    {
        $stmt = db()->prepare("UPDATE `questions` SET `question`='{$params['changeQuestion']}' WHERE `id`='{$params['showQuestionId']}' LIMIT 1");
        $stmt->execute();
    }

    //Изменение ответа
    public function changeAnswer($params)//6
    {
        $stmt = db()->prepare("UPDATE `questions` SET `answer`='{$params['changeAnswer']}' WHERE `id`='{$params['showQuestionId']}' LIMIT 1");
        $stmt->execute();
    }

    //Изменение темы
    public function changeTheme($params)//7
    {
        $stmt = db()->prepare("UPDATE `questions` SET `theme_id`='{$params['changeThemeId']}' WHERE `id`='{$params['showQuestionId']}' LIMIT 1");
        $stmt->execute();
    }

    //Получение всех вопросов без ответа во всех темах в порядке их добавления
    public function unansQuestions()//8
    {
        $sql = "SELECT `questions`.`id`, `theme_id`, `theme`, `question`, `answer`, `published`, `author_name`, `e-mail`, `date_added` FROM `questions` JOIN `themes` ON `themes`.`id`=`questions`.`theme_id` WHERE `answer` IS NULL OR `answer`='' ORDER BY `date_added` ASC";
        $allUnansQuestions = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $allUnansQuestions;
    }
}