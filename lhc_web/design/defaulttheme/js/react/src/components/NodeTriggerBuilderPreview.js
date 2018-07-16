import React, { Component } from 'react';
import { connect } from "react-redux";
import { updateTriggerName, updateTriggerType, addResponse } from "../actions/nodeGroupTriggerActions"
import NodeTriggerActionTextPreview from './preview/NodeTriggerActionTextPreview';
import NodeTriggerActionListPreview from './preview/NodeTriggerActionListPreview';
import NodeTriggerActionButtonsPreview from './preview/NodeTriggerActionButtonsPreview';
import NodeTriggerActionGenericPreview from './preview/NodeTriggerActionGenericPreview';
import NodeTriggerActionPredefinedPreview from './preview/NodeTriggerActionPredefinedPreview';
import NodeTriggerActionTypingPreview from './preview/NodeTriggerActionTypingPreview';
import NodeTriggerActionVideoPreview from './preview/NodeTriggerActionVideoPreview';


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
                    return <NodeTriggerActionTextPreview key={index+'-'+this.props.currenttrigger.get('currenttrigger').get('id')} id={index} action={action} />
                } else if (action.get('type') == 'list') {
                    return <NodeTriggerActionListPreview key={index+'-'+this.props.currenttrigger.get('currenttrigger').get('id')} id={index} action={action} />
                } else if (action.get('type') == 'buttons') {
                    return <NodeTriggerActionButtonsPreview key={index+'-'+this.props.currenttrigger.get('currenttrigger').get('id')} id={index} action={action} />
                } else if (action.get('type') == 'generic') {
                    return <NodeTriggerActionGenericPreview key={index+'-'+this.props.currenttrigger.get('currenttrigger').get('id')} id={index} action={action} />
                } else if (action.get('type') == 'predefined') {
                    return <NodeTriggerActionPredefinedPreview key={index+'-'+this.props.currenttrigger.get('currenttrigger').get('id')} id={index} action={action} />
                } else if (action.get('type') == 'typing') {
                    return <NodeTriggerActionTypingPreview key={index+'-'+this.props.currenttrigger.get('currenttrigger').get('id')} id={index} action={action} />
                } else if (action.get('type') == 'video') {
                    return <NodeTriggerActionVideoPreview key={index+'-'+this.props.currenttrigger.get('currenttrigger').get('id')} id={index} action={action} />
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
