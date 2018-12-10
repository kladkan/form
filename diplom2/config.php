<?php
function db()
{
    static $db = null;
    if ($db === null) {
        $config = [
            'host' => 'localhost',
            'dbname' => 'netology01',
            'user' => 'root',
            'pass' => 'fg2018start',
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


//////////////////из примера по лекции
return [
	'mysql' => [
		'host' => 'localhost',
		'dbname' => 'netology',
		'user' => 'netology',
		'pass' => 'netology',
	]
];
/////////////////////