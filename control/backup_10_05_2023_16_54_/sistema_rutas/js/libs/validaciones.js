const validarForm = (campos) => {
	const errores = [];

	let values = {};

	for (let i in campos) {
		const nombre_id = campos[i].input;
		const input = $(`#${nombre_id}`).val();
		const nombre = campos[i].nombre;
		const required = campos[i].hasOwnProperty('required') ? campos[i].required : false;
		const min = campos[i].hasOwnProperty('min') ? campos[i].min : false;
		const max = campos[i].hasOwnProperty('max') ? campos[i].max : false;
		const pattern = campos[i].hasOwnProperty('pattern') ? campos[i].pattern : false;
		const email = campos[i].hasOwnProperty('email') ? campos[i].email : false;
		const ci = campos[i].hasOwnProperty('ci') ? campos[i].email : false;
		const numero = campos[i].hasOwnProperty('numero') ? campos[i].numero : false;
		const celular = campos[i].hasOwnProperty('celular') ? campos[i].celular : false;

		
		let req = input === '' || input === undefined || input === null ? false : true;

		if (required !== false) {
			if (req === false) errores.push(`El campo ${nombre} es Requerido`);
		}
		if (min !== false && max !== false && req === true) {
			if (input.length < min || input.length > max) errores.push(`El campo ${nombre} tiene que tener un mínimo de ${min +1} (caracteres) y  un máximo  ${max - 1} (caracteres)`);
		}
		if (pattern !== false && req === true) {
			if (pattern.test(input) === false) errores.push(`El campo ${nombre} no es válido`);
		}
		if (email !== false && req === true) {
			if (RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/).test(input) === false) {
				errores.push(`Ingrese un Email válido`);
			}
		}
		if (ci !== false && req === true) {
			if (comprobarCI(input) === false) errores.push(`Ingrese un Cédula válida`);
		}
		if (numero !== false && req === true) {
			if ($.isNumeric(Number.parseInt(input)) === false) errores.push(`Ingrese un número en el campo ${nombre}`);
		}
		if (celular !== false && req === true) {
			if (comprobarCelular(Number.parseInt(input)) === false) errores.push('Ingrese un número de celular válido');
		}

		if (errores.length === 0) {
			values = {
				...values,
				[nombre_id]: input,
			};
		}

	}

	return {
		error: errores.length > 0 ? true : false,
		errores: errores,
		mensaje: errores.length > 0 ? convertirErroresAHtml(errores) : '',
		values: values,
	}

	/* Ejemplo de formato para validar json
	  const reglasAValidar = [
		{ input: 'cedula', nombre: 'Cédula', required: true, ci: true },
		{ input: 'nombre', nombre: 'Nombre completo', required: true, min: 3, max: 100 },
		{input :'fecha_nacimiento',nombre: 'Fecha de nacimiento',required :true},
		{ input: 'calle', nombre: 'Calle', required: true, min: 2, max: 50 },
		{ input: 'esquina', nombre: 'Esquina', required: true, min: 2, max: 50 },
		{ input: 'referencia', nombre: 'Referencia', required: true, min: 2, max: 50 },
		{ input: 'email', nombre: 'Email', required: true, min: 2, max: 100, email: true },
		{ input: 'celular', nombre: 'Celular', required: true, min: 6, max: 10, numero: true, celular: true }
	];
    
		const errores=validarForm(reglasAValidar);

	*/
}

const convertirErroresAHtml = (errores) => {
	let error = '';
	for (let i in errores) {
		error += `<span style='color:red;'>${errores[i]}</span><br />`;
	}
	return error;
}


function emailValido(email) {
	return new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/).test(email);
}

function testPassword(password, minLevel = 3) {
	let strength = 0;

	strength += /[A-Z]+/.test(password) ? 1 : 0;
	strength += /[a-z]+/.test(password) ? 1 : 0;
	strength += /[0-9]+/.test(password) ? 1 : 0;
	strength += /[\W]+/.test(password) ? 1 : 0;

	return strength >= minLevel;
}

function comprobarCI(ci) {
	const arrCoefs = [2, 9, 8, 7, 6, 3, 4, 1];
	let suma = 0;
	const difCoef = Number.parseInt(arrCoefs.length - ci.length);
	for (let i = ci.length - 1; i > -1; i--) {
		let dig = ci.substring(i, i + 1);
		let digInt = parseInt(dig);
		let coef = arrCoefs[i + difCoef];
		suma = suma + digInt * coef;
	}
	return (suma % 10) == 0;
}


$(".solo_numeros").keydown(function (e) {
	if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 40]) !== -1 || (e.keyCode >= 35 && e.keyCode <= 39)) {
		return;
	}
	if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		e.preventDefault();
	}
	if (e.altKey) {
		return false;
	}
});


$(".solo_letras").keydown(function (event) {
	const regex = /^[a-zA-Z ]+$/;
	let validar = regex.test(event.target.value);
	return validar == false ? event.preventDefault() : true;
});



function CamposVacio(array) {
	array.map((value) => {
		CampoVacio(value.id, value.mensaje);
	});
}

function CampoVacio(id, mensaje) {
	id = $(`#${id}`);
	if (id.length == 0 || id.val() === "" || id.val() === null) {
		id.append(`<div class='alert alert-danger'>${mensaje}</div>`);
		return false;
	} else return true;
}

function comprobarCelular(celular) {
	return /^[09][0-9]{1,7}$/.test(celular);
}	