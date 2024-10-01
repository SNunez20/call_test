<?php
$mysqli = mysqli_connect('localhost', 'root', '', 'eldesafi_o');

if (mysqli_connect_errno())
  {
    echo "Error al conectar a MySql: " . mysqli_connect_error();
  }
?>