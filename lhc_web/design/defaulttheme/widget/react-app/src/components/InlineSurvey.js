import React, { Component } from 'react';
import { connect } from "react-redux";
import { submitInlineSurvey } from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";
import parse, { domToReact } from 'html-react-parser';
import { withTranslation } from 'react-i18next';

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class InlineSurvey extends Component {

    state = {
        currentQuestion: 1,
        is_valid: null,
        collectedData: {}, // Holds questions data themself with post variables names matching standard one
        collectedQuestion: [] // Holds information which questions were answered/entered
    }

    constructor(props) {
        super(props);
    }

    abstractClick(attrs, attrsQuestion, e) {
        var presentState = this.state.collectedData;
        var collectedQuestion = this.state.collectedQuestion;
        var validData = false;

        if (attrs['data-inline'] == 'plain') {
            if (e.target.value != '') {
                validData = true;
                presentState[attrs.name] = e.target.value;
            }
        } else {
            validData = true;
            presentState[attrs.name] = attrs.value;
        }

        if (validData === true && collectedQuestion.indexOf(attrsQuestion.seq) === -1) {
            collectedQuestion.push(attrsQuestion.seq);
        } else if (validData === false && collectedQuestion.indexOf(attrsQuestion.seq) !== -1) {
            collectedQuestion.splice(collectedQuestion.indexOf(attrsQuestion.seq),1);
        }

        this.setState({'collectedData' : presentState, 'collectedQuestion': collectedQuestion});
    }

    submitSurvey() {
        this.state.collectedData['survey_id'] = this.props.survey_id;
        this.state.collectedData['chat_id'] = this.props.chatwidget.getIn(['chatData','id']);
        this.state.collectedData['hash'] = this.props.chatwidget.getIn(['chatData','hash']);
        submitInlineSurvey(this.state.collectedData).then((data) => {
            var newState = {'is_valid': data.data.is_valid};
                newState['feedback_text'] = data.data.result;

            this.setState(newState);
        })
    }

    render() {

        const { t } = this.props;

        var counter = 1;
        var totalQuestions = 0;

        domToReact(this.props.surveyOptions,{
            replace: domNode => {
                if (!domNode.attribs) {
                    return;
                }
                if (domNode.name === 'voteoption') {
                    totalQuestions++;
                }
            }
        })

        return (
            <div>

                {this.state.is_valid === false && parse(this.state.feedback_text, {
                    replace: domNode => {
                        if (domNode.attribs) {
                            if (domNode.name && domNode.name === 'button') {
                                return <button type="button" {...domNode.attribs} onClick={(e) => this.setState({'is_valid':null})} />
                            }
                        }
                    }})}
                {this.state.is_valid === true && <div>
                    {parse(this.state.feedback_text)}
                </div>}

                {this.state.is_valid !== true && domToReact(this.props.surveyOptions,{
                    replace: domNode => {
                        if (!domNode.attribs) {
                            return;
                        }

                        if (domNode.name === 'voteoption') {

                            let classNameItem = this.state.currentQuestion != counter ? 'd-none' : '';

                            counter++;

                            let disabledNext = domNode.attribs['is-required'] == 1 && this.state.collectedQuestion.indexOf(domNode.attribs.seq) === -1 ? true : false
                            let classNameButtons = "d-block pt-3" + (totalQuestions == 1 ? ' text-center' : '');
                            let classSubmitButton = "btn btn-outline-secondary btn-sm" + (totalQuestions > 1 ? ' float-end' : '') + ' btn-survey-submit';

                            return <div {...domNode.attribs} className={classNameItem} >
                                    {domToReact(domNode.children, {

                                        replace: domNodeChild => {
                                            if (!domNodeChild.attribs) {
                                                return;
                                            }

                                            if (domNodeChild.name && domNodeChild.name === 'input' && domNodeChild.attribs.type && domNodeChild.attribs.type == 'radio') {
                                                var cloneAttr = Object.assign({}, domNodeChild.attribs);
                                                return <input type="radio" {...domNodeChild.attribs} onChange={(e) => this.abstractClick(cloneAttr, domNode.attribs, e)} />
                                            } else if (domNodeChild.name && domNodeChild.name === 'textarea') {
                                                var cloneAttr = Object.assign({}, domNodeChild.attribs);
                                                return <textarea style={{"height": "55px"}} {...domNodeChild.attribs} onChange={(e) => this.abstractClick(cloneAttr, domNode.attribs, e)}></textarea>
                                            }
                                        }

                                    })}
                                <div className={classNameButtons}>

                                    {this.state.currentQuestion > 1 && <input type="button" className="btn btn-outline-secondary btn-sm btn-survey-prev" onClick={(e) => this.setState({'currentQuestion' : this.state.currentQuestion - 1})} value={t('button.back')}  name="Prev"/>}

                                    {totalQuestions > this.state.currentQuestion && <input disabled={disabledNext} type="button" className="btn btn-outline-secondary btn-sm float-end btn-survey-next" onClick={(e) => this.setState({'currentQuestion' : this.state.currentQuestion + 1})} value={t('button.next')}  name="Next"/>}

                                    {totalQuestions == this.state.currentQuestion && <input disabled={disabledNext} type="button" className={classSubmitButton} onClick={(e) => this.submitSurvey()} value={t('button.submit')}  name="Submit"/>}
                                </div>
                            </div>;
                        }
                    }
                })}
            </div>
        );
    }
}

export default withTranslation()(InlineSurvey);