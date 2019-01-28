import React, { Component } from 'react';
import NodeTriggerList from '../NodeTriggerList';

class NodeActionIntentItem extends Component {

    constructor(props) {
        super(props);
        this.onIncludeWordsChange = this.onIncludeWordsChange.bind(this);
        this.onExcludeWordsChange = this.onExcludeWordsChange.bind(this);
        this.onTypos = this.onTypos.bind(this);
        this.onTyposExc = this.onTyposExc.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);
    }

    onIncludeWordsChange(payload) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content', 'words'], value :  payload});
    }

    onMatchIncludeWordsChange(payload) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content', 'words_match'], value :  payload});
    }

    onExcludeWordsChange(payload) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content', 'exc_words'], value : payload});
    }

    deleteField() {
        this.props.onDeleteField(this.props.id);
    }

    onchangeAttr(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content'].concat(e.path), value : e.value});
    }

    onTypos(payload) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content', 'words_typo'], value :  payload});
    }

    onTyposExc(payload) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content', 'exc_words_typo'], value : payload});
    }

    render() {
        return (
            <div className="row">
                <div className="col-12">
                    <hr/>
                </div>
                <div className="col-6">
                    <div className="form-group">
                        <label>Should include any of these words</label>
                        <input type="text" placeholder="yes, thanks" className="form-control form-control-sm" onChange={(e) => this.onIncludeWordsChange(e.target.value)} defaultValue={this.props.action.getIn(['content','words'])} />
                    </div>
                </div>
                <div className="col-6">
                    <div className="form-group">
                        <label>But not any of these</label>
                        <input type="text" placeholder="no, nop" className="form-control form-control-sm" onChange={(e) => this.onExcludeWordsChange(e.target.value)} defaultValue={this.props.action.getIn(['content','exc_words'])} />
                    </div>
                </div>
                <div className="col-12">
                    <div className="form-group">
                        <label>Or preg match, new match per line</label>
                        <textarea className="form-control form-control-sm" placeholder="Match rule ==> Matched item number to use ==> identifier" onChange={(e) => this.onMatchIncludeWordsChange(e.target.value)} defaultValue={this.props.action.getIn(['content','words_match'])}></textarea>
                    </div>
                </div>
                <div className="col-6">
                    <div className="form-group">
                        <label>Number of typos allowed</label>
                        <input type="text" placeholder="0" className="form-control form-control-sm" onChange={(e) => this.onTypos(e.target.value)} defaultValue={this.props.action.getIn(['content','words_typo'])} />
                    </div>
                </div>
                <div className="col-6">
                    <div className="form-group">
                        <label>Number of typos allowed</label>
                        <input type="text" placeholder="0" className="form-control form-control-sm" onChange={(e) => this.onTyposExc(e.target.value)} defaultValue={this.props.action.getIn(['content','exc_words_typo'])} />
                    </div>
                </div>

                <div className="col-12">

                    <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['only_these'],'value' : e.target.checked})} defaultChecked={this.props.action.getIn(['content','only_these'])} /> Should include only words from above, not any.</label>

                    <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['exec_insta'],'value' : e.target.checked})} defaultChecked={this.props.action.getIn(['content','exec_insta'])} /> Do not schedule execution of this but execute it instantly. Blocks execution of all other responses and checks.</label>

                    <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['exec_cont'],'value' : e.target.checked})} defaultChecked={this.props.action.getIn(['content','exec_cont'])} /> Continue workflow if stop is returned from executed trigger.</label>

                    <div className="form-group">
                        <label>Schedule this trigger for execution</label>
                        <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['trigger_id'], value : e})} payload={this.props.action.getIn(['content','trigger_id'])} />
                    </div>
                </div>

                <div className="col-12">
                    <div className="btn-group float-right" role="group" aria-label="Trigger actions">
                        <button className="btn btn-warning btn-sm" onClick={this.deleteField.bind(this)}>Delete</button>
                    </div>
                </div>
            </div>
        );
    }
}

export default NodeActionIntentItem;
