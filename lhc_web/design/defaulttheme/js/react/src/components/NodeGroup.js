import React, { Component } from 'react';
import NodeGroupTrigger from './NodeGroupTrigger';
import { connect } from "react-redux";

import { fetchNodeGroupTriggers } from "../actions/nodeGroupTriggerActions"

@connect((store) => {
    return {
        nodegrouptriggers: store.nodegrouptriggers.nodegrouptriggers
    };
})

class NodeGroup extends Component {

    handleChange(e) {
        const name = e.target.value;
        this.props.changeTitle({id : this.props.group.id, name:name});
    }

    componentWillMount() {
        this.props.dispatch(fetchNodeGroupTriggers(this.props.group.id))
    }

    shouldComponentUpdate(nextProps, nextState) {

        if (this.props.group.name !== nextProps.group.name) {
            return true;
        }

        if (nextProps == null || (nextProps !== null && nextProps.nodegrouptriggers.length != this.props.nodegrouptriggers.length) )
        {
            return true;
        }

        return false;
    }

    render() {

        const { nodegrouptriggers } = this.props;

        if (typeof this.props.nodegrouptriggers[this.props.group.id] !== 'undefined'){
            var mappedNodeGroups = this.props.nodegrouptriggers[this.props.group.id].map(nodegrouptrigger =><NodeGroupTrigger key={nodegrouptrigger.id} trigger={nodegrouptrigger} />);
        } else {
            var mappedNodeGroups = "";
        }

        return (


            <div>
                <input className="form-control" value={this.props.group.name} onChange={this.handleChange.bind(this)} />

                {mappedNodeGroups}
            </div>
        );
    }
}

export default NodeGroup;
