$(function()
{	
	$(".solo_numeros").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 40]) !== -1 ||
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
        if(e.altKey){
            return false;
        }
	});

	$(document).on("wheel", "input[type=number]", function (e) {//EVITAR QUE AL MOVER LA RUEDA DEL MOUSE EN UN INPUT NUMBER CAMBIE DE NUMERO
		$(this).blur();
	});

	$("#cedBen").blur(function(){//CORROBORA EN TIEMPO REAL SI ES SOCIO O NO, AL SALIR DEL INPUT CONSULTA SI ES SOCIO
		$cedulaBen = $("#cedBen").val();
		if($cedulaBen != "" && comprobarCI($cedulaBen)){
			if($resultado_verificacion = verificarSocio($cedulaBen)){
				if($resultado_verificacion == "error"){
					mensaje("Ocurrio un error verificando su cedula",0,0,"error0");
				}else{
					mensaje("Usted ya es socio",0,0,"error0");
				}
			}
		}
	});
	// $("#prodBen").blur(function(){//SI CAMBIAN EL CAMPO OCULTO DE PRODUCTO Y NO ES UN PRODUCTO CORRECTO TE LLEVA AL INDEX
	// 	$producto = $("#prodBen").val();
	// 	if($producto != 1 && $producto != 2 && $producto != 3){
	// 		window.location.href = "index.php";
	// 	}
	// });
	$("#pay").submit(function(e){//EVITO QUE EL BOTON HAGA SUBMIT
		return false;
	});

	$("#depBen").change(function(){
		$id_departamento = $("#depBen").val();
		if($id_departamento != 0 && $id_departamento != null){
			cargarLocalidades($id_departamento);
		}
	});

	$("#producto").change(function(){
		$producto = $("#producto").val();
		mostrarCantHoras($producto);
	});
});

function mostraruno() {
	document.getElementById("pasouno").style.display = "block";
	document.getElementById("pasodos").style.display = "none";
	document.getElementById("titulouno").style.display = "block";
	document.getElementById("titulodos").style.display = "none";
	document.getElementById("dotuno").className = "dot";
	goToByScroll("vinetas");
}
function mostrardos() {
	document.getElementById("pasodos").style.display = "block";
	document.getElementById("pasouno").style.display = "none";
	document.getElementById("pasotres").style.display = "none";
	document.getElementById("titulouno").style.display = "none";
	document.getElementById("titulotres").style.display = "none";
	document.getElementById("titulodos").style.display = "block";
	document.getElementById("dotdos").className = "dot";
	document.getElementById("dotuno").className = "dotdisabled";
	document.getElementById("dottres").className = "dotdisabled";
	goToByScroll("vinetas");
}

function mostrartres() {
	document.getElementById("pasotres").style.display = "block";
	document.getElementById("pasodos").style.display = "none";
	document.getElementById("titulouno").style.display = "none";
	document.getElementById("titulodos").style.display = "block";
	document.getElementById("dotdos").className = "dotdisabled";
	document.getElementById("dottres").className = "dot";
	goToByScroll("vinetas");
  }
  
  function mostrarcuatro() {
	document.getElementById("pasocuatro").style.display = "block";
	document.getElementById("pasotres").style.display = "none";
	document.getElementById("titulotres").style.display = "block";
	document.getElementById("titulodos").style.display = "none";
	document.getElementById("dottres").className = "dotdisabled";
	document.getElementById("dotcuatro").className = "dot";
	goToByScroll("vinetas");
  }
  // Hacer scroll hasta una determinada DIV
function goToByScroll(id){
	// Remove "link" from the ID
  id = id.replace("link", "");
	// Scroll
  $('html,body').animate({
	  scrollTop: $("#"+id).offset().top},
	  'slow');
}

function siguiente(seccion,pagina){
	$precioProd = $("#prodPrecio").val();

	if(seccion == 0){
		$nombreBen		= $('#nomBen').val();
		$cedulaBen		= $('#cedBen').val();
		$dia        	= $('#nataliciodia').val();
    	$mes        	= $('#nataliciomes').val();
    	$anho       	= $('#natalicioano').val();
		$fecnacBen     	= $anho + '-' + $mes + '-' + $dia;
		$edad 			= calcularEdad($fecnacBen);
		$direccionBen 	= $("#dirBen").val();
		$departamentoBen= $("#depBen").val();
		$localidadBen= $("#locBen").val();
		$mailBen 		= $("#mailBen").val();
		$celularBen 	= $("#celBen").val();


		if($nombreBen == "" || $cedulaBen == "" || $fecnacBen == "--" || $direccionBen == "" || !$departamentoBen || $mailBen == "" || $celularBen == "" || !$localidadBen){
			mensaje("Complete todos los campos","0",seccion,"error"+seccion);
		}else if(!corroborarFecha($fecnacBen)){
			mensaje("Fecha incorrecta",0,seccion,"error"+seccion);
		}else if(!comprobarCI($cedulaBen) || $cedulaBen.length < 7){
			mensaje("Cedula incorrecta",0,seccion,"error"+seccion);
		}else if($edad < 18){
			mensaje("Debe ser mayor de edad para afiliarse",0,seccion);
		}else if($resultado_verificacion = verificarSocio($cedulaBen)){
			if($resultado_verificacion == "error"){
				mensaje("Ocurrio un error verificando su cedula",0,seccion,"error"+seccion);
			}else{
				mensaje("Usted ya es socio",0,seccion,"error"+seccion);
			}
		}else if(($mailBen != '') && (!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test($mailBen))){
			mensaje("Formato de mail incorrecto","0",seccion,"error"+seccion);
		}else if((!/^(09)/.test($celularBen) || $celularBen.length != 9) && (!/^4/.test($celularBen) || $celularBen.length != 8) && (!/^2/.test($celularBen) || $celularBen.length != 8)){
			mensaje("El numero debe empezar con (09) ,(4) o (2) y tener el largo adecuado","0",seccion,"error"+seccion);
		}else{
			$("#cedTit").val($cedulaBen);
			$("#nomTit").val($nombreBen);
			$depText = $("#depBen option:selected").text();
			$locText = $("#locBen option:selected").text();
			$("#cobDep").val($depText);
			$("#cobLoc").val($locText);
			$("#mailTit").val($mailBen);
			$("#celTit").val($celularBen);
			mensaje("Correcto",1,seccion);
			cambioPagina(pagina);
		}
	}else if(seccion == 1){
		$producto = $("#producto").val();
		$cant_horas_sanatorio = $("#hrsanatorio").val();
		$cant_horas_servicio = $("#hrservicio").val();

		if(($producto == null && $("#producto").is(":visible")) || ($cant_horas_sanatorio == null && $("#hrsanatorio").is(":visible")) || ($cant_horas_servicio == null && $("#hrservicio").is(":visible"))){
			mensaje("Seleccion productos y las cantidades de horas",0,seccion,"error"+seccion);
		}else{
			$dia        	= $('#nataliciodia').val();
			$mes        	= $('#nataliciomes').val();
			$anho       	= $('#natalicioano').val();
			$fecha_nacimiento     	= $anho + '-' + $mes + '-' + $dia;
			$localidad= $("#locBen").val();
			$precio = calcularPrecio($producto,$cant_horas_sanatorio,$cant_horas_servicio,$localidad,$fecha_nacimiento);
			if($precio != false){
				mensaje("Correcto",1,seccion);
				$('#spanPrecio').html("$"+$precio);
				cambioPagina(pagina);
			}else{
				mensaje("Ocurrió un error calculando el precio de su afiliación.",0,seccion,"error"+seccion);
			}
		}
	}else if(seccion == 2){
		//$productoBen = $("#prodBen").val();
		//if($productoBen != 1 && $productoBen != 2 && $productoBen != 3){
			//window.location.href = "index.php";
		//}else{
			$numTarjeta 	= $("#numTar").val();
			$cedulaTit  	= $("#cedTit").val();
			$nombreTit  	= $("#nomTit").val();
			$cvv        	= $("#cvv").val();
			$mesVencimiento = $("#mesVen").val();
			$anoVencimiento = $("#anoVen").val();
			$mailTit 		= $("#mailTit").val();
			$celularTit 	= $("#celTit").val();
			if($numTarjeta == "" || $cedulaTit == "" || $nombreTit == "" || $cvv == "" || !$mesVencimiento || !$anoVencimiento || $mailTit == "" || $celularTit == ""){
				mensaje("Complete todos los campos","0",seccion,"error"+seccion);
			}else if($numTarjeta.length < 16){
				mensaje("Numero de tarjeta incorrecto",0,seccion,"error"+seccion);
			}else if(!comprobarCI($cedulaTit) || $cedulaTit.length < 7){
				mensaje("Cedula incorrecta",0,seccion,"error"+seccion);
			}else if(($cvv.length < 3) || (!/^([0-9])*$/.test($cvv))){
				mensaje("CVV incorrecto",0,seccion,"error"+seccion);
			}else if(($mailTit != '') && (!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test($mailTit))){
				mensaje("Formato de mail incorrecto","0",seccion,"error"+seccion);
			}else if((!/^(09)/.test($celularTit) || $celularTit.length != 9) && (!/^4/.test($celularTit) || $celularTit.length != 8) && (!/^2/.test($celularTit) || $celularTit.length != 8)){
				mensaje("El numero debe empezar con (09) ,(4) o (2) y tener el largo adecuado","0",seccion,"error"+seccion);
			}else{
				tokenPagar();
			}
		//}
	}
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//FUNCIONES

function corroborarFecha(fecha)
{
    let fechita         = fecha.split('-');
	let diaFecha 		= fechita[2];
	let mesFecha 		= fechita[1];
	let anhoFecha 		= fechita[0];
	let fechaFinal 	= new Date(anhoFecha, mesFecha, 0);
	if((diaFecha - 0) > (fechaFinal.getDate() - 0))
	{
		return false;
	}
	return mesFecha > 0 && mesFecha < 13 && anhoFecha > 0 && anhoFecha < 32768 && diaFecha > 0 && diaFecha <= fechaFinal.getDate();
}

function comprobarCI(cedi){
    //Inicializo los coefcientes en el orden correcto
    var arrCoefs = [2,9,8,7,6,3,4,1];
    var suma = 0;
    //Para el caso en el que la CI tiene menos de 8 digitos
    //calculo cuantos coeficientes no voy a usar
    var difCoef = parseInt(arrCoefs.length - cedi.length);
    //var difCoef = parseInt(arrCoefs.length – ci.length);
    //recorro cada digito empezando por el de más a la derecha
    //o sea, el digito verificador, el que tiene indice mayor en el array
    for(var i=cedi.length - 1; i> -1;i--){
    //for (var i = ci.length – 1; i > -1; i–) {
    //ooObtengo el digito correspondiente de la ci recibida
    var dig = cedi.substring(i, i+1);
    //Lo tenía como caracter, lo transformo a int para poder operar
    var digInt = parseInt(dig);
    //Obtengo el coeficiente correspondiente al ésta posición del digito
    var coef = arrCoefs[i+difCoef];
    //Multiplico dígito por coeficiente y lo acumulo a la suma total
    suma = suma + digInt * coef;
    }
    // si la suma es múltiplo de 10 es una ci válida
    if ( (suma % 10) == 0 ) {
        return true;
    }else{
        return false;
    }
}

function calcularEdad(fechaIng) {
    fechaIng = new Date(fechaIng);
    var mes = fechaIng.getMonth()+1;  // 10
    var dia = fechaIng.getDate()+1;     // 30
    var ano = fechaIng.getFullYear(); // 2010
    
    fechaIng = mes+'/'+dia+'/'+ano;
    fechaIng = new Date(fechaIng);
     var hoy = new Date();
    var fNaci = new Date(fechaIng);
    var edad = hoy.getFullYear() - fNaci.getFullYear();
    var m = hoy.getMonth() - fNaci.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < fNaci.getDate())) {
        edad--;
    }
    return edad;
}

function cambioPagina(pagina){
	if(pagina == 0){
		mostraruno();
	}else if(pagina == 1){
		mostrardos();
	}else if(pagina == 2){
		mostrartres();
	}else if(pagina == 3){
		mostrarcuatro();
	}
}

function mensaje(mensaje,correcto_incorrecto,pagina,div=0){
	if(correcto_incorrecto == 1){
		$("#sok"+pagina).html(mensaje);
		$("#ok"+pagina).css("display","block");
		$("#error"+pagina).css("display","none");
	}else{
		$("#serror"+pagina).html(mensaje);
		$("#error"+pagina).css("display","block");
		$("#ok"+pagina).css("display","none");
		goToByScroll(div);
	}
}

function maxLengthCheck(object)
{
    if (object.value.length > object.maxLength)
        object.value = object.value.slice(0, object.maxLength)
}

function verificarSocio(cedula){
	$data = "cedula="+cedula;
	$.ajax({
		url 	: 'https://vida-apps.com/afiliacion_step_by_step_con_motor/ajax/verificarSocio.php',
		data 	: $data,
		method	: 'POST',
		dataType: 'json',
		async: false,
		success: function(content) {
			if(content.result){
				if(content.es_socio){
					$res =  true;//ES SOCIO
				}else{
					$res = false;//NO ES SOCIO
				}
			}else{
				$res = "error";
			}
		},
		error: function() {
			$res = "error";
		}
	});

	return $res;
}

function cargarLocalidades(id_departamento){
	var url="https://vida-apps.com/motorDePrecios/PHP/clases/Select.php?select=ciudades&departamento="+id_departamento;
	$("#locBen").empty();
	$('#locBen').append($('<option>', {
        value: "0",
        text: 'Seleccione localidad',
        disabled: true,
        selected: true
    }));
	$.getJSON(url,function(localidades){
        $.each(localidades, function(i,localidades){
            $('#locBen').append($('<option>', {
                value: localidades.id_filial,
                text: localidades.nombre
            }));
        });
	});
	$("#locBen").prop('disabled',false);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//MERCADOPAGO
function pagar(){
	goToByScroll("headerP");
	$("#loading").css("display","block");
	$("body").css("overflow", "hidden");
	$.ajax({
		url: "https://vida-apps.com/afiliacion_step_by_step_con_motor/ajax/procesoPago.php",
		data:
		{
			metodo: $('#paymentMethodId').val(),
			email: $('#mailTit').val(),
			celular: $('#celTit').val(),
			token: $('#tokenValidador').val(),
			nomBen: $('#nomBen').val(),
			cedBen: $('#cedBen').val(),
			nataliciodia: $('#nataliciodia').val(),
			nataliciomes: $('#nataliciomes').val(),
			natalicioano: $('#natalicioano').val(),
			dirBen: $('#dirBen').val(),
			depBen: $('#depBen').val(),
			mailBen: $('#mailBen').val(),
			locBen: $('#locBen').val(),
			celBen: $('#celBen').val(),
			numTar: $('#numTar').val(),
			nomTit: $('#nomTit').val(),
			cedTit: $('#cedTit').val(),
			celTit: $('#celTit').val(),
			producto: $("#producto").val(),
			cant_horas_sanatorio: $("#hrsanatorio").val(),
			cant_horas_servicio: $("#hrservicio").val()
		},
		method	: 'POST',
		dataType: "JSON",
		success: function(content){
			$("#loading").css("display","none");
			$("body").css("overflow-y", "scroll");
			if(content.correcto){
				if(content.status != "approved"){
					mensaje(content.mensaje,0,2,"error2");
				}else{
					mensaje(content.mensaje,1,2);
					cambioPagina(3);
				}
			}else{
				if(content.es_socio){
					mensaje(content.mensaje,0,2,"error2");
				}else if(content.medio_invalido){
					mensaje(content.mensaje,0,2,"error2");
				}else{
					mensaje("Ha ocurrido un error inesperado, intentelo mas tarde",0,2,"error2");
				}
			}
		},
		error: function() {
			$("#loading").css("display","none");
			$("body").css("overflow-y", "scroll");
			mensaje("Ha ocurrido un error inesperado, intentelo mas tarde",0,2,"error2");
		}
	});
}

function tokenPagar(){
	var $form = document.querySelector('#pay');
	Mercadopago.createToken($form, sdkResponseHandler);
	setTimeout(pagar,1000);
}

function addEvent(el, eventName, handler){
    if (el.addEventListener) {
        el.addEventListener(eventName, handler);
    } else {
        el.attachEvent('on' + eventName, function(){
            handler.call(el);
        });
    }
};

function getBin() {
    var ccNumber = document.querySelector('input[data-checkout="cardNumber"]');
    return ccNumber.value.replace(/[ .-]/g, '').slice(0, 6);
};

function guessingPaymentMethod(event)
{
    var bin = getBin();

    if (event.type == "keyup")
    {
        if (bin.length >= 6)
        {
            Mercadopago.getPaymentMethod(
            {
                "bin": bin
            }, setPaymentMethodInfo);
        }
    }
    else
    {
        setTimeout(function()
        {
            if (bin.length >= 6)
            {
                Mercadopago.getPaymentMethod(
                {
                    "bin": bin
                }, setPaymentMethodInfo);
            }
        }, 100);
    }
};

function setPaymentMethodInfo(status, response) 
{
    if (status == 200) 
    {
        document.querySelector("input[name=paymentMethodId]").value = response[0].id;
    }
};

addEvent(document.querySelector('#numTar'), 'keyup', guessingPaymentMethod);
addEvent(document.querySelector('#numTar'), 'change', guessingPaymentMethod);

function sdkResponseHandler(status, response)
{
    switch (status.toString()) {
        case '200':
            $("#tokenValidador").val(response.id);
            break;
        case '201':
            $("#tokenValidador").val(response.id);
            break;
		case '205':
			mensaje("Ingrese el número de la tarjeta.",0,4);
            break;
		case '208':
			mensaje("Especifique el mes de vencimiento de su tarjeta.",0,4);
            break;
		case '209':
			mensaje("Especifique el año de vencimiento de su tarjeta.",0,4);
            break;
        case '212':
        case '213':
		case '214':
			mensaje("Corrobore la cédula ingresada.",0,4);
            break;
		case '221':
			mensaje("Ingrese el nombre y apellido especificado en la tarjeta.",0,4);
            break;
		case '224':
			mensaje("Ingrese el código de seguridad especificado en la tarjeta.",0,4);
            break;
		case 'E301':
			mensaje("Corrobore el número de tarjeta e intente nuevamente.",0,4);
            break;
		case 'E302':
			mensaje("Corrobore el número de seguridad (CVV) especificado en el dorso de la tarjeta.",0,4);
            break;
		case '316':
			mensaje("Corrobore el nombre ingresado.",0,4);
            break;
        case '323':
		case '324':
			mensaje("Corrobore la cédula ingresada.",0,4);
            break;
		case '325':
			mensaje("Corrobore el mes de vencimiento ingresado.",0,4);
            break;
		case '326':
			mensaje("Corrobore el año de vencimiento ingresado.",0,4);
            break;
		default:
			mensaje("Por favor corrobore los datos y trate nuevamente.",0,4);
            break;
    }
};

function mostrarCantHoras(producto){
	$data = "producto="+producto;
	$.ajax({
		url 	: 'https://vida-apps.com/afiliacion_step_by_step_con_motor/ajax/mostrarCantHoras.php',
		data 	: $data,
		method	: 'POST',
		dataType: 'json',
		success: function(content) {
			if(content.result){
				$("#hrsanatorio").val(0);
				$("#hrservicio").val(0);
				if(content.cant_horas_sanatorio){
					$("#divHorasSanatorio").css("display","block");
				}else{
					$("#divHorasSanatorio").css("display","none");
				}

				if(content.cant_horas_servicio){
					$("#divHorasServicio").css("display","block");
				}else{
					$("#divHorasServicio").css("display","none");
				}
			}else{
				alert("Error");
			}
		},
		error: function() {
			alert("Ha ocurrido un error, intente mas tarde.");
		}
	});
}

function calcularPrecio(servicio,hrs_sanatorio,hrs_servicio,localidad,fecha_nacimiento){
	$data = "servicio="+servicio+"&hrs_sanatorio="+hrs_sanatorio+"&hrs_servicio="+hrs_servicio+"&localidad="+localidad+"&fecha_nacimiento="+fecha_nacimiento;
	$.ajax({
		url 	: 'https://vida-apps.com/afiliacion_step_by_step_con_motor/ajax/calcularPrecio.php',
		data 	: $data,
		method	: 'POST',
		dataType: 'json',
		async: false,
		success: function(content) {
			if(content.result){
				$precio = content.precio;
			}else{
				$precio = false;
			}
		},
		error: function() {
			$precio = false;
		}
	});
	return $precio;
}