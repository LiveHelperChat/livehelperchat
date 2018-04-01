import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';

class NodeTriggerActionList extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div>
                <hr/>
                <NodeTriggerActionType type={this.props.action.get('type')} />
                <p>Send list</p>
            </div>
        );
    }
}

export default NodeTriggerActionList;
