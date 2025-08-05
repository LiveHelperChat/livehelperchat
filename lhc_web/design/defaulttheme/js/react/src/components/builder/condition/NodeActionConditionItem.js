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

    upField() {
        this.props.onMoveUpField(this.props.id);
    }

    downField() {
        this.props.onMoveDownField(this.props.id);
    }

    render() {
        return (
            <div className="row">
                <div className="col-4">
                    {this.props.action.getIn(['content','comp']) !== "start_or" && <div className="form-group">
                        <label>Attribute <a href="#" onClick={(e) => {lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/cannedreplacerules'}); return false;}} className="material-icons text-muted">help</a></label>
                        <input type="text" placeholder="yes, thanks" className="form-control form-control-sm" onChange={(e) => this.onAttrChange(e.target.value)} defaultValue={this.props.action.getIn(['content','attr'])} />
                    </div>}
                </div>
                <div className="col-2">
                    <div className="form-group">
                        <label>Condition</label>
                        <select className="form-control form-control-sm" onChange={(e) => this.onConditionChange(e.target.value)} defaultValue={this.props.action.getIn(['content','comp'])} >
                            <option value="">--Choose--</option>
                            <option value="gt">&gt;</option>
                            <option value="gte">&gt;=</option>
                            <option value="lt">&lt;</option>
                            <option value="lte">&lt;=</option>
                            <option value="eq">=</option>
                            <option value="neq">!=</option>
                            <option value="like">Text like</option>
                            <option value="notlike">Text not like</option>
                            <option value="contains">Contains</option>
                            <option value="isempty">Empty</option>
                            <option value="notempty">Not Empty</option>
                            <option value="start_or">Start of OR</option>
                            <option value="in_list">In list, items separated by ||</option>
                            <option value="in_list_lowercase">In list (lowercase before comparison), items separated by ||</option>
                            <option value="not_in_list">Not in list, items separated by ||</option>
                            <option value="not_in_list_lowercase">Not in list (lowercase before comparison), items separated by ||</option>
                        </select>
                    </div>
                </div>
                <div className="col-4">
                    {this.props.action.getIn(['content','comp']) !== "start_or" &&
                        <div className="form-group">
                            <label>Value</label>
                            <input type="text" placeholder="" className="form-control form-control-sm" onChange={(e) => this.onValChange(e.target.value)} defaultValue={this.props.action.getIn(['content','val'])} />
                        </div>}
                </div>
                <div className="col-2">
                    <div><label>&nbsp;</label></div>
                    <div className="btn-group">
                        {this.props.isFirst == false && <button className="btn btn-secondary btn-sm" onClick={this.upField.bind(this)}><i className="material-icons me-0">keyboard_arrow_up</i></button>}
                        {this.props.isLast == false && <button className="btn btn-secondary btn-sm" onClick={this.downField.bind(this)}><i className="material-icons me-0">keyboard_arrow_down</i></button>}
                        <button type="button" className="btn btn-block btn-warning btn-sm" onClick={this.deleteField.bind(this)}><span className="material-icons me-0">delete</span></button>
                    </div>

                </div>

            </div>
        );
    }
}

export default NodeActionConditionItem;
