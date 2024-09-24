<?php
$mysqli250 = mysqli_connect('localhost', 'root', '', 'abmmod');

if (mysqli_connect_errno()) {
    logger("[ERROR]: Conexión 192.168.1.250" . mysqli_connect_error());
    die();
}
