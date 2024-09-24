/*FUNCIONES DE SESION */

/* COMPROBAR SESION */
 
const verificarSesionCliente = async () => {
    return new Promise((resolve, reject) => {
        if ((localStorage.getItem(`${nombre_app}id_usuario`) == "null" || localStorage.getItem(`${nombre_app}id_usuario`) == null) 
        || (localStorage.getItem(`${nombre_app}token`) == "null" || localStorage.getItem(`${nombre_app}token`) == null)) {
            window.location.replace("login.html");
            resolve();
        }
    });
}

const verificarSesionClienteLogin = async () => {
    return new Promise((resolve, reject) => {
        if ((localStorage.getItem(`${nombre_app}id_usuario`) != "null" || 
        localStorage.getItem(`${nombre_app}id_usuario`) != null) || 
        (localStorage.getItem(`${nombre_app}token`) != "null" || localStorage.getItem(`${nombre_app}token`) != null)) {
            window.location.replace("index.html");
            resolve();
        }
    });
}
/* COMPROBAR SESION */

/* CERRAR SESION  */
const CerrarSesion = async (event = null) => {
    if (event != null) event.preventDefault();
    localStorage.removeItem(`${nombre_app}id_usuario`);
    localStorage.removeItem(`${nombre_app}token`);
    window.location.replace("login.html");
}
/* CERRAR SESION   */

/*LOGIN */
const Login = async (event,redirect_html = 'index.html') => {
    event.preventDefault();
    showLoading();
    const respuesta = await http("login.php", "POST", $("#login").serialize());
    hideLoading();
    if (respuesta.success) {
        localStorage.setItem(`${nombre_app}id_usuario`, respuesta.id);
        localStorage.setItem(`${nombre_app}token`, respuesta.token);
        window.location.replace(redirect_html);
    }
    else error(respuesta.mensaje);
}
/* LOGIN */



