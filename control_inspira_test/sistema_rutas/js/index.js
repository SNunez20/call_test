$(document).ready(async function () {
    //Cargar api de google maps
    await loadScript(`https://maps.googleapis.com/maps/api/js?key=${key_api_maps}&libraries=places&callback=initMap`);

    $("#respuesta_modificar").html('');
});


let map = {};
let directionService = {};
let directionsDisplay = {};
let navigator_ok = false;
let marcador = {};
const input_busqueda = document.getElementById('gps_rutas_direccion');
let panorama = {};

function redirigirDatosPersonales(event = false) {
    if (event !== false) event.preventDefault();
    const url = getUrl('socio');
    const url_redirecct = getUrl('ver_formulario_cobro') ? 'ver_formulario_cobro' : 'ver_formulario_cobro';
    window.location.replace(`../index.php?${url_redirecct}=${url}`);
}
async function modificarDireccion(event) {
    event.preventDefault();
    $('#autocomplete_search').prop('disabled', true);
    $("#respuesta_modificar").html('');

    const reglasAValidar = [
        { input: 'gps_rutas_calle', nombre: 'Calle', required: true, min: 2, max: 200 },
        { input: 'gps_rutas_puerta', nombre: 'Puerta', required: false, min: 1, max: 100 },
        { input: 'gps_rutas_esquina', nombre: 'Esquina', required: true, min: 2, max: 100 },
        { input: 'gps_rutas_solar', nombre: 'Solar', required: false, min: 0, max: 8 },
        { input: 'gps_rutas_manzana', nombre: 'Manzana', required: false, min: 0, max: 8 },
        { input: 'gps_rutas_referencia', nombre: 'Referencia', required: true, min: 1, max: 70 },
        { input: 'gps_rutas_apartamento', nombre: 'Apartamento', required: false, min: 0, max: 100 },
    ];

    const errores = validarForm(reglasAValidar);

    if (errores.error) {
        warning(errores.mensaje);
        return false;
    }

    const form = serializeForm('#gps_rutas_editar_direccion');


    const esPuerta = form['manzana'] !== '' || form['solar'] !== '' ? false : true;

    const calle = form['calle'];
    const puerta = form['puerta'];
    const esquina = form['esquina'];
    const apartamento = form['apartamento'];
    const referencia = form['referencia'];
    const solar = form['solar'];
    const manzana = form['manzana'];

    form['direccion'] = armarDireccionRutaGps({ calle, puerta, esquina, apartamento, referencia, solar, manzana }, esPuerta);
    form['latitud'] = marcador.position.lat();
    form['longitud'] = marcador.position.lng();
    form['id_socio'] = getUrl('socio');
    $("#gps_rutas_direccion").val(form['direccion']);

    const response = await http('modificar_direccion_ruta.php', 'POST', form, 'Enviando...');

    if (response.error === false) {
        $("#respuesta_modificar").html(`<span class="alert alert-success">${response.mensaje}</span> ` || '<span class="alert alert-success">Se confirmo con exito la dirección del socio</span>');
        await buscar();


    }

    else warning(response.mensaje || 'Error al intentar modificar la direccion del socio');
}


function armarDireccionRutaGps(datosDir = {}, esPuerta = true) {

    if (esPuerta) {
        let direccion =
            datosDir.apto != ""
                ? datosDir.calle.substr(0, 14) + " " + datosDir.puerta + "/" + datosDir.apartamento + " E:"
                : datosDir.calle.substr(0, 17) + " " + datosDir.puerta + " E:";
        direccion += datosDir.esquina.substr(0, 36 - direccion.length);

        return direccion;
    } else {
        let direccion =
            datosDir.apto != ""
                ? datosDir.calle.substr(0, 14) + " M:" + datosDir.manzana + " S:" + datosDir.solar + "/" + datosDir.apartamento
                : datosDir.calle.substr(0, 14) + " M:" + datosDir.manzana + " S:" + datosDir.solar + " E:";
        direccion += datosDir.apartamento == "" ? datosDir.esquina.substr(0, 36 - direccion.length) : "";

        return direccion;
    }
}

function initMap() {

    return new Promise((resolve) => {
        let coordenadas_uruguay = {
            lat: -33.0000000,
            lng: -56.0000000
        };

        map = new google.maps.Map(
            document.getElementById('gps_rutas_map'),
            {
                zoom: 7.65,
                center: coordenadas_uruguay,
                icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png',
            }
        );



        marcador = new google.maps.Marker({
            position: coordenadas_uruguay,
            map: map,
            animation: google.maps.Animation.DROP,
        });


        directionService = new google.maps.DirectionsService();
        directionsDisplay = new google.maps.DirectionsRenderer({ polylineOptions: { strokeColor: '#gps_rutas_2E9AFE' } });
        navigator_ok = navigator.geolocation ? true : false;


        //Street view 
        const posicion_street = { lat: marcador.position.lat(), lng: marcador.position.lng() };
        panorama = new google.maps.StreetViewPanorama(
            document.getElementById("gps_rutas_pano"),
            {
                position: posicion_street,
                pov: {
                    heading: 34,
                    pitch: 10,
                },
            }
        );

        map.setStreetView(panorama);



        map.addListener('click', (event) => {
            marcador.setPosition(event.latLng);
            direccion(event.latLng);
            panorama.setPosition(event.latLng);

        });

        autocompletar();

        const url = getUrl('socio');

        if (url !== false) {
            $('#gps_rutas_socio').val(url);
            buscar();
        }



        resolve(true);
    });


}


function autocompletar(event = false) {
    if (event !== false) event.preventDefault();

    const input = document.getElementById("autocomplete_search");

    const options = {
        componentRestrictions: { country: "uy" }
    };

    const autocomplete = new google.maps.places.Autocomplete(input, options);


    autocomplete.addListener('place_changed', () => {
        const busqueda = autocomplete.getPlace();
        map.setCenter(busqueda.geometry.location);
        map.setZoom(18);
        marcador.setPosition(busqueda.geometry.location);

    });

}
function direccion(eventLatLng) {
    showLoading('Cargando...');

    const request = {
        origin: eventLatLng,
        destination: eventLatLng,
        travelMode: google.maps.DirectionsTravelMode.DRIVING
    };

    directionService.route(request, function (response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
            let direccion = response.routes[0].legs[0].end_address;

            let direccion_parseada_string = '';

            let direccion_parseada_array = [];

            for (let i = 0; i <= direccion.length; i++) {

                direccion_parseada_string += direccion[i];

                if (direccion[i] === ',') {
                    direccion_parseada_array.push(direccion_parseada_string);
                    direccion_parseada_string = '';
                }
            }
            const direccion_parseada = direccion_parseada_array[0].slice(0, -1);

            $("#gps_rutas_direccion").val(direccion_parseada);

            const ultimos_5_caracteres = Number.parseInt(direccion_parseada.length) - 5;

            let numero_de_puerta_string = direccion_parseada.slice(ultimos_5_caracteres, direccion_parseada.length);

            let numero_de_puerta = '';


            for (let j = 0; j <= numero_de_puerta_string.length; j++) {

                let n = Number.isInteger(Number.parseInt(numero_de_puerta_string[j])) ? Number.parseInt(numero_de_puerta_string[j]) : '';

                numero_de_puerta += numero_de_puerta_string[j] !== undefined ? n : '';

            }
            $("#gps_rutas_puerta").val(numero_de_puerta);

            let calle_parseada = '';

            for (let g = 0; g <= direccion_parseada.length; g++) {
                calle_parseada += Number.isInteger(Number.parseInt(direccion_parseada[g])) === false && direccion_parseada[g] !== undefined
                    ? direccion_parseada[g]
                    : '';
            }

            $("#gps_rutas_calle").val(calle_parseada);


            $("#gps_rutas_esquina").val('');

            abrirModal('gps_rutas_modalEditarDireccion');
        }
    });

    hideLoading();
}


function abrirEnMaps() {

    const lat = marcador.position.lat();

    const lng = marcador.position.lng();

    const url_map = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;

    window.open(url_map);

}
async function buscar(e = false, cargando = false) {
    return new Promise(async (resolve) => {
        if (e !== false) e.preventDefault();

        const form = $('#gps_rutas_socio').val();

        const response = await http('buscar_direccion_ruta.php', 'POST', { 'socio': form }, cargando !== false ? 'Buscando...' : false);
        if (response.error) {
            warning(response.mensaje || `No se encontro ningun socio de ID o cedula con valor ingresado (${form})`);
            return false;
        }

        const calle = response.calle || '';
        const puerta = response.puerta || '';
        const solar = response.solar || '';
        const manzana = response.manzana || '';
        const esquina = response.esquina && response.esquina !== '0' ? ` y ${response.esquina}` : '';
        const localidad = response.localidad ? `, ${response.localidad}` : '';
        const localidad_original = response.localidad ? `${response.localidad}` : ''
        const esquina_original = response.esquina && response.esquina !== '0' ? `${response.esquina}` : '';
        const referencia = response.referencia_utf8 || '';
        const apartamento = response.apartamento || '';
        const cedula_socio = response.cedula_socio || '';
        const direccion = response.direccion || '';
        const nombre_socio = response.nombre;
        const departamento = response.departamento || '';

        $("#localidad_form").html(`<p><strong>${localidad_original}</strong></p><br>`);

        $("#gps_rutas_info_direccion").html(
            `
        <p><strong>Calle: ${calle}</strong></p>
        <p><strong>Puerta : ${puerta}</strong></p>
        <p><strong>Solar : ${solar}</strong></p>
        <p><strong>Manzana: ${manzana}</strong></p>
        <p><strong>Esquina: ${esquina_original}</strong></p>
        <p><strong>Localidad : ${localidad_original}</strong></p>
        <p><strong>Departamento: ${departamento}</strong></p>
        <p><strong>Referencia: ${referencia}</strong></p>

        <p>
            <strong>
                ${calle} ${puerta} ${solar} ${manzana} ${esquina_original} , ${localidad_original}  ${departamento}  
            </strong>
        </p>
        `
        );

        $("#gps_rutas_info_socio").html(`
            <p><strong>Socio: ${nombre_socio}</strong></p>
            <p><strong>Cedula: ${cedula_socio}</strong></p>
        `);

        $('#gps_rutas_calle').val(calle);
        $('#gps_rutas_calle_actual').html(`Valor anterior : (${calle})`);

        $('#gps_rutas_puerta').val(puerta);
        $('#gps_rutas_puerta_actual').html(`Valor anterior : (${puerta})`);

        $('#gps_rutas_manzana').val(manzana);
        $('#gps_rutas_manzana_actual').html(`Valor anterior: (${manzana})`);

        $('#gps_rutas_solar').val(solar);
        $('#gps_rutas_solar_actual').html(`Valor anterior: (${solar})`);

        $('#gps_rutas_esquina').val(esquina_original);
        $('#gps_rutas_esquina_actual').html(`Valor anterior: (${esquina_original})`);

        $('#gps_rutas_localidad').val(localidad_original);
        $('#gps_rutas_localidade_actual').html(`Valor anterior: (${localidad_original})`);


        $('#gps_rutas_apartamento').val(apartamento);
        $('#gps_rutas_apartamento_actual').html(`Valor anterior: (${apartamento})`);

        $('#gps_rutas_referencia').val(referencia);
        $('#gps_rutas_referencia_actual').html(`Valor anterior: (${referencia})`);

        $('#gps_rutas_cedula_socio').val(cedula_socio);

        $('#gps_rutas_direccion').val(direccion);


        if (calle !== '') {
            let query = '';

            let busqueda = false;

            if (manzana !== '' && solar !== '') {
                query = `${calle}  ${manzana}${solar} ${localidad}`;

                busqueda = await busquedaEnMapa({ query: query, fields: ['name', 'geometry'] });
                if (busqueda === false) {
                    query = `${calle}  ${manzana}${solar} ${localidad} ${referencia}`;
                    busqueda = await busquedaEnMapa({ query: query, fields: ['name', 'geometry'] });
                    if (busqueda === false) {
                        error('No pudimos encontrar la dirección');
                    }
                }
            }
            else {
                query = `${calle} ${puerta}  ${esquina} ${localidad} ${departamento}`;

                busqueda = await busquedaEnMapa({ query: query, fields: ['name', 'geometry'] });

                if (busqueda === false) {
                    query = `${calle}   ${puerta}  ${localidad} ${departamento}`;

                    busqueda = await busquedaEnMapa({ query: query, fields: ['name', 'geometry'] });

                    if (busqueda === false) {
                        query = `${calle}    ${localidad} ${departamento}`;

                        busqueda = await busquedaEnMapa({ query: query, fields: ['name', 'geometry'] });

                        if (busqueda === false) {
                            query = `${calle} ${departamento} `;

                            busqueda = await busquedaEnMapa({ query: query, fields: ['name', 'geometry'] });

                            if (busqueda == false) {
                                error('No pudimos encontrar la dirección');

                            }
                        }
                    }
                }


            }


        }
        else warning('No pudimos traer los datos correctamente, recarga e intenta nuevamente, sino ponte en contacto con los adminsitradores del sistema');


    });
}

function busquedaEnMapa(request) {
    return new Promise((resolve, reject) => {
        request.query = `${request.query} ,Uruguay`;
        const service = new google.maps.places.PlacesService(map);
        service.findPlaceFromQuery(request, (results, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                marcador.setAnimation(google.maps.Animation.DROP);
                marcador.setPosition(results[0].geometry.location);

                map.setCenter(results[0].geometry.location);
                map.setZoom(14);
                panorama.setPosition(results[0].geometry.location);
                resolve(results);
            }
            else resolve(false);

        });
    });
}
