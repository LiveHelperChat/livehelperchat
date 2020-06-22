import React, { PureComponent } from 'react';
import parse, { domToReact } from 'html-react-parser';
import { connect } from "react-redux";
import { withTranslation } from 'react-i18next';

class ChatBotIntroMessage extends PureComponent {

    constructor(props) {
        super(props);
        this.abstractClick = this.abstractClick.bind(this);
        this.updateTriggerClicked = this.updateTriggerClicked.bind(this);
        this.processBotAction = this.processBotAction.bind(this);
        this.disableEditor = false;
    }

    addLoader(attrs, element) {
        if (!attrs["data-no-change"] && attrs.type == 'button') {
            element.setAttribute("disabled","disabled");
            element.innerHTML = "<i class=\"material-icons\">&#xf113;</i>" + element.innerHTML;
        }
    }

    /**
     * Here we handle bot buttons actions
     * */
    abstractClick(attrs, e) {

        const { t } = this.props;

        this.addLoader(attrs,e.target);

        if (attrs.onclick.indexOf('lhinst.updateTriggerClicked') !== -1) {
            this.updateTriggerClicked({type:'triggerclicked'}, attrs, e.target);
        } else if (attrs.onclick.indexOf('notificationsLHC.sendNotification') !== -1) {
            // todo
        } else if (attrs.onclick.indexOf('lhinst.buttonClicked') !== -1) {
            this.updateTriggerClicked({type:''}, attrs, e.target);
        } else if (attrs.onclick.indexOf('lhinst.updateChatClicked') !== -1) {
            this.updateTriggerClicked({type:'',mainType: 'updatebuttonclicked'}, attrs, e.target);
        } else if (attrs.onclick.indexOf('lhinst.editGenericStep') !== -1) {
            this.updateTriggerClicked({type:'editgenericstep'}, attrs, e.target);
        } else if (attrs.onclick.indexOf('lhinst.dropdownClicked') !== -1) {
            const list = document.getElementById('id_generic_list-' + attrs['data-id']);
            if (list && list.value != "0" && list.value != "") {
                attrs['data-payload'] = list.value;
                this.updateTriggerClicked({type:'valueclicked'}, attrs, e.target);
            } else {
                alert(t('bot.please_choose'));
            }
        } else {
            helperFunctions.emitEvent('MessageClick',[attrs, this.props.dispatch]);
            console.log('Unknown click event: ' + attrs.onclick);
        }

        e.preventDefault();
    }

    updateTriggerClicked(paramsType, attrs, target) {
        this.props.setBotPayload({type: paramsType['type'], payload: attrs['data-payload'], id : attrs['data-id'], processed : (typeof attrs['data-keep'] === 'undefined')})
    }

    processBotAction(domNode) {

        const attr = domNode.attribs;

        if (attr['data-bot-action'] == 'lhinst.disableVisitorEditor') {
            this.disableEditor = true;
        } else if (attr['data-bot-action'] == 'lhinst.setDelay') {
            //this.delayData.push(JSON.parse(attr['data-bot-args']));
        } else if (attr['data-bot-action'] == 'execute-js') {
            eval(domNode.children[0]['data']);
        }
    }

    render() {

        return parse(this.props.content, {

            replace: domNode => {
                if (domNode.attribs) {

                    var cloneAttr = Object.assign({}, domNode.attribs);

                    if (domNode.attribs.onclick) {
                        delete domNode.attribs.onclick;
                    }

                    if (domNode.name && domNode.name === 'button') {
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

export default ChatBotIntroMessage;