<?php
class TablesForStart
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function createAdminsTable()//Создаем таблицу с админами
    {
        $stmt = $this->db->prepare("CREATE TABLE `admins` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `login` varchar(50) NOT NULL,
            `password` varchar(150) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $stmt->execute();
    }

    public function createDefaultAdmin()//Создаем администратора по умолчанию
    {
        $stmt = $this->db->prepare("INSERT INTO `admins`(`login`, `password`) VALUES (?, ?)");
        $x = 'admin';
        $stmt->bindParam(1, $x);
        $stmt->bindParam(2, $x);
        $stmt->execute();
    }

    public function createQuestionsAnswersTable()//Создаем таблицу вопросов/ответов
    {
        $stmt = $this->db->prepare("CREATE TABLE `questions` (
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
    }

    public function createThemesTable() //Создаем таблицу тем
    {
        $stmt = $this->db->prepare("CREATE TABLE `themes` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `theme` varchar(100) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $stmt->execute();
    }
}