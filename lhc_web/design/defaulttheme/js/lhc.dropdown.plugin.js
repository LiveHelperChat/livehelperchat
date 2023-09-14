$.fn.makeDropdown = function(paramsDropdown) {
    var filterInput = this.find('.btn-block-department-filter > input');

    this.on("click", "[data-stopPropagation]", function(e) {
        e.stopPropagation();
    })

    var selectedItems = [];

    var _this = this;

    _this.each(function () {

        var limitMax = $(this).attr('data-limit') ? parseInt($(this).attr('data-limit')) : 0;

        var selectedItems = $(this).find('.selected-items-filter');

        $(this).find('.btn-department-dropdown').attr('data-text',$(this).find('.btn-department-dropdown').text());

        var _thisItem = $(this);

        _thisItem.find('.btn-department-dropdown').click(function(){
            if ($(this).hasClass('show')){
                _thisItem.find('.btn-block-department-filter > input').focus();
            }
        });

        var itemsSelectedCount = selectedItems.find('.delete-item').length;

        if (itemsSelectedCount > 0) {
            var defaultSelectedText = itemsSelectedCount == 1 ? selectedItems.find('.delete-item').first().parent().text().trim().replace('delete','') : '';
            $(this).find('.btn-department-dropdown').text((itemsSelectedCount == 1 ? defaultSelectedText : '['+itemsSelectedCount+'] ') + (itemsSelectedCount != 1 ? $(this).find('.btn-department-dropdown').attr('data-text') : ''));
        }

        var selectedItemsRadio = $(this).find('li input:checked');

        if (selectedItemsRadio.length == 1) {
            $(this).find('.btn-department-dropdown').text(selectedItemsRadio.first().parent().text());
        }

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
                selectedItems.find('.delete-item').parent().remove();
                selectedItems.prepend('<div class="fs12"><a data-stoppropagation="true" class="delete-item" data-value="' + $(this).val() + '"><input type="hidden" value="' + $(this).val() + '" name="'+_thisItem.find('.btn-block-department-filter > input').attr('data-scope')+(limitMax == 0 || limitMax > 1 ? '[]' : '')+'" /><i class="material-icons chat-unread">delete</i>' + $(this).parent().text().trim() + "</a></div>");
            }
        })

        $(this).on('click','.delete-item',function () {
            var itemDelete = _thisItem.find('input[value='+$(this).attr('data-value')+']');
            itemDelete.prop('checked',false);
            $(this).parent().remove();

            var itemsSelectedCount = _thisItem.find('li input:checked').length;

            if (itemsSelectedCount > 0) {
                _thisItem.find('.btn-department-dropdown').text('['+itemsSelectedCount+'] '+_thisItem.find('.btn-department-dropdown').attr('data-text'));
            } else {
                _thisItem.find('.btn-department-dropdown').text(_thisItem.find('.btn-department-dropdown').attr('data-text'));
            }

            if (paramsDropdown && paramsDropdown.on_delete) {
                paramsDropdown.on_delete(itemDelete);
            }
        });

        if (_thisItem.find('.btn-block-department-filter > input').attr('ajax-provider')) {
            _thisItem.find('.dropdown-result').scroll(function(e){
                if ((parseInt($(this)[0].scrollHeight) - parseInt($(this)[0].clientHeight)) == parseInt($(this).scrollTop())) {
                    ajaxScroll($(this).parent().find('.btn-block-department-filter > input'),$(this).find('li').length);
                }
            });
        }
    });

    // @todo add timout funtion
    var timeoutSearch = null;

    var ajaxScroll = function(itemElm, offset) {
        var parent = itemElm.parent().parent();
        var parentHolder = itemElm.parent();
        var typeElement = itemElm.parent().parent().parent().parent().parent().attr('data-type') ? itemElm.parent().parent().parent().parent().parent().attr('data-type') : 'checkbox';
        var noSelector = itemElm.parent().parent().parent().parent().parent().attr('data-noselector') ? true : false;
        var limitMax = itemElm.parent().parent().parent().parent().parent().attr('data-limit') ? parseInt(itemElm.parent().parent().parent().parent().parent().attr('data-limit')) : 0;

        $.getJSON(WWW_DIR_JAVASCRIPT + 'chat/searchprovider/' + itemElm.attr('ajax-provider') + '/?q=' + encodeURIComponent(itemElm.val()) + (offset ? '&offset=' + parseInt(offset) : ''), function(data) {
            var append = '';
            data.items.forEach(function(item) {
                var isSelected = parentHolder.find('.delete-item[data-value="' + item.id + '"]').length == 1;
                append += '<li class="search-option-item" data-stoppropagation="true"><label><input type="' + typeElement +'" '+(isSelected ? ' checked="checked" ' : '')+' name="'+(noSelector === true ? '' : 'selector-')+itemElm.attr('data-scope')+(limitMax == 0 || limitMax > 1 ? '[]' : '')+'" value="'+item.id+'"> ' + item.name +'</label></li>';
            });
            if (!offset) {
                parent.find('.search-option-item').remove();
            }
            parent.find('.dropdown-result > .dropdown-lhc').append(append);
        })
    }

    filterInput.keyup(function() {
        if ($(this).attr('ajax-provider')) {
            ajaxScroll($(this));
        } else {
            var filter = $(this).val();
            $(this).parent().parent().find('li.dropdown-result > ul').children('li').each(function(i) {
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