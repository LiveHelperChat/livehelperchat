import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeTriggerArgumentTemplate from './NodeTriggerArgumentTemplate';
import NodeTriggerList from './NodeTriggerList';

import { initRestMethods } from "../../actions/nodeGroupTriggerActions"
import { connect } from "react-redux";
import { fromJS, List } from 'immutable';


@connect((store) => {
    return {
        payloads: store.currenttrigger
    };
})

class NodeTriggerActionRestAPI extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);
        this.onRestAPIChange = this.onRestAPIChange.bind(this);
        this.onRestAPIMethodChange = this.onRestAPIMethodChange.bind(this);
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

    componentDidMount() {
        if (this.props.action.hasIn(['content','rest_api'])) {
            this.props.dispatch(initRestMethods(this.props.action.getIn(['content','rest_api'])));

            // We have to change value if it's a list
            if (List.isList(this.props.action.getIn(['content','rest_api_method_output']))){
                this.onchangeAttr({'path' : ['rest_api_method_output'], 'value' : fromJS({})});
            }

            if (List.isList(this.props.action.getIn(['content','rest_api_method_params']))){
                this.onchangeAttr({'path' : ['rest_api_method_params'], 'value' : fromJS({})});
            }
        }
    }

    onRestAPIChange(e) {
        this.props.dispatch(initRestMethods(e));
        this.onchangeAttr({'path' : ['rest_api'], 'value' : e});
        this.onchangeAttr({'path' : ['rest_api_method'], 'value' : null});

        this.onchangeAttr({'path' : ['rest_api_method_params'], 'value' : fromJS({})});
        this.onchangeAttr({'path' : ['rest_api_method_output'], 'value' : fromJS({})});
    }

    onRestAPIMethodChange(e) {
        this.onchangeAttr({'path' : ['rest_api_method'], 'value' : e});
        this.onchangeAttr({'path' : ['rest_api_method_params'], 'value' : fromJS({})});
        this.onchangeAttr({'path' : ['rest_api_method_output'], 'value' : fromJS({})});
    }

    render() {

        var list = this.props.payloads.get('rest_api_calls').map((option, index) => <option key={option.get('id')} value={option.get('id')}>{option.get('name')}</option>);

        const indexOfListingToUpdate = this.props.payloads.getIn(['rest_api_calls']).findIndex(listing => {
            return listing.get('id') == this.props.action.getIn(['content','rest_api']);
        });

        var listMethods = [];
        var userParams = []
        var outputCombinations = []

        if (indexOfListingToUpdate !== -1 && this.props.payloads.hasIn(['rest_api_calls',indexOfListingToUpdate,'methods'])) {
            listMethods = this.props.payloads.getIn(['rest_api_calls',indexOfListingToUpdate,'methods']).map((option, index) => <option key={option.get('id')} value={option.get('id')}>{option.get('name')}</option>);

            if (this.props.action.hasIn(['content','rest_api_method'])){
                const indexOfListingToUpdateMethod = this.props.payloads.getIn(['rest_api_calls',indexOfListingToUpdate,'methods']).findIndex(listing => {
                    return listing.get('id') == this.props.action.getIn(['content','rest_api_method']);
                });

                if (indexOfListingToUpdateMethod !== -1) {
                    userParams = this.props.payloads.getIn(['rest_api_calls',indexOfListingToUpdate,'methods',indexOfListingToUpdateMethod,'userparams']).map((option, index) =>
                        <div className="form-group" key={option.get('id')}>
                            <label>{option.get('value')}</label>
                            <input className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['rest_api_method_params',option.get('id')], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','rest_api_method_params',option.get('id')])} type="text" />
                        </div>
                    );

                    outputCombinations = this.props.payloads.getIn(['rest_api_calls',indexOfListingToUpdate,'methods',indexOfListingToUpdateMethod,'output']).map((option, index) => <div key={option.get('id')} className="form-group">
                        <div className="row">
                            <div className="col-6">
                                <label>Execute trigger for [<b>{option.get('success_name')}</b>]</label>
                            </div>
                            <div className="col-6">
                                <label><input type="checkbox" value="" onChange={(e) => this.onchangeAttr({'path' : ['rest_api_method_output',option.get('id') + '_chk'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','rest_api_method_output',option.get('id') + '_chk'])}  /> Check only this response. <i className="material-icons" title="By default we are checking all output combinations. You can force to check only selected ones.">info</i></label>
                            </div>
                        </div>

                        <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path':['rest_api_method_output',option.get('id')],'value':e})} payload={this.props.action.getIn(['content','rest_api_method_output',option.get('id')])} />
                    </div>);


                }
            }
        }

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
                            <label>Rest API</label>
                            <select className="form-control form-control-sm" defaultValue={this.props.action.getIn(['content','rest_api'])} onChange={(e) => this.onRestAPIChange(e.target.value)}>
                                <option value="">Choose a Rest API</option>
                                {list}
                            </select>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Method</label>
                            <select className="form-control form-control-sm" value={this.props.action.getIn(['content','rest_api_method'])} onChange={(e) => this.onRestAPIMethodChange(e.target.value)}>
                                <option value="">Choose a method</option>
                                {listMethods}
                            </select>
                        </div>
                    </div>
                </div>
                {userParams}
                {outputCombinations}

                <div className="form-group">
                    <label>Default trigger to execute</label>
                    <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path':['rest_api_method_output','default_trigger'],'value':e})} payload={this.props.action.getIn(['content','rest_api_method_output','default_trigger'])} />
                </div>

                <div className="form-group">
                    <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','background_process'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','background_process'])} /> Send Rest API Call in the background.</label> <i className="material-icons" title="You have to be using lhc-php-resque extension.">info</i>
                </div>

                <hr className="hr-big" />

            </div>
        );
    }
}

export default NodeTriggerActionRestAPI;
