import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';

class NodeTriggerActionText extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div>
                <hr/>
                <NodeTriggerActionType type={this.props.action.get('type')} />
                <p>Send text</p>
            </div>
        );
    }
}

export default NodeTriggerActionText;
