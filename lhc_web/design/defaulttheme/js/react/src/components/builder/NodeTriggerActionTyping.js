import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';


class NodeTriggerActionTyping extends Component {

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
                            <input type="text" className="form-control" readOnly={true} value={this.props.action.getIn(['_id'])} title="Action ID"/>
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
                
                <div className="row">
                    <div className="col-6">
                        <div role="group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['auto_translate'], 'value' : e.target.checked})} defaultChecked={this.props.action.getIn(['content','auto_translate'])} /> Automatic translations.</label> <i className="material-icons" title="If you have enabled automatic translations for translation group we will translate this message. You can't mix manual and automatic translations in the same message. Before final save we will translate all response including buttons to visitor language.">info</i>
                        </div>
                    </div>
                </div>

                <div className="row">
                    <div className="col-12">
                        <div className="form-group">
                            <label>Show typing for N seconds</label>
                            <input type="text" placeholder="Value in seconds" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['duration'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','duration'])}/>
                        </div>
                        <div className="form-group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['untill_message'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','untill_message'])} />OR untill next operator message</label> <i className="material-icons" title="Usefull if some background job posts a message.">info</i>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Default typing text</label>
                            <input placeholder="Typing..." type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['text'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','text'])}/>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Delay typing appearance for n seconds</label>
                            <input placeholder="Value in seconds" type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['delay'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','delay'])}/>
                        </div>
                    </div>
                    <div className="col-12">
                        <div role="group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['on_start_chat'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','on_start_chat'])} /> Send typing only at chat start.</label> <i className="material-icons" title="Typing will be send only on chat start event.">info</i>
                        </div>
                    </div>
                </div>
                <hr className="hr-big" />

            </div>
        );
    }
}

export default NodeTriggerActionTyping;
