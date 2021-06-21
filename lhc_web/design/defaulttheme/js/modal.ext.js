(function () {


    window.lhcHelperfunctions.eventEmitter.addListener('modal_ext.init', function (params, dispatch, getstate) {

        if (typeof params.cmd !== 'undefined' && params.cmd == 'close') {
            var elem = document.getElementById('modal_ext-modal');
            elem.parentNode.removeChild(elem);
            return;
        }

        function parseOptions() {

            var LHCChatOptions = window.parent.LHCChatOptions;

            argumentsQuery = new Array();
            var paramsReturn = '';
            if (typeof LHCChatOptions != 'undefined') {
                if (typeof LHCChatOptions.attr != 'undefined') {
                    if (LHCChatOptions.attr.length > 0) {
                        for (var index in LHCChatOptions.attr) {
                            if (typeof LHCChatOptions.attr[index] != 'undefined' && typeof LHCChatOptions.attr[index].type != 'undefined') {
                                argumentsQuery.push('name[]=' + encodeURIComponent(LHCChatOptions.attr[index].name) + '&encattr[]=' + (typeof LHCChatOptions.attr[index].encrypted != 'undefined' && LHCChatOptions.attr[index].encrypted == true ? 't' : 'f') + '&value[]=' + encodeURIComponent(LHCChatOptions.attr[index].value) + '&type[]=' + encodeURIComponent(LHCChatOptions.attr[index].type) + '&size[]=' + encodeURIComponent(LHCChatOptions.attr[index].size) + '&req[]=' + (typeof LHCChatOptions.attr[index].req != 'undefined' && LHCChatOptions.attr[index].req == true ? 't' : 'f') + '&sh[]=' + ((typeof LHCChatOptions.attr[index].show != 'undefined' && (LHCChatOptions.attr[index].show == 'on' || LHCChatOptions.attr[index].show == 'off')) ? LHCChatOptions.attr[index].show : 'b'));
                            }
                        }
                    }
                }


                if (typeof LHCChatOptions.attr_prefill != 'undefined') {
                    if (LHCChatOptions.attr_prefill.length > 0) {
                        for (var index in LHCChatOptions.attr_prefill) {
                            if (typeof LHCChatOptions.attr_prefill[index] != 'undefined' && typeof LHCChatOptions.attr_prefill[index].name != 'undefined') {
                                argumentsQuery.push('prefill[' + LHCChatOptions.attr_prefill[index].name + ']=' + encodeURIComponent(LHCChatOptions.attr_prefill[index].value));
                                if (typeof LHCChatOptions.attr_prefill[index].hidden != 'undefined') {
                                    argumentsQuery.push('hattr[]=' + encodeURIComponent(LHCChatOptions.attr_prefill[index].name));
                                }
                            }
                        }
                    }
                }


                if (typeof LHCChatOptions.attr_prefill_admin != 'undefined') {
                    if (LHCChatOptions.attr_prefill_admin.length > 0) {
                        for (var index in LHCChatOptions.attr_prefill_admin) {
                            if (typeof LHCChatOptions.attr_prefill_admin[index] != 'undefined') {
                                argumentsQuery.push('value_items_admin[' + LHCChatOptions.attr_prefill_admin[index].index + ']=' + encodeURIComponent(LHCChatOptions.attr_prefill_admin[index].value));

                                if (typeof LHCChatOptions.attr_prefill_admin[index].hidden != 'undefined') {
                                    argumentsQuery.push('via_hidden[' + LHCChatOptions.attr_prefill_admin[index].index + ']=' + encodeURIComponent(LHCChatOptions.attr_prefill_admin[index].hidden == true ? 't' : 'f'));
                                }


                                if (typeof LHCChatOptions.attr_prefill_admin[index].encrypted != 'undefined') {
                                    argumentsQuery.push('via_encrypted[' + LHCChatOptions.attr_prefill_admin[index].index + ']=' + encodeURIComponent(LHCChatOptions.attr_prefill_admin[index].encrypted == true ? 't' : 'f'));
                                }
                            }
                        }
                    }
                }

                if (argumentsQuery.length > 0) {
                    paramsReturn = '&' + argumentsQuery.join('&');
                }
            }

            var state = getstate();
            var js_args = [];
            var currentVar = null;

            var jsVars = state.chatwidget.get('jsVars');

            Object.keys(jsVars).forEach(key => {
                js_args.push('jsvar[' + key + ']=' + encodeURIComponent(jsVars[key]));
            })

            if (js_args.length > 0) {
                paramsReturn = paramsReturn + '&' + js_args.join('&');
            }

            if (state.chatwidget.hasIn(['chatData','id'])) {
                paramsReturn = paramsReturn + '&chat_id=' + state.chatwidget.getIn(['chatData','id']) + '&hash=' + state.chatwidget.getIn(['chatData','hash']);
            }

            return paramsReturn;
        }

        setTimeout(function () {
            if (document.querySelector(".modal-backdrop") === null) {
                // Build HTML
                var bodyPrepend = document.getElementById("root");
                var parent = document.createElement("div");
                parent.id = "modal_ext-modal";

                var args = parseOptions();

                parent.innerHTML = "<div class=\"fade modal-backdrop show\"></div><div role=\"dialog\" id=\"dialog-content\" aria-modal=\"true\" class=\"fade modal show d-block\" tabindex=\"-1\">\n" +
                    "    <div class=\"modal-dialog modal-lg h-100 d-flex m-0 p-1\">\n" +
                    "        <div class=\"modal-content\">\n" +
                    "            <div class=\"modal-body p-0\" id=\"modal_ext-body\"><iframe allowtransparency=\"true\" src=" + params['url'] + '?' + args + " frameBorder=\"0\" class=\"flex-grow-1 position-relative iframe-modal\"/>\n" +
                    "            </div>\n" +
                    "        </div>\n" +
                    "    </div>\n" +
                    "</div>"
                bodyPrepend.prepend(parent);
            }
        }, (typeof params['delay'] != 'undefined' ? parseInt(params['delay']) * 1000 : 0))
    });

})();
