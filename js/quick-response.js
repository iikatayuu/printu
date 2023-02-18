
$(document).ready(function () {
  const queryParams = new URLSearchParams(window.location.search)
  const id = queryParams.get('id')
  if (id === null) {
    window.location.href = '/'
    return
  }

  const url = window.location.origin + '/print.php?id=' + id
  const element = $('#qrcode').get(0)
  new QRCode(element, {
    text: url
  })
})
