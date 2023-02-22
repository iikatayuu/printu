
$(document).ready(function () {
  let uploadId = null
  let filename = ''

  $('#pdf-file').on('change', function () {
    const files = $(this).prop('files')
    if (files.length > 0) {
      const file = files[0]
      const formdata = new FormData()
      formdata.append('file', file)

      $('.default-state').css('display', 'none')
      $('.uploading-state').css('display', 'inline-block')

      $.ajax('/api/upload.php', {
        method: 'post',
        data: formdata,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
          uploadId = data
          filename = file.name
          $('.w-file-upload-success').css('display', 'inline-block')
          $('.w-file-upload-file-name').text(filename)
        },
        error: function () {
          $('.w-file-upload-error').css('display', 'inline-block')
        },
        complete: function () {
          $('.uploading-state').css('display', 'none')
        }
      })
    } else {
      uploadId = null
    }
  })

  $('#form-submit').on('submit', function (event) {
    event.preventDefault()

    if (uploadId === null) {
      alert('Please upload your file first')
      return
    }

    const form = $(this).get(0)
    const formdata = new FormData(form)
    formdata.delete('file-2')
    formdata.append('file', uploadId)
    formdata.append('filename', filename)

    $.ajax('/api/submit.php', {
      method: 'post',
      data: formdata,
      cache: false,
      contentType: false,
      processData: false,
      success: function (data) {
        window.location.href = '/receipt.php?id=' + data
      },
      error: function () {
        alert('Error occured!')
      }
    })
  })
})
