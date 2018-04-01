import React, { Component } from 'react';
import NodeGroupTrigger from './NodeGroupTrigger';
import { connect } from "react-redux";

import { fetchNodeGroupTriggers } from "../actions/nodeGroupTriggerActions"

@connect((store) => {
    return {
        nodegrouptriggers: store.nodegrouptriggers
    };
})

class NodeGroup extends Component {

    handleChange(e) {
        const name = e.target.value;
        this.props.changeTitle({id : this.props.group.get('id'), name:name});
    }

    componentWillMount() {
        this.props.dispatch(fetchNodeGroupTriggers(this.props.group.get('id')))
    }

    shouldComponentUpdate(nextProps, nextState) {

        if (this.props.group !== nextProps.group) {
            return true;
        }

        if (nextProps.nodegrouptriggers !== this.props.nodegrouptriggers)
        {
            return true;
        }

        return false;
    }

    render() {

        if (this.props.nodegrouptriggers.get('nodegrouptriggers').has(this.props.group.get('id'))) {
            var mappedNodeGroups = this.props.nodegrouptriggers.get('nodegrouptriggers').get(this.props.group.get('id')).map(nodegrouptrigger =><NodeGroupTrigger key={nodegrouptrigger.get('id')} trigger={nodegrouptrigger}  />);
        } else {
            var mappedNodeGroups = "";
        }

        return (

            <div className="row">
                <div className="col-xs-12">
                    <hr/>
                    <input className="form-control gbot-group-name" value={this.props.group.get('name')} onChange={this.handleChange.bind(this)} />
                    <ul className="gbot-trglist">{mappedNodeGroups}</ul>
                </div>
            </div>
        );
    }
}

export default NodeGroup;
