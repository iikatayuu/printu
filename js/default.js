
$(document).ready(function () {
  $(document).on('click', function (event) {
    const isDropdown = $(event.target).parents('.w-dropdown').length > 0 || $(event.target).is('.w-dropdown')
    if (!isDropdown) $('.w-dropdown-list.w--open').removeClass('w--open')

    const isMenu = $(event.target).parents('.w-nav-menu').length > 0 || $(event.target).parents('.w-nav-button').length > 0 || $(event.target).is('.w-nav-menu') || $(event.target).is('.w-nav-button')
    if (!isMenu) {
      $('.w-nav-button').removeClass('w--open')
      $('.w-nav-menu.active').removeClass('active')
    }
  })

  $('.w-dropdown-toggle').on('click', function () {
    const list = $(this).parent().find('.w-dropdown-list')
    const open = $(list).hasClass('w--open')

    if (open) $(list).removeClass('w--open')
    else $(list).addClass('w--open')
  })

  $('.w-nav-button').on('click', function () {
    const isActive = $(this).hasClass('w--open')
    if (isActive) {
      $(this).removeClass('w--open')
      $('.w-nav-menu').removeClass('active')
    } else {
      $(this).addClass('w--open')
      $('.w-nav-menu').addClass('active')
    }
  })
})
