
$(document).ready(function () {
  let form = null
  let uploadId = null
  let filename = ''

  $('.w-file-remove-link').on('click', function () {
    uploadId = null
    $('.default-state').css('display', 'inline-block')
    $('.uploading-state').css('display', 'none')
    $('.w-file-upload-success').css('display', 'none')
    $('.w-file-upload-error').css('display', 'none')
  })

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

    form = $(this).get(0)
    const files = $('#pdf-file').prop('files')
    const src = URL.createObjectURL(files[0])
    $('#pdf-embed').attr('src', src)
    modal('#pdf-embed-modal', 'open')
  })

  $('#form-confirm').click(async function () {
    if (form === null) {
      alert('Invalid action')
      return
    }

    if (uploadId === null) {
      alert('Please upload your file first')
      return
    }

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

  function updateStatus () {
    $.ajax('/api/active.php', {
      method: 'get',
      cache: false,
      success: function (data) {
        if (data === 'active') {
          $('.text-block-6').css({
            width: '',
            'background-color': ''
          }).text('Available')
        } else {
          $('.text-block-6').css({
            width: '175px',
            'background-color': 'red'
          }).text('Not Available')
        }
      }
    })
  }

  updateStatus()
  setInterval(updateStatus, 300000)
})
