<?php
session_start();
if(isset($_SESSION['idadmin'])){
$grupo_usuarios = $_SESSION['grupo_usuarios'];
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
        if($grupo_usuarios == 0){
            $where = "(`h`.`fecha` >= '$desde') and (`h`.`fecha`< '$hasta' or `h`.`fecha` like '$hasta"."%')";
        }else{
            $where = "(`h`.`fecha` >= '$desde') and (`h`.`fecha`< '$hasta' or `h`.`fecha` like '$hasta"."%') and `u`.`idgrupo` = $grupo_usuarios";
        }
    }else{
        if($grupo_usuarios == 0){
            $where = "";
        }else{
            $where = "`u`.`idgrupo` = $grupo_usuarios";
        }
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
$table = 'historico';

 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => '`h`.`id`', 'dt' => 0, 'field' => 'id' ),
	array( 'db' => '`h`.`numero`', 'dt' => 1, 'field' => 'numero' ),
	array( 'db' => '`h`.`estado`',  'dt' => 2, 'field' => 'estado' ),
	array( 'db' => '`u`.`usuario`',   'dt' => 3, 'field' => 'usuario' ),
    array( 'db' => '`g`.`nombre`',     'dt' => 4, 'field' => 'nombre' ),
	array( 'db' => '`h`.`fecha`',     'dt' => 5, 'field' => 'fecha'),
    array( 'db' => '`h`.`preguntas`',     'dt' => 6, 'field' => 'preguntas'),
	array( 'db' => '`d`.`integrantes_familia`',     'dt' => 7, 'field' => 'integrantes_familia' ),
    array( 'db' => '`d`.`direccion`',     'dt' => 8, 'field' => 'direccion' ),
	array( 'db' => '`d`.`otro_servicio`',     'dt' => 9, 'field' => 'otro_servicio' ),
    array( 'db' => '`d`.`observaciones`',     'dt' => 10, 'field' => 'observaciones' ),
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
$joinQuery = "FROM `historico` AS `h` LEFT JOIN `detalles` AS `d` ON (`d`.`idhistorico` = `h`.`id`) INNER JOIN `usuarios` AS `u` ON (`u`.`id` = `h`.`idusuario`) INNER JOIN `gruposusuarios` AS `g` ON (`u`.`idgrupo` = `g`.`id`)";
//$extraWhere = "`h`.`estado` = 'vendido'";
echo json_encode(
        SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $where )
);
}else{
   $response 	= array( 'result' => false, 'message' => 'Sin Sesion'); 
}
?>