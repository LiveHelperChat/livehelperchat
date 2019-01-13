import React, { Component } from 'react';
import { connect } from "react-redux";

@connect((store) => {
    return {
       nodegroups: store.nodegroups,
       nodegrouptriggers: store.nodegrouptriggers
    };
})

class NodeTriggerList extends Component {

    constructor(props) {
        super(props);
    }

    onChange(e) {
        this.props.onSetPayload(e.target.value);
    }

    render() {
        const mappedNodeGroups = this.props.nodegroups.get('nodegroups').map(nodegroup => <optgroup label={nodegroup.get('name')} key={nodegroup.get('id')}>
            {this.props.nodegrouptriggers.get('nodegrouptriggers').has(nodegroup.get('id')) &&
                this.props.nodegrouptriggers.get('nodegrouptriggers').get(nodegroup.get('id')).map(nodegrouptrigger =><option key={nodegrouptrigger.get('id')} value={nodegrouptrigger.get('id')} >{nodegrouptrigger.get('name')}</option>)
            }
        </optgroup>);

        return (
            <select className="form-control form-control-sm" onChange={this.onChange.bind(this)} value={this.props.payload}>
                <option value="">Choose a trigger</option>
                {mappedNodeGroups}
            </select>
        );
    }
}

export default NodeTriggerList;
