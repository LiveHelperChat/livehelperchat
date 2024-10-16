/*lhcAppControllers.controller('DepartmentCustomPeriodCtrl',['$scope', function($scope) {

    this.customPeriods = [];

    var that = this;

    this.add = function() {
        that.customPeriods.push({
            'date_from' : that.custom_date_from,
            'date_to' 	: that.custom_date_to,
            'start_hour' : that.custom_start_hour,
            'start_hour_min' : that.custom_start_hour_min,
            'end_hour' 	: that.custom_end_hour,
            'end_hour_min' 	: that.custom_end_hour_min
        });
    };

    this.delete = function(period) {
        that.customPeriods.splice(that.customPeriods.indexOf(period),1);
    };
}]);*/

function deleteCustomPeriod(index) {

}

$( document ).ready(function() {

    let period = document.getElementById('online-hours-active');
    period.addEventListener("change", function() {
        if ($(period).is(':checked')){
            $('#online-hours-active-block').removeClass('hide');
        } else {
            $('#online-hours-active-block').addClass('hide');
        }
    });

    $('.depend-block-show-control').each(function(){
        if ($(this).is(':checked')) {
            $('.depend-block-show-'+$(this).attr('name')).removeClass('hide');
        } else {
            $('.depend-block-show-'+$(this).attr('name')).addClass('hide');
        }
        $(this).change(function(){
            if ($(this).is(':checked')) {
                $('.depend-block-show-'+$(this).attr('name')).removeClass('hide');
            } else {
                $('.depend-block-show-'+$(this).attr('name')).addClass('hide');
            }
        })
    })

    if ($(period).is(':checked')){
        $('#online-hours-active-block').removeClass('hide');
    } else {
        $('#online-hours-active-block').addClass('hide');
    }

    function changeblock(inst){
        let day = $(inst).attr('name');
        if ($(inst).is(':checked')) {
            $('.'+day+'-block').removeClass('hide');
        } else {
            $('.'+day+'-block').addClass('hide');
        }
    }

    $('.day-control-block').each(function() {
        changeblock(this);
    });

    $('.day-control-block').change(function() {
        changeblock(this);
    });

    $('#period-repetitiveness').change(function() {
       $('.show-by-period').hide();
       $('.show-by-period-'+$(this).val()).show();
    });

    $('#add-period-button').click(function(){
        depCustomPeriods.push({
            'date_from': document.getElementById('custom_date_from').value,
            'date_to': document.getElementById('custom_date_to').value,
            'start_hour': document.getElementById('custom_start_hour').value,
            'start_hour_min': document.getElementById('custom_start_hour_min').value,
            'end_hour': document.getElementById('custom_end_hour').value,
            'end_hour_min': document.getElementById('custom_end_hour_min').value,
            'repetitiveness': document.getElementById('period-repetitiveness').value,
            'day_of_week': document.getElementById('day-of-week').value
        });
        renderPeriods()
    });

    ee.addListener('delete_custom_period',function (index) {
        depCustomPeriods.splice(index,1);
        renderPeriods();
    });

    var entityMap = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
        '/': '&#x2F;',
        '`': '&#x60;',
        '=': '&#x3D;'
    };

    function escapeHtml (string) {
        return String(string).replace(/[&<>"'`=\/]/g, function fromEntityMap (s) {
            return entityMap[s];
        });
    }

    function renderPeriods(){
        let periodList = document.getElementById('custom-periods-list');
        periodList.innerHTML = '';
        let item = '';
        depCustomPeriods.forEach((item, index) => {
            periodList.innerHTML += '<tr><td>'+(item.repetitiveness == 0 ? escapeHtml(item.date_from) + ' - ' + escapeHtml(item.date_to) : 'Week day [' + item.day_of_week + ']') + '</td><td>' + escapeHtml(item.start_hour) +':'+ escapeHtml(item.start_hour_min) +'</td>' +
                '<td>'+escapeHtml(item.end_hour) +':' + escapeHtml(item.end_hour_min) +'</td><td><button class="btn btn-sm btn-danger" type="button" onclick="ee.emitEvent(\'delete_custom_period\',['+index+'])">'+periodList.getAttribute('remove-action')+'</button>' +
                '<input type="hidden" name="customPeriodDateFrom[]" value="'+escapeHtml(item.date_from)+'">' +
                '<input type="hidden" name="customPeriodDateTo[]" value="'+escapeHtml(item.date_to)+'">' +
                '<input type="hidden" name="customPeriodStartHour[]" value="'+escapeHtml(item.start_hour)+'">' +
                '<input type="hidden" name="customPeriodStartHourMin[]" value="'+escapeHtml(item.start_hour_min)+'">' +
                '<input type="hidden" name="customPeriodEndHour[]" value="'+escapeHtml(item.end_hour)+'">' +
                '<input type="hidden" name="customPeriodEndHourMin[]" value="'+escapeHtml(item.end_hour_min)+'">' +
                '<input type="hidden" name="customPeriodRepetitiveness[]" value="'+escapeHtml(item.repetitiveness)+'">' +
                '<input type="hidden" name="customPeriodDayOfWeek[]" value="'+escapeHtml(item.day_of_week)+'">' +
                '<input type="hidden" name="customPeriodId[]" value=""></td></tr>';
        });
    }

    renderPeriods();

});