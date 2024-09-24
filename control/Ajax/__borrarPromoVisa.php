<?php

/**
 * Recorre la piscina y el padrón y borra la promo VISA a la tarjeta seleccionada
 *
 * @param string $tarjeta Buscar tarjeta para cancelar promo Visa
 * @return void
 */
return function ($tarjeta) {
  require '../../_conexion.php';
  require '../../_conexion250.php';

  if (strlen($tarjeta) < 14)
    return;

  $qSelect = <<<SQL
  SELECT
    `cedula`
  FROM
    `padron_datos_socio`
  WHERE
    `numero_tarjeta` = '{$tarjeta}';
SQL;
  $select = $mysqli->query($qSelect);

  if ($select->num_rows === 0)
    return;

  $fetch = $select->fetch_all(MYSQLI_ASSOC);
  $cedulas = mb_substr(trim(
    array_reduce(
      $fetch,
      function ($carry, $socio) {
        $cedula = $socio['cedula'];
        return $carry .= " '{$cedula}',";
      },
      ''
    )
  ), 0, -1);

  $qUpdate = <<<SQL
  UPDATE
    `padron_producto_socio`
  SET
    `cod_promo` = "20"
  WHERE
    `cedula` IN ({$cedulas})
    AND `servicio` = '01';
SQL;
  $mysqli->query($qUpdate);
  $mysqli250->query($qUpdate);

  $qUpdate = <<<SQL
  UPDATE
    `padron_producto_socio`
  SET
    `cod_promo` = '0'
  WHERE
    `cedula` IN ({$cedulas})
    AND `servicio` != '01';
SQL;
  $mysqli->query($qUpdate);
  $mysqli250->query($qUpdate);
};
