
$(document).ready(function () {
  let uploadId = null

  $('.w-file-remove-link').on('click', function () {
    uploadId = null
    $('.default-state').css('display', 'inline-block')
    $('.uploading-state').css('display', 'none')
    $('.w-file-upload-success').css('display', 'none')
    $('.w-file-upload-error').css('display', 'none')
  })

  $('#file-2').on('change', function () {
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
          $('.w-file-upload-success').css('display', 'inline-block')
          $('.w-file-upload-file-name').text(file.name)
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

  $('#wf-form-Print-Entry').on('submit', function (event) {
    event.preventDefault()

    if (uploadId === null) {
      alert('Please upload your file first')
      return
    }

    const form = $(this).get(0)
    const formdata = new FormData(form)
    formdata.delete('file-2')
    formdata.append('file', uploadId)

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
