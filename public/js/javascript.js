
// Department display for manager only
// Manager department handling (hide for users and admin, but not for managers)
$('#user_form_roles').on('change', function() {

  if ($(this).val()=='ROLE_MANAGER') {
    console.log("Ciao");
    $('.handlingDep').show();

  } else {
    $('.handlingDep').hide();
  }
});

//Datepicker
$(function(){

});
$('.js-datepicker').datepicker({
  startDate: "04-08-2019"
}).on("changeDate",function(){
  console.log("change");
});

  console.log("ciao adrien");

