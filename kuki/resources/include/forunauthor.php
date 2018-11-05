<?php
if (empty($_SESSION)) {
  //echo 'никто не авторизован';
  header('Location: ./index.php');
}
?>