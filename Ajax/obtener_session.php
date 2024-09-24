<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    die(json_encode(['error' => true]));

session_start();

if (!isset($_SESSION['grupoUsuario'], $_SESSION['idusuario']))
    die(json_encode(['error' => true]));

die(json_encode($_SESSION));
