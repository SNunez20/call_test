const produccion = true;

const nombre_app = produccion ? 'sistema_rutas' : 'sistema_rutas';

const version = '1.0.4';

const url_servidor_prod = 'http://192.168.1.13/call/control';

const url_app = produccion ? `${url_servidor_prod}/${nombre_app}/` : `http://localhost/call/control/${nombre_app}/`;

const url_api = produccion ? `${url_servidor_prod}/${nombre_app}/ajax/app` : `http://localhost/call/control/${nombre_app}/ajax/app`;

const servidor = url_servidor_prod;

const key_api_maps = 'AIzaSyBzCt83McUQQ5KbkmG61IYuEot5jJdLcA4';

const dir_views = "views/";

const dir_partials = `${dir_views}partials/`;

const archivos = ['index'];


const funciones_app_default = ['fechas', 'url', 'utils', 'inputs', 'imagenes', 'modal', 'partials_views', 'sesion', 'sweetAlert', 'tablas',
  'validaciones', 'http'];


async function loadScripts(_archivos = archivos, url = false) {
  await loadScriptAsync(_archivos, url);
}
function loadScriptAsync(_archivos, url = false) {
  return new Promise((resolve, reject) => {
    _archivos.map((archivo) => {
      let url_mod = url !== false ? `${url_app}/js/${url}/${archivo}.js?v=${version}` : `${url_app}/js/${archivo}.js?v=${version}`;

      const s = document.createElement('script');
      s.type = 'text/javascript';
      s.src = url_mod;
      $('body').append(s);
      resolve(true);
    });
  });
}

function loadScript(script) {
  return new Promise((resolve, reject) => {
    const sc = document.createElement('script');
    sc.type = 'text/javascript';
    sc.src = script;
    $('body').append(sc);
    resolve(true);
  });
}


const defaultsJS = async () => { await loadScripts(funciones_app_default, 'libs'); }

defaultsJS();


