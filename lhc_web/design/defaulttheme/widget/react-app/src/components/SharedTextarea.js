import React from 'react';

class SharedTextarea extends React.Component {
    componentDidMount() {
        const el = this.props.textareaRef && this.props.textareaRef.current;
        if (!el) return;
        this._resizeObserver = new ResizeObserver(() => {
            if (typeof this.props.onResize === 'function') {
                this.props.onResize();
            }
        });
        this._resizeObserver.observe(el);
    }

    componentWillUnmount() {
        if (this._resizeObserver) {
            this._resizeObserver.disconnect();
            this._resizeObserver = null;
        }
    }

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