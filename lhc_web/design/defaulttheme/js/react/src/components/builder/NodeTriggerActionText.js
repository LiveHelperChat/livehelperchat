import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeTriggerActionQuickReply from './NodeTriggerActionQuickReply';
import NodeTriggerCallbackItem from './NodeTriggerCallbackItem';
import shortid from 'shortid';

class NodeTriggerActionText extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.setText = this.setText.bind(this);
        this.setHTML = this.setHTML.bind(this);
        this.addQuickReply = this.addQuickReply.bind(this);
        this.addAction = this.addAction.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.addAnswerVariation = this.addAnswerVariation.bind(this);

        this.onQuickReplyNameChange = this.onQuickReplyNameChange.bind(this);
        this.onPrecheckChange = this.onPrecheckChange.bind(this);
        this.onRenderArgsChange = this.onRenderArgsChange.bind(this);
        this.onQuickReplyPayloadChange = this.onQuickReplyPayloadChange.bind(this);
        this.onDeleteQuickReply = this.onDeleteQuickReply.bind(this);
        this.onQuickReplyPayloadTypeChange = this.onQuickReplyPayloadTypeChange.bind(this);
        this.onPayloadAttrChange = this.onPayloadAttrChange.bind(this);
        this.showHelp = this.showHelp.bind(this);

        this.onStoreNameChange = this.onStoreNameChange.bind(this);
        this.onStoreValueChange = this.onStoreValueChange.bind(this);
        this.onButtonIDChange = this.onButtonIDChange.bind(this);

        // Abstract methods
        this.onDeleteField = this.onDeleteField.bind(this);
        this.onchangeFieldAttr = this.onchangeFieldAttr.bind(this);

        this.upChildField = this.upChildField.bind(this);
        this.downChildField = this.downChildField.bind(this);

        // Text area focys
        this.textMessageRef = React.createRef();
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    setText(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','text'], value : e.target.value});
    }

    addAnswerVariation() {
        console.log('add answer validation');
        var newVal = this.props.action.getIn(['content','text'])+" |||\n";
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','text'], value : newVal});
        this.textMessageRef.current.focus();
        this.textMessageRef.current.value = newVal;
    }

    setHTML(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','html'], value : e.target.value});
    }

    addQuickReply(e) {
        this.props.addQuickReply({id : this.props.id});
    }

    addAction(e) {
        this.props.addSubelement({id : this.props.id, 'path' : ['content','callback_list'], 'default' : {'_id': shortid.generate(), content : {'success_message' : '','success_text_pattern' : '', 'success_callback' : '', 'type' : '','field' : '', 'event' : ''}}});
    }

    onQuickReplyNameChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','name'], value : e.value});
    }

    onQuickReplyPayloadChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','payload'], value : e.value});
    }

    onQuickReplyPayloadTypeChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'type'], value : e.value});
    }

    onPrecheckChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','render_precheck_function'], value : e.value});
    }


    onStoreNameChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','store_name'], value : e.value});
    }

    onStoreValueChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','store_value'], value : e.value});
    }

    onButtonIDChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','button_id'], value : e.value});
    }


    onRenderArgsChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','render_args'], value : e.value});
    }

    onPayloadAttrChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content', e.payload.attr], value : e.payload.value});
    }

    onDeleteQuickReply(e) {
        this.props.removeQuickReply({id : this.props.id, 'path' : ['content','quick_replies',e.id]});
    }

    removeAction() {
        this.props.removeAction({id : this.props.id});
    }

    onDeleteField(fieldIndex) {
        this.props.deleteSubelement({id : this.props.id, 'path' : ['content','callback_list',fieldIndex]});
    }

    onchangeFieldAttr(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','callback_list',e.id].concat(e.path), value : e.value});
    }

    onchangeAttr(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content'].concat(e.path), value : e.value});
    }

    showHelp(e) {
        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/'+e});
    }

    upChildField(fieldIndex) {
        this.props.moveUpSubelement({id : this.props.id, 'index' : fieldIndex, 'path' : ['content','quick_replies']});
    }

    downChildField(fieldIndex) {
        this.props.moveDownSubelement({id : this.props.id, 'index' : fieldIndex, 'path' : ['content','quick_replies']});
    }

    render() {

        var quick_replies = [];

        if (this.props.action.hasIn(['content','quick_replies'])) {
            var totalButtons = this.props.action.getIn(['content','quick_replies']).size;

            quick_replies = this.props.action.getIn(['content','quick_replies']).map((reply, index) => {
                return <NodeTriggerActionQuickReply onPayloadAttrChange={this.onPayloadAttrChange} upField={(e) => this.upChildField(index)} downField={(e) => this.downChildField(index)} onButtonIDChange={this.onButtonIDChange} isFirst={index == 0} isLast={index + 1 == totalButtons} onStoreValueChange={this.onStoreValueChange} onStoreNameChange={this.onStoreNameChange} onPrecheckChange={this.onPrecheckChange} onRenderArgsChange={this.onRenderArgsChange} onPayloadTypeChange={this.onQuickReplyPayloadTypeChange} deleteReply={this.onDeleteQuickReply} onNameChange={this.onQuickReplyNameChange}  onPayloadChange={this.onQuickReplyPayloadChange} id={index} key={reply.get('_id') || index} reply={reply} />
            });
        }

        var callback_list = [];

        if (this.props.action.hasIn(['content','callback_list'])) {
            callback_list = this.props.action.getIn(['content','callback_list']).map((callback, index) => {
                return <NodeTriggerCallbackItem onChangeFieldAttr={this.onchangeFieldAttr} onDeleteField={this.onDeleteField} id={index} key={index} callback={callback} />
            });
        }

        return (
            <div className="row">
                <div className="col-12">
                    <div className="row">
                        <div className="col-2">
                            <div className="btn-group float-left" role="group" aria-label="Trigger actions">
                                <button disabled="disabled" className="btn btn-sm btn-info">{this.props.id + 1}</button>
                                {this.props.isFirst == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.upField(this.props.id)}><i className="material-icons mr-0">keyboard_arrow_up</i></button>}
                                {this.props.isLast == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.downField(this.props.id)}><i className="material-icons mr-0">keyboard_arrow_down</i></button>}
                            </div>
                        </div>
                        <div className="col-9">
                            <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                        </div>
                        <div className="col-1">
                            <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm float-right">
                                <i className="material-icons mr-0">delete</i>
                            </button>
                        </div>
                    </div>

                    <a title="Need help?" className="float-right" onClick={(e) => this.showHelp('text')}><i className="material-icons mr-0">help</i></a>

                    <div className="form-group">
                        <label>Enter text</label>

                        <a title="Add answer variation" className="float-right" onClick={this.addAnswerVariation}><i className="material-icons mr-0">question_answer</i></a>

                        <textarea rows="3" placeholder="Write your response here!" onChange={this.setText} ref={this.textMessageRef} defaultValue={this.props.action.getIn(['content','text'])} className="form-control form-control-sm"></textarea>
                    </div>

                    <div className="form-group">
                        <label>Enter HTML</label>
                        <textarea placeholder="Write your response here!" onChange={this.setHTML} defaultValue={this.props.action.getIn(['content','html'])} className="form-control form-control-sm"></textarea>
                    </div>

                    <div className="row">
                        <div className="col-6">
                            <div role="group">
                                <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','hide_text_area'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','hide_text_area'])} /> Hide text area on response.</label> <i className="material-icons" title="Textarea to enter user message will be disabled. Make sure you include buttons for user to click.">info</i>
                            </div>
                            <div role="group">
                                <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','on_start_chat'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','on_start_chat'])} /> Send message only at chat start.</label> <i className="material-icons" title="Message will be send only on chat start event.">info</i>
                            </div>
                        </div>
                        <div className="col-6 text-right">
                            <div className="btn-group" role="group">
                                <button onClick={this.addAction} className="btn btn-xs btn-secondary"><i className="material-icons mr-0">add</i> Add action on message</button>
                                <button onClick={this.addQuickReply} className="btn btn-xs btn-secondary"><i className="material-icons mr-0">add</i> Add quick reply</button>
                            </div>
                        </div>
                    </div>

                </div>
                <div className="col-12">
                    {callback_list}
                    {callback_list.size > 0 && quick_replies.size > 0 &&
                    <hr/>}
                    {quick_replies}
                </div>
                <div className="col-12">
                    <hr className="hr-big" />
                </div>
            </div>
        );
    }
}

export default NodeTriggerActionText;