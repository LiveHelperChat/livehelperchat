import React, { PureComponent } from 'react';
import { withTranslation } from 'react-i18next';

class ChatStartOptions extends PureComponent {

    constructor(props) {
        super(props);
    }

    componentDidMount() {
        const enabledOptionsCount = (this.props.bbEnabled ? 1 : 0) + (this.props.langEnabled ? 1 : 0);
        
        // Only initialize dropdown if multiple options are enabled
        if (enabledOptionsCount > 1) {
            var bsn = require("bootstrap.native/dropdown");
            new bsn(document.getElementById('chat-dropdown-options'));
        }
    }

    render() {
        const { t } = this.props;
        const enabledOptionsCount = (this.props.bbEnabled ? 1 : 0) + (this.props.langEnabled ? 1 : 0);
        
        // If only one option is enabled, show it directly
        if (enabledOptionsCount === 1) {
            if (this.props.bbEnabled) {
                return (
                    <div className="disable-select ps-1 ps-1 d-flex flex-column justify-content-end align-items-stretch" id="chat-dropdown-options-wrapper">
                        <button onClick={(e) => this.props.toggleModal()} title={t('button.bb_code')} type="button" className="border-0 p-0 material-icons settings text-muted bbcode-ico" id="chat-dropdown-options" role="button" tabIndex="0">&#xf104;</button>
                    </div>
                );
            } else if (this.props.langEnabled) {
                return (
                    <div className="disable-select ps-1 ps-1 d-flex flex-column justify-content-end align-items-stretch" id="chat-dropdown-options-wrapper">
                        <button onClick={(e) => this.props.changeLanguage()} title={t('button.lang')} type="button" className="border-0 p-0 material-icons settings text-muted lang-ico" id="chat-dropdown-options" role="button" tabIndex="0">&#xf11e;</button>
                    </div>
                );
            }
        }
        
        // If multiple options or no options, show dropdown
        return (
            <div className="btn-group dropup disable-select ps-1 d-flex flex-column justify-content-end align-items-stretch" id="chat-dropdown-options-wrapper">
                <button type="button" className="border-0 p-0 material-icons settings text-muted" id="chat-dropdown-options" role="button" data-bs-toggle="dropdown" tabIndex="0" aria-haspopup="true" aria-expanded="false">&#xf100;</button>
                <div className="dropdown-menu shadow bg-white lhc-dropdown-menu rounded ms-1">
                    <div className="d-flex flex-row px-1">
                        {this.props.bbEnabled && <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.props.toggleModal() : '' }} onClick={(e) => this.props.toggleModal()} title={t('button.bb_code')}><i className="material-icons chat-setting-item text-muted me-0 bbcode-ico">&#xf104;</i></a>}
                        {this.props.langEnabled && <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.props.changeLanguage() : '' }} onClick={this.props.changeLanguage} title={t('button.lang')} ><i className="material-icons chat-setting-item text-muted me-0 lang-ico">&#xf11e;</i></a>}
                    </div>
                </div>
            </div>
        );
    }
}

export default withTranslation()(ChatStartOptions);

