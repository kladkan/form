<?php
if (!empty($_POST)) {
  foreach ($_POST as $key => $value) {
      $result[$key] = explode('-', $key);
    }

  $testnum = str_replace('_', '.', $result[$key][0]);
  $forcheck = file_get_contents(__DIR__ . '/tests/' . $testnum);
  $check = json_decode($forcheck, true);

  foreach ($check as $key => $value) {
    if (empty($_POST[substr(key($_POST), 0, -1).$key])) {
      echo $check[$key]['q'].' Вы не ответили на этот вопрос.<br>';
    } elseif ($_POST[substr(key($_POST), 0, -1).$key] == $check[$key]['trueans']) {
      echo $check[$key]['q'].' Ваш ответ: '. $_POST[substr(key($_POST), 0, -1).$key].' верный.<br>';
    } else {
      echo $check[$key]['q'].' Ваш ответ: '. $_POST[substr(key($_POST), 0, -1).$key].' не верный.<br>';
    }
  }
  exit;
}

$list = scandir('./tests');

if (empty($_GET['testnumber'])) {
  echo 'Вы не ответили ни на один вопрос.Вернитесь на предыдущую страницу';
  exit;
}

for ($i=2; $i < count($list); $i++) {
    if ($_GET['testnumber'] == $list[$i]) {
    $json = file_get_contents(__DIR__ . '/tests/' . $list[$i]);
      $test = json_decode($json, true);
  }
}
if (empty($test)) {
  http_response_code(404);
  echo 'Теста не существует';
  exit;
}

?>

<html>
<head>
  <title>Тестирование</title>
</head>
<body>
  <form action="test.php" method="POST">
  <?php foreach ($test as $key => $val) : ?>
    <fieldset>
      <legend><?php echo $val['q'] ?></legend>
      <?php foreach ($val['answer'] as $row => $ans) : ?>
        <label><input type="radio" name="<?php echo $_GET['testnumber'].'-' ?>q<?php echo $key ?>" value="<?php echo $ans ?>"><?php echo $ans ?></label>
      <?php endforeach; ?>
    </fieldset>
  <?php endforeach; ?>
  <input type="submit" value="Отправить">
</form>
</body>
</html>
