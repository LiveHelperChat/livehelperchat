import React, { Component } from 'react';
import { connect } from "react-redux";
import { updateTriggerName } from "../actions/nodeGroupTriggerActions"
import NodeTriggerActionText from './builder/NodeTriggerActionText';
import NodeTriggerActionList from './builder/NodeTriggerActionList';


@connect((store) => {
    return {
        currenttrigger: store.currenttrigger
    };
})

class NodeTriggerBuilder extends Component {

    constructor(props) {
        super(props);
        this.state = {value: ''};
        this.handleChange = this.handleChange.bind(this);
    }

    handleChange(e) {
        const name = e.target.value;
        this.props.dispatch(updateTriggerName(this.props.currenttrigger.get('currenttrigger').set('name',name)));
    }

    render() {

        if (this.props.currenttrigger.get('currenttrigger').has('name')){
            this.state.value = this.props.currenttrigger.get('currenttrigger').get('name');
        }

        var actions = [];
        if (this.props.currenttrigger.get('currenttrigger').has('actions')) {
            actions = this.props.currenttrigger.get('currenttrigger').get('actions').map(function(action, index) {
                if (action.get('type') == 'text') {
                    return <NodeTriggerActionText key={index} action={action} />
                } else if (action.get('type') == 'list') {
                    return <NodeTriggerActionList key={index} action={action} />
                }
            });
        }

        return (
            <div>
                <input className="form-control gbot-group-name" value={this.state.value} onChange={this.handleChange} />
                {actions}
            </div>

        );
    }
}

export default NodeTriggerBuilder;
