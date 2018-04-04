import React, { Component } from 'react';
import NodeGroupTrigger from './NodeGroupTrigger';
import { connect } from "react-redux";

import { fetchNodeGroupTriggers, addTrigger } from "../actions/nodeGroupTriggerActions"

@connect((store) => {
    return {
        nodegrouptriggers: store.nodegrouptriggers
    };
})

class NodeGroup extends Component {

    handleChange(e) {
        const name = e.target.value;
        this.props.changeTitle(this.props.group.set('name',name));
    }

    addTrigger() {
        this.props.dispatch(addTrigger({id: this.props.group.get('id')}));
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
            var mappedNodeGroupTriggers = this.props.nodegrouptriggers.get('nodegrouptriggers').get(this.props.group.get('id')).map(nodegrouptrigger =><NodeGroupTrigger key={nodegrouptrigger.get('id')} trigger={nodegrouptrigger}  />);
        } else {
            var mappedNodeGroupTriggers = "";
        }

        return (

            <div className="row">
                <div className="col-xs-12">
                    <hr/>
                    <input className="form-control gbot-group-name" value={this.props.group.get('name')} onChange={this.handleChange.bind(this)} />
                    <ul className="gbot-trglist">
                        {mappedNodeGroupTriggers}
                        <li><a className="btn btn-xs btn-info" onClick={this.addTrigger.bind(this)} >Add new</a></li>
                    </ul>
                </div>
            </div>
        );
    }
}

export default NodeGroup;
