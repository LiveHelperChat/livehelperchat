import React, { Component } from 'react';
import {fromJS} from 'immutable';

import NodeGroupTriggerEvent from './events/NodeGroupTriggerEvent';
import { connect } from "react-redux";
import {saveEventTemplate, deleteEventTemplate, loadEventTemplate} from "../actions/nodeGroupTriggerActions"

@connect((store) => {
    return {
        payloads: store.currenttrigger
    };
})

class NodeGroupTriggerEvents extends Component {

    constructor(props) {
        super(props);
        this.addEvent = this.addEvent.bind(this);
        this.deleteEvent = this.deleteEvent.bind(this);
        this.state = {templateName : '', template: '', templateNameTemporary: ''};
    }

    addEvent() {
        this.props.addEvent();
    }

    updateEvent(event) {
        this.props.updateEvent(event);
    }

    deleteEvent(event) {
        this.props.deleteEvent(event);
    }

    saveTemplate(e) {
        this.props.dispatch(saveEventTemplate({
            "actions" : this.props.trigger.get('events').toJS(),
            "name" : this.state.templateName
        }));
    }

    loadTemplate(){
        this.props.dispatch(loadEventTemplate({
            id: this.state.template,
            trigger_id: this.props.payloads.getIn(['currenttrigger','id'])
        }));
        this.setState({ templateName: this.state.templateNameTemporary});
    }

    deleteTemplate() {
        this.props.dispatch(deleteEventTemplate({id:this.state.template}));
        this.setState({template: '', templateNameTemporary:''});
    }

    setTemplateTemporary(e) {
        this.setState({template: e.target.value, templateNameTemporary: (e.target.value != '' ? e.target.options[e.target.selectedIndex].text : '')});
    }

    render() {

        const mappedEvents = this.props.trigger.get('events').map(event => <NodeGroupTriggerEvent deleteEvent={this.deleteEvent.bind(this)} updateEvent={this.updateEvent.bind(this)} key={event.get('id')} event={event}/>);

        const eventTemplates = this.props.payloads.get('event_templates').map(template => <option key={template.get('id')} value={template.get('id')} >{template.get('name')}</option>)

        return (
            <div className="row">
                <div className="col-12">

                    <div className="row mt-2">
                        <div className="col-6">
                            <div className="input-group input-group-sm mb-3">
                                <input type="text" disabled={this.props.trigger.get('events').size == 0} className="form-control" placeholder="Template name" title="If you set same name as existing template we will update it" value={this.state.templateName} onChange={(e) => this.setState({'templateName' : e.target.value})} aria-label="Template name" aria-describedby="basic-addon2" />
                                <button type="button" disabled={this.props.trigger.get('events').size == 0 || this.state.templateName == ''} className="btn btn-secondary" onClick={(e) => this.saveTemplate(e)}>Save events as template</button>
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="input-group mb-3">
                                <select title="Choose event template" className="form-control form-control-sm" onChange={(e) => this.setTemplateTemporary(e)} value={this.state.template}>
                                    <option value="">Choose a template</option>
                                    {eventTemplates}
                                </select>
                                <button disabled={this.state.template == ''} className="btn btn-secondary btn-sm" title="Loading event template will replace present templates!" onClick={(e) => this.loadTemplate()}>Load</button>
                                {this.state.template != '' && <button className="btn btn-danger btn-sm" onClick={(e) => this.deleteTemplate()}>Delete</button>}
                            </div>
                        </div>
                    </div>

                    <hr className="my-1" />

                    {mappedEvents}

                    <button onClick={this.addEvent} className="btn btn-info btn-sm">Add event</button><span className="ps-2"><i><small>Saved automatically.</small></i></span>
                </div>
            </div>
        );
    }
}


export default NodeGroupTriggerEvents;