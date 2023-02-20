
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
      const element = $('#qrcode').get(0)
      new QRCode(element, { text: upload })
    },
    error: function () {
      alert('Error loading document')
    }
  })
})
