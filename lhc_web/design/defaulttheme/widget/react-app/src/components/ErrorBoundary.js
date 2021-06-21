import React, { Component } from 'react';
import { helperFunctions } from "../lib/helperFunctions";

class ErrorBoundary extends React.Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false };
    }

    componentDidCatch(error, info) {
        // Display fallback UI
        this.setState({ hasError: true, error : error, info : info });

        helperFunctions.logJSError({
            'stack' : (error.stack ? JSON.stringify(error.stack) : "")
        });
    }

    render() {
        if (this.state.hasError) {
            // You can render any custom fallback UI
            return <p>Please re-load window because of an error.</p>;
            //return this.props.children;
        } else {
            return this.props.children;
        }

    }
}

export default ErrorBoundary;