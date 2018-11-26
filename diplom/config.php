<?php
function db()
{
    static $db = null;
    if ($db === null) {
        $config = [
            'host' => 'localhost',
            'dbname' => 'ayakovlev',
            'user' => 'ayakovlev',
            'pass' => 'neto1880',
        ];
        try {
            $db = new PDO(
                'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8',
                $config['user'],
                $config['pass']
            );
        } catch (PDOException $e) {
            die('Database error: ' . $e->getMessage() . '<br/>');
        }
    }
    return $db;
}