import React, { Component } from 'react';
import NodeGroupTrigger from './NodeGroupTrigger';
import NodeGroupTriggerEvents from './NodeGroupTriggerEvents';
import { connect } from "react-redux";

import { fetchNodeGroupTriggers, addTrigger, addTriggerEvent, updateTriggerEvent, deleteTriggerEvent, deleteGroup } from "../actions/nodeGroupTriggerActions"

@connect((store) => {
    return {
        nodegrouptriggers: store.nodegrouptriggers,
        currenttrigger: store.currenttrigger
    };
})

class NodeGroup extends Component {

    handleChange(e) {
        const name = e.target.value;
        this.props.changeTitle(this.props.group.set('name',name));
        this.addTriggerEvent = this.addTriggerEvent.bind(this);
        this.updateEvent = this.updateEvent.bind(this);
        this.deleteEvent = this.deleteEvent.bind(this);
    }

    addTrigger() {
        this.props.dispatch(addTrigger({id: this.props.group.get('id')}));
    }

    componentWillMount() {
        this.props.dispatch(fetchNodeGroupTriggers(this.props.group.get('id')))
    }

    /*
    * Add trigger event
    * */
    addTriggerEvent() {
          this.props.dispatch(addTriggerEvent({id: this.props.currenttrigger.getIn(['currenttrigger','id'])}));
    }

    updateEvent(event) {
        this.props.dispatch(updateTriggerEvent(event));
    }

    deleteEvent(event) {
        this.props.dispatch(deleteTriggerEvent(event));
    }

    deleteGroup() {
        this.props.dispatch(deleteGroup({id: this.props.group.get('id')}));
    }

    /*shouldComponentUpdate(nextProps, nextState) {

        if (this.props.group !== nextProps.group) {
            return true;
        }

        if (nextProps.nodegrouptriggers !== this.props.nodegrouptriggers)
        {
            return true;
        }

        return false;
    }*/

    render() {

        if (this.props.nodegrouptriggers.get('nodegrouptriggers').has(this.props.group.get('id'))) {
            var mappedNodeGroupTriggers = this.props.nodegrouptriggers.get('nodegrouptriggers').get(this.props.group.get('id')).map(nodegrouptrigger =><NodeGroupTrigger key={nodegrouptrigger.get('id')} trigger={nodegrouptrigger}  />);
        } else {
            var mappedNodeGroupTriggers = "";
        }

        if (this.props.currenttrigger.getIn(['currenttrigger','id']) && this.props.currenttrigger.getIn(['currenttrigger','group_id']) == this.props.group.get('id')){
            var triggerAction = <NodeGroupTriggerEvents deleteEvent={this.deleteEvent.bind(this)} addEvent={this.addTriggerEvent.bind(this)} updateEvent={this.updateEvent.bind(this)} trigger={this.props.currenttrigger.get('currenttrigger')} />;
        } else {
            var triggerAction = "";
        }

        var classNameCurrent = "material-icons chat-active";

        if (this.props.group.get('bot_id') != this.props.botId) {
            classNameCurrent = "material-icons chat-unread";
        }

        return (
            <div className="row">
                <div className="col-12">
                    <hr/>

                    <div className="row">
                        <div className="col-10">
                            <div className="input-group">
                                <div className="input-group-prepend">
                                    <span><i className={classNameCurrent} title={'Bot Id - '+this.props.group.get('bot_id')}>&#xf2dc;</i></span>
                                </div>
                                <input className="form-control form-control-sm gbot-group-name" value={this.props.group.get('name')} onChange={this.handleChange.bind(this)} />
                            </div>
                        </div>
                        <div className="col-2">
                            <button className="btn btn-sm btn-secondary float-right" onClick={this.deleteGroup.bind(this)}><i className="material-icons mr-0">&#xf1c0;</i></button>
                        </div>
                    </div>

                    <div className="row">
                        <div className="col-12">
                            <ul className="gbot-trglist">
                                {mappedNodeGroupTriggers}
                                <li><button className="btn btn-sm btn-secondary" onClick={this.addTrigger.bind(this)} ><i className="material-icons mr-0">&#xf415;</i></button></li>
                            </ul>
                        </div>
                    </div>
                    
                    {triggerAction}
                </div>
            </div>
        );
    }
}

export default NodeGroup;
