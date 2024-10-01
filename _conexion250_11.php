<?php
$mysqli250_11 = mysqli_connect('localhost', 'root', '', 'coordinacion');
if (mysqli_connect_errno()) {
  logger("[ERROR]: Conexión localhost" . mysqli_connect_error());
  die();
}
