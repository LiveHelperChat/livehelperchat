import React, { PureComponent, Suspense } from 'react';
import parse, { domToReact } from 'html-react-parser';
import { connect } from "react-redux";
import { updateTriggerClicked, subscribeNotifications, parseScript } from "../actions/chatActions";
import { withTranslation } from 'react-i18next';
import { helperFunctions } from "../lib/helperFunctions";
import ChatModal from './ChatModal';
const InlineSurvey = React.lazy(() => import('./InlineSurvey'));


class ChatMessage extends PureComponent {

    state = {
        jsExecuted : false,
        moreReactions : false,
        reactToMessageId : 0
    }

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

            } else if (attrs.onclick.indexOf('lhinst.moreReactions') !== -1) {
                this.setState({moreReactions : true, reactToMessageId: attrs['data-id']});
                e.stopPropagation();
            } else if (attrs.onclick.indexOf('lhinst.reactionsToolbar') !== -1) {
                this.props.setReactingTo(attrs['data-id'] != this.props.reactToMessageId ? attrs['data-id'] : 0);
                e.stopPropagation();
            } else if (attrs.onclick.indexOf('lhinst.reactionsClicked') !== -1) {
                this.updateTriggerClicked({type:'/(type)/reactions' + (this.props.themeId ? '/(theme)/' + this.props.themeId : '')}, attrs, e.target);
                this.props.setReactingTo(0);
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
            } else if (attrs.onclick.indexOf('lhinst.executeJS') !== -1) {
                parseScript(attrs, this);
            } else if (attrs.onclick.indexOf('lhinst.dropdownClicked') !== -1) {
                const list = document.getElementById('id_generic_list-' + attrs['data-id']);
                if (list && list.value != "0" && list.value != "") {
                    attrs['data-payload'] = list.value;
                    this.updateTriggerClicked({type:'/(type)/valueclicked'}, attrs, e.target);
                } else {
                    alert(t('bot.please_choose'));
                }
            } else if (attrs.onclick.indexOf('lhinst.zoomImage') !== -1) {
                helperFunctions.sendMessageParentDirect('zoomImage', [{'txt_download': t('bbcode.img_download'), 'src' : attrs.src, title: attrs.title ? attrs.title : '' }]);
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
        this.props.dispatch(updateTriggerClicked(paramsType, {"payload-id": (typeof attrs['data-identifier'] === 'undefined' ? null : attrs['data-identifier']) ,payload: attrs['data-payload'], id : attrs['data-id'], processed : (typeof attrs['data-keep'] === 'undefined')})).then((data) => {
            if (!attrs['data-keep']) {
                this.removeMetaMessage(attrs['data-id']);
            }

            if (data.data.t) {
                helperFunctions.sendMessageParent('botTrigger', [{'trigger' : data.data.t}]);
            }

            if (data.data.update_message) {
                this.props.updateMessage(attrs['data-id'], this);
            } else {
                this.props.updateMessages();
                this.props.updateStatus();
            }
        });
    }

    imageLoaded(attrs) {
        if (this.props.scrollBottom) {
            this.props.scrollBottom(true, true);
        }
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (this.props.reactToMessageId != 0) {

            var elm = document.getElementById('reactions-toolbar-'+this.props.reactToMessageId);
            var elmMessage = document.getElementById('msg-'+this.props.reactToMessageId);

            if (!elm || !elmMessage) {
                return;
            }

            // Only half of the width goes to the right
            var halfWidth = elm.clientWidth / 2;

            var messageMaxWidth = elmMessage.offsetWidth;

            var offsetContainer = elm.parentNode.offsetLeft;

            if ((offsetContainer + halfWidth) > messageMaxWidth) {
                elm.style.right = '-'+ (halfWidth - 10) +'px';
            } else if (offsetContainer < halfWidth - 30) {
                elm.style.left = (halfWidth - 30) +'px';
            }
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
                this.props.scrollBottom(false, false);
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

                        if (domNode.attribs.className.indexOf('message-row') !== -1 && parseInt(this.props.reactToMessageId) == parseInt(domNode.attribs.id.replace("msg-",""))){
                            domNode.attribs.className += ' current-reacting-to';
                        }

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

                        if (typeof domNode.attribs['data-ignore-load'] === 'undefined') {
                            return <img {...domNode.attribs} onLoad={this.imageLoaded} onClick={(e) => this.abstractClick(cloneAttr, e)} />
                        }

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

                    } else if (domNode.name && domNode.name === 'inlinesurvey') {

                        return <Suspense fallback="..."><InlineSurvey {...domNode.attribs} surveyOptions={domNode.children} /></Suspense>;

                    } else if (domNode.name && domNode.name === 'input') {

                        if (domNode.attribs.type && domNode.attribs.type == 'checkbox' && cloneAttr.onchange) {

                            if (domNode.attribs.style) {
                                domNode.attribs.style = this.getStyleObjectFromString(domNode.attribs.style);
                            }

                            return <input type="checkbox" {...domNode.attribs} onChange={(e) => this.abstractClick(cloneAttr, e)} />

                        } else if (domNode.attribs.type && domNode.attribs.type == 'radio') {

                            if (domNode.attribs.style) {
                                domNode.attribs.style = this.getStyleObjectFromString(domNode.attribs.style);
                            }

                            if (domNode.attribs.checked) {
                                domNode.attribs.defaultChecked = true;
                                delete domNode.attribs.checked;
                            }

                            return <input type="radio" {...domNode.attribs} />
                        }

                    } else if (domNode.name && domNode.name === 'script' && domNode.attribs['data-bot-action']) {

                        if (!domNode.attribs['data-bot-always']) {
                            // Execute JS only once
                            // Happens if new message indicator is passed
                            // We rerender elements, but we should not execute JS
                            if (this.state.jsExecuted == true) {
                                return <React.Fragment></React.Fragment>;
                            }

                            this.setState({jsExecuted : true});
                        }

                        parseScript(domNode, this);

                        // Return empty element
                        return <React.Fragment></React.Fragment>;
                    }
                }
            }
        });

        return <React.Fragment>{this.state.moreReactions && <ChatModal setReaction={(attrs) => {this.updateTriggerClicked({type:'/(type)/reactions' + (this.props.themeId ? '/(theme)/' + this.props.themeId : '')}, JSON.parse(attrs), null);this.setState({moreReactions : false});this.props.setReactingTo(0);}} confirmClose={(e) => {this.setState({moreReactions : false})}} cancelClose={(e) => {this.setState({moreReactions : false})}} toggle={(e) => {this.setState({moreReactions : false})}} dataUrl={"/chat/reacttomessagemodal/"+this.state.reactToMessageId + (this.props.themeId ? '/(theme)/' + this.props.themeId : '') } />}{this.props.hasNew == true && this.props.id == this.props.newId && <div id="scroll-to-message" className="message-admin border-bottom new-msg-holder border-danger text-center"><span className="new-msg bg-danger text-white d-inline-block fs12 rounded-top">{this.props.newTitle}</span></div>}{messages}</React.Fragment>
    }
}

export default withTranslation()(connect()(ChatMessage));
