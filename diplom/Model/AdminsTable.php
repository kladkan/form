<?php
class AdminsTable
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    //авторизация
    public function getAdminForAuth($params)
    {
        $sql = "SELECT `id` FROM `admins` WHERE `login`='{$params['authname']}' AND `password`='{$params['authpass']}'";
        foreach ($this->db->query($sql) as $adminAuth) {
        }
    
        if (isset($adminAuth)) {
            return $adminAuth;
        }
    }

    //Получение списка администраторов
    public function adminsList()
    {
        $sql = "SELECT `login`, `password` FROM `admins`";
        $admins = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $admins;
    }

    //Поиск администратора перед регистрацией нового
    public function getAdminControl($param)
    {
        $sql = "SELECT `id` FROM `admins` WHERE `login`='$param'";
        foreach ($this->db->query($sql) as $adminOk) {
        }
        return $adminOk;
    }

    //Добавляем админа
    public function addAdmin($params)
    {
        $stmt = $this->db->prepare("INSERT INTO `admins`(`login`, `password`) VALUES (?, ?)");
        $stmt->bindParam(1, $params['newLogin']);
        $stmt->bindParam(2, $params['newPassword']);
        $stmt->execute();
    }

    //Изменение пароля администратора
    public function changePassword($params)
    {
        $stmt = $this->db->prepare("UPDATE `admins` SET `password`='{$params['changePassword']}' WHERE `login`='{$params['login']}' LIMIT 1");
        $stmt->execute();
    }

    //Удаление администратора
    public function delAdmin($param)
    {
        $stmt = $this->db->prepare("DELETE FROM `admins` WHERE `login`='$param' LIMIT 1");
        $stmt->execute();
    }
}