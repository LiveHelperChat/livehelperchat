import React, { Component } from 'react';
import NodeGroupTrigger from './NodeGroupTrigger';
import NodeGroupTriggerEvents from './NodeGroupTriggerEvents';
import { connect } from "react-redux";

import { fetchNodeGroupTriggers, addTrigger, addTriggerEvent, updateTriggerEvent, deleteTriggerEvent, deleteGroup } from "../actions/nodeGroupTriggerActions";
import { updateNodeGroup } from "../actions/nodeGroupActions";

@connect((store) => {
    return {
        nodegrouptriggers: store.nodegrouptriggers,
        currenttrigger: store.currenttrigger
    };
})

class NodeGroup extends Component {

    constructor(props) {
        super(props);
        this.state = {position: this.props.group.get('pos')};
        this.addTriggerEvent = this.addTriggerEvent.bind(this);
        this.updateEvent = this.updateEvent.bind(this);
        this.deleteEvent = this.deleteEvent.bind(this);
    }

    handleChange(e)
    {
        const name = e.target.value;
        this.props.changeTitle(this.props.group.set('name',name));
    }

    handleCollapse()
    {
        this.props.dispatch(updateNodeGroup(this.props.group.set('is_collapsed',!this.props.group.get('is_collapsed'))));
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
        if (confirm('Are you sure?')){
            this.props.dispatch(deleteGroup({id: this.props.group.get('id')}));
        }
    }

    setPosition() {
        this.props.dispatch(updateNodeGroup(this.props.group.set('pos',this.state.position)));
    }

    render() {

        if (this.props.nodegrouptriggers.get('nodegrouptriggers').has(this.props.group.get('id'))) {
            var mappedNodeGroupTriggers = this.props.nodegrouptriggers.get('nodegrouptriggers').get(this.props.group.get('id')).map(nodegrouptrigger =><NodeGroupTrigger triggerId={this.props.triggerId} key={nodegrouptrigger.get('id')} trigger={nodegrouptrigger}  />);
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
                    <hr className="my-2" />

                    <div className="d-flex">
                        <div className="flex-grow-1">
                            <div className="input-group">
                                <span><i className={classNameCurrent} title={'Bot Id - '+this.props.group.get('bot_id')}>home</i></span>
                                <input className="form-control form-control-sm gbot-group-name" value={this.props.group.get('name')} onChange={this.handleChange.bind(this)} />
                            </div>
                        </div>
                        <div>
                            <div className="btn-toolbar">
                                <div className="input-group input-group-sm me-2">
                                    <input type="number" title="Position" onChange={(e) => this.setState({position: parseInt(e.target.value)})} className="form-control" style={{"width" : "65px"}} defaultValue={this.props.group.get('pos')} placeholder="Position" aria-label="Input group example" aria-describedby="btnGroupAddon" />
                                    <button className="btn btn-secondary" disabled={this.props.group.get('pos') == this.state.position} onClick={this.setPosition.bind(this)} type="button" id="button-addon1"><span className="material-icons me-0">done</span></button>
                                </div>
                                <div className="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                    <button title="Collapse/Expand" className="btn btn-sm btn-secondary float-end" onClick={this.handleCollapse.bind(this)} ><span className="material-icons me-0">{this.props.group.get('is_collapsed') ? 'unfold_more' : 'unfold_less'}</span> <span className="fs11">{this.props.nodegrouptriggers.get('nodegrouptriggers').get(this.props.group.get('id')) ? "["+this.props.nodegrouptriggers.get('nodegrouptriggers').get(this.props.group.get('id')).size+"]" : ""}</span></button>
                                    <a className="btn btn-sm btn-secondary float-end" href={WWW_DIR_JAVASCRIPT + "genericbot/downloadbotgroup/" + this.props.group.get('id')}><i className="material-icons me-0">cloud_download</i></a>
                                    <button className="btn btn-sm btn-danger float-end" onClick={this.deleteGroup.bind(this)}><i className="material-icons me-0">delete</i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className={this.props.group.get('is_collapsed') ? "d-none" : ""}>

                        <div className="row">
                            <div className="col-12">
                                <ul className="gbot-trglist">
                                    {mappedNodeGroupTriggers}
                                    <li><button className="btn btn-sm btn-secondary" onClick={this.addTrigger.bind(this)} ><i className="material-icons me-0">add</i></button></li>
                                </ul>
                            </div>
                        </div>

                        {triggerAction}
                    </div>

                </div>
            </div>
        );
    }
}

export default NodeGroup;
