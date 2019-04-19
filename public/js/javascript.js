
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

$(function() {
  $('.dateBegin')
      .datepicker({
        format: 'dd-mm-yyyy',
        startDate: '04-01-2019'
      })
      .on('changeDate', function() {
        let dayLeft = $('#begin').datepicker('getDate');
        dayLeft.setDate(dayLeft.getDate() + 25);
        $('#end').datepicker('setEndDate', dayLeft);
      });
  $('.dateEnd').datepicker();

  $(".accept").on("click",function(event){
      event.preventDefault();



      let id = $(this).attr("id");

      let elementDiv = $(this).parent(".pendingRow");

      console.log(id);
      $.ajax(
          {
              url: '/holiday/accept/'+id,
              method: 'GET',
              success: function(json,elementDiv){
                  console.log(elementDiv);

                  $(elementDiv).html("Accepted");
                  $(elementDiv).addClass(".accepted",{duration:500});
                  $(elementDiv).fadeOut();
              },
              error: function(){
                  console.log("error");
              }
          }
      );
  });


});
