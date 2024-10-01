<?php
session_start();
if(isset($_SESSION['idauditoria'])){
$response 	= array( 'result' => false, 'message' => 'Intentelo nuevamente mas tarde !!');
include('../../_conexion.php');
require_once '../Classes/PHPExcel.php';


if($_POST['desde3'] != ""){
    $desde = $_POST['desde3']; 
}else{
    $desde = 'sin fecha';
}
if($_POST['hasta3']!=""){
    $hasta = $_POST['hasta3']; 
}else{
    $hasta = 'sin fecha';
}
    
if($desde != 'sin fecha' and $hasta!='sin fecha'){
    $where = " where(`e`.`fecha` >= '$desde') and (`e`.`fecha`< '$hasta' or `e`.`fecha` like '$hasta"."%')";
}else{
    $where = "";
}
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("Vida")
							 ->setLastModifiedBy("Vida")
							 ->setTitle("Exportar Excel")
							 ->setSubject("Exportar Excel")
							 ->setDescription("Exportar Excel")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Exportaciones A Excel");
    
        
    $nombreexcel = "Evaluaciones";
    $q = "select e.id,e.idagendado,e.numero,u.usuario,g.nombre,e.evaluacion,e.comentario,e.fechaagendada,e.fechadeagendado,e.fecha,a.nombre_auditora from `evaluacionagendados` AS `e` INNER JOIN `usuarios` AS `u` ON (`e`.`idusuario` = `u`.`id`) INNER JOIN `auditoriausuarios` AS `a` ON (`e`.`idauditora` = `a`.`id`) INNER JOIN `gruposusuarios` AS `g` ON (`e`.`idcall` = `g`.`id`)".$where;
    $result = mysqli_query($mysqli,$q);   
        $numrow = 1;
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $numrow, 'id');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $numrow, 'id_agendado');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $numrow, 'numero');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $numrow, 'ci_vendedor');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $numrow, 'call');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $numrow, 'evaluacion');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $numrow, 'comentario');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $numrow, 'fecha_agendada');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $numrow, 'fecha_de_agendado');
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $numrow, 'fecha_evaluacion');
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $numrow, 'auditora');
    
    	$numrow++;
    while($row = mysqli_fetch_array($result)){   
   	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $numrow, $row['id']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $numrow, $row['idagendado']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $numrow, $row['numero']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $numrow, $row['usuario']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $numrow, $row['nombre']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $numrow, $row['evaluacion']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $numrow, $row['comentario']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $numrow, $row['fechaagendada']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $numrow, $row['fechadeagendado']);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $numrow, $row['fecha']);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $numrow, $row['nombre_auditora']); 
        $numrow++;
    }
    $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFont()->setSize(13);
    $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
    
    // Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle($nombreexcel);
	
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$nombreexcel.'.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    mysqli_close($mysqli);
        $objWriter->save('php://output');
        exit; 
}


?>