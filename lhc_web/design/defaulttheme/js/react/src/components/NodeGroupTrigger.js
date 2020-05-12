import React, { Component } from 'react';

import { fetchNodeGroupTriggerAction, removeTrigger, setDefaultTrigger, setDefaultUnknownTrigger, setDefaultAlwaysTrigger, setDefaultUnknownBtnTrigger, makeTriggerCopy } from "../actions/nodeGroupTriggerActions"
import { connect } from "react-redux";

@connect((store) => {
    return {
        currenttrigger: store.currenttrigger
    };
})

class NodeGroupTrigger extends Component {

    constructor(props) {
        super(props);
        this.state = {isCurrent: false};
        this.removeTrigger = this.removeTrigger.bind(this);
        this.setDefaultTrigger = this.setDefaultTrigger.bind(this);
        this.setDefaultUnknownTrigger = this.setDefaultUnknownTrigger.bind(this);
        this.setDefaultUnknownBtnTrigger = this.setDefaultUnknownBtnTrigger.bind(this);
        this.setDefaultAlwaysTrigger = this.setDefaultAlwaysTrigger.bind(this);
        this.makeCopy = this.makeCopy.bind(this);
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

    makeCopy() {
        this.props.dispatch(makeTriggerCopy(this.props.trigger));
    }

    shouldComponentUpdate(nextProps, nextState) {
        
        if (this.props.trigger !== nextProps.trigger) {
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

        return false;
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

        //<li><a href="#" ng-click="changeGroup(trigger)"><i class="material-icons">&#xE8D2;</i>Change Group</a></li>
        //<li><a href="#" ng-click="duplicateTrigger(trigger)"><i class="material-icons">&#xE14D;</i>Duplicate</a></li>

        return (
                <li>
                    <div class="btn-group trigger-btn">
                        <button onClick={this.loadTriggerActions.bind(this)} className={classNameCurrent}>{this.props.trigger.get('name')}</button>
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </button>
                        <ul class="dropdown-menu dropdown-menu-trigger">
                            <li className="dropdown-item"><a href="#" onClick={this.removeTrigger}><i class="material-icons">delete</i> Delete</a></li>
                            <li className="dropdown-item"><a href="#" onClick={this.makeCopy}><i class="material-icons">file_copy</i> Copy</a></li>
                            <li className="dropdown-item"><label title="This message will be send tu visitor then chat starts"><input onChange={this.setDefaultTrigger} type="checkbox" checked={this.props.trigger.get('default')} />Default</label></li>
                            <li className="dropdown-item"><label title="This message will be send to visitor then we could dot determine what we should do with a visitor message"><input onChange={this.setDefaultUnknownTrigger} type="checkbox" checked={this.props.trigger.get('default_unknown')} />Default for unknown message</label></li>
                            <li className="dropdown-item"><label title="This message will be send to visitor then we could dot determine what we should do with a button click"><input onChange={this.setDefaultUnknownBtnTrigger} type="checkbox" checked={this.props.trigger.get('default_unknown_btn')} />Default for unknown button click</label></li>
                            <li className="dropdown-item"><label title="This trigger will be always checking independently in what process we are"><input onChange={this.setDefaultAlwaysTrigger} type="checkbox" checked={this.props.trigger.get('default_always')} />Execute always</label></li>
                        </ul>
                    </div>
                </li>
        );
    }
}

export default NodeGroupTrigger;
