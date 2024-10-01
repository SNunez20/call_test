<?php
    function validateDate($date, $format = 'Y-m-d'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    if(!isset($_GET['desde']) || !isset($_GET['hasta'])){
        die("DEBE PONER COMO PARAMETROS GET 'DESDE' Y 'HASTA'");
    }else if($_GET['desde'] == ''){
        die("DEBE RELLENAR 'DESDE'");
    }else if($_GET['hasta'] == ''){
        die("DEBE RELLENAR 'HASTA'");
    }else if(!validateDate($_GET['desde'])){
         die("'DESDE' NO ES UNA FECHA CORRECTA");
    }else if(!validateDate($_GET['hasta'])){
         die("'HASTA' NO ES UNA FECHA CORRECTA");
    }else if( date("Y-m-d", strtotime($_GET['desde'])) > date("Y-m-d", strtotime($_GET['hasta']))){
        die("'DESDE' NO PUEDE SER MAYOR A 'HASTA'");
    }

    $desde = $_GET['desde'];
    $hasta = $_GET['hasta'];
    echo "<input type = 'hidden' id = 'desde' value = '".$desde."' />";
    echo "<input type = 'hidden' id = 'hasta' value = '".$hasta."' />";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afiliaciones pagas por mercadopago</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <style>
        body{
            width: 100%;
            margin: auto;
            padding: 30px;
        }
    </style>
</head>
<body>
    <h1>Afiliaciones aprobadas Mercadopago</h1>

    <br>
    <br>
    <table id="TableAfiliados" class="display table responsive" style="width: 100%">
        <thead>
            <tr>
                <th>Cedula</th>
                <th>Nombre</th>
                <th>Fecha Afil.</th>
                <th>Fecha Pago</th> 
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="js/js.js"></script>
</body>
</html>