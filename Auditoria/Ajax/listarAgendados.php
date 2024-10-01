<?php
session_start();
if(isset($_SESSION['idauditoria'])){
    if($_GET['desde'] != ""){
       $desde = $_GET['desde']; 
    }else{
        $desde = 'sin fecha';
    }
    if($_GET['hasta']!=""){
       $hasta = $_GET['hasta']; 
    }else{
        $hasta = 'sin fecha';
    }
    
    if($desde != 'sin fecha' and $hasta!='sin fecha'){
        $where = "(`a`.`fecha` >= '$desde') and (`a`.`fecha`< '$hasta' or `a`.`fecha` like '$hasta"."%')";
    }else{
        $where = "";
    }
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
$table = 'agendados';

 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => '`a`.`id`', 'dt' => 0, 'field' => 'id' ),
	array( 'db' => '`a`.`numero`', 'dt' => 1, 'field' => 'numero' ),
	array( 'db' => '`u`.`usuario`',  'dt' => 2, 'field' => 'usuario' ),
    array( 'db' => '`g`.`nombre`',  'dt' => 3, 'field' => 'nombre' ),
	array( 'db' => '`a`.`fecha_agendado`',   'dt' => 4, 'field' => 'fecha_agendado' ),
    array( 'db' => '`a`.`no_contesta`',   'dt' => 5, 'field' => 'no_contesta' ),
    array( 'db' => '`a`.`fecha`',     'dt' => 6, 'field' => 'fecha' ),
    array( 'db' => '`a`.`evaluado`',     'dt' => 7, 'field' => 'evaluado' ),
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
//$where = "";
$joinQuery = "FROM `agendados` AS `a` INNER JOIN `usuarios` AS `u` ON (`a`.`usuarioid` = `u`.`id`) INNER JOIN `gruposusuarios` AS `g` ON (`u`.`idgrupo` = `g`.`id`)";

echo json_encode(
        SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where )
);
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
?>