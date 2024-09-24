<?php

require_once './../../_conexion250_11.php';

$qSelect = <<<SQL
SELECT
  `id_patologia`, `patologia`
FROM
  `patologias` ORDER BY `patologia`;
SQL;

$select = $mysqli250_11->query($qSelect);
$fetch = $select->fetch_all(MYSQLI_ASSOC);

die(json_encode($fetch));
