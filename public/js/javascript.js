// Datepicker
$('.datepicker').datepicker({
  format: 'mm/dd/yyyy',
  startDate: '-3d'
});

// Department display for manager only
// Manager department handling (hide for users and admin, but not for managers)
$('#user_form_roles').on('change', function() {
  if ($(this).value('ROLE_MANAGER')) {
    $('.handlingDep').css('display', 'none');
  } else {
    $('.handlingDep').css('display', 'auto');
  }
});
