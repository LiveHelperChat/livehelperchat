import React, { Component } from 'react';
import { connect } from "react-redux";
import { updateTriggerName, updateTriggerType, addResponse, updateTriggerContent, saveTrigger, initBot } from "../actions/nodeGroupTriggerActions"
import NodeTriggerActionText from './builder/NodeTriggerActionText';
import NodeTriggerActionList from './builder/NodeTriggerActionList';
import NodeTriggerActionCollectable from './builder/NodeTriggerActionCollectable';
import NodeTriggerActionButtons from './builder/NodeTriggerActionButtons';
import NodeTriggerActionCommand from './builder/NodeTriggerActionCommand';


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
        this.cancelChanges = this.cancelChanges.bind(this);
        this.addQuickReply = this.addQuickReply.bind(this);
        this.removeQuickReply = this.removeQuickReply.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.addSubelement = this.addSubelement.bind(this);
        this.deleteSubelement = this.deleteSubelement.bind(this);
        this.moveUpSubelement = this.moveUpSubelement.bind(this);
        this.moveDownSubelement = this.moveDownSubelement.bind(this);

        this.props.dispatch(initBot(this.props.botId));
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

    cancelChanges() {
        this.props.dispatch({'type':'CANCEL_TRIGGER', 'payload':this.props.currenttrigger.get('currenttrigger')});
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

    addQuickReply(obj) {
        this.setState({dataChanged : true});
        this.props.dispatch({'type' : 'HANDLE_ADD_QUICK_REPLY','payload' : obj});
    }

    removeQuickReply(obj) {
        this.setState({dataChanged : true});
        this.props.dispatch({'type' : 'HANDLE_ADD_QUICK_REPLY_REMOVE','payload' : obj});
    }

    removeAction(obj) {
        this.setState({dataChanged : true});
        this.props.dispatch({'type' : 'REMOVE_TRIGGER_RESPONSE','payload' : obj});
    }

    addSubelement(obj) {
        this.setState({dataChanged : true});
        this.props.dispatch({'type' : 'ADD_SUBELEMENT','payload' : obj});
    }

    deleteSubelement(obj) {
        this.setState({dataChanged : true});
        this.props.dispatch({'type' : 'REMOVE_SUBELEMENT','payload' : obj});
    }

    moveUpSubelement(obj) {
        this.setState({dataChanged : true});
        this.props.dispatch({'type' : 'MOVE_UP_SUBELEMENT','payload' : obj});
    }

    moveDownSubelement(obj) {
        this.setState({dataChanged : true});
        this.props.dispatch({'type' : 'MOVE_DOWN_SUBELEMENT','payload' : obj});
    }

    render() {

        var actions = [];
        if (this.props.currenttrigger.get('currenttrigger').has('actions')) {
            actions = this.props.currenttrigger.get('currenttrigger').get('actions').map((action, index) => {
                if (action.get('type') == 'text') {
                    return <NodeTriggerActionText addSubelement={this.addSubelement} deleteSubelement={this.deleteSubelement} key={index+'-'+this.props.currenttrigger.get('currenttrigger').get('id')} id={index} removeAction={this.removeAction} removeQuickReply={this.removeQuickReply} addQuickReply={this.addQuickReply} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} />
                } else if (action.get('type') == 'list') {
                    return <NodeTriggerActionList moveDownSubelement={this.moveDownSubelement} moveUpSubelement={this.moveUpSubelement} addSubelement={this.addSubelement} removeQuickReply={this.removeQuickReply} addQuickReply={this.addQuickReply} deleteSubelement={this.deleteSubelement} key={index+'-'+this.props.currenttrigger.get('currenttrigger').get('id')} id={index} removeAction={this.removeAction} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} />
                } else if (action.get('type') == 'collectable') {
                    return <NodeTriggerActionCollectable moveDownSubelement={this.moveDownSubelement} moveUpSubelement={this.moveUpSubelement} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} key={index+'-'+this.props.currenttrigger.get('currenttrigger').get('id')} id={index} removeAction={this.removeAction} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} />
                } else if (action.get('type') == 'buttons') {
                    return <NodeTriggerActionButtons moveDownSubelement={this.moveDownSubelement} moveUpSubelement={this.moveUpSubelement} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} key={index+'-'+this.props.currenttrigger.get('currenttrigger').get('id')} id={index} removeAction={this.removeAction} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} />
                } else if (action.get('type') == 'command') {
                    return <NodeTriggerActionCommand key={index+'-'+this.props.currenttrigger.get('currenttrigger').get('id')} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} />
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
                        <div className="btn-group" role="group" aria-label="Trigger actions">
                            <a className="btn btn-success btn-sm" disabled={!this.state.dataChanged} onClick={this.saveResponse} >Save</a>
                            <a className="btn btn-success btn-sm" onClick={this.cancelChanges} >Cancel</a>
                        </div>
                    </div>
            );
        } else {
            return (<div className="alert alert-warning" role="alert">Choose a trigger</div>);
        }
    }
}

export default NodeTriggerBuilder;
