import React, { useEffect, useState } from "react";
import axios from "axios";

const CannedMessages = props => {
    const [data, setData] = useState([]);
    const [isLoaded, setLoaded] = useState(false);

    const getRootCategory = () => {
        if (!isLoaded) {
            axios.get(WWW_DIR_JAVASCRIPT  + "cannedmsg/filter/"+props.chatId).then(result => {
                setData(result.data);
                setLoaded(true);
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
    }

    const fillAndSend = (message) => {
        setTimeout(() => {
            const formData = new FormData();
            formData.append('msg', message.msg);
            axios.post(WWW_DIR_JAVASCRIPT  + 'chat/addmsgadmin/' + props.chatId, formData,{
                headers: {'X-CSRFToken': confLH.csrf_token}
            }).then(restul => {
                if (LHCCallbacks.addmsgadmin) {
                    LHCCallbacks.addmsgadmin(props.chatId);
                };
                ee.emitEvent('chatAddMsgAdmin', [props.chatId]);
                lhinst.syncadmincall();
                return true;
            });
        }, message.delay);
    }

    const applyFilter = (e) => {

        if (e.keyCode == 13) {
            data.map((item, index) => (
                item.messages.map(message => {
                    if (message.current) {
                        document.getElementById('CSChatMessage-' + props.chatId).value = message.msg;
                        document.getElementById('CSChatMessage-' + props.chatId).focus();
                    }
                })
            ));
        } else if (e.keyCode == 38) { // Up
            data.map((item, index) => (
                item.messages.map(message => {
                    if (message.current) {
                        message.current = false;
                    }
                })
            ));

        } else if (e.keyCode == 40) { // Down

            console.log(data);

            var messageSet = false;

            data.map((item, index) => {
                item.messages.map(message => {
                    if (messageSet == true) {
                        message.current = true;
                        messageSet = false;
                    } else if (message.current) {
                        message.current = false;
                        messageSet = true;
                    }
                })
            });
            
            setData(data);

            //var messageSet = false;

            /*var data = data.map((item, index) => {
                    console.log(item)
            }*/

                /*item.messages.map(message => {
                    if (messageSet == true) {
                        message.current = true;
                        messageSet = false;
                    } else if (message.current) {
                        message.current = false;
                        messageSet = true;
                    }
                })*/
            //);

            console.log(data);


        } else {
            axios.get(WWW_DIR_JAVASCRIPT  + "cannedmsg/filter/"+props.chatId + '?q=' + encodeURIComponent(e.target.value)).then(result => {
                setData(result.data);
            });
        }
    }

    return (
        <React.Fragment>
            <div className="col-6">

                <input type="text" onFocus={getRootCategory} className="form-control form-control-sm" onKeyUp={(e) => applyFilter(e)} defaultValue="" placeholder="Type to search"/>

                {!isLoaded &&
                    <p className="border mt-1"><a className="fs13" onClick={getRootCategory}><span className="material-icons">expand_more</span> Canned messages</a></p>
                }
                {isLoaded &&
                    <ul className="list-unstyled fs13 border mt-1">
                        {data.map((item, index) => (
                        <li><a className="font-weight-bold" onClick={() => expandCategory(item, index)}><span className="material-icons">{item.expanded ? 'expand_less' : 'expand_more'}</span>{item.title} [{item.messages.length}]</a>
                            {item.expanded &&
                            <ul className="list-unstyled ml-4">
                                {item.messages.map(message => (
                                    <li className={message.current ? 'font-italic font-weight-bold' : ''}>
                                        <a title="Send instantly" onClick={(e) => fillAndSend(message)}><span className="material-icons fs12">send</span></a><a title={message.msg} onClick={(e) => fillMessage(message)}>{message.message_title}</a>
                                    </li>
                                ))}
                            </ul>}
                        </li>
                        ))}
                    </ul>
                }
            </div>
            <div className="col-6">
                Preview rendered...
            </div>
        </React.Fragment>
    );
}

export default CannedMessages