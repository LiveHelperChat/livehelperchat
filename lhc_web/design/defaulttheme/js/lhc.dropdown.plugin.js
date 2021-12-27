$.fn.makeDropdown = function() {
    var filterInput = this.find('.btn-block-department-filter > input');

    this.click(function(){
        setTimeout(function(){
            filterInput.focus();
        },50);
    })

    this.on("click", "[data-stopPropagation]", function(e) {
        e.stopPropagation();
    })

    var limitMax = this.attr('data-limit') ? parseInt(this.attr('data-limit')) : 0;

    var selectedItems = [];

    var _this = this;

    _this.each(function () {
        var selectedItems = $(this).find('.selected-items-filter');

        $(this).find('.btn-department-dropdown').attr('data-text',$(this).find('.btn-department-dropdown').text());

        var itemsSelectedCount = selectedItems.find('.delete-item').length;

        if (itemsSelectedCount > 0) {
            var defaultSelectedText = itemsSelectedCount == 1 ? selectedItems.find('.delete-item').first().parent().text().trim().replace('delete','') : '';
            $(this).find('.btn-department-dropdown').text((itemsSelectedCount == 1 ? defaultSelectedText : '['+itemsSelectedCount+'] ') + (itemsSelectedCount != 1 ? $(this).find('.btn-department-dropdown').attr('data-text') : ''));
        }

        var selectedItemsRadio = $(this).find('li input:checked');

        if (selectedItemsRadio.length == 1) {
            $(this).find('.btn-department-dropdown').text(selectedItemsRadio.first().parent().text());
        }

        var _thisItem = $(this);

        _thisItem.on("change","li input:checkbox",function() {
            var itemsSelectedCount = 0;
            var singleSelectedText = '';

            // We want to keep presently checked item always
            var presentId = 0;
            if ($(this).is(':checked')) {
                if (selectedItems.find('.delete-item[data-value="'+$(this).val()+'"]').length == 0) {
                    selectedItems.prepend('<div class="fs12"><a data-stoppropagation="true" class="delete-item" data-value="' + $(this).val() + '"><input type="hidden" value="' + $(this).val() + '" name="'+_thisItem.find('.btn-block-department-filter > input').attr('data-scope')+(limitMax == 0 || limitMax > 1 ? '[]' : '')+'" /><i class="material-icons chat-unread">delete</i>' + $(this).parent().text().trim() + "</a></div>");
                }
                if (limitMax > 0) {
                    selectedItems.find('.delete-item:gt('+(limitMax - 1)+')').each(function(){
                        $(this).parent().remove();
                        $('.search-option-item > label > input[value='+$(this).attr('data-value')+']').prop('checked',false);
                    });
                }
            } else {
                selectedItems.find('.delete-item[data-value="'+$(this).val()+'"]').remove();
            }

            itemsSelectedCount = selectedItems.find('.delete-item').length;
            singleSelectedText = itemsSelectedCount == 1 ? selectedItems.find('.delete-item').first().parent().text().trim().replace('delete','') : '';

            if (itemsSelectedCount > 0) {
                _thisItem.find('.btn-department-dropdown').text((itemsSelectedCount == 1 ? singleSelectedText : '['+itemsSelectedCount+'] ')+ (itemsSelectedCount != 1 ? _thisItem.find('.btn-department-dropdown').attr('data-text') : ''));
            } else {
                _thisItem.find('.btn-department-dropdown').text(_thisItem.find('.btn-department-dropdown').attr('data-text'));
            }
        });

        _thisItem.on("change","li input:radio",function() {
            if ($(this).is(':checked')) {
                _thisItem.find('.btn-department-dropdown').text($(this).parent().text());
            }
        })

        $(this).on('click','.delete-item',function () {
            _thisItem.find('input[value='+$(this).attr('data-value')+']').prop('checked',false);
            $(this).parent().remove();

            var itemsSelectedCount = _thisItem.find('li input:checked').length;

            if (itemsSelectedCount > 0) {
                _thisItem.find('.btn-department-dropdown').text('['+itemsSelectedCount+'] '+_thisItem.find('.btn-department-dropdown').attr('data-text'));
            } else {
                _thisItem.find('.btn-department-dropdown').text(_thisItem.find('.btn-department-dropdown').attr('data-text'));
            }
        });
    });

    // @todo add timout funtion
    var timeoutSearch = null;

    filterInput.keyup(function() {
        if ($(this).attr('ajax-provider')) {
            var parent = $(this).parent().parent();
            var parentHolder = $(this).parent();
            $.getJSON(WWW_DIR_JAVASCRIPT + 'chat/searchprovider/' + $(this).attr('ajax-provider') + '/?q=' + encodeURIComponent($(this).val()), function(data) {
                var append = '';
                data.items.forEach(function(item) {
                    var isSelected = parentHolder.find('.delete-item[data-value="' + item.id + '"]').length == 1;
                    append += '<li class="search-option-item" data-stoppropagation="true"><label><input type="checkbox" '+(isSelected ? ' checked="checked" ' : '')+' name="selector-'+data.props.list_id+'[]" value="'+item.id+'"> ' + item.name +'</label></li>';
                });
                parent.find('.search-option-item').remove();
                parent.append(append);
            })
        } else {
            var filter = $(this).val();
            $(this).parent().parent().children('li').each(function(i) {
                if (i > 0) {
                    if (!$(this).text().toLowerCase().includes(filter) && filter != ''){
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                }
            });
        }
    });
};