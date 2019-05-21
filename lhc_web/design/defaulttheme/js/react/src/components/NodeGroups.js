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

    importGroup() {
        lhc.revealModal({'iframe':true,'height':500,'url':WWW_DIR_JAVASCRIPT + '/genericbot/botimportgroup/' + this.props.botId});
    }

    changeTitle(obj) {
        this.props.dispatch(updateNodeGroup(obj))
    }

    render() {
        const mappedNodeGroups = this.props.nodegroups.get('nodegroups').map(nodegroup =><NodeGroup botId={this.props.botId} changeTitle={this.changeTitle.bind(this)} key={nodegroup.get('id')} group={nodegroup} />);

        return (
            <div>
                {mappedNodeGroups}
                <hr/>
                <div className="btn-group" role="group" aria-label="Basic example">
                    <button className="btn btn-sm btn-secondary" onClick={this.addGroup.bind(this)}><i class="material-icons">add</i>Add group</button>
                    <button className="btn btn-sm btn-secondary" onClick={this.importGroup.bind(this)}><i class="material-icons">cloud_upload</i>Import group</button>
                </div>
            </div>
        );
    }
}

export default NodeGroups;