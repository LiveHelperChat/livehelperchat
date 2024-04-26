$(document).ready(function() {
    $('#add-field-row button[name="custom_field_add"]').click(function(){
        botCommandFields.push({
            'name': document.getElementById('custom_field_name').value,
            'placeholder': document.getElementById('custom_field_placeholder').value,
            'type': document.getElementById('custom_field_type').value,
            'rows': document.getElementById('custom_field_rows').value,
            'required': document.getElementById('custom_field_required').value,
        });
        renderPeriods()
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

    ee.addListener('delete_custom_command_field',function (index) {
        botCommandFields.splice(index,1);
        renderPeriods();
    });

    function escapeHtml (string) {
        return String(string).replace(/[&<>"'`=\/]/g, function fromEntityMap (s) {
            return entityMap[s];
        });
    }

    function renderPeriods(){
        let periodList = document.getElementById('field-rows-container');
        periodList.innerHTML = '';
        botCommandFields.forEach((item, index) => {
            periodList.innerHTML += '<div class="row pt-1"><div class="col-3"><b>'+periodList.getAttribute('name-field')+'</b> - '+escapeHtml(item.name)+'<br><span class="text-muted fs12"> {args.arg_' + (index+1) +'} or {arg_' + (index+1) +'} in bot, in Rest API {{args.arg_' + (index+1) +'}}</span></div>'+
                '<div class="col-2"><b>'+periodList.getAttribute('placeholder-field')+'</b> - '+escapeHtml(item.placeholder) + '</div>'+
                '<div class="col-2"><b>'+periodList.getAttribute('type-field')+'</b> - '+escapeHtml(item.type) + '</div>'+
                '<div class="col-2"><b>'+periodList.getAttribute('rows-field')+'</b> - '+escapeHtml(item.rows ? item.rows : 1) + '</div>'+
                '<div class="col-2"><b>'+periodList.getAttribute('required-field')+'</b> - '+escapeHtml(item.required ? item.required : 'required') + '</div>'+
                '<div class="col-1"><button class="btn btn-danger btn-sm w-100" type="button" onclick="ee.emitEvent(\'delete_custom_command_field\',['+index+'])">'+periodList.getAttribute('remove-action')+'</button></div>'+
                '<input type="hidden" name="custom_field_name[]" value="'+escapeHtml(item.name)+'">' +
                '<input type="hidden" name="custom_field_placeholder[]" value="'+escapeHtml(item.placeholder)+'">' +
                '<input type="hidden" name="custom_field_type[]" value="'+escapeHtml(item.type)+'">' +
                '<input type="hidden" name="custom_field_rows[]" value="'+escapeHtml(item.rows)+'"></div>'+
                '<input type="hidden" name="custom_field_required[]" value="'+escapeHtml(item.required ? item.required : 'required')+'"></div>';
        });
    }
    renderPeriods();
});

$(document).ready(function() {
    $('select[name="bot_id"]').change(function(){
        $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $(this).val(), { }, function(data) {
            $('#trigger-list-id').html(data);
        }).fail(function() {

        });
    });
    $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $('select[name="bot_id"]').val() + '/<?php echo $item->trigger_id?>',  { }, function(data) {
        $('#trigger-list-id').html(data);
    }).fail(function() {

    });
});