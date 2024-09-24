<?php

function htmlBodyPagoAprobado($data)
{
  $year = date('Y');

  $html = <<<EOF
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{$data['title']}</title>
  <link rel="shortcut icon" href="{$data['urlFavicon']}" />
</head>

<body style="width: 100%; height: 100%; padding: 0; margin: 0;  overflow-x: hidden; font-family: 'Open Sans', sans-serif;">
  <div style="max-width: 860px; width: 60%; margin: 0 auto; padding-top: 1rem; padding-bottom: 1rem;">
    <div style="padding-top: 1rem; padding-bottom: 1rem;">
    <img src="{$data['urlImg']}" alt="Logo" style="display: block; width: 50px; max-width: 50px; min-width: 50px; margin: 0 auto;">
    </div>
    <div style="width: 100%; height: 3px; background-color: #d4dadf;"></div>
    <div>
      <h1>Pago realizado con éxito</h1>
      <p>Le damos la bienvenida a VIDA servicio de compañía.</p>
  
    </div>
    <div style="padding-top: 1rem; padding-bottom: 1rem;">
      <p>Su pago ha sido aprobado</p>
    </div>
    <div style="padding-top: 1rem; padding-bottom: 1rem;">
      <p>Accede al siguiente enlace para visualizar el comprobante de pago:<br />
        <a style="color: #26448c;" href="{$data['urlBase']}" target="_blank">{$data['urlComprobante']}</a>
    </div>
    <div>
      <p>Saludos,<br>VIDA</p>
    </div>

    <footer style="padding-top: 1rem; padding-bottom: 1rem; text-align: center;"><small>© $year VIDA</small></footer>
  </div>
</body>

</html>
EOF;
  return $html;
}
