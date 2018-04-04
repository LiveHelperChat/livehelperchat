import React, { Component } from 'react';
import { connect } from "react-redux";
import { updateTriggerName, updateTriggerType, addResponse } from "../actions/nodeGroupTriggerActions"
import NodeTriggerActionTextPreview from './preview/NodeTriggerActionTextPreview';
import NodeTriggerActionListPreview from './preview/NodeTriggerActionListPreview';


@connect((store) => {
    return {
        currenttrigger: store.currenttrigger
    };
})

class NodeTriggerBuilderPreview extends Component {

    constructor(props) {
        super(props);
    }

    render() {

        var actions = [];
        if (this.props.currenttrigger.get('currenttrigger').has('actions')) {
                actions = this.props.currenttrigger.get('currenttrigger').get('actions').map((action, index) => {
                    if (action.get('type') == 'text') {
                    return <NodeTriggerActionTextPreview key={index} id={index} action={action} />
                } else if (action.get('type') == 'list') {
                    return <NodeTriggerActionListPreview key={index} id={index} action={action} />
                }
            });
        }

        return (
            <div>
                {actions}
            </div>
        );
    }
}

export default NodeTriggerBuilderPreview;
