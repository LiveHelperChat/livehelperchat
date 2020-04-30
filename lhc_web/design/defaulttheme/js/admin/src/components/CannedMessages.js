import React, { useEffect, useState } from "react";
import axios from "axios";

const CannedMessages = props => {
    const [data, setData] = useState([]);
    const [isLoaded, setLoaded] = useState(false);

    const getRootCategory = () => {
        axios.get(WWW_DIR_JAVASCRIPT  + "cannedmsg/filter/"+props.chatId).then(result => {
            //setData(result.data)
            setLoaded(true);
        });
    }

    /*useEffect(() => {
        axios
            .get("https://jsonplaceholder.typicode.com/users")
            .then(result => setData(result.data));
    }, []);*/

    //<?php $canned_options = erLhcoreClassModelCannedMsg::groupItems(erLhcoreClassModelCannedMsg::getCannedMessages($chat->dep_id, erLhcoreClassUser::instance()->getUserID()), $chat, erLhcoreClassUser::instance()->getUserData(true)); ?>
    //<?php include(erLhcoreClassDesign::designtpl('lhchat/part/canned_messages_options.tpl.php')); ?>

    // <?php include(erLhcoreClassDesign::designtpl('lhchat/part/send_delayed_canned_action.tpl.php')); ?>


    return (
        <React.Fragment>
            <div className="col-7">
                {!isLoaded &&
                    <p><button type="button" onClick={getRootCategory}>Load</button></p>
                }
                {isLoaded &&
                <p>Data was loaded</p>
                }
                <select class="form-control form-control-sm" name="CannedMessage-<?php echo $chat->id?>" id="id_CannedMessage-<?php echo $chat->id?>">

                </select>
            </div>
            <div className="col-3">
                <input type="text" class="form-control form-control-sm" id="id_CannedMessageSearch-<?php echo $chat->id?>" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Type to search')?>"/>
            </div>
            <div class="col-2 sub-action-chat" id="sub-action-chat-<?php echo $chat->id?>">
                <div className="row d-flex">
                    <div class="col pl-0 pr-2">
                        <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Fill textarea with canned message')?>" href="#" onclick="$('#CSChatMessage-<?php echo $chat->id?>').val(($('#id_CannedMessage-<?php echo $chat->id?>').val() > 0) ? $('#id_CannedMessage-<?php echo $chat->id?>').find(':selected').attr('data-msg') : '');return false;" class="btn btn-secondary w-100 btn-sm fill-editor-canned"><i class="material-icons mr-0">mode_edit</i></a>
                    </div>
                    <div className="col pl-0 pr-2">
                        <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Send delayed canned message instantly')?>" href="#" className="btn btn-secondary w-100 btn-sm send-delayed-canned" onClick="return lhinst.sendCannedMessage('<?php echo $chat->id?>',$(this))">
                            <i className="material-icons mr-0">mail</i>
                        </a>
                    </div>
                </div>
            </div>
        </React.Fragment>
    );

    /*return (
        <div>
            <ul>
                {data.map(item => (
                    <li key={item.username}>
                        {item.username}: {item.name}
                    </li>
                ))}
            </ul>
        </div>
    );*/
}

export default CannedMessages