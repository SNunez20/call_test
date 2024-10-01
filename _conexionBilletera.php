<?php
$mysqliBilletera = mysqli_connect('localhost', 'root', '', 'billeteravida');
if (mysqli_connect_errno()) {
    logger("[ERROR]: Conexión localhost" . mysqli_connect_error());
    die();
}
