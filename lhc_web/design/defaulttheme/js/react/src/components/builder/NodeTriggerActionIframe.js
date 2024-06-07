import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';

class NodeTriggerActionIframe extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);
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

    render() {

        let sampleCSS = '{"rel":"stylesheet","integrity":"sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65", "crossOrigin" : "anonymous",  "href" : "https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"}';
        let sampleFont = '{"as":"font", "rel":"preload", "type": "font/woff", "crossOrigin" : "anonymous", "href" : "path/to/font.woff2"}';
        let sampleJS = '{"src":"https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js", "async":false, "id": "script_id", "crossOrigin" : "anonymous"}';

        return (
            <div>
                <div className="d-flex flex-row">
                    <div>
                        <div className="btn-group float-start" role="group" aria-label="Trigger actions">
                            <button disabled="disabled" className="btn btn-sm btn-info">{this.props.id + 1}</button>
                            {this.props.isFirst == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.upField(this.props.id)}><i className="material-icons me-0">keyboard_arrow_up</i></button>}
                            {this.props.isLast == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.downField(this.props.id)}><i className="material-icons me-0">keyboard_arrow_down</i></button>}
                        </div>
                    </div>
                    <div className="flex-grow-1 px-2">
                        <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                    </div>
                    <div className="pe-2">
                        <div className="input-group input-group-sm">
                            <span className="input-group-text" id="basic-addon1"><span className="material-icons">vpn_key</span></span>
                            <input type="text" className="form-control" readOnly="true" value={this.props.action.getIn(['_id'])} title="Action ID"/>
                        </div>
                    </div>
                    <div className="pe-2 pt-1 text-nowrap">
                        <label className="form-check-label" title="Response will not be executed. Usefull for a quick testing."><input onChange={(e) => this.props.onChangeContent({id : this.props.id, 'path' : ['skip_resp'], value : e.target.checked})} defaultChecked={this.props.action.getIn(['skip_resp'])} type="checkbox"/> Skip</label>
                    </div>
                    <div>
                        <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm float-end">
                            <i className="material-icons me-0">delete</i>
                        </button>
                    </div>
                </div>
                <div className="row">
                    <div className="col-6">
                        <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['iframe_options','one_per_chat'], 'value' : e.target.checked})} defaultChecked={this.props.action.getIn(['content','iframe_options','one_per_chat'])} /> Only one instance will be visible at any given time.</label>
                    </div>
                    <div className="col-6">
                        <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['iframe_options','hide_op'], 'value' : e.target.checked})} defaultChecked={this.props.action.getIn(['content','iframe_options','hide_op'])} /> Hide operator nick for the message.</label>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label>Body HTML</label>
                            <textarea className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['body_html'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','body_html'])}></textarea>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Form ID</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['body_form'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','body_form'])}></input>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Iframe URL</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['iframe_url'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','iframe_url'])}></input>
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label>Iframe style</label>
                            <textarea className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['iframe_style'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','iframe_style'])}></textarea>
                            <code>border:0;width:100%;height:200px</code>
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label>CSS</label>
                            <textarea className="form-control form-control-sm" placeholder="[{css_rule},{font_options}]" onChange={(e) => this.onchangeAttr({'path' : ['payload_css'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','payload_css'])}></textarea>
                            <div>CSS Sample (include bootstrap library)</div>
                            <code>{sampleCSS}</code>
                            <div>Font Sample (just custom font)</div>
                            <code>{sampleFont} </code>
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label>JS</label>
                            <textarea className="form-control form-control-sm" placeholder="[{js_rule},..]" onChange={(e) => this.onchangeAttr({'path' : ['payload_js'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','payload_js'])}></textarea>
                            <div>JS Sample (include jQuery)</div>
                            <code>{sampleJS}</code>
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label>JS plain source code</label>
                            <textarea className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['payload_js_source'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','payload_js_source'])}></textarea>
                           </div>
                    </div>
                </div>
                <hr className="hr-big" />

            </div>
        );
    }
}

export default NodeTriggerActionIframe;
