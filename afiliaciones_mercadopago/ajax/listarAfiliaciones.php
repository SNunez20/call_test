<?php
    include '../../_conexion.php';

    $desde = $_POST['desde'];
    $hasta = $_POST['hasta'];
    
    $q = "SELECT
            pd.cedula,
            pd.nombre,
            pd.fechafil,
            p.fecha AS fechapago
        FROM
            padron_datos_socio AS pd
            INNER JOIN pagos AS p ON pd.id = p.id_afiliado 
        WHERE
            p.estado = 'approved' AND
            (pd.fechafil >= '$desde' AND pd.fechafil <= '$hasta') AND
            (CAST(p.fecha AS date) >= '$desde' AND CAST(p.fecha AS date) <= '$hasta')
        GROUP BY
            pd.cedula";

    $result = mysqli_query($mysqli,$q);
    

    if(mysqli_num_rows($result) != 0){

        while($row = mysqli_fetch_array($result)){
            $response['data'][] = array(
                $row['cedula'],
                $row['nombre'],
                $row['fechafil'],
                date("Y-m-d", strtotime($row['fechapago']))
            );
        }
    }else{
        $response['data'] = [] ;
    }

    mysqli_close($mysqli);
    echo json_encode($response);
?>