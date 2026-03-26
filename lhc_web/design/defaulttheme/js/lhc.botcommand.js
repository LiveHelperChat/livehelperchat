$(document).ready(function() {
    $('#add-field-row button[name="custom_field_add"]').click(function () {

        if (typeof $(this).attr('update-id') !== 'undefined') {
            botCommandFields[$(this).attr('update-id')]['name'] = document.getElementById('custom_field_name').value;
            botCommandFields[$(this).attr('update-id')]['placeholder'] = document.getElementById('custom_field_placeholder').value;
            botCommandFields[$(this).attr('update-id')]['type'] = document.getElementById('custom_field_type').value;
            botCommandFields[$(this).attr('update-id')]['rows'] = document.getElementById('custom_field_rows').value;
            botCommandFields[$(this).attr('update-id')]['required'] = document.getElementById('custom_field_required').value;
            botCommandFields[$(this).attr('update-id')]['options'] = document.getElementById('custom_field_options').value;
            $(this).removeAttr('update-id');
            document.getElementById('field-action-button').innerText = document.getElementById('field-action-button').getAttribute('data-add');
        } else {
            botCommandFields.push({
                'name': document.getElementById('custom_field_name').value,
                'placeholder': document.getElementById('custom_field_placeholder').value,
                'type': document.getElementById('custom_field_type').value,
                'rows': document.getElementById('custom_field_rows').value,
                'required': document.getElementById('custom_field_required').value,
                'options': document.getElementById('custom_field_options').value,
            });
        }

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

    ee.addListener('delete_custom_command_field', function (index) {
        botCommandFields.splice(index, 1);
        renderPeriods();
    });

    ee.addListener('edit_custom_command_field', function (index) {
        document.getElementById('custom_field_name').value = botCommandFields[index]['name'];
        document.getElementById('custom_field_placeholder').value = botCommandFields[index]['placeholder'];
        document.getElementById('custom_field_type').value = botCommandFields[index]['type'];
        document.getElementById('custom_field_rows').value = botCommandFields[index]['rows'];
        document.getElementById('custom_field_required').value = botCommandFields[index]['required'];
        document.getElementById('custom_field_options').value = botCommandFields[index]['options'] ? botCommandFields[index]['options'] : '';
        document.getElementById('field-action-button').innerText = document.getElementById('field-action-button').getAttribute('data-update');
        document.getElementById('field-action-button').setAttribute('update-id',index);

        // ensure options textarea visibility matches selected type
        $('#custom_field_type').trigger('change');

    });

    // Toggle visibility of options textarea when dropdown type selected
    $(document).on('change', '#custom_field_type', function() {
        if ($(this).val() === 'dropdown') {
            $('#custom_field_options').show();
        } else {
            $('#custom_field_options').hide();
        }
    });

    // initialize visibility
    if ($('#custom_field_type').length) {
        $('#custom_field_type').trigger('change');
    }

    function escapeHtml(string) {
        return String(string).replace(/[&<>"'`=\/]/g, function fromEntityMap(s) {
            return entityMap[s];
        });
    }


    function renderPeriods() {
        let periodList = document.getElementById('field-rows-container');
        periodList.innerHTML = '';
        botCommandFields.forEach((item, index) => {
            let optionsPreview = '';
            if (item.options) {
                const optionsArr = item.options.split(/\r?\n/).filter(Boolean).slice(0,3).map(function(v){ return v.replace(/\|\|/, ' - '); });
                if (optionsArr.length > 0) {
                    optionsPreview = '<ul class="mb-0 pl-3">' + optionsArr.map(function(opt){ return '<li>' + escapeHtml(opt) + '</li>'; }).join('') + '</ul>';
                }
            }

            let row = '<div class="row pt-1">';
            row += '<div class="col-3"><b>' + periodList.getAttribute('name-field') + '</b> - ' + escapeHtml(item.name) + '<br><span class="text-muted fs12"> {args.arg_' + (index + 1) + '} or {arg_' + (index + 1) + '} in bot, in Rest API {{args.arg_' + (index + 1) + '}}</span></div>';
            row += '<div class="col-2"><b>' + periodList.getAttribute('placeholder-field') + '</b> - ' + escapeHtml(item.placeholder) + '</div>';
            row += '<div class="col-2"><b>' + periodList.getAttribute('type-field') + '</b> - ' + escapeHtml(item.type) + '</div>';
            row += '<div class="col-2"><b>Options</b>' + optionsPreview + '</div>';
            row += '<div class="col-1"><b>' + periodList.getAttribute('rows-field') + '</b> - ' + escapeHtml(item.rows ? item.rows : 1) + '</div>';
            row += '<div class="col-1"><b>' + periodList.getAttribute('required-field') + '</b> - ' + escapeHtml(item.required ? item.required : 'required') + '</div>';
            row += '<div class="col-1"><div class="btn-group w-100" role="group" aria-label="First group"><button class="btn btn-success btn-sm w-100" type="button" onclick="ee.emitEvent(\'edit_custom_command_field\',[' + index + '])">' + periodList.getAttribute('edit-action') + '</button><button class="btn btn-danger btn-sm w-100" type="button" onclick="ee.emitEvent(\'delete_custom_command_field\',[' + index + '])">' + periodList.getAttribute('remove-action') + '</button></div></div>';

            // hidden inputs
            row += '<input type="hidden" name="custom_field_name[]" value="' + escapeHtml(item.name) + '">';
            row += '<input type="hidden" name="custom_field_placeholder[]" value="' + escapeHtml(item.placeholder) + '">';
            row += '<input type="hidden" name="custom_field_type[]" value="' + escapeHtml(item.type) + '">';
            row += '<input type="hidden" name="custom_field_rows[]" value="' + escapeHtml(item.rows) + '">';
            row += '<input type="hidden" name="custom_field_required[]" value="' + escapeHtml(item.required ? item.required : 'required') + '">';
            row += '<input type="hidden" name="custom_field_options[]" value="' + escapeHtml(item.options ? item.options : '') + '">';

            row += '</div>';
            periodList.innerHTML += row;
        });
    }

    renderPeriods();
});