import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeTriggerList from './NodeTriggerList';

class NodeTriggerActionSurvey extends Component {

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

                <div className="row">
                    <div className="col-12">
                        <div className="form-group">
                            <a title="Find it in survey list" target="_blank" className="float-end" href={WWW_DIR_JAVASCRIPT+'abstract/list/Survey'} ><i className="material-icons me-0">help</i></a>
                            <label>Survey ID or identifier. If more than one survey is found with same identifier we will show random one.</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['survey_options','survey_id'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','survey_options','survey_id'])} />
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['survey_options','unique_per_chat'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','survey_options','unique_per_chat'])} /> Unique survey per chat</label>
                            <p className="small text-muted">We will not send same survey twice in the same chat session.</p>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['survey_options','unique_vote'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','survey_options','unique_vote'])} /> Visitor can fill a survey once per selected period.</label>
                            <p className="small text-muted">If unchecked we will not check has he filled this survey or not and will always show a survey.</p>
                        </div>
                    </div>
                    <div className="col-6">
                        <label>Choose after how long visitor can fill a survey he has already filled some time ago</label>
                        <select className="form-control form-control-sm" defaultValue={this.props.action.getIn(['content','expires_vote'])} onChange={(e) => this.onchangeAttr({'path' : ['expires_vote'], 'value' : e.target.value})}>
                            <option value="0">Never</option>
                            <option value="1">After 1 day</option>
                            <option value="2">After 2 day's</option>
                            <option value="3">After 3 day's</option>
                            <option value="7">After a week</option>
                            <option value="14">After two weeks</option>
                            <option value="28">After a month</option>
                            <option value="56">After 2 months</option>
                            <option value="84">After 3 months</option>
                            <option value="112">After 4 months</option>
                            <option value="140">After 5 months</option>
                            <option value="168">After 6 months</option>
                            <option value="336">After a year</option>
                        </select>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label>If visitor has already filled survey(s) or there is no more surveys to send, send him this message instead.</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path':['payload'],'value':e})} payload={this.props.action.getIn(['content','payload'])} />
                        </div>
                    </div>
                </div>

                <hr className="hr-big" />

            </div>
        );
    }
}

export default NodeTriggerActionSurvey;
