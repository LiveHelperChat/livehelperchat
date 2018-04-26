import React, { Component } from 'react';

import { fetchNodeGroupTriggerAction, removeTrigger, setDefaultTrigger } from "../actions/nodeGroupTriggerActions"
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

        var classNameCurrent = 'btn btn-default btn-xs';
        if (this.props.currenttrigger.get('currenttrigger').get('id') === this.props.trigger.get('id')) {
            classNameCurrent = 'btn btn-default btn-xs btn-success';
        }

        if (this.props.trigger.get('default') == 1) {
            classNameCurrent = classNameCurrent + ' default-trigger-btn';
        }

        //<li><a href="#" ng-click="changeGroup(trigger)"><i class="material-icons">&#xE8D2;</i>Change Group</a></li>
        //<li><a href="#" ng-click="duplicateTrigger(trigger)"><i class="material-icons">&#xE14D;</i>Duplicate</a></li>

        return (
                <li>
                    <div class="btn-group trigger-btn">
                        <button onClick={this.loadTriggerActions.bind(this)} className={classNameCurrent}>{this.props.trigger.get('name')}</button>
                        <button type="button" class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-trigger">
                            <li><a href="#" onClick={this.removeTrigger}><i class="material-icons">delete</i> Delete</a></li>
                            <li><label><input onChange={this.setDefaultTrigger} type="checkbox" checked={this.props.trigger.get('default')} />Default</label></li>
                        </ul>
                    </div>
                </li>
        );
    }
}

export default NodeGroupTrigger;
