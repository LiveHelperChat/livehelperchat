import React, { useEffect, useState, useReducer } from "react";
import axios from "axios";

var timeoutCannedMessage = null;

const CannedMessages = props => {
    const [data, setData] = useState([]);
    const [isLoaded, setLoaded] = useState(false);
    const [ignored, forceUpdate] = useReducer(x => x + 1, 0);

    const getRootCategory = () => {
        if (!isLoaded) {
            axios.get(WWW_DIR_JAVASCRIPT  + "cannedmsg/filter/"+props.chatId).then(result => {
                setData(result.data);
                setLoaded(true);
                renderPreview(null);
            });
        }
    }

    const expandCategory = (categoryUpdate, indexUpdate) => {
        categoryUpdate.expanded = !categoryUpdate.expanded;
        setData(data.map((category, index) => (indexUpdate == index ? categoryUpdate : category)));
    }

    const fillMessage = (message) => {
        document.getElementById('CSChatMessage-'+props.chatId).value = message.msg;
        document.getElementById('CSChatMessage-'+props.chatId).focus();
        renderPreview(message);
    }

    const fillAndSend = (message) => {
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
        },100);
    }

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
                        document.getElementById('CSChatMessage-' + props.chatId).value = message.msg;
                        document.getElementById('CSChatMessage-' + props.chatId).focus();
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
            } else {
                data.map((val, index, array) => (
                    array[array.length - 1 - index].messages.map((messageValue, indexMessages, arrayMessages) => {
                            let message = arrayMessages[arrayMessages.length - 1 - indexMessages]
                            if (messageSet == true) {
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
            } else {
                data.map((item, index) => {
                    item.messages.map(message => {
                        if (messageSet == true) {
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
            <div className="col-6">

                <input type="text" onFocus={getRootCategory} className="form-control form-control-sm" onKeyUp={(e) => applyFilter(e, true)} onKeyDown={(e) => applyFilter(e, false)} defaultValue="" placeholder="Type to search"/>

                {!isLoaded &&
                    <p className="border mt-1"><a className="fs13" onClick={getRootCategory}><span className="material-icons">expand_more</span> Canned messages</a></p>
                }
                {isLoaded &&
                    <ul className="list-unstyled fs13 border mt-1">
                        {data.map((item, index) => (
                        <li><a className="font-weight-bold" key={index} onClick={() => expandCategory(item, index)}><span className="material-icons">{item.expanded ? 'expand_less' : 'expand_more'}</span>{item.title} [{item.messages.length}]</a>
                            {item.expanded &&
                            <ul className="list-unstyled ml-4">
                                {item.messages.map(message => (
                                    <li key={message.id} className={message.current ? 'font-italic font-weight-bold' : ''}>
                                        <a title="Send instantly" onClick={(e) => fillAndSend(message)}><span className="material-icons fs12">send</span></a><a title={message.msg} onClick={(e) => fillMessage(message)}>{message.message_title}</a>
                                    </li>
                                ))}
                            </ul>}
                        </li>
                        ))}
                    </ul>
                }
            </div>
            <div className="col-6 mx300" id={'chat-render-preview-'+props.chatId}>

            </div>
        </React.Fragment>
    );
}

export default CannedMessages