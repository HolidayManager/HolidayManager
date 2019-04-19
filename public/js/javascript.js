
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

      let elementDiv = $(this).parent().parent();

      console.log(id);
      $.ajax(
          {
              url: '/holiday/accept/'+id,
              method: 'GET',
              success: function(json){
                  console.log(this);

                  $(this).html("Accepted");
                  $(this).addClass("accepted");

                  window.setTimeout(function(){
                    $(this).fadeOut();
                  }.bind(this), 3000);
              }.bind(elementDiv),
              error: function(){
                  console.log("error");
              }
          }
      );
  });
    $(".refuse").on("click",function(event){
        event.preventDefault();



        let id = $(this).parent().prev().children().attr("id");

        let elementDiv = $(this).parent().parent();

        console.log(id);
        $.ajax(
            {
                url: '/holiday/refuse/'+id,
                method: 'GET',
                success: function(json){
                    console.log(this);

                    $(this).html("Refused");
                    $(this).addClass("refused");

                    window.setTimeout(function(){
                        $(this).fadeOut();
                    }.bind(this), 3000);
                }.bind(elementDiv),
                error: function(){
                    console.log("error");
                }
            }
        );
    });

});
