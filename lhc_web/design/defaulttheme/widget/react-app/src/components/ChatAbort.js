import React, { PureComponent } from 'react';

class ChatAbort extends PureComponent {

    constructor(props) {
        super(props);
    }

    render() {
        return <React.Fragment>
            <div className="fade modal-backdrop show"></div>
            <div role="dialog" id="dialog-content" aria-modal="true" className="fade modal show d-block p-2" tabIndex="-1">
                <div className={"modal-content p-2 "+(this.props.full_height ? 'h-100' : '')}>

                    {this.props.as_html && <div className={this.props.full_height ? 'h-100' : ''} dangerouslySetInnerHTML={{__html:this.props.text}}></div>}
                    {!this.props.as_html && <p>{this.props.text}</p>}

                    <div className="modal-footer">
                        <button className="btn btn-secondary btn-sm" onClick={this.props.close} type="button">{this.props.closeText}</button>
                    </div>
                </div>
            </div>
        </React.Fragment>;
    }
}

export default ChatAbort;
