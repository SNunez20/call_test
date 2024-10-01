<?php

$mysqli = mysqli_connect(DB_CALL['host'], DB_CALL['user'], DB_CALL['password'], DB_CALL['db']);
$mysqli_motor = mysqli_connect(DB_MOTOR['host'], DB_MOTOR['user'], DB_MOTOR['password'], DB_MOTOR['db']);


if (mysqli_connect_errno()) {
  echo "Error al conectar a MySql: " . mysqli_connect_error();
}
/*CONEXIONES DB */
