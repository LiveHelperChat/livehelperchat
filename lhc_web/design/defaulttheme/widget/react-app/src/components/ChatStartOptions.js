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
            <div className="btn-group dropup disable-select pl-2 pt-2">
                <i className="material-icons settings text-muted" id="chat-dropdown-options" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">&#xf100;</i>
                <div className="dropdown-menu shadow bg-white lhc-dropdown-menu rounded ml-1">
                    <div className="d-flex flex-row px-1">
                        <a onClick={(e) => this.props.toggleModal()} ><i className="material-icons chat-setting-item text-muted mr-0">&#xf104;</i></a>
                    </div>
                </div>
            </div>
        );
    }
}

export default ChatStartOptions;

