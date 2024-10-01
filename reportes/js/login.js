$(document).ready(function () {
  $('#btnEntrar').click(login);
  
  $('body').on('keypress', 'input', function (args) {
    if (args.keyCode == 13) {
      $('#btnEntrar').click();
      return false;
    }
  });
});


function login(e) {
  e.preventDefault();
  const $username = $('#user').val();
  const $password = $('#password').val();


  if (!$username) {
    Swal.fire('Error!', 'Usuario requerido', 'error');
  } else if (!$password) {
    Swal.fire('Error!','Contraseña requerida','error');
  } else {
    $.ajax({
      url: 'ajax/procesoLogin.php',
      dataType: 'json',
      data: { usuario: $username, password: $password },
      method: 'POST',
      beforSend: function () {},
      success: function (response) {
        if (response.result) {
          localStorage.username = $username;
          localStorage.nombreUser = response.data.nombre;
          localStorage.typeUser = response.data.tipo_usuario;
          window.location.href = 'index.php?reporte=1';
        } else {
          Swal.fire('Error!',response.message,'error');
        }
      },
      error: function (error) {
        console.log(error.responseText);
      }
    });
  }
}
