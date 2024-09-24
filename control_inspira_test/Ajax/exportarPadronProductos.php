<?php
require "../../vendor/autoload.php";
require_once "../../_conexion250.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\IOFactory;


$cabeceras = ['id', 'cedula','servicio', 'hora','importe','cod_promo','fecha_registro','numero_contrato','fecha_afiliacion','nombre_vendedor','observaciones','lugar_venta','vendedor_independiente','activo','movimiento','fecha_inicio_derechos','numero_vendedor','keepprice1','promoactivo','tipo_de_cobro','tipo_iva','idrelacion','codigo_precio','aumento','empresa','nactual','servdecod','count','version','abm','abmactual','usuario','usuarioid','precioOriginal','abitab','cedula_titular_gf'];

$fecha = date('Y-m-d');
$sql = "SELECT * FROM padron_producto_socio WHERE abmactual='1'";
$filename = "Padron producto ".$fecha."xlsx";
$spread = new Spreadsheet();
$sheet = $spread->getActiveSheet();
$sheet->setTitle("Padron productos");
$sheet->fromArray($cabeceras,null,'A1');

if ($result = mysqli_query($mysqli250,$sql)) {
    $fila = 2;
    while($row = mysqli_fetch_row($result)){
  
        for ($j=0; $j < count($row) ; $j++) { 

            $sheet->getStyleByColumnAndRow($j+1, $fila)
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->setCellValueByColumnAndRow($j+1, $fila, $row[$j]);
            if ($j+1===14) {
                $value = $sheet->getCellByColumnAndRow($j+1, $fila);
                $sheet->getCellByColumnAndRow($j+1, $fila)->setValueExplicit(
                    $value,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
            }
           
        }

        $fila++;
    }
   
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
mysqli_close($mysqli250);
$writer = IOFactory::createWriter($spread, 'Xlsx');
$writer->save('php://output');
exit;

