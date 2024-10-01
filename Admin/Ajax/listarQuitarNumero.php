<?php
session_start();
if(isset($_SESSION['idadmin'])){
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */
 
// DB table to use
$table = 'listanegra';

 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => '`l`.`id`', 'dt' => 0, 'field' => 'id' ),
	array( 'db' => '`l`.`numero`', 'dt' => 1, 'field' => 'numero' ),
	array( 'db' => '`l`.`observacion`',  'dt' => 2, 'field' => 'observacion' ),
	array( 'db' => '`l`.`fecha`',   'dt' => 3, 'field' => 'fecha' ),
    array( 'db' => '`g`.`nombre`',     'dt' => 4, 'field' => 'nombre' ),
    array( 'db' => '`u`.`usuario`',     'dt' => 5, 'field' => 'usuario' ),
);

 
 require('../tabla/examples/server_side/scripts/config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
require( '../tabla/examples/server_side/scripts/ssp.customized.class.php' );
$where = "";
$joinQuery = "FROM `listanegra` AS `l` LEFT JOIN `usuarios` AS `u` ON (`l`.`usuarioid` = `u`.`id`) LEFT JOIN `gruposusuarios` AS `g` ON (`u`.`idgrupo` = `g`.`id`)";
//$extraWhere = "`h`.`estado` = 'vendido'";
echo json_encode(
        SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where )
);
    
    
    
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
?>