import React, { Component } from 'react';
import { connect } from "react-redux";
import { updateTriggerName, updateTriggerType, addResponse, updateTriggerContent, saveTrigger } from "../actions/nodeGroupTriggerActions"
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
        this.state = {dataChanged : false, value : ''};

        this.handleChange = this.handleChange.bind(this);
        this.handleTypeChange = this.handleTypeChange.bind(this);
        this.handleContentChange = this.handleContentChange.bind(this);
        this.addResponse = this.addResponse.bind(this);
        this.saveResponse = this.saveResponse.bind(this);
    }

    handleChange(e) {
        this.setState({dataChanged : true});
        const name = e.target.value;
        this.props.dispatch(updateTriggerName(this.props.currenttrigger.get('currenttrigger').set('name',name)));
    }

    saveResponse() {
        this.setState({dataChanged : false});
        this.props.dispatch(saveTrigger(this.props.currenttrigger.get('currenttrigger')));
    }

    handleTypeChange(obj) {
        this.setState({dataChanged : true});
        this.props.dispatch(updateTriggerType(obj));
    }

    handleContentChange(obj) {
        this.setState({dataChanged : true});
        this.props.dispatch({'type' : 'HANDLE_CONTENT_CHANGE','payload' : obj});
    }

    addResponse() {
        this.setState({dataChanged : true});
        this.props.dispatch({'type' : 'ADD_TRIGGER_RESPONSE'});
    }

    render() {

        var actions = [];
        if (this.props.currenttrigger.get('currenttrigger').has('actions')) {
            actions = this.props.currenttrigger.get('currenttrigger').get('actions').map((action, index) => {
                if (action.get('type') == 'text') {
                    return <NodeTriggerActionText key={index} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} />
                } else if (action.get('type') == 'list') {
                    return <NodeTriggerActionList key={index} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} />
                }
            });
        }

        if (this.props.currenttrigger.hasIn(['currenttrigger','name']))
        {
            return (
                    <div>
                    <input className="form-control gbot-group-name" value={this.props.currenttrigger.getIn(['currenttrigger','name'])} onChange={this.handleChange} />
                    <hr/>
                    {actions}
                    <a className="btn btn-info btn-sm" onClick={this.addResponse} >Add response</a>
                    <hr/>
                    <a className="btn btn-success btn-sm" disabled={!this.state.dataChanged} onClick={this.saveResponse} >Save</a>
                    </div>
            );
        } else {
            return (<p>Choose a trigger</p>);
        }
    }
}

export default NodeTriggerBuilder;
