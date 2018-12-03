<?php
class ThemesTable
{
    //Получение списка тем (для всех)
    function getThemes()
    {
        $sql = "SELECT * FROM `themes`";
        $themes = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $themes;
    }

    //Добавление новой темы
    function addTheme($param)
    {
        $stmt = db()->prepare("INSERT INTO `themes` (`theme`) VALUES (?)");
        $stmt->bindParam(1, $param);
        $stmt->execute();
    }

    //Удаление темы со всеми вопросами
    function delTheme($param)//22222222222222
    {
        $stmt = db()->prepare("DELETE FROM `themes` WHERE `id`='$param'");
        $stmt->execute();
    }
}