<?php 
set_time_limit(10000);

$mysqli = mysqli_connect('localhost', 'root', '','call');

if (mysqli_connect_errno())
  {
    echo "Error al conectar a MySql: " . mysqli_connect_error();
  }

$prefijos=['091','092','093','094','095','096','097','098','099'];

$sql="SELECT id,numero FROM numeros_nuevos ORDER BY id desc limit 1";

$result= mysqli_query($mysqli,$sql);

// var_dump($result);
if(mysqli_num_rows($result)){

    #toma el ultimo numero 
    $num=mysqli_fetch_array($result)[1];

    #redondea el numero 
    $digitos=  intval(substr($num,-6));

    #se le suma un nuemro al id
    $digitos++;

    $cont=2;
    $maxreg=1000; #****MODIFICAR ESTA VARIABLE PARA CAMBIAR LA CANTIDAD DE REGISTROS POR CADA PREFIJO****
    $inicio= microtime(true);
    foreach ($prefijos as $p ) {

                
        while($cont<=$maxreg && $digitos<=999999){

            $tel= str_pad($digitos,6,0,STR_PAD_LEFT);
        
            $telef=$p.$tel;
         
    
            $query="SELECT numero FROM numeros WHERE numero=$telef";
    
                
            if ($result=mysqli_query($mysqli,$query)) {
    
                if (mysqli_num_rows($result)==0) {
                    $insert="INSERT INTO numeros_nuevos (numero) VALUES ('$telef')";
                    mysqli_query($mysqli,$insert);
                    $cont++;
    
                }
            }
            $digitos++; 
        }
        $digitos=0;
        $cont=1;
    }
    $fin=microtime(true)-$inicio;
    $total=count($prefijos)*$maxreg;
    $query="INSERT INTO tiempo_ejecucion_script (tiempo_ejecucion,cant_registros) VALUES ($fin,$total)";
    mysqli_query($mysqli,$query);
  
    
}else{
    $numero=0;
  
    $tel= str_pad($numero,6,0,STR_PAD_LEFT);
    $telef=$prefijos[0].$tel;

    if(mysqli_query($mysqli,"INSERT INTO numeros_nuevos (numero) VALUES ('$telef')")){

        echo "guardado $telef <br>";
    }
}


?>