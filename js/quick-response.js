
$(document).ready(function () {
  const queryParams = new URLSearchParams(window.location.search)
  const id = queryParams.get('id')
  if (id === null) {
    window.location.href = '/'
    return
  }

  $.ajax('/api/getupload.php?id=' + id, {
    method: 'get',
    cache: true,
    success: function (upload) {
      $('#qrcode-img').attr('src', 'https://chart.googleapis.com/chart?chs=400x400&chld=H|0&cht=qr&choe=UTF-8&chl=' + upload)
    },
    error: function () {
      alert('Error loading document')
    }
  })
})
