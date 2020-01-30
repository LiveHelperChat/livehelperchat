import React, { PureComponent } from 'react';

class ChatStartOptions extends PureComponent {

    constructor(props) {
        super(props);
    }

    componentDidMount() {
        var bsn = require("bootstrap.native/dist/bootstrap-native-v4");
        new bsn.Dropdown(document.getElementById('chat-dropdown-options'));
    }

    render() {
        return (
            <div className="btn-group dropup pt-1 disable-select pl-2 pt-2">
                <i className="material-icons settings text-muted" id="chat-dropdown-options" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">settings</i>
                <div className="dropdown-menu shadow bg-white lhc-dropdown-menu rounded">
                    <div className="d-flex flex-row">
                        <a href="#" onClick={(e) => this.props.toggleModal()} title="BB Code"><i className="material-icons chat-setting-item text-muted">face</i></a>
                    </div>
                </div>
            </div>
        );
    }
}

export default ChatStartOptions;

