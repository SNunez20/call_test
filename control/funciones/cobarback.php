<?php
if($resultado_cobro->result){

    if($resultado_cobro->correcto && ($resultado_cobro->approved || $resultado_cobro->pending)){
       
           
       //passo a padron

        

     
    }

    //modificar 
    $response = array('result' => true, 'error' => $resultado_cobro->error, 'mensaje' => $resultado_cobro->mensaje, 'codigo_compra' => $codigo_compra, 'tipo_mensaje' => $resultado_cobro->tipo_mensaje, 'titulo_mensaje' => $resultado_cobro->titulo_mensaje, 'approved' => $resultado_cobro->approved, 'pending' => $resultado_cobro->pending,'vidashops_restantes' => $vidashops_restantes, 'comprobante' => $resultado_cobro->comprobante);
}