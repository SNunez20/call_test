<?php

require_once __DIR__ . '/../../_conexion.php';

$desde = isset($_GET['desde']) && !empty($_GET['desde'])
	? $mysqli->real_escape_string($_GET['desde'])
	: '2022-12-01';
$hasta = isset($_GET['hasta']) && !empty($_GET['hasta'])
	? $mysqli->real_escape_string($_GET['hasta'])
	: (new DateTime())->format('Y-m-d');

$qSelect = <<<SQL
SELECT
	`ac_id`,
	`ac_id_socio`,
	UPPER(`ac_nombre`) AS `ac_nombre`,
	`ac_cedula`,
	`ac_telefono`,
	`ac_celular`,
	`ac_fecha_nacimiento`,
	`ac_direccion`,
	`ac_observacion`,
	`ac_metodo_pago`,
	`ac_fecha_afiliacion`,
	`u_id`,
	UPPER(`u_nombre`) AS `u_nombre`,
	`u_usuario`,
IF
	( `u_activo` = 1, 'Si', 'No' ) AS `u_activo`
FROM
	`v_ventas_comepa`
WHERE
	DATE (`ac_fecha_afiliacion`) BETWEEN '{$desde}' AND '{$hasta}';
SQL;
$query = $mysqli->query($qSelect);

if (!$query)
	die(json_encode([
		'error' => true,
		'mysqli' => true,
		'mysqli_query' => $mysqli->error
	]));

$fetch = $query->fetch_all(MYSQLI_ASSOC);

$data = array_map(function ($row) {
	$row['ac_fecha_afiliacion'] = (new DateTime($row['ac_fecha_afiliacion']))->format('d/m/Y');
	$row['u_usuario'] = convertirCedula($row['u_usuario']);
	return $row;
}, $fetch);

function convertirCedula($cedula)
{
	switch ($cedula) {
		case 'CO785836':
			$cedula = '42785836';
			break;
		case 'CO329598':
			$cedula = '51329598';
			break;
		case 'CO259058':
			$cedula = '41259058';
			break;
		case 'CO468719':
			$cedula = '18468719';
			break;
		case 'CO767845':
			$cedula = '31767845';
			break;
		case 'CO023215':
			$cedula = '45023215';
			break;
		case 'CO169453':
			$cedula = '47169453';
			break;
		case 'CO531190':
			$cedula = '44531190';
			break;
		case 'CO413266':
			$cedula = '15413266';
			break;
		case 'CO987805':
			$cedula = '47987805';
			break;
		case 'CO084029':
			$cedula = '45084029';
			break;
		case 'CO060414':
			$cedula = '52060414';
			break;
		case 'CO145893':
			$cedula = '47145893';
			break;
		case 'CO407831':
			$cedula = '49407831';
			break;
		case 'CO031832':
			$cedula = '17031832';
			break;
		case 'CO682668':
			$cedula = '18682668';
			break;
		case 'CO122906':
			$cedula = '19122906';
			break;
		case 'CO881028':
			$cedula = '26881028';
			break;
		case 'CO316285':
			$cedula = '50316285';
			break;
		case 'CO569301':
			$cedula = '44569301';
			break;
		case 'CO881028':
			$cedula = '26881028';
			break;
		case 'CO501408':
			$cedula = '32501408';
			break;
		case 'CO646898':
			$cedula = '36646898';
			break;
		case 'CO614538':
			$cedula = '42614538';
			break;
		case 'CO918774':
			$cedula = '36918774';
			break;
		case 'CO936030':
			$cedula = '51936030';
			break;
		case 'CO012688':
			$cedula = '19012688';
			break;
		case 'CO328728':
			$cedula = '15328728';
			break;
		case 'CO149335':
			$cedula = '20149335';
			break;
		case 'CO531190':
			$cedula = '44531190';
			break;
		case 'CO149335':
			$cedula = '20149335';
			break;
		case 'CO918774':
			$cedula = '36918774';
			break;
		case 'CO328728':
			$cedula = '15328728';
			break;
		case 'CO468719':
			$cedula = '18468719';
			break;
		case 'CO413266':
			$cedula = '15413266';
			break;
		case 'CO531190':
			$cedula = '44531190';
			break;
		case 'CO179249':
			$cedula = '26179249';
			break;
		case 'CO215956':
			$cedula = '44215956';
			break;
		case 'CO328728':
			$cedula = '15328728';
			break;
		case 'CO424490':
			$cedula = '46424490';
			break;
		case 'CO936030':
			$cedula = '51936030';
			break;
		case 'CO215956':
			$cedula = '44215956';
			break;
		case 'CO149335':
			$cedula = '20149335';
			break;
		case 'CO328728':
			$cedula = '15328728';
			break;
		case 'CO275431':
			$cedula = '48275431';
			break;
		case 'CO179249':
			$cedula = '26179249';
			break;
		case 'CO132264':
			$cedula = '43132264';
			break;
		case 'CO179249':
			$cedula = '26179249';
			break;
		case 'CO936030':
			$cedula = '51936030';
			break;
		case 'CO328728':
			$cedula = '15328728';
			break;
		case 'CO602748':
			$cedula = '31602748';
			break;
		case 'CO032078':
			$cedula = '35032078';
			break;
		case 'CO715059':
			$cedula = '52715059';
			break;
		case 'CO179249':
			$cedula = '26179249';
			break;
		case 'CO328728':
			$cedula = '15328728';
			break;
		case 'CO602748':
			$cedula = '31602748';
			break;
		case 'CO032078':
			$cedula = '35032078';
			break;
		case 'CO179249':
			$cedula = '26179249';
			break;
		case 'CO328728':
			$cedula = '15328728';
			break;
		case 'CO531190':
			$cedula = '44531190';
			break;
		case 'CO602748':
			$cedula = '31602748';
			break;

		default:
			$cedula;
			break;
	}
	return $cedula;
}

die(json_encode([
	'success' => true,
	'data' => $data
]));
