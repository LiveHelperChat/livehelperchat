import React, { Component } from 'react';

import { fetchNodeGroupTriggerAction } from "../actions/nodeGroupTriggerActions"
import { connect } from "react-redux";

@connect((store) => {
    return {
        currenttrigger: store.currenttrigger
    };
})

class NodeGroupTrigger extends Component {

    constructor(props) {
        super(props);
        this.state = {isCurrent: false};
    }

    loadTriggerActions() {
        this.props.dispatch(fetchNodeGroupTriggerAction(this.props.trigger.get('id')))
    }

    shouldComponentUpdate(nextProps, nextState) {
        
        if (this.props.trigger !== nextProps.trigger) {
            return true;
        }

        if (nextProps.currenttrigger.get('currenttrigger').get('id') === this.props.trigger.get('id') && this.state.isCurrent == false) {
            this.state.isCurrent = true;
            return true;
        }

        if (nextProps.currenttrigger.get('currenttrigger').get('id') !== this.props.trigger.get('id') && this.state.isCurrent == true) {
            this.state.isCurrent = false;
            return true;
        }

        return false;
    }

    render() {

        var classNameCurrent = 'btn btn-default btn-xs';
        if (this.props.currenttrigger.get('currenttrigger').get('id') === this.props.trigger.get('id')) {
            classNameCurrent = 'btn btn-default btn-xs btn-success';
        }

        return (
                <li><a onClick={this.loadTriggerActions.bind(this)} className={classNameCurrent}>{this.props.trigger.get('name')}</a></li>
        );
    }
}

export default NodeGroupTrigger;
