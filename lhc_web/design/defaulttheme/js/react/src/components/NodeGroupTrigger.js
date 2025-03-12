import React, { Component } from 'react';

import { fetchNodeGroupTriggerAction, removeTrigger, setDefaultTrigger, setDefaultUnknownTrigger, setDefaultAlwaysTrigger, setInProgressTrigger, setDefaultUnknownBtnTrigger, makeTriggerCopy, setTriggerGroup, setAsArgumentTrigger, setTriggerPosition } from "../actions/nodeGroupTriggerActions"
import { connect } from "react-redux";

@connect((store) => {
    return {
        currenttrigger: store.currenttrigger,
        nodegroups: store.nodegroups
    };
})

class NodeGroupTrigger extends Component {

    constructor(props) {
        super(props);
        this.state = {isCurrent: false, changingGroup: false, position: this.props.trigger.get('pos')};
        this.removeTrigger = this.removeTrigger.bind(this);
        this.setDefaultTrigger = this.setDefaultTrigger.bind(this);
        this.setDefaultUnknownTrigger = this.setDefaultUnknownTrigger.bind(this);
        this.setDefaultUnknownBtnTrigger = this.setDefaultUnknownBtnTrigger.bind(this);
        this.setDefaultAlwaysTrigger = this.setDefaultAlwaysTrigger.bind(this);
        this.setInProgressTrigger = this.setInProgressTrigger.bind(this);
        this.setAsArgumentTrigger = this.setAsArgumentTrigger.bind(this);
        this.changeGroup = this.changeGroup.bind(this);
        this.makeCopy = this.makeCopy.bind(this);

        if (this.props.triggerId == this.props.trigger.get('id')) {
            this.props.dispatch(fetchNodeGroupTriggerAction(this.props.trigger.get('id')))
        }
    }

    loadTriggerActions() {
        this.props.dispatch(fetchNodeGroupTriggerAction(this.props.trigger.get('id')))
    }

    removeTrigger () {
        this.props.dispatch(removeTrigger(this.props.trigger));
    }
    
    setDefaultTrigger(e) {
        const value = e.target.checked;
        this.props.dispatch(setDefaultTrigger(this.props.trigger.set('default',value == true ? 1 : 0)));
    }

    setAsArgumentTrigger(e) {
        const value = e.target.checked;
        this.props.dispatch(setAsArgumentTrigger(this.props.trigger.set('as_argument',value == true ? 1 : 0)));
    }

    setDefaultUnknownTrigger(e) {
        const value = e.target.checked;
        this.props.dispatch(setDefaultUnknownTrigger(this.props.trigger.set('default_unknown',value == true ? 1 : 0)));
    }

    setDefaultUnknownBtnTrigger(e) {
        const value = e.target.checked;
        this.props.dispatch(setDefaultUnknownBtnTrigger(this.props.trigger.set('default_unknown_btn',value == true ? 1 : 0)));
    }

    setDefaultAlwaysTrigger(e) {
        const value = e.target.checked;
        this.props.dispatch(setDefaultAlwaysTrigger(this.props.trigger.set('default_always',value == true ? 1 : 0)));
    }

    setInProgressTrigger(e) {
        const value = e.target.checked;
        this.props.dispatch(setInProgressTrigger(this.props.trigger.set('in_progress',value == true ? 1 : 0)));
    }

    changeGroup(state, group_id) {

        if (this.props.currenttrigger.getIn(['currenttrigger','id']) == this.props.trigger.get('id')) {
            alert("Please cancel trigger editing before changing it's group!");
            return;
        }

        this.setState({changingGroup: state});

        if (group_id) {
            this.props.dispatch(setTriggerGroup(this.props.trigger.set('group_id',parseInt(group_id)),this.props.trigger.get('group_id')));
        }
    }

    makeCopy() {
        this.props.dispatch(makeTriggerCopy(this.props.trigger));
    }

    shouldComponentUpdate(nextProps, nextState) {
        
        if (this.props.trigger !== nextProps.trigger || this.props.nodegroups !== nextProps.nodegroups) {
            return true;
        }

        if (nextProps.currenttrigger.getIn(['currenttrigger','id']) === this.props.trigger.get('id') && this.state.isCurrent == false) {
            this.state.isCurrent = true;
            return true;
        }

        if (nextProps.currenttrigger.getIn(['currenttrigger','id']) !== this.props.trigger.get('id') && this.state.isCurrent == true) {
            this.state.isCurrent = false;
            return true;
        }

        if (this.state.changingGroup != nextState.changingGroup){
            return true;
        }

        if (this.state.position != nextState.position){
            return true;
        }

        return false;
    }
    setPosition() {
        this.props.dispatch(setTriggerPosition(this.props.trigger.set('pos',this.state.position)));
    }

    render() {

        var classNameCurrent = 'btn btn-secondary btn-sm';
        if (this.props.currenttrigger.get('currenttrigger').get('id') === this.props.trigger.get('id')) {
            classNameCurrent = 'btn btn-secondary btn-sm btn-success';
        }

        if (this.props.trigger.get('default') == 1) {
            classNameCurrent = classNameCurrent + ' default-trigger-btn';
        }

        if (this.props.trigger.get('default_unknown') == 1 || this.props.trigger.get('default_unknown_btn') == 1) {
            classNameCurrent = classNameCurrent + ' btn-warning';
        }

        if (this.props.trigger.get('default_always') == 1) {
            classNameCurrent = classNameCurrent + ' btn-info';
        }

        var list = this.props.nodegroups.get('nodegroups').sortBy(group => group.get('pos')).map(nodegroup =><option key={nodegroup.get('id')} value={nodegroup.get('id')}>{nodegroup.get('name')}</option>);

        return (
                <li>
                    <div className="btn-group trigger-btn">
                        <button onClick={this.loadTriggerActions.bind(this)} className={classNameCurrent}>{this.props.trigger.get('name')}</button>
                            <button type="button" className="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </button>
                        <ul className="dropdown-menu dropdown-menu-trigger">
                            <li className="dropdown-item"><a href="#" onClick={this.removeTrigger}><i className="material-icons">delete</i> Delete</a></li>
                            <li className="dropdown-item"><a href="#" onClick={this.makeCopy}><i className="material-icons">file_copy</i> Copy</a></li>
                            <div className="dropdown-divider"></div>
                            <li className="dropdown-item"><a href="#" onClick={(e) => this.changeGroup(!this.state.changingGroup)}><i className="material-icons">home</i> Change group</a></li>
                            <li className="dropdown-item">
                                <span><span className="material-icons text-muted">swap_vert</span><b>Trigger position in group</b></span>
                                <div className="input-group input-group-sm me-2 mt-1">
                                    <input type="number" title="Position" onChange={(e) => this.setState({position: parseInt(e.target.value)})} className="form-control" style={{"width" : "65px"}} defaultValue={this.props.trigger.get('pos')} placeholder="Position" aria-label="Input group example" aria-describedby="btnGroupAddon" />
                                    <button className="btn btn-secondary" disabled={this.props.trigger.get('pos') == this.state.position} onClick={this.setPosition.bind(this)} type="button" id="button-addon1"><span className="material-icons me-0">done</span></button>
                                </div>
                            </li>
                            <div className="dropdown-divider"></div>
                            <li className="dropdown-item"><label className="mb-0" title="This message will be send tu visitor then chat starts"><input onChange={this.setDefaultTrigger} type="checkbox" checked={this.props.trigger.get('default')}/> Default</label></li>
                            <li className="dropdown-item"><label className="mb-0" title="This message will be send to visitor then we could dot determine what we should do with a visitor message"><input onChange={this.setDefaultUnknownTrigger} type="checkbox" checked={this.props.trigger.get('default_unknown')} /> Default for unknown message</label></li>
                            <li className="dropdown-item"><label className="mb-0" title="This message will be send to visitor then we could dot determine what we should do with a button click"><input onChange={this.setDefaultUnknownBtnTrigger} type="checkbox" checked={this.props.trigger.get('default_unknown_btn')} /> Default for unknown button click</label></li>
                            <li className="dropdown-item"><label className="mb-0" title="This trigger will be always checking independently in what process we are"><input onChange={this.setDefaultAlwaysTrigger} type="checkbox" checked={this.props.trigger.get('default_always')} /> Execute always</label></li>
                            <li className="dropdown-item"><label className="mb-0" title="This trigger will be executed if previous process has not finished yet."><input onChange={this.setInProgressTrigger} type="checkbox" checked={this.props.trigger.get('in_progress')} /> In progress trigger</label></li>
                            <li className="dropdown-item"><label className="mb-0" title="This trigger can be passed as argument. Required for it to work for Themes, Proactive chat and for trigger_id argument."><input onChange={this.setAsArgumentTrigger} type="checkbox" checked={this.props.trigger.get('as_argument')} /> Can be passed as argument <b>{this.props.trigger.get('id')}</b></label></li>
                        </ul>
                    </div>


                    {this.state.changingGroup && <div className="btn-group ms-1">

                        <select className="form-control form-control-sm" onChange={(e) => this.changeGroup(false, e.currentTarget.value)} value={this.props.trigger.get('group_id')}>{list}</select>

                        <button type="button" className="btn btn-sm btn-warning" onClick={(e) => this.changeGroup(false)}><span className="material-icons me-0">close</span></button>

                    </div>}


                </li>
        );
    }
}

export default NodeGroupTrigger;
