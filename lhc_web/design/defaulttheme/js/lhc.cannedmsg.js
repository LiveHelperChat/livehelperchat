$( document ).ready(function() {

    let period = document.getElementById('canned-repeat-period');
    period.addEventListener("change", function() {
            $('.show-by-period').addClass('hide');
            $('.show-by-period-'+period.value).removeClass('hide');
    });

    $('.show-by-period').addClass('hide');
    $('.show-by-period-'+period.value).removeClass('hide');

    $('.show-by-date').change(function(){
        let day = $(this).attr('name');
        if ($(this).is(':checked')) {
            $('.show-by-date-'+day).removeClass('hide');
        } else {
            $('.show-by-date-'+day).addClass('hide');
        }
    });

    $('.show-by-date').each(function(){
        let day = $(this).attr('name');
        if ($(this).is(':checked')) {
            $('.show-by-date-'+day).removeClass('hide');
        } else {
            $('.show-by-date-'+day).addClass('hide');
        }
    });


});