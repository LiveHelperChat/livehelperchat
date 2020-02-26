import React, { PureComponent } from 'react';
import axios from "axios";
import parse, { domToReact } from 'html-react-parser';
import { withTranslation } from 'react-i18next';
import Xwiper from 'xwiper';

class ChatModal extends PureComponent {

    state = {
        body: null
    };
    
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        axios.get(window.lhcChat['base_url'] + this.props.dataUrl)
        .then((response) => {
            this.setState({'body' : response.data});
            var container = document.getElementById('dialog-content');
            var bsn = require("bootstrap.native/dist/bootstrap-native-v4");
            var tabs = container.querySelectorAll('[data-toggle="tab"]');

            if (tabs.length > 0) {
                var activeTab = 0;
                Array.prototype.forEach.call(tabs, function(element){ new bsn.Tab( element) });

                const xwiper = new Xwiper('.tab-content');
                xwiper.onSwipeLeft(() => {
                    activeTab = activeTab < (tabs.length - 1) ? (activeTab + 1) : 0;
                    tabs[activeTab].Tab.show();
                });

                xwiper.onSwipeRight(() => {
                    activeTab = activeTab > 0 ? (activeTab - 1) : (tabs.length - 1);
                    tabs[activeTab].Tab.show();
                });
            }

        })
        .catch((err) => {
            console.log(err);
        })
    }

    dismissModal = () => {
        this.props.toggle()
    }

    generalOnClick = (e) => {
        const { t } = this.props;

        var txtToAdd =  e['data-bb-code'];
        if (e['data-promt'] && e['data-promt'] == 'img') {
            var link = prompt(t('bbcode.img_link'));
            if (link) {
                txtToAdd = '[' + txtToAdd + ']' + link + '[/' + txtToAdd + ']';
            }
        } else if (e['data-promt'] && e['data-promt'] == 'url') {
            var link = prompt(t('bbcode.link'));
            if (link) {
                txtToAdd = '[url=' + link + ']'+t('bbcode.link_here')+'[/url]';
            }
        }
        this.props.insertText(txtToAdd);
    }

    generalDataActionClick = (e) => {
        if (e['data-action'] && this.props[e['data-action']]) {
            this.props[e['data-action']]();
        }
    }

    render() {

        return (
            <React.Fragment>
                {this.state.body !== null && <div className="fade modal-backdrop show"></div>}
                {this.state.body !== null && <div role="dialog" id="dialog-content" aria-modal="true" className="fade modal show d-block" tabIndex="-1">{parse(this.state.body, {
                        replace: domNode => {

                            if (domNode.attribs && domNode.attribs.id === 'react-close-modal') {
                                return <button type="button" className="close float-right" data-dismiss="modal" onClick={this.dismissModal} aria-label="Close"><span aria-hidden="true">&times;</span></button>;
                            } else if (domNode.attribs && domNode.attribs.bbitem) {
                                if (domNode.attribs.class) {
                                    domNode.attribs.className = domNode.attribs.class;
                                    delete domNode.attribs.class;
                                }
                                return (
                                    <a {...domNode.attribs} onClick={(e) => this.generalOnClick(domNode.attribs)}>{domToReact(domNode.children)}</a>
                                );
                            } else if (domNode.type && domNode.type === 'tag' && domNode.name && domNode.name == 'input' && domNode.attribs && domNode.attribs.type && domNode.attribs.type == "button") {

                                if (domNode.attribs.class) {
                                    domNode.attribs.className = domNode.attribs.class;
                                    delete domNode.attribs.class;
                                }

                                return (<input {...domNode.attribs} onClick={(e) => this.generalDataActionClick(domNode.attribs)} />);

                            } else if (domNode.type && domNode.type === 'script') {
                                if (domNode.children.length > 0)
                                {
                                    setTimeout(() => {
                                        const newScript = document.createElement("script");
                                        newScript.appendChild(document.createTextNode(domNode.children[0].data));
                                        var head = document.getElementsByTagName('head').item(0);
                                        head.appendChild(newScript);
                                    },500);
                                } else if (domNode.attribs && domNode.attribs.src) {
                                    const newScript = document.createElement('script');
                                    newScript.src = domNode.attribs.src;
                                    newScript.type ='text/javascript';
                                    var head = document.getElementsByTagName('head').item(0);
                                    head.appendChild(newScript);
                                }
                                return <React.Fragment></React.Fragment>;
                            }
                        }
                    }
                )}</div>}
            </React.Fragment>
        )
    }
}

export default withTranslation()(ChatModal);
