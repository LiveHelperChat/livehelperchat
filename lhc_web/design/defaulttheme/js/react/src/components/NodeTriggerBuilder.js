import React, { Component } from 'react';
import { connect } from "react-redux";
import { updateTriggerName, updateTriggerType, addResponse, updateTriggerContent, saveTrigger, initBot, loadUseCases, fetchNodeGroupTriggerAction, saveTemplate} from "../actions/nodeGroupTriggerActions"
import NodeTriggerActionText from './builder/NodeTriggerActionText';
import NodeTriggerActionList from './builder/NodeTriggerActionList';
import NodeTriggerActionGeneric from './builder/NodeTriggerActionGeneric';
import NodeTriggerActionCollectable from './builder/NodeTriggerActionCollectable';
import NodeTriggerActionButtons from './builder/NodeTriggerActionButtons';
import NodeTriggerActionCommand from './builder/NodeTriggerActionCommand';
import NodeTriggerActionPredefined from './builder/NodeTriggerActionPredefined';
import NodeTriggerActionTyping from './builder/NodeTriggerActionTyping';
import NodeTriggerActionPause from './builder/NodeTriggerActionPause';
import NodeTriggerActionProgress from './builder/NodeTriggerActionProgress';
import NodeTriggerActionVideo from './builder/NodeTriggerActionVideo';
import NodeTriggerActionAttribute from './builder/NodeTriggerActionAttribute';
import NodeTriggerActionActions from './builder/NodeTriggerActionActions';
import NodeTriggerActionIntent from './builder/NodeTriggerActionIntent';
import NodeTriggerActionIntentCheck from './builder/NodeTriggerActionIntentCheck';
import NodeTriggerActionConditions from './builder/NodeTriggerActionConditions';
import NodeTriggerActionMatchActions from './builder/NodeTriggerActionMatchActions';
import NodeTriggerActionEventType from './builder/NodeTriggerActionEventType';
import NodeTriggerActionRepeatRestrict from './builder/NodeTriggerActionRepeatRestrict';
import NodeTriggerActionExecuteJS from './builder/NodeTriggerActionExecuteJS';
import NodeTriggerActionRestAPI from './builder/NodeTriggerActionRestAPI';
import NodeTriggerActionIframe from './builder/NodeTriggerActionIframe';
import NodeTriggerActionTbody from './builder/NodeTriggerActionTbody';
import NodeTriggerActionTextConditional from './builder/NodeTriggerActionTextConditional';
import NodeTriggerActionAlertIcon from './builder/NodeTriggerActionAlertIcon';
import NodeTriggerActionMail from './builder/NodeTriggerActionMail';
import NodeTriggerActionLogAction from './builder/NodeTriggerActionLogAction';
import NodeTriggerActionSurvey from './builder/NodeTriggerActionSurvey';
import NodeTriggerListTemplate from './builder/NodeTriggerListTemplate';

@connect((store) => {
    return {
        currenttrigger: store.currenttrigger
    };
})

class NodeTriggerBuilder extends Component {

    constructor(props) {
        super(props);
        this.state = {dataChanged : false, value : '', viewCode : false, viewUseCases : false, compressCode : false, sourceCopied: false, templateName : ''};
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
        this.viewCode = this.viewCode.bind(this);
        this.viewUseCases = this.viewUseCases.bind(this);
        this.navigateToTrigger = this.navigateToTrigger.bind(this);


        this.upField = this.upField.bind(this);
        this.downField = this.downField.bind(this);

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

    viewCode() {
        this.setState({viewCode : !this.state.viewCode});
    }

    viewUseCases() {
        this.setState({viewUseCases : !this.state.viewUseCases});
        if (this.state.viewUseCases == false) {
            this.props.dispatch(loadUseCases(this.props.currenttrigger.get('currenttrigger')));
        }
    }

    upField(fieldIndex) {
        this.setState({dataChanged : true});
        this.props.dispatch({'type' : 'MOVE_UP','payload' : {'index' : fieldIndex}});
    }

    downField(fieldIndex) {
        this.setState({dataChanged : true});
        this.props.dispatch({'type' : 'MOVE_DOWN','payload' : {'index' : fieldIndex}});
    }

    navigateToTrigger(obj) {
        this.setState({viewUseCases : false});
        this.props.dispatch(fetchNodeGroupTriggerAction(obj.get('id')))
    }

    copyToClipboard(e) {
        var txtdom = document.getElementById('json-body-trigger');
        txtdom.select();
        document.execCommand("copy");
        if (window.getSelection) {
            window.getSelection().removeAllRanges();
        } else if (document.selection) {
            document.selection.empty();
        }
        this.setState({sourceCopied : true});
    }

    saveTemplate(e) {
        this.props.dispatch(saveTemplate({
            "actions" : this.props.currenttrigger.getIn(['currenttrigger','actions']).toJS(),
            "name" : this.state.templateName
        }));
    }

    loadFromText(e) {
        var value = document.getElementById('json-body-trigger-input').value;
        if (value != '') {
            try {
                var object = JSON.parse(value);
                this.props.dispatch({type: "LOAD_TEMPLATE_FULFILLED", payload: {result: object}});
            } catch (error) {
                alert(error);
            }
        } else {
            alert('Please enter trigger body!');
        }
    }

    render() {

        var actions = [];
        if (this.props.currenttrigger.get('currenttrigger').has('actions')) {
            var totalTriggers = this.props.currenttrigger.get('currenttrigger').get('actions').size;
            actions = this.props.currenttrigger.get('currenttrigger').get('actions').map((action, index) => {
                let key = index+'-'+this.props.currenttrigger.get('currenttrigger').get('id')+'-'+action.get('_id') + '-' + this.state.templateName;
                if (action.get('type') == 'text') {
                    return <NodeTriggerActionText upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} moveDownSubelement={this.moveDownSubelement} moveUpSubelement={this.moveUpSubelement} addSubelement={this.addSubelement} deleteSubelement={this.deleteSubelement} key={key} id={index} removeAction={this.removeAction} removeQuickReply={this.removeQuickReply} addQuickReply={this.addQuickReply} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} />
                } else if (action.get('type') == 'list') {
                    return <NodeTriggerActionList upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} moveDownSubelement={this.moveDownSubelement} moveUpSubelement={this.moveUpSubelement} addSubelement={this.addSubelement} removeQuickReply={this.removeQuickReply} addQuickReply={this.addQuickReply} deleteSubelement={this.deleteSubelement} key={key} id={index} removeAction={this.removeAction} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} />
                } else if (action.get('type') == 'generic') {
                    return <NodeTriggerActionGeneric upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} moveDownSubelement={this.moveDownSubelement} moveUpSubelement={this.moveUpSubelement} addSubelement={this.addSubelement} removeQuickReply={this.removeQuickReply} addQuickReply={this.addQuickReply} deleteSubelement={this.deleteSubelement} key={key} id={index} removeAction={this.removeAction} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} />
                } else if (action.get('type') == 'collectable') {
                    return <NodeTriggerActionCollectable upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} moveDownSubelement={this.moveDownSubelement} moveUpSubelement={this.moveUpSubelement} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} key={key} id={index} removeAction={this.removeAction} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} />
                } else if (action.get('type') == 'buttons') {
                    return <NodeTriggerActionButtons upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} moveDownSubelement={this.moveDownSubelement} moveUpSubelement={this.moveUpSubelement} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} key={key} id={index} removeAction={this.removeAction} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} />
                } else if (action.get('type') == 'command') {
                    return <NodeTriggerActionCommand upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} />
                } else if (action.get('type') == 'predefined') {
                    return <NodeTriggerActionPredefined upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} />
                } else if (action.get('type') == 'typing') {
                    return <NodeTriggerActionTyping upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} />
                } else if (action.get('type') == 'pauseBot') {
                    return <NodeTriggerActionPause upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} />
                } else if (action.get('type') == 'progress') {
                    return <NodeTriggerActionProgress upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} />
                } else if (action.get('type') == 'video') {
                    return <NodeTriggerActionVideo upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} />
                } else if (action.get('type') == 'attribute') {
                    return <NodeTriggerActionAttribute upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} />
                } else if (action.get('type') == 'actions') {
                    return <NodeTriggerActionActions upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} />
                } else if (action.get('type') == 'intent') {
                    return <NodeTriggerActionIntent upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} moveDownSubelement={this.moveDownSubelement} moveUpSubelement={this.moveUpSubelement} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} />
                } else if (action.get('type') == 'intentcheck') {
                    return <NodeTriggerActionIntentCheck upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} />
                } else if (action.get('type') == 'conditions') {
                    return <NodeTriggerActionConditions upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} />
                } else if (action.get('type') == 'match_actions') {
                    return <NodeTriggerActionMatchActions upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} />
                } else if (action.get('type') == 'event_type') {
                    return <NodeTriggerActionEventType upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} />
                } else if (action.get('type') == 'repeat_restrict') {
                    return <NodeTriggerActionRepeatRestrict upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} />
                } else if (action.get('type') == 'execute_js') {
                    return <NodeTriggerActionExecuteJS upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} />
               } else if (action.get('type') == 'tbody') {
                    return <NodeTriggerActionTbody upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} />
                } else if (action.get('type') == 'restapi') {
                    return <NodeTriggerActionRestAPI upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} />
                } else if (action.get('type') == 'text_conditional') {
                    return <NodeTriggerActionTextConditional upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} />
                } else if (action.get('type') == 'alert_icon') {
                    return <NodeTriggerActionAlertIcon upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} />
                } else if (action.get('type') == 'survey') {
                    return <NodeTriggerActionSurvey upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} />
                } else if (action.get('type') == 'mail') {
                    return <NodeTriggerActionMail upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} />
                } else if (action.get('type') == 'laction') {
                    return <NodeTriggerActionLogAction upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} />
                } else if (action.get('type') == 'iframe') {
                    return <NodeTriggerActionIframe upField={this.upField} downField={this.downField} isFirst={index == 0} isLast={index + 1 == totalTriggers} key={key} id={index} onChangeContent={this.handleContentChange} onChangeType={this.handleTypeChange} action={action} removeAction={this.removeAction} deleteSubelement={this.deleteSubelement} addSubelement={this.addSubelement} />
                }
            });
        }

        var usecases = [];
        if (this.props.currenttrigger.get('currenttrigger').has('use_cases')) {
            usecases = this.props.currenttrigger.get('currenttrigger').get('use_cases').map((use_case, index) => {
                return <button onClick={(e) => this.navigateToTrigger(use_case)} className="btn btn-secondary btn-xs m-1">{use_case.get('name')}</button>
            })
        }

        if (this.props.currenttrigger.hasIn(['currenttrigger','name']))
        {
            return (
                    <div>
                        <input className="form-control gbot-group-name" key={this.props.currenttrigger.get('currenttrigger').get('id')} defaultValue={this.props.currenttrigger.getIn(['currenttrigger','name'])} onChange={this.handleChange} />
                    <hr/>
                    {actions}
                    <div className="form-group">
                        <div className="btn-group" role="group" aria-label="Trigger actions">
                            <button className="btn btn-info btn-sm" onClick={this.addResponse} >Add response</button>
                            <button className="btn btn-info btn-sm" onClick={this.viewCode} ><i className="material-icons">code</i>{this.state.viewCode == true ? ('Hide code') : ('Show code')}</button>
                            <button className="btn btn-info btn-sm" onClick={this.viewUseCases} ><i className="material-icons">info</i>{this.state.viewUseCases == true ? ('Hide use cases') : ('Show use cases')}</button>
                        </div>
                    </div>

                        {this.state.viewCode == true ? (
                            <div className="form-group">
                                <div className="float-end"><label><input type="checkbox" value="on" onChange={(e) => this.setState({compressCode : !this.state.compressCode})} defaultChecked={this.state.compressCode} /> Compressed version</label></div>
                                <label>JSON body you can use for REST API</label>

                                <div className="row">
                                    <div className="col-4">
                                        <div className="mb-2">
                                            <div className="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                                <button type="button" className="btn btn-secondary" onClick={(e) => this.copyToClipboard(e)}>{this.state.sourceCopied == true ? 'Copied!, Copy again.' : 'Copy to clipboard'}</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-4">
                                        <div className="input-group input-group-sm mb-3">
                                            <input type="text" className="form-control" placeholder="Template name" title="If you set same name as existing template we will update it" value={this.state.templateName} onChange={(e) => this.setState({'templateName' : e.target.value})} aria-label="Template name" aria-describedby="basic-addon2" />
                                            <button type="button" disabled={this.state.templateName == ''} className="btn btn-secondary" onClick={(e) => this.saveTemplate(e)}>Save as template</button>
                                        </div>
                                    </div>
                                    <div className="col-4">
                                        <NodeTriggerListTemplate setTemplateName={(e) => this.setState({'templateName' : e})} />
                                    </div>
                                </div>

                                <textarea readOnly="readOnly" id="json-body-trigger" rows="10" className="form-control fs11" value={JSON.stringify(this.props.currenttrigger.getIn(['currenttrigger','actions']).toJSON(), null, (this.state.compressCode == false ? 4 : 0))}></textarea>
                                <p><small><i>&quot;_id&quot; can be ignored</i></small></p>

                                <div className="row">
                                    <div className="col-9">
                                        <textarea id="json-body-trigger-input" rows="10" className="form-control fs11" defaultValue="" placeholder="Paste manually trigger body"></textarea>
                                    </div>
                                    <div className="col-3">
                                        <button className="btn btn-sm btn-secondary" onClick={(e) => this.loadFromText()}>Load</button>
                                    </div>
                                </div>

                            </div>
                        ) : ''}

                        {this.state.viewUseCases == true ? (
                            <div className="form-group">
                                {(!this.props.currenttrigger.get('currenttrigger').has('use_cases') || this.props.currenttrigger.getIn(['currenttrigger','use_cases']).size == 0) ? (
                                    <p>No use cases were found</p>
                                ) : ''}
                                {usecases}
                            </div>
                        ) : ''}

                    <hr/>
                        <div className="btn-group" role="group" aria-label="Trigger actions">
                            <button className="btn btn-success btn-sm" disabled={!this.state.dataChanged} onClick={this.saveResponse} >Save</button>
                            <button className="btn btn-success btn-sm" onClick={this.cancelChanges} >Cancel</button>
                        </div>
                    </div>
            );
        } else {
            return (<div className="alert alert-warning" role="alert">Choose a trigger</div>);
        }
    }
}

export default NodeTriggerBuilder;
