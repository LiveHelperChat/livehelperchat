import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';

class NodeTriggerActionMail extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);
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

    render() {
        return (
            <div>
                <div className="d-flex flex-row">
                    <div>
                        <div className="btn-group float-left" role="group" aria-label="Trigger actions">
                            <button disabled="disabled" className="btn btn-sm btn-info">{this.props.id + 1}</button>
                            {this.props.isFirst == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.upField(this.props.id)}><i className="material-icons mr-0">keyboard_arrow_up</i></button>}
                            {this.props.isLast == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.downField(this.props.id)}><i className="material-icons mr-0">keyboard_arrow_down</i></button>}
                        </div>
                    </div>
                    <div className="flex-grow-1 px-2">
                        <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                    </div>
                    <div className="pr-2 pt-1">
                        <label className="form-check-label" title="Response will not be executed. Usefull for a quick testing."><input onChange={(e) => this.props.onChangeContent({id : this.props.id, 'path' : ['skip_resp'], value : e.target.checked})} defaultChecked={this.props.action.getIn(['skip_resp'])} type="checkbox"/> Skip</label>
                    </div>
                    <div>
                        <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm float-right">
                            <i className="material-icons mr-0">delete</i>
                        </button>
                    </div>
                </div>

                <div className="row">
                    <div className="col-6">
                        <div className="form-group">
                            <label>Subject</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['mail_options','subject'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','mail_options','subject'])} />
                        </div>
                    </div>

                    <div className="col-6">
                        <div className="form-group">
                            <label>From name</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['mail_options','from_name'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','mail_options','from_name'])} />
                        </div>
                    </div>

                    <div className="col-6">
                        <div className="form-group">
                            <label>From e-mail</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['mail_options','from_email'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','mail_options','from_email'])} />
                        </div>
                    </div>

                    <div className="col-6">
                        <div className="form-group">
                            <label>Reply to</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['mail_options','reply_to'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','mail_options','reply_to'])} />
                        </div>
                    </div>

                    <div className="col-6">
                        <div className="form-group">
                            <label>Recipient</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['mail_options','recipient'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','mail_options','recipient'])} />
                        </div>
                    </div>

                    <div className="col-6">
                        <div className="form-group">
                            <label>CC Recipient</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['mail_options','cc_recipient'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','mail_options','cc_recipient'])} />
                        </div>
                    </div>

                    <div className="col-6">
                        <div className="form-group">
                            <label>BCC Recipient</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['mail_options','bcc_recipient'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','mail_options','bcc_recipient'])} />
                        </div>
                    </div>

                    <div className="col-12">
                        <div className="form-group">
                            <label>Mail body</label>
                            <textarea placeholder="Video URL" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['text'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','text'])}/>
                        </div>
                    </div>
                </div>
                <hr className="hr-big" />

            </div>
        );
    }
}

export default NodeTriggerActionMail;
