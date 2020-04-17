import React, { Component } from 'react';
import { connect } from "react-redux";

class ChatErrorList extends Component {

    constructor(props) {
        super(props);
    }

    render() {

        var mappedFields = this.props.errors.mapEntries(([k, v]) => {
            if (k == 'captcha') {
                return [<li>{v}</li>]
            }
        });

        if (mappedFields.size > 0) {
            return (
                <div data-alert="" className="mt-2 alert alert-danger alert-dismissible fade show">
                    <button type="button" className="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <ul className="pl-1 m-0">
                        {mappedFields}
                    </ul>
                </div>
            )
        } else {
            return null;
        }
    }
}

export default ChatErrorList;
