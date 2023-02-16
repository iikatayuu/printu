
$(document).ready(function () {
  $(document).on('click', function (event) {
    const isDropdown = $(event.target).parents('.w-dropdown').length > 0 || $(this).is('.w-dropdown')
    if (!isDropdown) $('.w-dropdown-list.w--open').removeClass('w--open')
  })

  $('.w-dropdown-toggle').on('click', function () {
    const list = $(this).parent().find('.w-dropdown-list')
    const open = $(list).hasClass('w--open')

    if (open) $(list).removeClass('w--open')
    else $(list).addClass('w--open')
  })
})
