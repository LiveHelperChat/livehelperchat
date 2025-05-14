import React, { Component } from 'react';
import { searchNodeTriggers } from '../actions/nodeGroupActions';
import { connect } from "react-redux";

@connect((store) => {
    return {
        currenttrigger: store.currenttrigger
    };
})

class NodeTriggerSearch extends Component {
    constructor(props) {
        super(props);
        this.state = {
            keyword: '',
            triggers: [],
            loading: false,
            error: null,
            includeTranslations: false
        };

        this.handleInputChange = this.handleInputChange.bind(this);
        this.handleCheckboxChange = this.handleCheckboxChange.bind(this);
        this.searchTimeout = null;
    }

    handleInputChange(e) {
        const keyword = e.target.value;
        this.setState({ keyword });
        
        // Clear any existing timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        // Set a new timeout to execute search after 200ms
        this.searchTimeout = setTimeout(() => {
            this.executeSearch();
        }, 200);
    }
    
    handleCheckboxChange(e) {
        const includeTranslations = e.target.checked;
        this.setState({ includeTranslations }, () => {
            // Execute search with the new setting
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            
            this.searchTimeout = setTimeout(() => {
                this.executeSearch();
            }, 200);
        });
    }
      executeSearch() {
        const { keyword, includeTranslations } = this.state;
        
        if (!keyword.trim()) {
            this.setState({ triggers: [] });
            return;
        }

        this.setState({ loading: true, error: null, triggers: []});

        searchNodeTriggers(this.props.botId, keyword, includeTranslations ? 'include_translations' : null)
            .then(data => {
                this.setState({
                    triggers: data,
                    loading: false
                });
            })
            .catch(error => {
                console.error('Error searching triggers:', error);
                this.setState({
                    error: 'Failed to fetch triggers. Please try again.',
                    loading: false
                });
            });
    }

    // This is a placeholder for the click handler that you'll implement
    handleTriggerClick(triggerId) {
        // You'll implement this function
        document.location.hash = '#!#%2Ftrigger-'+triggerId;
    }

    render() {
        const { keyword, triggers, loading, error } = this.state;

        return (
            <div className="node-trigger-search">
            <div className="mb-1">
            <div className="input-group input-group-sm">
            <span className="input-group-text"><i className="material-icons me-0">search</i></span>
            <input
                type="text"
                className="form-control form-control-sm"
                placeholder="Search..."
                value={keyword}
                onChange={this.handleInputChange}
            />
            <span className="input-group-text">
                <label>
                   <input
                   type="checkbox"
                   className="form-check-input"
                   checked={this.state.includeTranslations}
                   onChange={this.handleCheckboxChange}
                   />&nbsp;<span className="form-check-label">Search in translations</span>
               </label>
            </span>
            </div>
            </div>

            {error && <div className="alert alert-danger pt-1 pb-1 mb-0">{error}</div>}
            
            {loading && <div className="alert alert-info pt-1 pb-1 mb-0">Searching...</div>}
            {triggers.length > 0 && (
            <div className="row">
            <div className="col-12">
                <ul className="gbot-trglist">
                {triggers.reduce((elements, trigger, index) => {
                // Add a divider li if this is not the first trigger and the group_id has changed
                if (index == 0 || trigger.group_id !== triggers[index - 1].group_id) {
                    elements.push(<li key={`divider-${trigger.group_id}`} className="d-block mt-2 fw-bold fst-italic fs13 mb-2 float-none border-bottom" style={{clear: 'both'}}>{trigger.group_name}</li>);
                }
                
                elements.push(
                <li key={trigger.id}>
                    <div className="btn-group trigger-btn">
                    <button
                    className={"btn btn-sm " + (this.props.currenttrigger.getIn(['currenttrigger','id']) == trigger.id ? 'btn-success' : 'btn-secondary')}
                    onClick={() => this.handleTriggerClick(trigger.id)}
                    >
                    {trigger.name}
                    </button>
                    </div>
                </li>
                );
                
                return elements;
                }, [])}
                </ul>
            </div>
            </div>
            )}

            {!loading && triggers.length === 0 && keyword.trim() && !error && (
            <div className="alert alert-info pt-1 pb-1 mb-0">No triggers found matching your search.</div>
            )}
            </div>
        );
    }
}

export default NodeTriggerSearch;