import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';

class NodeTriggerActionList extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    render() {
        return (
            <div>
                <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                <p>Send list</p>
                <hr/>
            </div>
        );
    }
}

export default NodeTriggerActionList;
