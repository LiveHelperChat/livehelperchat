import React from 'react';

class SharedTextarea extends React.Component {
    render() {
        const {text, onTextChange, textareaRef, classNameText, textPlaceholder, onTextKeyDown, onTextFocus, textAutoFocus, textMaxLength, onTextTouchStart, onTextKeyUp, textReadOnly} = this.props;

        return (
            <textarea onChange={onTextChange}
                      aria-label="Type your message here..."
                      id="CSChatMessage"
                      rows="1"
                      onTouchStart={onTextTouchStart}
                      maxLength={textMaxLength}
                      autoFocus={textAutoFocus}
                      ref={textareaRef}
                      value={text}
                      onKeyUp={onTextKeyUp}
                      onKeyDown={onTextKeyDown}
                      onFocus={onTextFocus}
                      className={classNameText}
                      placeholder={textPlaceholder}
                      readOnly={textReadOnly}
                     ></textarea>
        );
    }
}

export default SharedTextarea;