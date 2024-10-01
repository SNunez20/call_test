function subirBajas() {
  $('#primaria').css('display', 'block');
  if (document.getElementById('file').files.length == 0) {
    $('#primaria').css('display', 'none');
    alert('Debe Seleccionar un archivo');
  } else {
    var data = new FormData($('#frmSubir')[0]);
    $.each($('#file')[0].files, function (i, file) {
      data.append('file-' + i, file);
    });
    $.ajax({
      url: 'ajax/subir.php',
      data: data,
      method: 'POST',
      cache: false,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function (content) {
        if (content.result) {
          alert(content.message);
          $('#primaria').css('display', 'none');
          $('#file').val('');
        } else {
          alert(content.message);
          $('#primaria').css('display', 'none');
        }
      },
      error: function () {
        alert(content.message);
        $('#primaria').css('display', 'none');
      }
    });
  }
}
