import React, { Component } from 'react';
import {fromJS} from 'immutable';

export default ({onChange, type}) => {
    var options = fromJS([
        {
            'value':'text',
            'text' : 'Send Text',
        },
        {
            'value':'collectable',
            'text' : 'Collect information',
        },
        {
            'value':'buttons',
            'text' : 'Button list',
        },
        {
            'value':'list',
            'text' : 'Send List',
        },
        {
            'value':'predefined',
            'text' : 'Send predefined block',
        },
        {
            'value':'typing',
            'text' : 'Send Typing',
        },
        {
            'value':'progress',
            'text' : 'Progress',
        },
        {
            'value':'video',
            'text' : 'Send Video',
        },/*,
        {
            'value':'audio',
            'text' : 'Send Audio',
        },
        {
            'value':'file',
            'text' : 'Send File',
        },,*/
        {
            'value':'generic',
            'text' : 'Send Carrousel',
        },
        {
            'value':'command',
            'text' : 'Update Current chat',
        },
        {
            'value':'attribute',
            'text' : 'Collect custom attribute',
        },
        {
            'value': 'actions',
            'text' : 'Execute action',
        },
        {
            'value': 'intent',
            'text' : 'Intent detection',
        },
        {
            'value': 'intentcheck',
            'text' : 'Check for pending intentions',
        },
        {
            'value': 'conditions',
            'text' : 'Check for conditions to proceed',
        },
        {
            'value': 'match_actions',
            'text' : 'Search for default actions on message',
        },
        {
            'value': 'event_type',
            'text' : 'Trigger to execute by response',
        },
        {
            'value': 'execute_js',
            'text' : 'Execute Javascript',
        },
        {
            'value': 'repeat_restrict',
            'text' : 'Restrict execution more than defined times',
        }
    ]);

    var list = options.map((option, index) => <option key={index} value={option.get('value')}>{option.get('text')}</option>);

    return (
        <div className="row">
            <div className="col-6">
            Response type
            </div>
            <div className="col-6">
                <div className="form-group">
                    <select onChange={(e) => onChange(e)} className="form-control form-control-sm" defaultValue={type}>
                        {list}
                    </select>
                </div>
            </div>
        </div>
    );
}
