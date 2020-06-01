import React, { Component } from 'react';
import {fromJS} from 'immutable';

export default ({onChange, type}) => {
    var options = fromJS([
        {
            'value':'text',
            'text' : 'Send Text',
        },
        {
            'value':'attribute',
            'text' : 'Collect custom attribute',
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
            'value':'video',
            'text' : 'Send Video',
        },
        {
            'value':'generic',
            'text' : 'Send Carrousel',
        },
        {
            'value':'command',
            'text' : 'Update Current chat',
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
            'value': 'repeat_restrict',
            'text' : 'Restrict execution more than defined times',
        }
    ]);

    var options_advanced = fromJS([
        {
            'value':'collectable',
            'text' : 'Collect information',
        },
        {
            'value':'progress',
            'text' : 'Progress',
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
            'value': 'actions',
            'text' : 'Execute action',
        },
        {
            'value': 'restapi',
            'text' : 'Rest API',
        },
        {
            'value': 'tbody',
            'text' : 'Execute trigger body',
        }
    ]);

    var list = options.map((option, index) => <option key={index} value={option.get('value')}>{option.get('text')}</option>);

    var list_advanced = options_advanced.map((option, index) => <option key={index} value={option.get('value')}>{option.get('text')}</option>);

    return (
        <div className="row">
            <div className="col-6">
            Response type
            </div>
            <div className="col-6">
                <div className="form-group">
                    <select onChange={(e) => onChange(e)} className="form-control form-control-sm" defaultValue={type}>
                        <optgroup label="Basic">
                            {list}
                        </optgroup>
                        <optgroup label="Advanced">
                            {list_advanced}
                        </optgroup>
                    </select>
                </div>
            </div>
        </div>
    );
}
