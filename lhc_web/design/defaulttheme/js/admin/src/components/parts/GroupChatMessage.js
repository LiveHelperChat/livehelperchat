import parse, { domToReact } from 'html-react-parser';
import React from "react";

const GroupChatMessage = ({message, index}) => {

    var operatorChanged = false;

    return parse(message['msg'], {

        replace: domNode => {
            if (domNode.attribs) {

                var cloneAttr = Object.assign({}, domNode.attribs);

                if (domNode.attribs.class) {
                    domNode.attribs.className = domNode.attribs.class;

                    // Animate only if it's not first sync call
                    if (domNode.attribs.className.indexOf('message-row') !== -1 && index > 0) {
                        domNode.attribs.className += ' fade-in-fast';
                        if (message['msop'] > 0 && message['msop'] != message['lmsop'] && operatorChanged == false) {
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
                    return <img {...domNode.attribs} />
                } else if (domNode.name && domNode.name === 'a') {
                    if (cloneAttr.onclick) {
                        return <a {...domNode.attribs}  >{domToReact(domNode.children)}</a>
                    }
                }
            }
        }
    });
}

export default React.memo(GroupChatMessage);