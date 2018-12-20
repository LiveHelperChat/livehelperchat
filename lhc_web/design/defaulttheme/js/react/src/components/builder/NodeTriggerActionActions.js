import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeTriggerList from './NodeTriggerList';

class NodeTriggerActionActions extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);

        this.addAnswerVariation = this.addAnswerVariation.bind(this);
        // Text area focys
        this.textMessageRef = React.createRef();
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    removeAction() {
        this.props.removeAction({id : this.props.id});
    }

    onchangeAttr(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content'].concat(e.path), value : e.value});
    }

    addAnswerVariation() {
        var newVal = this.props.action.getIn(['content','success_message'])+" |||\n";
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','success_message'], value : newVal});
        this.textMessageRef.current.focus();
        this.textMessageRef.current.value = newVal;
    }

    render() {
        return (
            <div>
                <div className="row">
                    <div className="col-xs-2">
                        <div className="btn-group pull-left" role="group" aria-label="Trigger actions">
                            <button disabled="disabled" className="btn btn-xs btn-info">{this.props.id + 1}</button>
                            {this.props.isFirst == false && <a className="btn btn-default btn-xs" onClick={(e) => this.props.upField(this.props.id)}><i className="material-icons mr-0">keyboard_arrow_up</i></a>}
                            {this.props.isLast == false && <a className="btn btn-default btn-xs" onClick={(e) => this.props.downField(this.props.id)}><i className="material-icons mr-0">keyboard_arrow_down</i></a>}
                        </div>
                    </div>
                    <div className="col-xs-9">
                        <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                    </div>
                    <div className="col-xs-1">
                        <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm pull-right">
                            <i className="material-icons mr-0">delete</i>
                        </button>
                    </div>
                </div>

                <div className="form-group">
                    <label>Message before dispatching event</label>
                    <a title="Add answer variation" className="pull-right" onClick={this.addAnswerVariation}><i className="material-icons mr-0">question_answer</i></a>
                    <textarea rows="3" className="form-control" ref={this.textMessageRef} defaultValue={this.props.action.getIn(['content','success_message'])} onChange={(e) => this.onchangeAttr({'path' : ['success_message'], 'value' : e.target.value})}></textarea>
                </div>

                <div className="row">
                    <div className="col-xs-6">
                        <div className="form-group">
                            <label>Event identifier</label>
                            <input type="text" placeholder="Event name" className="form-control input-sm" onChange={(e) => this.onchangeAttr({'path' : ['event'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','event'])} />
                        </div>
                    </div>
                    <div className="col-xs-6">
                        <div>
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['event_background'],'value' : e.target.checked})} defaultChecked={this.props.action.getIn(['content','event_background'])} /> Event is processed on next visitor message.</label>
                        </div>
                    </div>
                </div>

                <div className="row">
                    <div className="col-xs-8">
                        <div className="form-group">
                            <label>For success visitor message has to contain one of these words</label>
                            <input type="text" placeholder="ok, yes, great, go ahead" className="form-control" onChange={(e) => this.onchangeAttr({'path' : ['event_validate'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','event_validate'])} />
                        </div>
                    </div>
                    <div className="col-xs-4">
                        <div className="form-group">
                            <label>How many typos are allowed in a word?</label>
                            <input type="text" placeholder="0" className="form-control input-sm" onChange={(e) => this.onchangeAttr({'path' : ['event_typos'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','event_typos'])} />
                        </div>
                    </div>
                    <div className="col-xs-8">
                        <div className="form-group">
                            <label>Bot not any of</label>
                            <input type="text" placeholder="no, not" className="form-control" onChange={(e) => this.onchangeAttr({'path' : ['event_validate_exc'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','event_validate_exc'])} />
                        </div>
                    </div>
                    <div className="col-xs-4">
                        <div className="form-group">
                            <label>How many typos are allowed in a word?</label>
                            <input type="text" placeholder="0" className="form-control input-sm" onChange={(e) => this.onchangeAttr({'path' : ['event_typos_exc'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','event_typos_exc'])} />
                        </div>
                    </div>
                </div>

                <div className="row">
                    <div className="col-xs-6">
                        <div className="form-group">
                            <label>Execute trigger on fail</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','collection_callback_cancel'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','collection_callback_cancel'])} />
                        </div>
                    </div>
                    <div className="col-xs-6">
                        <div className="form-group">
                            <label>Execute trigger on success</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','collection_callback_pattern'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','collection_callback_pattern'])} />
                        </div>
                    </div>
                    <div className="col-xs-12">
                        <div className="form-group">
                            <label>Execute trigger on failed format</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','collection_callback_format'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','collection_callback_format'])} />
                        </div>
                    </div>

                    <div className="col-xs-6">
                        <div className="form-group">
                            <label>Alternative answer match</label>
                            <input type="text" title="no, nop, not ok, ne" placeholder="no, nop, not ok, ne" className="form-control input-sm" onChange={(e) => this.onchangeAttr({'path' : ['event_in_validate'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','event_in_validate'])} />
                        </div>
                    </div>

                    <div className="col-xs-6">
                        <div className="form-group">
                            <label>Execute trigger</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','collection_callback_alternative'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','collection_callback_alternative'])} />
                        </div>
                    </div>

                </div>
                <hr/>

            </div>
        );
    }
}

export default NodeTriggerActionActions;
