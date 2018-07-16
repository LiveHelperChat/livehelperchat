import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';

class NodeTriggerActionVideo extends Component {

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
        return (
            <div>
                <div className="row">
                    <div className="col-xs-11">
                        <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                    </div>
                    <div className="col-xs-1">
                        <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm pull-right">
                            <i className="material-icons mr-0">delete</i>
                        </button>
                    </div>
                </div>

                <div className="row">
                    <div className="col-xs-6">
                        <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['video_options','autoplay'], 'value' : e.target.checked})} defaultChecked={this.props.action.getIn(['content','video_options','autoplay'])} /> Autoplay</label>
                    </div>
                    <div className="col-xs-6">
                        <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['video_options','controls'], 'value' : e.target.checked})} defaultChecked={this.props.action.getIn(['content','video_options','controls'])} /> Controls</label>
                    </div>
                    <div className="col-xs-12">
                        <div className="form-group">
                            <label>Video URL</label>
                            <input type="text" placeholder="Video URL" className="form-control" onChange={(e) => this.onchangeAttr({'path' : ['payload'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])}/>
                        </div>
                    </div>
                </div>
                <hr/>

            </div>
        );
    }
}

export default NodeTriggerActionVideo;
