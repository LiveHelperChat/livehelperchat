import React, { Component } from 'react';

import NodeTriggerPayloadList from '../builder/NodeTriggerPayloadList';

class NodeGroupTriggerEvent extends Component {

    constructor(props) {
        super(props);
        this.typeChange = this.typeChange.bind(this);
        this.textChange = this.textChange.bind(this);
        this.textChangeExc = this.textChangeExc.bind(this);
        this.payloadChange = this.payloadChange.bind(this);
        this.deleteEvent = this.deleteEvent.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);
    }

    typeChange(e) {
        this.props.updateEvent(this.props.event.set('type',e.target.value));
    }

    textChange(e) {
        this.props.updateEvent(this.props.event.set('pattern',e.target.value));
    }

    payloadChange(payload) {
        this.props.updateEvent(this.props.event.set('pattern',payload));
    }

    textChangeExc(e) {
        this.props.updateEvent(this.props.event.set('pattern_exc',e.target.value));
    }

    deleteEvent() {
        this.props.deleteEvent(this.props.event);
    }

    onchangeAttr(payload){
        this.props.updateEvent(this.props.event.setIn(payload.path,payload.value));
    }

    render() {

        var typeRender;
        if (this.props.event.get('type') == 0)
        {
            typeRender = <input onChange={this.textChange} placeholder="Matching text phrase" type="text" className="form-control input-sm" value={this.props.event.get('pattern')} />;
        } else if (this.props.event.get('type') == 2) {
            typeRender =
                <div className="row">
                    <div className="col-12">
                        <div className="form-group">
                            <label>Should include any of these words</label>
                            <input type="text" placeholder="yes, thanks" className="form-control input-sm" onChange={this.textChange} value={this.props.event.get('pattern')} />
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label>But not any of these</label>
                            <input type="text" placeholder="no, nop" className="form-control input-sm" onChange={this.textChangeExc} value={this.props.event.get('pattern_exc')} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Number of typos allowed</label>
                            <input type="text" placeholder="0" className="form-control input-sm" onChange={(e) => this.onchangeAttr({'path' : ['configuration_array','words_typo'],'value' : e.target.value})} defaultValue={this.props.event.getIn(['configuration_array','words_typo'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Number of typos allowed</label>
                            <input type="text" placeholder="0" className="form-control input-sm" onChange={(e) => this.onchangeAttr({'path' : ['configuration_array','exc_words_typo'],'value' : e.target.value})} defaultValue={this.props.event.getIn(['configuration_array','exc_words_typo'])} />
                        </div>
                    </div>

                    <div className="col-12">
                        <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['configuration_array','only_these'],'value' : e.target.checked})} defaultChecked={this.props.event.getIn(['configuration_array','only_these'])} /> Should include only words from above, not any.</label>
                    </div>
                </div>
        } else {
            typeRender = <NodeTriggerPayloadList showOptional={true} onSetPayload={this.payloadChange} payload={this.props.event.get('pattern')} />;
        }

        return (
            <div className="row">
                <div className="col-12">
                    <div className="form-group">

                        <div className="row">
                            <div className="col-10">
                                <label>Type</label>
                            </div>
                            <div className="col-2">
                                <a className="pull-right" onClick={this.deleteEvent}><i className="material-icons mr-0">delete</i></a>
                            </div>
                        </div>

                        <select className="form-control input-sm" defaultValue={this.props.event.get('type')} onChange={this.typeChange}>
                            <option value="0">Text</option>
                            <option value="1">Click</option>
                            <option value="2">Custom text matching</option>
                        </select>
                    </div>
                </div>
                <div className="col-12">
                        {typeRender}
                </div>
                <div className="col-12">
                    <hr/>
                </div>
            </div>
        );
    }
}

export default NodeGroupTriggerEvent;
