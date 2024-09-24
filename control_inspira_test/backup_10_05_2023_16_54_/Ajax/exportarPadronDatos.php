<?php
require "../../vendor/autoload.php";
require_once "../../_conexion250.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\IOFactory;


$cabeceras = ['id', 'nombre','tel', 'cedula','direccion','sucursal','ruta','radio','activo','fecha_nacimiento','edad','trajeta','tipo_tarjeta','numero_tarjeta','nombre_titular','cedula_titular','telefono_titular','anio_e','mes_e','sucursal_cobranzas','sucursal_cobranzas_num','empresa_marca','flag','count','observaciones','grupo','id_relacion','empresa_rut','total_importe','nactual','version','flagchange','rutcentralizado','PRINT','EMITIDO','movimientoabm','abm','abmactual','check','usuario','usuarioid','fechafil','radioViejo','extra','nomodifica'];

$fecha = date('Y-m-d');
$sql = "SELECT * FROM padron_datos_socio WHERE abmactual='1'";
$filename = "Padron datos ".$fecha;
$spread = new Spreadsheet();
$sheet = $spread->getActiveSheet();
$sheet->setTitle("Padron datos");
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
header('Cache-Control: max-age=1');
$writer = IOFactory::createWriter($spread, 'Xlsx');
$writer->save('php://output');
mysqli_close($mysqli250);

exit;

