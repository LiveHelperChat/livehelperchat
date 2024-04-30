$(document).ready(function() {
    $('#add-field-row button[name="custom_field_add"]').click(function () {

        if (typeof $(this).attr('update-id') !== 'undefined') {
            botCommandFields[$(this).attr('update-id')]['name'] = document.getElementById('custom_field_name').value;
            botCommandFields[$(this).attr('update-id')]['placeholder'] = document.getElementById('custom_field_placeholder').value;
            botCommandFields[$(this).attr('update-id')]['type'] = document.getElementById('custom_field_type').value;
            botCommandFields[$(this).attr('update-id')]['rows'] = document.getElementById('custom_field_rows').value;
            botCommandFields[$(this).attr('update-id')]['required'] = document.getElementById('custom_field_required').value;
            $(this).removeAttr('update-id');
            document.getElementById('field-action-button').innerText = document.getElementById('field-action-button').getAttribute('data-add');
        } else {
            botCommandFields.push({
                'name': document.getElementById('custom_field_name').value,
                'placeholder': document.getElementById('custom_field_placeholder').value,
                'type': document.getElementById('custom_field_type').value,
                'rows': document.getElementById('custom_field_rows').value,
                'required': document.getElementById('custom_field_required').value,
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
        document.getElementById('field-action-button').innerText = document.getElementById('field-action-button').getAttribute('data-update');
        document.getElementById('field-action-button').setAttribute('update-id',index);

    });

    function escapeHtml(string) {
        return String(string).replace(/[&<>"'`=\/]/g, function fromEntityMap(s) {
            return entityMap[s];
        });
    }


    function renderPeriods() {
        let periodList = document.getElementById('field-rows-container');
        periodList.innerHTML = '';
        botCommandFields.forEach((item, index) => {
            periodList.innerHTML += '<div class="row pt-1"><div class="col-3"><b>' + periodList.getAttribute('name-field') + '</b> - ' + escapeHtml(item.name) + '<br><span class="text-muted fs12"> {args.arg_' + (index + 1) + '} or {arg_' + (index + 1) + '} in bot, in Rest API {{args.arg_' + (index + 1) + '}}</span></div>' +
                '<div class="col-2"><b>' + periodList.getAttribute('placeholder-field') + '</b> - ' + escapeHtml(item.placeholder) + '</div>' +
                '<div class="col-2"><b>' + periodList.getAttribute('type-field') + '</b> - ' + escapeHtml(item.type) + '</div>' +
                '<div class="col-2"><b>' + periodList.getAttribute('rows-field') + '</b> - ' + escapeHtml(item.rows ? item.rows : 1) + '</div>' +
                '<div class="col-2"><b>' + periodList.getAttribute('required-field') + '</b> - ' + escapeHtml(item.required ? item.required : 'required') + '</div>' +
                '<div class="col-1"><div class="btn-group w-100" role="group" aria-label="First group"><button class="btn btn-success btn-sm w-100" type="button" onclick="ee.emitEvent(\'edit_custom_command_field\',[' + index + '])">' + periodList.getAttribute('edit-action') + '</button><button class="btn btn-danger btn-sm w-100" type="button" onclick="ee.emitEvent(\'delete_custom_command_field\',[' + index + '])">' + periodList.getAttribute('remove-action') + '</button></div></div>' +
                '<input type="hidden" name="custom_field_name[]" value="' + escapeHtml(item.name) + '">' +
                '<input type="hidden" name="custom_field_placeholder[]" value="' + escapeHtml(item.placeholder) + '">' +
                '<input type="hidden" name="custom_field_type[]" value="' + escapeHtml(item.type) + '">' +
                '<input type="hidden" name="custom_field_rows[]" value="' + escapeHtml(item.rows) + '"></div>' +
                '<input type="hidden" name="custom_field_required[]" value="' + escapeHtml(item.required ? item.required : 'required') + '"></div>';
        });
    }

    renderPeriods();
});