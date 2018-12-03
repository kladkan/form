<?php
class AdminsTable
{
    //авторизация
    function getAdminForAuth($params)
    {
        $sql = "SELECT `id` FROM `admins` WHERE `login`='{$params['authname']}' AND `password`='{$params['authpass']}'";
        foreach (db()->query($sql) as $adminAuth) {
        }
    
        if (isset($adminAuth)) {
            return $adminAuth;
        }
    }

    //Получение списка администраторов
    function adminsList()
    {
        $sql = "SELECT `login`, `password` FROM `admins`";
        $admins = db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $admins;
    }

    //Поиск администратора перед регистрацией нового
    function getAdminControl($param)
    {
        $sql = "SELECT `id` FROM `admins` WHERE `login`='$param'";
        foreach (db()->query($sql) as $adminOk) {
        }
        return $adminOk;
    }

    //Добавляем админа
    function addAdmin($params)
    {
        $stmt = db()->prepare("INSERT INTO `admins`(`login`, `password`) VALUES (?, ?)");
        $stmt->bindParam(1, $params['newLogin']);
        $stmt->bindParam(2, $params['newPassword']);
        $stmt->execute();
    }

    //Изменение пароля администратора
    function changePassword($params)
    {
        $stmt = db()->prepare("UPDATE `admins` SET `password`='{$params['changePassword']}' WHERE `login`='{$params['login']}' LIMIT 1");
        $stmt->execute();
    }

    //Удаление администратора
    function delAdmin($param)
    {
        $stmt = db()->prepare("DELETE FROM `admins` WHERE `login`='$param' LIMIT 1");
        $stmt->execute();
    }
}