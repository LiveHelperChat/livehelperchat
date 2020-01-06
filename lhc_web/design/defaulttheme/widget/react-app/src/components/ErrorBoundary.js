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
            return <h1>Something went wrong.{JSON.stringify(this.state.error)} {JSON.stringify(this.state.info)}</h1>;
            //return this.props.children;
        } else {
            return this.props.children;
        }

    }
}

export default ErrorBoundary;