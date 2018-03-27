import React, { Component } from 'react';
import NodeGroups from './components/NodeGroups';

class App extends Component {
  render() {
    return (
      <div className="row">
        <div className="col-xs-4">
              <NodeGroups />
        </div>
        <div className="col-xs-4">
              REsponse
        </div>
        <div className="col-xs-4">
              Pewview
        </div>
      </div>
    );
  }
}

export default App;
