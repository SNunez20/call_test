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
        $where = "(`e`.`fecha` >= '$desde') and (`e`.`fecha`< '$hasta' or `e`.`fecha` like '$hasta"."%')";
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
$table = 'evaluacionagendados';

 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => '`e`.`id`', 'dt' => 0, 'field' => 'id' ),
	array( 'db' => '`e`.`idagendado`', 'dt' => 1, 'field' => 'idagendado' ),
	array( 'db' => '`e`.`numero`',  'dt' => 2, 'field' => 'numero' ),
    array( 'db' => '`u`.`usuario`',  'dt' => 3, 'field' => 'usuario' ),
    array( 'db' => '`g`.`nombre`',  'dt' => 4, 'field' => 'nombre' ),
	array( 'db' => '`e`.`evaluacion`',   'dt' => 5, 'field' => 'evaluacion' ),
    array( 'db' => '`e`.`comentario`',   'dt' => 6, 'field' => 'comentario' ),
    array( 'db' => '`e`.`fechaagendada`',     'dt' => 7, 'field' => 'fechaagendada' ),
    array( 'db' => '`e`.`fechadeagendado`',     'dt' => 8, 'field' => 'fechadeagendado' ),
    array( 'db' => '`e`.`fecha`',     'dt' => 9, 'field' => 'fecha' ),
    array( 'db' => '`a`.`nombre_auditora`',     'dt' => 10, 'field' => 'nombre_auditora' ),
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
$joinQuery = "FROM `evaluacionagendados` AS `e` INNER JOIN `usuarios` AS `u` ON (`e`.`idusuario` = `u`.`id`) INNER JOIN `auditoriausuarios` AS `a` ON (`e`.`idauditora` = `a`.`id`) INNER JOIN `gruposusuarios` AS `g` ON (`e`.`idcall` = `g`.`id`)";

echo json_encode(
        SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where )
);
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
?>