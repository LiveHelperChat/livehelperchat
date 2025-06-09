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

    componentDidMount() {
        this.props.dispatch(fetchNodeGroups(this.props.botId))
    }

    addGroup() {
        this.props.dispatch(addNodeGroup(this.props.botId))
    }

    importGroup() {
        lhc.revealModal({'iframe':true,'height':500,'url':WWW_DIR_JAVASCRIPT + '/genericbot/botimportgroup/' + this.props.botId});
    }

    flowchart() {
        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + '/genericbot/bot/' + this.props.botId + '/(type)/chart'});
    }

    changeTitle(obj) {
        this.props.dispatch(updateNodeGroup(obj))
    }

    render() {

        const mappedNodeGroups = this.props.nodegroups.get('nodegroups').sortBy(group => group.get('pos')).map(nodegroup =><NodeGroup triggerId={this.props.triggerId} botId={this.props.botId} changeTitle={this.changeTitle.bind(this)} key={nodegroup.get('id')} group={nodegroup} />);

        return (
            <div>
                {mappedNodeGroups}
                <hr/>
                <div className="btn-group" role="group" aria-label="Basic example">
                    <button className="btn btn-sm btn-secondary" onClick={this.addGroup.bind(this)}><i className="material-icons">add</i>Add group</button>
                    <button className="btn btn-sm btn-secondary" onClick={this.importGroup.bind(this)}><i className="material-icons">cloud_upload</i>Import group</button>
                    <button className="btn btn-sm btn-secondary" onClick={this.flowchart.bind(this)}><i className="material-icons">network_node</i>Flow chart</button>
                </div>
            </div>
        );
    }
}

export default NodeGroups;