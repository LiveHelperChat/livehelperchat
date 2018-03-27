import React, { Component } from 'react';
import NodeGroup from './NodeGroup';
import { connect } from "react-redux"

import { fetchNodeGroups, addNodeGroup, updateNodeGroup } from "../actions/nodeGroupActions"

@connect((store) => {
    return {
        nodegroups: store.nodegroups.nodegroups
    };
})

class NodeGroups extends Component {

    componentWillMount() {
        this.props.dispatch(fetchNodeGroups())
    }

    addGroup() {
        this.props.dispatch(addNodeGroup())
    }

    changeTitle(obj) {
        this.props.dispatch(updateNodeGroup(obj))
     }

    render() {
        const { nodegroups } = this.props;

        const mappedNodeGroups = this.props.nodegroups.map(nodegroup =><NodeGroup changeTitle={this.changeTitle.bind(this)} key={nodegroup.id} group={nodegroup} />)

        return (
            <div>
                <p>Node group data</p>
                {mappedNodeGroups}
                <button className="btn btn-default" onClick={this.addGroup.bind(this)}>Add group</button>
            </div>
        );
    }
}

export default NodeGroups;
