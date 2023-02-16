
$(document).ready(function () {
  $('#proceed-gcash').on('click', async function (event) {
    event.preventDefault()

    const token = btoa(`${pk}:`)
    const res = await $.ajax('https://api.paymongo.com/v1/payment_methods', {
      method: 'post',
      data: JSON.stringify({
        data: {
          attributes: {
            type: 'gcash'
          }
        }
      }),
      headers: {
        Authorization: `Basic ${token}`
      },
      contentType: 'application/json'
    })

    const url = `https://api.paymongo.com/v1/payment_intents/${output.data.id}/attach`
    const paymentRes = await $.ajax(url, {
      method: 'post',
      data: JSON.stringify({
        data: {
          attributes: {
            payment_method: res.data.id,
            client_key: output.data.attributes.client_key,
            return_url: returnUrl
          }
        }
      }),
      headers: {
        Authorization: `Basic ${token}`
      },
      contentType: 'application/json'
    })

    window.location.href = paymentRes.data.attributes.next_action.redirect.url
  })
})
