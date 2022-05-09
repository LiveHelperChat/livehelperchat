import React, {Component} from 'react';
import NodeGroups from './components/NodeGroups';
import NodeTriggerBuilder from './components/NodeTriggerBuilder';
import NodeTriggerBuilderPreview from './components/NodeTriggerBuilderPreview';
import {fetchNodeGroupTriggerAction} from "./actions/nodeGroupTriggerActions"
import {connect} from "react-redux";

@connect((store) => {
    return {
        currenttrigger: store.currenttrigger,
        nodegroups: store.nodegroups
    };
})

class App extends Component {

    constructor(props) {
        super(props);
        this.hashChanged = this.hashChanged.bind(this);
    }

    componentDidMount() {
        window.addEventListener("hashchange", this.hashChanged, false);
    }

    componentWillUnmount() {
        window.removeEventListener("hashchange", this.hashChanged, false);
    }

    hashChanged() {
        var hash = window.location.hash;
        if (hash != '') {
            var matchData = hash.match(/\d+$/);
            if (matchData !== null && matchData[0]) {
                if (parseInt(this.props.currenttrigger.getIn(['currenttrigger','id'])) != parseInt(matchData[0])) {
                    this.props.dispatch(fetchNodeGroupTriggerAction(parseInt(matchData[0])))
                }
            }
        }
    }

    render() {
        return (
            <div className="row">
                <div className="col-4">
                    <NodeGroups triggerId={this.props.triggerId} botId={this.props.botId}/>
                </div>
                <div className="col-5">
                    <NodeTriggerBuilder botId={this.props.botId}/>
                </div>
                <div className="col-3">
                    <NodeTriggerBuilderPreview/>
                </div>
            </div>
        );
    }
}

export default App;
