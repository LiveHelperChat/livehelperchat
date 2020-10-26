import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';

class NodeTriggerActionTextConditional extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);
        this.showHelp = this.showHelp.bind(this);
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

    showHelp(e) {
        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/'+e});
    }

    render() {
        return (
            <div>
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

                <div className="row">
                    <div className="col-12">
                        <div className="form-group">
                            <a title="Need help?" className="float-right" onClick={(e) => this.showHelp('conditional_message')}><i className="material-icons mr-0">help</i></a>
                            <label>Intro message to the user</label>
                            <textarea rows="3" placeholder="This message will receive a user" onChange={(e) => this.onchangeAttr({'path' : ['intro_us'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','intro_us'])} className="form-control form-control-sm"></textarea>
                        </div>
                    </div>

                    <div className="col-12">
                        <div className="form-group">
                            <label>Full message to the user</label>
                            <textarea rows="3" placeholder="This is full message body after read more is clicked." onChange={(e) => this.onchangeAttr({'path' : ['full_us'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','full_us'])} className="form-control form-control-sm"></textarea>
                        </div>
                    </div>

                    <div className="col-6">
                        <div className="form-group">
                            <label>Read more text for the visitor</label>
                            <input type="text" placeholder="Read more" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['readmore_us'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','readmore_us'])} />
                        </div>
                    </div>

                    <div className="col-12">
                        <div className="form-group">
                            <label>Intro message to the operator</label>
                            <textarea rows="3" placeholder="This message will receive a user" onChange={(e) => this.onchangeAttr({'path' : ['intro_op'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','intro_op'])} className="form-control form-control-sm"></textarea>
                        </div>
                    </div>

                    <div className="col-12">
                        <div className="form-group">
                            <label>Full message to the operator</label>
                            <textarea rows="3" placeholder="This is full message body after read more is clicked." onChange={(e) => this.onchangeAttr({'path' : ['full_op'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','full_op'])} className="form-control form-control-sm"></textarea>
                        </div>
                    </div>

                    <div className="col-6">
                        <div className="form-group">
                            <label>Read more text for the operator</label>
                            <input type="text" placeholder="Read more" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['readmore_op'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','readmore_op'])} />
                        </div>
                    </div>

                </div>

                <hr className="hr-big" />

            </div>
        );
    }
}

export default NodeTriggerActionTextConditional;
