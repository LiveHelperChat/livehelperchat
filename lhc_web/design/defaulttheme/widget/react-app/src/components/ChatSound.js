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
            if (e.sound_on === true) {
                var sound = new Howl({
                    src: [
                        window.lhcChat['base_url'] + "/widgetrestapi/loadsound/new_message_mp3",
                        window.lhcChat['base_url'] + "/widgetrestapi/loadsound/new_message_ogg",
                        window.lhcChat['base_url'] + "/widgetrestapi/loadsound/new_message_wav"],
                    format: ['mp3', 'ogg', 'wav'],
                    autoplay : true
                });
            }

            if (e.widget_open == false) {
                helperFunctions.sendMessageParent('unread_message',[{'type' : 'unread_message'}]);
            }
        }
    }

    render() {
        return null;
    }
}

export default connect()(ChatSound)

