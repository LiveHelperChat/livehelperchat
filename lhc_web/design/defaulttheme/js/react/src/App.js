import React, { Component } from 'react';
import NodeGroups from './components/NodeGroups';
import NodeTriggerBuilder from './components/NodeTriggerBuilder';
import NodeTriggerBuilderPreview from './components/NodeTriggerBuilderPreview';

class App extends Component {
  render() {
    return (
      <div className="row">
        <div className="col-xs-3">
              <NodeGroups botId={this.props.botId} />
        </div>
        <div className="col-xs-6">
              <NodeTriggerBuilder botId={this.props.botId} />
        </div>
        <div className="col-xs-3">
              <NodeTriggerBuilderPreview />
        </div>
      </div>
    );
  }
}

export default App;
