import React, { Component } from 'react';
import NodeTriggerList from '../NodeTriggerList';

class NodeActionConditionItem extends Component {

    constructor(props) {
        super(props);
        this.onAttrChange = this.onAttrChange.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);
        this.onConditionChange = this.onConditionChange.bind(this);
    }

    onAttrChange(payload) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content', 'attr'], value :  payload});
    }

    onValChange(payload) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content', 'val'], value : payload});
    }

    onConditionChange(payload) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content', 'comp'], value : payload});
    }

    deleteField() {
        this.props.onDeleteField(this.props.id);
    }

    onchangeAttr(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content'].concat(e.path), value : e.value});
    }

    render() {
        return (
            <div className="row">
                <div className="col-4">
                    <div className="form-group">
                        <label>Attribute</label>
                        <input type="text" placeholder="yes, thanks" className="form-control form-control-sm" onChange={(e) => this.onAttrChange(e.target.value)} defaultValue={this.props.action.getIn(['content','attr'])} />
                    </div>
                </div>
                <div className="col-2">
                    <div className="form-group">
                        <label>Condition</label>
                        <select className="form-control form-control-sm" onChange={(e) => this.onConditionChange(e.target.value)} defaultValue={this.props.action.getIn(['content','comp'])} >
                            <option value="gt">&gt;</option>
                            <option value="gte">&gt;=</option>
                            <option value="lt">&lt;</option>
                            <option value="lte">&lt;=</option>
                            <option value="eq">=</option>
                            <option value="neq">!=</option>
                            <option value="like">Text like</option>
                            <option value="notlike">Text not like</option>
                        </select>
                    </div>
                </div>
                <div className="col-4">
                    <div className="form-group">
                        <label>Value</label>
                        <input type="text" placeholder="" className="form-control form-control-sm" onChange={(e) => this.onValChange(e.target.value)} defaultValue={this.props.action.getIn(['content','val'])} />
                    </div>
                </div>
                <div className="col-2">
                    <div><label>&nbsp;</label></div>
                    <button type="button" className="btn btn-block btn-warning btn-sm" onClick={this.deleteField.bind(this)}>Delete</button>
                </div>

            </div>
        );
    }
}

export default NodeActionConditionItem;
