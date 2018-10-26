<?php
if (!empty($_POST)) {
    if (array_key_exists('age', $_POST) && $_POST['age'] !== '') {
        $options = [
            'options' => [
                'min_range' => 10,
                'max_range' => 150
            ]
        ];
        $validate = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT, $options);

        if ($validate === false) {
            echo 'Данные не верны';
        } else {
            echo 'Возраст: '.$validate;
        }
    }
    if (array_key_exists('name', $_POST)) {
        echo htmlspecialchars($_POST['name']);
    }
}

if (!empty($_FILES) && array_key_exists('avatar', $_FILES)) {
    $hash = md5($_FILES['avatar']['name'].time());
    move_uploaded_file($_FILES['avatar']['tmp_name'], $hash.'.jpg');
    echo '<img src="'.$hash.'.jpg">';
}

//print_r($options);
//$age = (int)$_POST['age'];
//echo $age;
?>