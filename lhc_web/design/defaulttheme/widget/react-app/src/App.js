import React, { Component } from 'react';
import BodyChat from './components/BodyChat';
import ChatSound from './components/ChatSound';
import ErrorBoundary from './components/ErrorBoundary';

class App extends Component {
  render() {
      return (
          <React.Fragment>
              <ErrorBoundary>
                  <ChatSound />
                  <BodyChat />
              </ErrorBoundary>
          </React.Fragment>
    );
  }
}

export default App;
