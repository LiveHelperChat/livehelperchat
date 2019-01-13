import React, { Component } from 'react';
import {fromJS} from 'immutable';

import NodeGroupTriggerEvent from './events/NodeGroupTriggerEvent';

class NodeGroupTriggerEvents extends Component {

    constructor(props) {
        super(props);
        this.addEvent = this.addEvent.bind(this);
        this.deleteEvent = this.deleteEvent.bind(this);
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

    render() {

        const mappedEvents = this.props.trigger.get('events').map(event => <NodeGroupTriggerEvent deleteEvent={this.deleteEvent.bind(this)} updateEvent={this.updateEvent.bind(this)} key={event.get('id')} event={event}/>);

        return (
            <div className="row">
                <div className="col-12">

                    <br/>
                    {mappedEvents}

                    <button onClick={this.addEvent} className="btn btn-info btn-sm">Add event</button>
                </div>
            </div>
        );
    }
}


export default NodeGroupTriggerEvents;