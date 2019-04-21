



//Datepicker

$(document).ready(function() {
    $('#user_form_roles').on('change', function() {

        if ($(this).val()=='ROLE_MANAGER') {

            $('.handlingDep').show();

        } else {
            $('.handlingDep').hide();
        }
    });

    // Department display for manager only
// Manager department handling (hide for users and admin, but not for managers)


    /*$('#holiday_form_endDate').datepicker({
        format: "dd/mm/yyyy"
    });
*/
    $('#holiday_form_startDate').datepicker({
        format: {
            toDisplay: function (date, format, language) {
                var d = new Date("dd-mm-yyyy", date);
                return d.toISOString();
            },
            toValue: function (date, format, language) {
                var d = new Date("yyyy-mm-dd", date);
                return new Date(d);
            },
        },
        startDate: new Date()

    })
        .on('changeDate', function() {
            let beginDate = $('#holiday_form_startDate').datepicker('getDate');

            let dayLeft = Number($('#holidayLeft').text());

            //dayLeft.setDate(dayLeft.getDate()+Number($('#holidayLeft').text()));

            date = moment(beginDate); // use a clone

            console.log("dayLeft: " + dayLeft);

            while (dayLeft > 0) {
                date = date.add(1, 'days');
                // decrease "days" only if it's a weekday.
                if (date.isoWeekday() !== 6 && date.isoWeekday() !== 7) {
                    dayLeft -= 1;
                }
            }

            console.log("begindate: " + new Date(beginDate));

            //dayLeft.setDate(dayLeft.getDate()+Number($('#holidayLeft').text()));
            //console.log(dayLeft.getDate());
            $('#holiday_form_endDate').datepicker('setEndDate', new Date(date.get()));
            $('#holiday_form_endDate').datepicker('setStartDate', new Date(beginDate));


        });
    $('#holiday_form_endDate').datepicker({
        format: {
            toDisplay: function (date, format, language) {
                var d = new Date("dd-mm-yyyy",date);
                return d.toISOString();
            },
            toValue: function (date, format, language) {
                var d = new Date("yyyy-mm-dd",date);
                return new Date(d);
            }
        },
        startDate: new Date()
    });



// Accept and refuse holiday for managers
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
                  $(this).html("Accepted");
                  $(this).addClass("accepted");

                  window.setTimeout(function(){
                    $(this).fadeOut();
                  }.bind(this), 3000);
              }.bind(elementDiv)
          }
      );
  });

  $(".refuse").on("click",function(event){
        event.preventDefault();

        let id = $(this).parent().prev().children().attr("id");

        let elementDiv = $(this).parent().parent();

        $.ajax(
            {
                url: '/holiday/refuse/'+id,
                method: 'GET',
                success: function(json){
                    $(this).html("Refused");
                    $(this).addClass("refused");

                    window.setTimeout(function(){
                        $(this).fadeOut();
                    }.bind(this), 3000);
                }.bind(elementDiv)
            }
        );
    });

});
