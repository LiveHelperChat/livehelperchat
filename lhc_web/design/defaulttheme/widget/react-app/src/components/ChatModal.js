import React, { PureComponent } from 'react';
import axios from "axios";
import parse, { domToReact } from 'html-react-parser';

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
            Array.prototype.forEach.call(container.querySelectorAll('[data-toggle="tab"]'), function(element){ new bsn.Tab( element) });
        })
        .catch((err) => {
            console.log(err);
        })
    }

    dismissModal = () => {
        this.props.toggle()
    }

    generalOnClick = (e) => {
        var txtToAdd =  e['data-bb-code'];
        if (e['data-promt'] && e['data-promt'] == 'img') {
            var link = prompt("Please enter link to an image");
            if (link) {
                txtToAdd = '[' + txtToAdd + ']' + link + '[/' + txtToAdd + ']';
            }
        } else if (e['data-promt'] && e['data-promt'] == 'url') {
            var link = prompt("Please enter a link");
            if (link) {
                txtToAdd = '[url=' + link + ']Here is a link[/url]';
            }
        }
        this.props.insertText(txtToAdd);
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

export default ChatModal;
