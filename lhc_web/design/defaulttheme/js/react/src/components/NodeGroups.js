import React, { Component } from 'react';
import NodeGroup from './NodeGroup';
import { connect } from "react-redux"

import { fetchNodeGroups, addNodeGroup, updateNodeGroup } from "../actions/nodeGroupActions"

@connect((state) => {
    return {
        nodegroups: state.nodegroups
    };
})

class NodeGroups extends Component {

    componentWillMount() {
        this.props.dispatch(fetchNodeGroups(this.props.botId))
    }

    addGroup() {
        this.props.dispatch(addNodeGroup(this.props.botId))
    }

    changeTitle(obj) {
        this.props.dispatch(updateNodeGroup(obj))
    }

    render() {
        const mappedNodeGroups = this.props.nodegroups.get('nodegroups').map(nodegroup =><NodeGroup changeTitle={this.changeTitle.bind(this)} key={nodegroup.get('id')} group={nodegroup} />);

        return (
            <div>
                {mappedNodeGroups}
                <hr/>
                <button className="btn btn-secondary" onClick={this.addGroup.bind(this)}>Add group</button>
            </div>
        );
    }
}

export default NodeGroups;