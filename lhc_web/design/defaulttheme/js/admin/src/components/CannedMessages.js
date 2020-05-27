import React, { useEffect, useState, useReducer } from "react";
import axios from "axios";

var timeoutCannedMessage = null;

const CannedMessages = props => {
    const [data, setData] = useState([]);
    const [isLoaded, setLoaded] = useState(false);
    const [ignored, forceUpdate] = useReducer(x => x + 1, 0);
    const [isCollapsed, setCollapsed] = useState(true);

    const getRootCategory = () => {
        if (!isLoaded) {
            axios.get(WWW_DIR_JAVASCRIPT  + "cannedmsg/filter/" + props.chatId).then(result => {
                setCollapsed(false);
                setData(result.data);
                setLoaded(true);

                result.data.map((item, index) => {
                    item.messages.map(message => {
                        if (message.current == true) {
                            renderPreview(message);
                        }
                    })
                });

            });
        }
    }

    const expandCategory = (categoryUpdate, indexUpdate) => {
        categoryUpdate.expanded = !categoryUpdate.expanded;
        setData(data.map((category, index) => (indexUpdate == index ? categoryUpdate : category)));
    }

    const fillMessage = (message) => {
        let element = document.getElementById('CSChatMessage-'+props.chatId);
        element.value = message.msg;
        element.focus();
        renderPreview(message);
    }

    const fillAndSend = (message,e) => {

        if (typeof e !== 'undefined') {
            e.stopPropagation();
            e.preventDefault();
        }

        setTimeout(() => {
            const formData = new FormData();
            formData.append('msg', message.msg);
            axios.post(WWW_DIR_JAVASCRIPT  + 'chat/addmsgadmin/' + props.chatId, formData,{
                headers: {'X-CSRFToken': confLH.csrf_token}
            }).then(result => {
                if (LHCCallbacks.addmsgadmin) {
                    LHCCallbacks.addmsgadmin(props.chatId);
                };
                ee.emitEvent('chatAddMsgAdmin', [props.chatId]);
                lhinst.syncadmincall();
                return true;
            });
        }, message.delay);
    }

    const mouseOver = (message) => {
        renderPreview(message);
    }

    const mouseLeave = (message) => {
        renderPreview(null);
        data.map((item, index) => {
            item.messages.map(message => {
                if (message.current == true) {
                    renderPreview(message);
                }
            })
        });
    }

    const renderPreview = (message) => {
        clearTimeout(timeoutCannedMessage);

        if (message === null) {
            document.getElementById('chat-render-preview-'+props.chatId).innerHTML = '';
            return;
        }

        let element = document.getElementById('chat-render-preview-'+props.chatId);
        element.innerHTML = message.msg;

        const formData = new FormData();
        formData.append('msg', message.msg);
        formData.append('msg_body', true);

        timeoutCannedMessage = setTimeout(() => {
            axios.post(WWW_DIR_JAVASCRIPT + 'chat/previewmessage/', formData).then((result) => {
                element.innerHTML = result.data;
            });
        }, 100);
    }

    const isVisible = (lookInId, elementId, settings) => {
        let lookIn = document.getElementById(lookInId);
        let element = document.getElementById(elementId);
        return (lookIn.offsetHeight + lookIn.scrollTop) >= (element.offsetTop + settings.threshold) && (element.offsetTop > lookIn.scrollTop - settings.threshold)
    };

    useEffect(() => {
        data.map((item, index) => {
            item.messages.map(message => {
                if (message.current) {
                    let messageElement = document.getElementById('canned-msg-'+props.chatId+'-'+message.id);
                    if (messageElement != null && !isVisible('canned-list-'+props.chatId,'canned-msg-'+props.chatId+'-'+message.id,{threshold:10})) {
                        messageElement.scrollIntoView();
                    }
                }
            })
        });
    });

    useEffect(() => {

        function sendManualMessage(chatId, messageId) {
            if (props.chatId == chatId) {
                axios.get(WWW_DIR_JAVASCRIPT  + "cannedmsg/filter/" + props.chatId).then(result => {

                    if (!isLoaded) {
                        setData(result.data);
                        renderPreview(null);
                        setLoaded(true);
                    }

                    result.data.map((item, index) => {
                        item.messages.map(message => {
                            if (message.id == messageId) {
                                fillAndSend(message);
                            }
                        })
                    });
                });
            }
        }

        ee.addListener('sendCannedByMessageId',sendManualMessage)

        // Cleanup
        return function cleanup() {
            ee.removeListener('sendCannedByMessageId', sendManualMessage);
        };

    },[]);

    const applyFilter = (e, doSearch) => {

        if ((e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40) && doSearch == true){
            e.preventDefault();
            e.stopPropagation();
            return;
        }

        if (e.keyCode == 13) {
            data.map((item, index) => (
                item.messages.map(message => {
                    if (message.current) {
                        let element = document.getElementById('CSChatMessage-' + props.chatId);
                        element.value = message.msg;
                        element.focus();
                        setCollapsed(true);
                    }
                })
            ));
            e.preventDefault();
            e.stopPropagation();
        } else if (e.keyCode == 38) { // Up

            var messageSet = false;

            // Very first is current message
            if (data[0]['messages'][0].current == true) {
                data[0]['messages'][0].current = false;
                let index = data.length - 1;
                data[index]['messages'][data[index]['messages'].length - 1].current = true;
                renderPreview(data[index]['messages'][data[index]['messages'].length - 1]);

                if (!data[index].expanded) {
                    data[index].expanded = true;
                }
            } else {
                data.map((val, index, array) => (
                    array[array.length - 1 - index].messages.map((messageValue, indexMessages, arrayMessages) => {
                            let message = arrayMessages[arrayMessages.length - 1 - indexMessages]
                            if (messageSet == true) {

                                if (!array[array.length - 1 - index].expanded) {
                                    array[array.length - 1 - index].expanded = true;
                                }

                                message.current = true;
                                messageSet = false;
                                renderPreview(message);
                            } else if (message.current) {
                                message.current = false;
                                messageSet = true;
                            }
                        }
                    )
                ));
            }

            setData(data);
            forceUpdate();
            e.preventDefault();
            e.stopPropagation();

        } else if (e.keyCode == 40) { // Down
            var messageSet = false;

            if (data[data.length -1]['messages'][ data[data.length -1]['messages'].length -1 ].current == true) {
                data[data.length -1]['messages'][ data[data.length -1]['messages'].length -1 ].current = false;
                data[0]['messages'][0].current = true;
                renderPreview(data[0]['messages'][0]);

                if (!data[0].expanded) {
                    data[0].expanded = true;
                }

            } else {
                data.map((item, index) => {
                    item.messages.map(message => {
                        if (messageSet == true) {
                            if (!item.expanded) {
                                item.expanded = true;
                            }
                            message.current = true;
                            renderPreview(message);
                            messageSet = false;
                        } else if (message.current) {
                            message.current = false;
                            messageSet = true;
                        }
                    })
                });
            }

            setData(data);
            forceUpdate();
            e.preventDefault();
            e.stopPropagation();

        } else if (doSearch === true) {
            axios.get(WWW_DIR_JAVASCRIPT  + "cannedmsg/filter/"+props.chatId + '?q=' + encodeURIComponent(e.target.value)).then(result => {
                setData(result.data);
                setCollapsed(false);
                renderPreview(null);
                result.data.map((item, index) => {
                    item.messages.map(message => {
                        if (message.current == true) {
                            renderPreview(message);
                        }
                    })
                });
            });
        }
    }

    return (
        <React.Fragment>
            <div className="col-12 col-xl-6">

                {!isLoaded &&
                <p className="border mt-0 pb-1 pt-1"><a className="fs13 d-block" onClick={getRootCategory}><span className="material-icons">expand_more</span>Canned messages</a></p>
                }

                {isLoaded && isCollapsed && <ul className="list-unstyled fs13 border mt-0 mx300">
                    <li className="pt-1 pb-1"><a className="d-block" onClick={(e) => setCollapsed(false)}><span className="material-icons">expand_more</span>Canned messages</a></li>
                </ul>}

                {isLoaded && !isCollapsed &&
                <ul className="list-unstyled fs13 border mt-0 mx300" id={'canned-list-'+props.chatId}>
                    <li className="border-bottom pt-1 pb-1"><a onClick={(e) => setCollapsed(true)}><span className="material-icons">expand_less</span>Canned messages</a></li>
                    {data.map((item, index) => (
                        <li><a className="font-weight-bold" key={index} onClick={() => expandCategory(item, index)}><span className="material-icons">{item.expanded ? 'expand_less' : 'expand_more'}</span>{item.title} [{item.messages.length}]</a>
                            {item.expanded &&
                            <ul className="list-unstyled ml-4">
                                {item.messages.map(message => (
                                    <li key={message.id} className={message.current ? 'font-italic font-weight-bold' : ''} id={'canned-msg-'+props.chatId+'-'+message.id}>
                                        <a className="hover-canned d-block" onMouseLeave={(e) => mouseLeave(message)} onMouseEnter={(e) => mouseOver(message)} title={message.msg} onClick={(e) => fillMessage(message)}><span title="Send instantly" onClick={(e) => fillAndSend(message,e)} className="material-icons fs12">send</span> {message.message_title}</a>
                                    </li>
                                ))}
                            </ul>}
                        </li>
                    ))}
                </ul>
                }
            </div>
            <div className="col-12 col-xl-6">
                <input type="text" onFocus={getRootCategory} className="form-control form-control-sm" onKeyUp={(e) => applyFilter(e, true)} onKeyDown={(e) => applyFilter(e, false)} defaultValue="" placeholder="&#128269; Navigate with &#11139; and &#8629; Enter"/>
                {!isCollapsed && <div className="mx275 mh275 mt-1 break-words" id={'chat-render-preview-'+props.chatId}></div>}
            </div>
        </React.Fragment>
    );
}

export default CannedMessages