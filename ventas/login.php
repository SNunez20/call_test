<?php
include_once '_init.php';
include_once 'conexiones.php';

if (isset($_SESSION['logueado_ventas']) && $_SESSION['logueado_ventas'] !== false) {
    header("Location: index.php");
    die();
}

?>
<html lang="en" class="no-js">
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

    <meta charset="utf-8">
    <title>Login Ventas</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta content="Vida, el mejor servicio humano y profesional de compañía de enfermos" name="description">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/jquery.validate.js" type="text/javascript"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/localization/messages_es.js"></script>



    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/site.css" rel="stylesheet" type="text/css">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all" rel="stylesheet" type="text/css">
    <!--link href="//apps.neoogilvy.uy/vidauy/public/js/angular-csp.css" rel="stylesheet" type="text/css"-->
    <!--link href="//apps.neoogilvy.uy/vidauy/public/js/font-awesome-4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"-->

    <!-- SELECT 2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SELECT 2 -->

    <!-- SWEET ALERT 2-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- SWEET ALERT 2-->


</head>

<body>
    <div class="container">
        <form method="POST" action="login.php">
            <?php
            if (isset($_GET['error_login'])) {
                echo "<br/>";
                echo '<div class="alert alert-danger">Error el usuario o contraseña </div>';
                echo "<br/>";
            }
            ?>
            <div class="form-group">
                <label>CIN</label>
                <input type="text" name="usuario" id="usuario" placeholder="Usuario" class="form-control" required minlength="4" maxlength="20">

            </div>
            <div class="form-group">
                <label>Contrase&ntilde;a</label>
                <input type="password" name="password" id="password" class="form-control" required minlength="4" maxlength="20">

            </div>

            <br>
            <input type="submit" name="enviar" class="btn btn-primary" value="Enviar">
    </div>

    </form>

</body>

</html>

<?php
if (isset($_POST['enviar'])) {
    $usuario = mysqli_real_escape_string($mysqli, $_POST['usuario']);
    $password = mysqli_real_escape_string($mysqli, $_POST['password']);

    $q = "SELECT id FROM admin_ventas WHERE usuario = '$usuario' AND password = '$password' AND activo = 1";

    $query = mysqli_query($mysqli, $q);

    if (mysqli_num_rows($query) > 0) {
        $_SESSION['logueado_ventas'] = true;
        header("Location: index.php");
        die();
    } else  header("Location: login.php?error_login=true");
}
?>