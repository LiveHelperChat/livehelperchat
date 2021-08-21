import React, { PureComponent } from 'react';
import parse, { domToReact } from 'html-react-parser';
import { connect } from "react-redux";
import { updateTriggerClicked, subscribeNotifications, parseScript } from "../actions/chatActions";
import { withTranslation } from 'react-i18next';
import { helperFunctions } from "../lib/helperFunctions";

class ChatMessage extends PureComponent {

    constructor(props) {
        super(props);
        this.abstractClick = this.abstractClick.bind(this);
        this.imageLoaded = this.imageLoaded.bind(this);
        this.updateTriggerClicked = this.updateTriggerClicked.bind(this);
        this.disableEditor = false;
        this.delayData = [];
    }

    addLoader(attrs, element) {
        if (!attrs["data-no-change"] && attrs.type == 'button') {
            element.setAttribute("disabled","disabled");
            element.innerHTML = "<i class=\"material-icons lhc-spin\">&#xf113;</i>" + element.innerHTML;
        }
    }
    
    /**
     * Here we handle bot buttons actions
     * */
    abstractClick(attrs, e) {

        const { t } = this.props;



        if (typeof attrs.onchange !== 'undefined') {

            // Checkbox support
            if (attrs.type && attrs.type == "checkbox") {
                if (attrs['payload-type'] == 'enable-confirm') {
                    var elm = document.getElementById('confirm-button-'+attrs['data-id']);
                    if (e.target.checked) {
                        elm.removeAttribute('disabled');
                        elm.onclick = (e) => this.updateTriggerClicked({type:''}, {'data-payload':'confirm', 'data-id' : attrs['data-id']}, e.target);
                    } else {
                        elm.setAttribute('disabled','disabled');
                    }
                }
                return ;
            }

            // Drop down support
            const optionSelected = e.target.options[e.target.selectedIndex];

            const attrLoad = {
                'data-payload': optionSelected.getAttribute('data-payload'),
                'data-id' : optionSelected.getAttribute('data-id')
            };

            if (optionSelected.getAttribute('payload-type') == 'trigger') {
                this.updateTriggerClicked({type:'/(type)/triggerclicked'}, attrLoad , e.target);
            } else if (optionSelected.getAttribute('payload-type') == 'button' || optionSelected.getAttribute('payload-type') == 'payload') {
                this.updateTriggerClicked({type:''}, attrLoad, e.target);
            }

            return ;
        }

        this.addLoader(attrs,e.target);

        if (attrs.onclick) {
            if (attrs.onclick.indexOf('lhinst.updateTriggerClicked') !== -1) {
                this.updateTriggerClicked({type:'/(type)/triggerclicked'}, attrs, e.target);
            } else if (attrs.onclick.indexOf('notificationsLHC.sendNotification') !== -1) {

                this.props.dispatch(subscribeNotifications());
                e.target.innerHTML = t('notifications.subscribing');
                setTimeout(() => {
                    this.removeMetaMessage(attrs['data-id']);
                }, 500);

            } else if (attrs.onclick.indexOf('lhinst.buttonClicked') !== -1) {
                this.updateTriggerClicked({type:''}, attrs, e.target);
            } else if (attrs.onclick.indexOf('lhinst.startVoiceCall') !== -1) {
                this.props.voiceCall();
            } else if (attrs.onclick.indexOf('lhinst.chooseFile') !== -1) {
                this.props.abstractAction('fileupload');
            } else if (attrs.onclick.indexOf('lhinst.updateChatClicked') !== -1) {
                this.updateTriggerClicked({type:'',mainType: 'updatebuttonclicked'}, attrs, e.target);
            } else if (attrs.onclick.indexOf('lhinst.editGenericStep') !== -1) {
                this.updateTriggerClicked({type:'/(type)/editgenericstep'}, attrs, e.target);
            } else if (attrs.onclick.indexOf('lhinst.hideShowAction') !== -1) {
                const args = JSON.parse(attrs['data-load']);
                var more = document.getElementById('message-more-'+args['id']);
                if (more.classList.contains('hide')) {
                    e.target.innerText = args['hide_text'];
                    more.classList.remove('hide');
                } else {
                    e.target.innerText = args['show_text'];
                    more.classList.add('hide');
                }
            } else if (attrs.onclick.indexOf('lhinst.dropdownClicked') !== -1) {
                const list = document.getElementById('id_generic_list-' + attrs['data-id']);
                if (list && list.value != "0" && list.value != "") {
                    attrs['data-payload'] = list.value;
                    this.updateTriggerClicked({type:'/(type)/valueclicked'}, attrs, e.target);
                } else {
                    alert(t('bot.please_choose'));
                }
            } else {
                helperFunctions.emitEvent('MessageClick',[attrs, this.props.dispatch]);
                console.log('Unknown click event: ' + attrs.onclick);
            }
        }

        e.preventDefault();

        // Why did we previously auto focused on button click?
        // It just makes a screen smaller and is bad for UI
        /*if (!(attrs.src && attrs.class && attrs.class == 'img-fluid')) {
            this.props.focusMessage();
        }*/
    }

    removeMetaMessage(messageId) {
        setTimeout(() => {
            var block = document.getElementById('msg-' + messageId);
            if (block) {
                var x = block.getElementsByClassName("meta-message-" + messageId);
                var i;
                for (i = 0; i < x.length; i++) {
                    x[i].parentNode.removeChild(x[i]);
                }
            }
        },500);
    }

    updateTriggerClicked(paramsType, attrs, target) {
        this.props.dispatch(updateTriggerClicked(paramsType, {payload: attrs['data-payload'], id : attrs['data-id'], processed : (typeof attrs['data-keep'] === 'undefined')})).then((data) => {
            if (!attrs['data-keep']) {
                this.removeMetaMessage(attrs['data-id']);
            }

            if (data.data.t) {
                helperFunctions.sendMessageParent('botTrigger', [{'trigger' : data.data.t}]);
            }

            this.props.updateMessages();
            this.props.updateStatus();
        });
    }

    imageLoaded(attrs) {
        if (this.props.scrollBottom) {
            this.props.scrollBottom(true);
        }
    }

    componentDidMount() {

        this.props.setMetaUpdateState(this.props.msg['msg'].indexOf('meta-auto-hide') !== -1);

        if (this.disableEditor == true) {
            this.props.setEditorEnabled(false);
        } else {
            this.props.setEditorEnabled(true);
        }

        if (this.delayData.length > 0) {
            this.delayData.forEach((item) => {
                this.props.sendDelay(item);
            })
        }
    }

    formatStringToCamelCase(str) {
        const splitted = str.split("-");
        if (splitted.length === 1) return splitted[0];
        return (
            splitted[0] +
            splitted
                .slice(1)
                .map(word => word[0].toUpperCase() + word.slice(1))
                .join("")
        );
    };

    getStyleObjectFromString(str) {
        const style = {};
        str.split(";").forEach(el => {
            const [property, value] = el.split(":");
            if (!property) return;

            const formattedProperty = this.formatStringToCamelCase(property.trim());
            style[formattedProperty] = value.trim();
        });

        return style;
    };

    render() {

        const { t } = this.props;

        var operatorChanged = false;

        var messages = parse(this.props.msg['msg'], {

            replace: domNode => {
                if (domNode.attribs) {

                    var cloneAttr = Object.assign({}, domNode.attribs);

                    if (domNode.attribs.class) {
                        domNode.attribs.className = domNode.attribs.class;

                        // Animate only if it's not first sync call
                        if (domNode.attribs.className.indexOf('message-row') !== -1 && this.props.id > 0) {

                            domNode.attribs.className += ' fade-in-fast';

                            if (this.props.msg['msop'] > 0 && this.props.msg['msop'] != this.props.msg['lmsop'] && operatorChanged == false) {
                                domNode.attribs.className += ' operator-changes';
                                operatorChanged = true;
                            }
                        } else if (this.props.profilePic && domNode.attribs.className.indexOf('vis-icon-hld') !== -1) {
                            return <img className="profile-msg-pic" onLoad={this.imageLoaded} src={this.props.profilePic} alt="" title="" />
                        }

                        delete domNode.attribs.class;
                    }

                    if (domNode.attribs.onclick) {
                        delete domNode.attribs.onclick;
                    }

                    if (domNode.name && domNode.name === 'img') {

                        if (domNode.attribs.style) {
                            domNode.attribs.style = this.getStyleObjectFromString(domNode.attribs.style);
                        }

                        return <img {...domNode.attribs} onLoad={this.imageLoaded} onClick={(e) => this.abstractClick(cloneAttr, e)} />

                    } else if (domNode.name && domNode.name === 'button') {
                        if (cloneAttr.onclick) {

                            if (domNode.attribs.style) {
                                domNode.attribs.style = this.getStyleObjectFromString(domNode.attribs.style);
                            }

                            return <button {...domNode.attribs} onClick={(e) => this.abstractClick(cloneAttr, e)} >{domToReact(domNode.children)}</button>
                        }
                    } else if (domNode.name && domNode.name === 'a') {
                        if (cloneAttr.onclick) {

                            if (domNode.attribs.style) {
                                domNode.attribs.style = this.getStyleObjectFromString(domNode.attribs.style);
                            }

                            return <a {...domNode.attribs} onClick={(e) => this.abstractClick(cloneAttr, e)} >{domToReact(domNode.children)}</a>
                        }
                    } else if (domNode.name && domNode.name === 'select') {

                        if (cloneAttr.onchange) {

                            if (domNode.attribs.style) {
                                domNode.attribs.style = this.getStyleObjectFromString(domNode.attribs.style);
                            }

                            return <select {...domNode.attribs} onChange={(e) => this.abstractClick(cloneAttr, e)} >{domToReact(domNode.children)}</select>
                        }

                    } else if (domNode.name && domNode.name === 'input') {

                        if (domNode.attribs.type && domNode.attribs.type == 'checkbox' && cloneAttr.onchange) {
                            if (domNode.attribs.style) {
                                domNode.attribs.style = this.getStyleObjectFromString(domNode.attribs.style);
                            }
                            return <input type="checkbox" {...domNode.attribs} onChange={(e) => this.abstractClick(cloneAttr, e)} />
                        }

                    } else if (domNode.name && domNode.name === 'script' && domNode.attribs['data-bot-action']) {
                        parseScript(domNode, this);
                    }
                }
            }
        });

        return <React.Fragment>{this.props.hasNew == true && this.props.id == this.props.newId && <div id="scroll-to-message" className="message-admin border-bottom new-msg-holder border-danger text-center"><span className="new-msg bg-danger text-white d-inline-block fs12 rounded-top">{this.props.newTitle}</span></div>}{messages}</React.Fragment>
    }
}

export default withTranslation()(connect()(ChatMessage));
