import React, { Component } from 'react';

class ErrorBoundary extends React.Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false };
    }

    componentDidCatch(error, info) {
        // Display fallback UI
        this.setState({ hasError: true, error : error, info : info });

        var e;
        e = {};
        e.stack = error.stack ? JSON.stringify(error.stack) : "";
        e.stack = e.stack.replace(/(\r\n|\n|\r)/gm, "");
        var xhr = new XMLHttpRequest();
        xhr.open( "POST",  window.lhcChat['base_url'] + 'audit/logjserror', true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send( "data=" + encodeURIComponent( JSON.stringify(e) ) );
    }

    render() {
        if (this.state.hasError) {
            // You can render any custom fallback UI
            return <p></p>;
            //return this.props.children;
        } else {
            return this.props.children;
        }

    }
}

export default ErrorBoundary;