import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';

class NodeTriggerActionAlertIcon extends Component {

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
                    <div className="col-6">
                        <div className="form-group">
                            <label>Icon identifier. <a target="_blank" href="https://material.io/resources/icons/?style=baseline"><span className="material-icons">open_in_new</span> Material Icons</a></label>
                            <input type="text" placeholder="Icon identifier - new_releases" onChange={(e) => this.onchangeAttr({'path' : ['alert_icon'], 'value' : e.target.value})} defaultValue={this.props.action.hasIn(['content','alert_icon']) ? this.props.action.getIn(['content','alert_icon']) : "new_releases"} className="form-control form-control-sm" />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Color E.g green, #509646</label>
                            <input type="text" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','aicon_color'], 'value' : e.target.value})} defaultValue={this.props.action.hasIn(['content','attr_options','aicon_color']) ? this.props.action.getIn(['content','attr_options','aicon_color']) : ""} className="form-control form-control-sm" />
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','show_alert'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','show_alert'])} /> Show alert. We will show alert if this icon is added to the chat.</label>
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','remove_icon'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','remove_icon'])} /> Remove icon if it exists.</label>
                        </div>
                    </div>
                </div>

                <hr className="hr-big" />

            </div>
        );
    }
}

export default NodeTriggerActionAlertIcon;
