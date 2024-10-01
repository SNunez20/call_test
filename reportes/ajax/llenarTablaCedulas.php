<?php
require '../../_conexion.php';
require '../../_conexion250.php';

$qCedulasAltas = "SELECT
pp.cedula,
'ALTA' AS tipo
FROM
padron_producto_socio AS pp
INNER JOIN padron_datos_socio AS pd
ON pd.cedula = pp.cedula
WHERE
pp.abmactual = 1 
AND (pp.abm = 'ALTA')
AND pd.sucursal NOT IN (1370,1372,1371,1375)
GROUP BY
pp.cedula;";

  mysqli_query($mysqli, "TRUNCATE TABLE cedulas_padron_prueba");

   $rCedulasPadron = mysqli_query($mysqli250, $qCedulasAltas);

    if (mysqli_num_rows($rCedulasPadron) > 0) {
      
        while ($row = mysqli_fetch_assoc($rCedulasPadron)) {
            mysqli_query($mysqli, "INSERT INTO cedulas_padron_prueba VALUES (null,'".$row['cedula']."','".$row['tipo']."')");
        }
    }

$qCedulasIncrementos = "SELECT
pp.cedula,
'INCREMENTO' AS tipo
FROM
padron_producto_socio AS pp
INNER JOIN padron_datos_socio AS pd
ON pd.cedula = pp.cedula
WHERE
pp.abmactual = 1 
AND (pp.abm = 'ALTA-PRODUCTO')
AND pd.sucursal NOT IN (1370,1372,1371,1375)
GROUP BY
pp.cedula";

   $rCedulasPadron = mysqli_query($mysqli250, $qCedulasIncrementos);

    if (mysqli_num_rows($rCedulasPadron) > 0) {
  
        while ($row = mysqli_fetch_assoc($rCedulasPadron)) {
            mysqli_query($mysqli, "INSERT INTO cedulas_padron_prueba VALUES (null,'".$row['cedula']."','".$row['tipo']."')");
        }
    }

    var_dump('listo');