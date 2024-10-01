<?php
include_once '_init.php';
include_once 'conexiones.php';



if (!isset($_SESSION['logueado_ventas'])) {
    header("Location: login.php");
    die();
}

?>

<!DOCTYPE html>
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <link href='img/logovida.png' rel='shortcut icon' type='image/x-icon' />
    <style type="text/css">
        [ng\:cloak],
        [ng-cloak],
        [data-ng-cloak],
        [x-ng-cloak],
        .ng-cloak,
        .x-ng-cloak,
        .ng-hide:not(.ng-hide-animate) {
            display: none !important;
        }

        ng\:form {
            display: block;
        }

        .ng-animate-shim {
            visibility: hidden;
        }

        .ng-anchor {
            position: absolute;
        }
    </style>

    <meta http-equiv=”Content-Type” content=”text/html; charset=utf-8″ />
    <title>Vida, Ventas Uruguay</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta content="Vida, el mejor servicio humano y profesional de compañía de enfermos" name="description">

    <!--SCRIPTS APP-->
    <?php include_once PATH_PARTIALS_VENTAS . '/scripts.php'; ?>
    <!--SCRIPTS APP-->

</head>

<body ng-app="litApp" class="ng-scope">
    <div class="container-fluid">
        
        <br />
        <div id="totalizador_servicios"></div>
        <br />
        <div id="totalizador_padron_piscina"></div>
        
        <div id="totalizador_ventas"></div>
        <hr>
        <h5><div id="filtrado_por_fecha"></div></h5> 
        <hr>
        <br />

        <!-- TABLA VENTAS -->
        <?php include PATH_PARTIALS_VENTAS . '/tabla_ventas.php'; ?>
        <!-- TABLA VENTAS -->

        <!-- MODAL SERVICIOS S -->
        <?php include PATH_PARTIALS_VENTAS . '/modal_servicios.php'; ?>
        <!-- MODAL SERVICIOS S -->

        <!-- TABLA VENDEDEDORES -->
        <?php include PATH_PARTIALS_VENTAS . '/tabla_vendedores.php'; ?>
        <!-- TABLA VENDEDEDORES -->

        <!-- MODAL VENTAS VENDEDOR -->
        <?php include PATH_PARTIALS_VENTAS . '/modal_ventas_vendedor.php'; ?>
        <!-- MODAL VENTAS VENDEDOR -->

        <!-- TABLA SUPERVISORES -->
        <?php include PATH_PARTIALS_VENTAS . '/tabla_supervisores.php'; ?>
        <!-- TABLA SUPERVISORES -->


    </div>
</body>

</html>