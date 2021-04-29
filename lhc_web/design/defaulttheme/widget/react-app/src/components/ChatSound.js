import React, { PureComponent } from 'react';
import {Howl, Howler} from 'howler';
import { storeSubscriber } from "../actions/chatActions"

import { connect } from "react-redux";
import { helperFunctions } from "../lib/helperFunctions";

class ChatSound extends PureComponent {

    state = {

    };

    constructor(props) {
        super(props);
        helperFunctions.eventEmitter.addListener('play_sound', (e) => this.playSound(e));
    }

    playSound = (e) => {
        if (e.type == 'new_message') {
            if (e.sound_on === true && (e.widget_open === false || (e.widget_open === true && window.lhcChat['is_focused'] == false))) {
                this.playSoundFile('new_message');
            }
            if (e.widget_open == false) {
                helperFunctions.sendMessageParent('unread_message',[{'msop': (e.msop || null), 'msg_body':(e.msg_body || null), 'type' : 'unread_message','otm' : (e.otm || 0)}]);
            }
        } else if (e.type == 'new_invitation' && e.sound_on === true) {
             if (helperFunctions.getSessionStorage('_invs') === null) {
                 helperFunctions.setSessionStorage('_invs',1);
                 this.playSoundFile('new_invitation');
             }
        } else if (e.type == 'new_chat' && e.sound_on === true) {
            this.playSoundFile('new_invitation');
        }

        if (window.lhcChat['is_focused'] == false) {
            helperFunctions.sendMessageParent('unread_message_title',[{'status':false}]);
        }

    }

    playSoundFile = (file) => {
        var sound = new Howl({
            src: [
                window.lhcChat['base_url'] + "/widgetrestapi/loadsound/"+file+"_mp3",
                window.lhcChat['base_url'] + "/widgetrestapi/loadsound/"+file+"_ogg",
                window.lhcChat['base_url'] + "/widgetrestapi/loadsound/"+file+"_wav"],
            format: ['mp3', 'ogg', 'wav'],
            autoplay : true
        });
    }

    render() {
        return null;
    }
}

export default connect()(ChatSound)

