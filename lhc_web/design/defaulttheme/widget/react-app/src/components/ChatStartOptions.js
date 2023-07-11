import React, { PureComponent } from 'react';
import { withTranslation } from 'react-i18next';

class ChatStartOptions extends PureComponent {

    constructor(props) {
        super(props);
    }

    componentDidMount() {
        var bsn = require("bootstrap.native/dist/components/dropdown-native");
        new bsn(document.getElementById('chat-dropdown-options'));
    }

    render() {
        const { t } = this.props;
        
        return (
            <div className="btn-group dropup disable-select ps-1 pt-2">
                <i className="material-icons settings text-muted" id="chat-dropdown-options" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">&#xf100;</i>
                <div className="dropdown-menu shadow bg-white lhc-dropdown-menu rounded ms-1">
                    <div className="d-flex flex-row px-1">
                        {this.props.bbEnabled && <a onClick={(e) => this.props.toggleModal()} title={t('button.bb_code')}><i className="material-icons chat-setting-item text-muted me-0 bbcode-ico">&#xf104;</i></a>}
                        {this.props.langEnabled && <a onClick={this.props.changeLanguage} title={t('button.lang')} ><i className="material-icons chat-setting-item text-muted me-0 lang-ico">&#xf11e;</i></a>}
                    </div>
                </div>
            </div>
        );
    }
}

export default withTranslation()(ChatStartOptions);

