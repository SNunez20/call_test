<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<link rel="stylesheet" href="css/estilos.css">
<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/>
<meta content="text/html" http-equiv="content-type">
<meta content="lolkittens" name="author">
<title>Call</title>
<link href="img/icon.png" type="image/png" rel="icon">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="js/call.js" type="text/javascript"></script>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/jquery.validate.js" type="text/javascript"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/localization/messages_es.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="/jquery.datetimepicker.css" / >
<script src="js/jquery.js"></script>
<script src="build/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript" language="javascript" src="../tabla/media/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="../tabla/media/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="../tabla/media/css/jquery.dataTables.css" media="screen" />
</head>

<table id="Jtabla" cellpadding="0" cellspacing="0" border="0" class="display" >
 
<thead>
<tr>
 
  <th width="58" align="left">Codigo</th>
<th width="274" align="left">Cliente</th>
 
<th width="124" align="left">Nota Pedido</th>
<th width="119" align="left">Estado Pedido</th>
 
<th width="103" align="left">Importe</th>
</tr>
 
</thead>
<tbody>
 
<tr>
  <td>1001</td>
 
<td>Carlos Alcantara </td>
<td>1203</td>
 
<td>Enviado</td>
<td class="center">1000</td>
 
</tr>
<tr class="gradeC">
 
  <td>1002</td>
<td>Jose Albertez </td>
 
<td>1204</td>
<td>Pendiente</td>
 
<td class="center">500</td>
</tr>
 
<tr class="gradeA">
  <td>1003</td>
 
<td>Carriles SL </td>
<td>1345</td>
 
<td>En Proceso </td>
<td class="center">2500</td>
 
</tr>
<tr class="gradeA">
 
  <td>1004</td>
<td>Maria Pado </td>
 
<td>1320</td>
<td>Enviado</td>
 
<td class="center">350</td>
</tr>
 
<tr class="gradeA">
  <td>1018</td>
 
  <td>Alina Sereno </td>
  <td>1358</td>
 
  <td>Enviado</td>
  <td class="center">50</td>
 
</tr>
</table>
 
<script>
 
$(document).ready(function(){
    $('#Jtabla').DataTable();
 
});
</script>