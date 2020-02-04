import React, { Component } from 'react';

class ErrorBoundary extends React.Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false };
    }

    componentDidCatch(error, info) {
        // Display fallback UI
        this.setState({ hasError: true, error : error, info : info });
        console.log(error);
        console.log(info);
        // You can also log the error to an error reporting service
        //logErrorToMyService(error, info);
    }

    render() {
        if (this.state.hasError) {
            // You can render any custom fallback UI
            console.log(JSON.stringify(this.state.error));
            console.log(JSON.stringify(this.state.info));
            return <p></p>;
            //return this.props.children;
        } else {
            return this.props.children;
        }

    }
}

export default ErrorBoundary;