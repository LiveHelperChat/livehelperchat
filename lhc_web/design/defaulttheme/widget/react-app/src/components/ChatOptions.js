import React, { PureComponent } from 'react';

class ChatOptions extends PureComponent {

    state = {
        dropdown: null
    };

    constructor(props) {
        super(props);
    }

    componentDidMount() {
        if (document.getElementById(this.props.elementId)) {
            var bsn = require("bootstrap.native/dist/components/dropdown-native");
            this.setState({'dropdown' : new bsn(document.getElementById(this.props.elementId))});
        }
    }

    componentWillUnmount() {
        if (this.state.dropdown) {
            delete this.state.dropdown;
        }
    }

    render() {
        return this.props.children;
    }
}

export default ChatOptions;
