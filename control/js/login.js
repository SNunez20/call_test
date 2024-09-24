$(document).ready(function () {
  $("#btnLogin").click(login);
});

function login(e) {
  e.preventDefault();
  const $username = $("#username").val();
  const $password = $("#password").val();
  const $error = $(".error");
  $error.text("");
  if (!$username) {
    $error.text(`Usuario requerido`);
  } else if(!$password) {
    $error.text(`Contraseña requerida`);
  } else {
    $.ajax({
      url: "Ajax/procesoLogin.php",
      dataType: "json",
      data: {username: $username, password: $password},
      method: "POST",
      beforSend: function () {},
      success: function (response) {
        $error.text("");
        if (response.result) {
          localStorage.username = $username;
          localStorage.idUser = response.userData.id;
          localStorage.name = response.userData.nombre;
          localStorage.typeAdmin = response.userData.tipo_admin;
          window.location.href = "index.php";
        } else {
          $error.text("El usuario y/o contraseña no coinciden");
        }
      },
      error: function (error) {
        console.log(error.responseText);
      }
    })
  }
}