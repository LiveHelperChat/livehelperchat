import React, { PureComponent } from 'react';
import parse, { domToReact } from 'html-react-parser';
import { connect } from "react-redux";
import { updateTriggerClicked, subscribeNotifications } from "../actions/chatActions";
import { withTranslation } from 'react-i18next';
import { helperFunctions } from "../lib/helperFunctions";

class ChatMessage extends PureComponent {

    constructor(props) {
        super(props);
        this.abstractClick = this.abstractClick.bind(this);
        this.imageLoaded = this.imageLoaded.bind(this);
        this.updateTriggerClicked = this.updateTriggerClicked.bind(this);
        this.processBotAction = this.processBotAction.bind(this);
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

        this.addLoader(attrs,e.target);
        
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
        } else if (attrs.onclick.indexOf('lhinst.chooseFile') !== -1) {
            this.props.abstractAction('fileupload');
        } else if (attrs.onclick.indexOf('lhinst.updateChatClicked') !== -1) {
            this.updateTriggerClicked({type:'',mainType: 'updatebuttonclicked'}, attrs, e.target);
        } else if (attrs.onclick.indexOf('lhinst.editGenericStep') !== -1) {
            this.updateTriggerClicked({type:'/(type)/editgenericstep'}, attrs, e.target);
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

        e.preventDefault();
        this.props.focusMessage();
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
        this.props.dispatch(updateTriggerClicked(paramsType, {payload: attrs['data-payload'], id : attrs['data-id'], processed : (typeof attrs['data-keep'] === 'undefined')})).then(() => {
            if (!attrs['data-keep']) {
                this.removeMetaMessage(attrs['data-id']);
            }
            this.props.updateMessages();
            this.props.updateStatus();
        });
    }

    imageLoaded(attrs) {
        if (this.props.scrollBottom) {
            this.props.scrollBottom();
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

    processBotAction(domNode) {
        const attr = domNode.attribs;
        if (attr['data-bot-action'] == 'lhinst.disableVisitorEditor') {
            this.disableEditor = true;
        } else if (attr['data-bot-action'] == 'lhinst.setDelay') {
            this.delayData.push(JSON.parse(attr['data-bot-args']));
        } else if (attr['data-bot-action'] == 'execute-js') {
            if (attr['data-bot-extension']) {
                var args = {};
                if (typeof attr['data-bot-args'] !== 'undefined') {
                    args = JSON.parse(attr['data-bot-args']);
                }
                helperFunctions.emitEvent('extensionExecute',[attr['data-bot-extension'],[args]]);
            } else if (attr['data-bot-event']) {
                this.props[attr['data-bot-event']]();
            } else {
                eval(domNode.children[0]['data']);
            }
        }
    }

    render() {

        var operatorChanged = false;

        return parse(this.props.msg['msg'], {

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
                        }

                        delete domNode.attribs.class;
                    }

                    if (domNode.attribs.onclick) {
                        delete domNode.attribs.onclick;
                    }

                    if (domNode.name && domNode.name === 'img') {
                        return <img {...domNode.attribs} onLoad={this.imageLoaded} onClick={(e) => this.abstractClick(cloneAttr, e)} />
                    } else if (domNode.name && domNode.name === 'button') {
                        if (cloneAttr.onclick) {
                            return <button {...domNode.attribs} onClick={(e) => this.abstractClick(cloneAttr, e)} >{domToReact(domNode.children)}</button>
                        }
                    } else if (domNode.name && domNode.name === 'a') {
                        if (cloneAttr.onclick) {
                            return <a {...domNode.attribs} onClick={(e) => this.abstractClick(cloneAttr, e)} >{domToReact(domNode.children)}</a>
                        }
                    } else if (domNode.name && domNode.name === 'script' && domNode.attribs['data-bot-action']) {
                        this.processBotAction(domNode);
                    }
                }
            }
        });
    }
}

export default withTranslation()(connect()(ChatMessage));
