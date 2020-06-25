import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';

class NodeTriggerActionExecuteJS extends Component {

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
                            <a title="Need help?" className="float-right" onClick={(e) => this.showHelp('execute_js')}><i className="material-icons mr-0">help</i></a>
                            <label>Execute JS</label>
                            <textarea rows="3" placeholder="Write your javascript here!" onChange={(e) => this.onchangeAttr({'path' : ['payload'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} className="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Script identifier</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['ext_execute'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','ext_execute'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Arguments for the callback</label>
                            <input type="text" className="form-control form-control-sm" placeholder="{show:true}" onChange={(e) => this.onchangeAttr({'path' : ['ext_args'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','ext_args'])} />
                        </div>
                    </div>
                </div>

                <hr className="hr-big" />

            </div>
        );
    }
}

export default NodeTriggerActionExecuteJS;
