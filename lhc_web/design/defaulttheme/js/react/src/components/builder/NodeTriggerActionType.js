import React, { Component } from 'react';
import {fromJS} from 'immutable';

class NodeTriggerActionType extends Component {

    constructor(props) {
        super(props);
    }

    render() {
       var options = fromJS([{
            'value':'text',
            'text' : 'Send Text',
           },
           {
            'value':'typing',
            'text' : 'Send Typing',
           },
           {
            'value':'predefined',
            'text' : 'Send predefined block',
           },
           {
            'value':'image',
            'text' : 'Send Image',
           },
           {
            'value':'video',
            'text' : 'Send Video',
           },
           {
            'value':'audio',
            'text' : 'Send Audio',
           },
            {
            'value':'file',
            'text' : 'Send File',
           },
            {
            'value':'button',
            'text' : 'Send Buttons',
           },
            {
            'value':'generic',
            'text' : 'Send Carrousel',
           },
           {
            'value':'list',
            'text' : 'Send List',
           },
           {
            'value':'command',
            'text' : 'Update Current Lead',
           }
        ]);
        var list = options.map((option, index) => <option key={index} value={option.get('value')}>{option.get('text')}</option>);

         return (
            <select defaultValue={this.props.type}>
                {list}
            </select>
       );
    }
}

export default NodeTriggerActionType;
