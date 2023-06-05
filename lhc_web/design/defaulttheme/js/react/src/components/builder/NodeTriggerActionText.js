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
        this.setReactions = this.setReactions.bind(this);
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
        this.onButtonStoreTypeChange = this.onButtonStoreTypeChange.bind(this);
        this.onButtonNoName = this.onButtonNoName.bind(this);

        // Abstract methods
        this.onDeleteField = this.onDeleteField.bind(this);
        this.onchangeFieldAttr = this.onchangeFieldAttr.bind(this);

        this.upChildField = this.upChildField.bind(this);
        this.downChildField = this.downChildField.bind(this);

        // Text area focys
        this.textMessageRef = React.createRef();
        this.reactionMessageRef = React.createRef();
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    setText(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','text'], value : e.target.value});
    }

    addAnswerVariation() {
        var newVal = this.props.action.getIn(['content','text'])+" |||\n";
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','text'], value : newVal});
        this.textMessageRef.current.focus();
        this.textMessageRef.current.value = newVal;
    }

    setHTML(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','html'], value : e.target.value});
    }

    setReactions(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','reactions'], value : e.target.value});
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

    onButtonStoreTypeChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','as_variable'], value : e.value});
    }

    onButtonNoName(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','no_name'], value : e.value});
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
                return <NodeTriggerActionQuickReply onPayloadAttrChange={this.onPayloadAttrChange} upField={(e) => this.upChildField(index)} downField={(e) => this.downChildField(index)} onButtonNoName={this.onButtonNoName} onButtonStoreTypeChange={this.onButtonStoreTypeChange} onButtonIDChange={this.onButtonIDChange} isFirst={index == 0} isLast={index + 1 == totalButtons} onStoreValueChange={this.onStoreValueChange} onStoreNameChange={this.onStoreNameChange} onPrecheckChange={this.onPrecheckChange} onRenderArgsChange={this.onRenderArgsChange} onPayloadTypeChange={this.onQuickReplyPayloadTypeChange} deleteReply={this.onDeleteQuickReply} onNameChange={this.onQuickReplyNameChange}  onPayloadChange={this.onQuickReplyPayloadChange} id={index} key={reply.get('_id') || index} reply={reply} />
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
                    <div className="d-flex flex-row">
                        <div>
                            <div className="btn-group float-start" role="group" aria-label="Trigger actions">
                                <button disabled="disabled" className="btn btn-sm btn-info">{this.props.id + 1}</button>
                                {this.props.isFirst == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.upField(this.props.id)}><i className="material-icons me-0">keyboard_arrow_up</i></button>}
                                {this.props.isLast == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.downField(this.props.id)}><i className="material-icons me-0">keyboard_arrow_down</i></button>}
                            </div>
                        </div>
                        <div className="flex-grow-1 px-2">
                            <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                        </div>
                        <div className="pe-2">
                            <div className="input-group input-group-sm">
                                <span className="input-group-text" id="basic-addon1"><span className="material-icons">vpn_key</span></span>
                                <input type="text" className="form-control" readOnly="true" value={this.props.action.getIn(['_id'])} title="Action ID"/>
                            </div>
                        </div>
                        <div className="pe-2 pt-1 text-nowrap">
                            <label className="form-check-label" title="Response will not be executed. Usefull for a quick testing."><input onChange={(e) => this.props.onChangeContent({id : this.props.id, 'path' : ['skip_resp'], value : e.target.checked})} defaultChecked={this.props.action.getIn(['skip_resp'])} type="checkbox"/> Skip</label>
                        </div>
                        <div>
                            <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm float-end">
                                <i className="material-icons me-0">delete</i>
                            </button>
                        </div>
                    </div>

                    <a title="Need help?" className="float-end" onClick={(e) => this.showHelp('text')}><i className="material-icons me-0">help</i></a>

                    <div className="form-group">
                        <label>Enter text</label>
                        <a title="Add answer variation" className="float-end" onClick={this.addAnswerVariation}><i className="material-icons me-0">question_answer</i></a>
                        <textarea rows="3" placeholder="Write your response here!" onChange={this.setText} ref={this.textMessageRef} defaultValue={this.props.action.getIn(['content','text'])} className="form-control form-control-sm"></textarea>
                    </div>

                    <div className="form-group">
                        <label>Enter HTML</label>
                        <textarea placeholder="Write your response here!" onChange={this.setHTML} defaultValue={this.props.action.getIn(['content','html'])} className="form-control form-control-sm"></textarea>
                    </div>

                    <div className="form-group">
                        <label className="mb-0">Reaction options <button onClick={(e) => {this.reactionMessageRef.current.value = "thumb_up|1|thumb|Thumbs up"+"\n"+"thumb_down|0|thumb|Thumbs down";this.props.onChangeContent({id : this.props.id, 'path' : ['content','reactions'], value : this.reactionMessageRef.current.value})}} className="btn btn-secondary btn-xs">Set thumbs sample</button> </label>
                        <p><small><i>Icon from material icons or Unicode Character&lt;required&gt;|internal value&lt;required&gt;|identifier&lt;required&gt;|Title&lt;optional&gt;</i></small></p>
                        <textarea rows="3" ref={this.reactionMessageRef} placeholder={"E.g"+"\n"+"thumb_up|1|thumb|Thumbs up"+"\n"+"thumb_down|0|thumb|Thumbs down"} onChange={this.setReactions} defaultValue={this.props.action.getIn(['content','reactions'])} className="form-control form-control-sm"></textarea>
                    </div>

                    <div className="row">
                        <div className="col-6">
                            <div role="group">
                                <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','hide_text_area'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','hide_text_area'])} /> Hide text area on response.</label> <i className="material-icons" title="Textarea to enter user message will be disabled. Make sure you include buttons for user to click.">info</i>
                            </div>
                            <div role="group">
                                <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','on_start_chat'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','on_start_chat'])} /> Send message only at chat start.</label> <i className="material-icons" title="Message will be send only on chat start event.">info</i>
                            </div>
                            <div role="group">
                                <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','hide_on_next'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','hide_on_next'])} /> Hide on next message.</label> <i className="material-icons" title="Hide message content on next message.">info</i>
                            </div>
                            <div role="group">
                                <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','auto_translate'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','auto_translate'])} /> Automatic translations.</label> <i className="material-icons" title="If you have enabled automatic translations for translation group we will translate this message. You can't mix manual and automatic translations in the same message. Before final save we will translate all response including buttons to visitor language.">info</i>
                            </div>
                        </div>
                        <div className="col-6">
                            <div role="group">
                                <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','as_system'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','as_system'])} /> Save as a system message.</label> <i className="material-icons" title="Message will be saved as system message and will be invisible by visitor.">info</i>
                            </div>
                            <div role="group">
                                <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','as_visitor'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','as_visitor'])} /> Save as a visitor message.</label> <i className="material-icons" title="Message will be saved as a visitor message.">info</i>
                            </div>
                            <div role="group">
                                <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','as_log_msg'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','as_log_msg'])} /> Save as a log message.</label> <i className="material-icons" title="Message will be saved in audit log only.">info</i>
                            </div>
                            <div role="group">
                                <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','reactions_visible'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','reactions_visible'])} /> Reactions always visible.</label> <i className="material-icons" title="Make reactions icons always visible. By default they are visible on mouse over.">info</i>
                            </div>
                        </div>
                        <div className="col-12 pb-2">
                            <label>Webhook execution delay</label> <i className="material-icons" title="Sometimes if you have background workers webhook messages events are executed in paralell. If you want to keep exact order you can add a delay.">info</i>
                            <input type="number" max="30" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','wh_delay'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','attr_options','wh_delay'])} className="form-control form-control-sm" placeholder="Webhook execution delay in seconds" />
                        </div>

                        <div className="col-12 text-right">
                            <div className="btn-group" role="group">
                                <button onClick={this.addAction} className="btn btn-xs btn-secondary"><i className="material-icons me-0">add</i> Add action on message</button>
                                <button onClick={this.addQuickReply} className="btn btn-xs btn-secondary"><i className="material-icons me-0">add</i> Add quick reply</button>
                            </div>
                        </div>
                    </div>

                </div>
                <div className="col-12">
                    {callback_list}
                    {callback_list.size > 0 && quick_replies.size > 0 &&
                    <hr/>}

                    {quick_replies.size > 0 && <React.Fragment>
                        <hr/>
                        <div className="row">
                            <div className="col-6">
                                <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','as_dropdown'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','as_dropdown'])} /> Render buttons as dropdown.</label><br/>
                            </div>
                            <div className="col-6">
                                <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','always_show'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','always_show'])} /> Render buttons even if it's not the last message.</label>
                            </div>

                            {this.props.action.hasIn(['content', 'attr_options', 'always_show']) && this.props.action.getIn(['content', 'attr_options', 'always_show']) == true &&
                                <div className="col-12">
                                    <label>How many messages can be after buttons before they are hidden permanently? (applies only after page refresh)</label>
                                    <input type="number" placeholder="1" className="form-control form-control-sm"
                                           min="1" onChange={(e) => this.onchangeAttr({
                                        'path': ['attr_options', 'after_messages_number'],
                                        'value': e.target.value
                                    })}
                                           defaultValue={this.props.action.getIn(['content', 'attr_options', 'after_messages_number'])}/>
                                </div>
                            }

                        </div>
                    </React.Fragment>}

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