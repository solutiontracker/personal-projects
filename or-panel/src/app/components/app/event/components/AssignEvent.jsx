import React, { Component } from 'react'
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { Translation } from "react-i18next";
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import { NavLink } from 'react-router-dom';

export default class AssignEvents extends Component {
    state = {
        data: { assigned: [], unassigned: [] },
        preLoader: false,
        assigned_ids: [],
        unassigned_ids: [],
        assigned_query: '',
        unassigned_query: '',
        typingTimeout : 0,
    }


    componentDidMount() {
        this.listing();
    }

    listing = () => {
        this.setState({ preLoader: true });
        let id = this.props.match.params.id;
        if (id !== undefined) {
            service.post(`${process.env.REACT_APP_URL}/event/sub_admins/${id}`, {assigned_query: this.state.assigned_query, unassigned_query: this.state.unassigned_query})
                .then(
                    response => {
                        if (response.success) {
                            if (response.data) {
                                this.setState({
                                    data: response.data,
                                    preLoader: false
                                });
                            }
                        }
                    },
                    error => { }
                );
        }
    }

    onAssingedSearchChange = () => e => {
        const self = this;
        if (self.state.typingTimeout) {
            clearTimeout(self.state.typingTimeout);
        }
        self.setState({
            assigned_query: e.target.value,
            typingTimeout: setTimeout(function () {
                self.listing()
            }, 1000)
        });
    }
    
    
    onUnassingedSearchChange = () => e => {
        const self = this;
        if (self.state.typingTimeout) {
            clearTimeout(self.state.typingTimeout);
        }
        self.setState({
            unassigned_query: e.target.value,
            typingTimeout: setTimeout(function () {
                self.listing()
            }, 1000)
        });

    }

    handleUnassignChange = (id) => e => {
        var unassigned_ids = this.state.unassigned_ids;

        if (e.target.checked) {
            unassigned_ids.push(id);
        } else {
            const index = unassigned_ids.indexOf(id);
            if (index !== -1)
                unassigned_ids.splice(index, 1);
        }

        this.setState({
            unassigned_ids: unassigned_ids
        });
    }


    handleAssignChange = (id) => e => {
        var assigned_ids = this.state.assigned_ids;

        if (e.target.checked) {
            assigned_ids.push(id);
        } else {
            const index = assigned_ids.indexOf(id);
            if (index !== -1)
                assigned_ids.splice(index, 1);
        }

        this.setState({
            assigned_ids: assigned_ids
        });
    }

    bulkAssign = () => e => {
        var unassigned_ids = this.state.unassigned_ids;
        this.sendAssignRequest(unassigned_ids);
    }

    bulkUnassign = () => e => {
        var assigned_ids = this.state.assigned_ids;
        this.sendUnassignRequest(assigned_ids);
    }

    checkAssignedAll = () => e => {
        let checkboxes = document.querySelectorAll('.assigned_checkbox');
        let assigned_ids = [];
        if (e.target.checked) {
            checkboxes.forEach((item, index) => {
                item.checked = true
                assigned_ids.push(item.value);
            });
        } else {
            checkboxes.forEach((item, index) => {
                item.checked = false
            });
        }

        this.setState({ assigned_ids: assigned_ids });
    }

    checkUnassignedAll = () => e => {
        let checkboxes = document.querySelectorAll('.unassigned_checkbox');
        let unassigned_ids = [];
        if (e.target.checked) {
            checkboxes.forEach((item, index) => {
                item.checked = true
                unassigned_ids.push(item.value);
            });
        } else {
            checkboxes.forEach((item, index) => {
                item.checked = false
            });
        }
        this.setState({ unassigned_ids: unassigned_ids });
    }


    sendAssignRequest = (admin_ids) => {

        this.setState({ preLoader: true });
        var assigned = this.state.data.assigned;
        var unassigned = this.state.data.unassigned;
        let id = this.props.match.params.id;
        if (id !== undefined) {
            service.post(`${process.env.REACT_APP_URL}/event/assign/admin/${id}`, { keys: admin_ids })
                .then(
                    response => {
                        if (response.success) {

                            admin_ids.forEach((item, i) => {
                                var index = unassigned.findIndex(x => x.id === item);
                                assigned.push(unassigned[index]);
                                unassigned.splice(index, 1);
                            });

                            this.setState({
                                preLoader: false,
                                data: { assigned: assigned, unassigned: unassigned },
                                assigned_ids: [],
                                unassigned_ids: []
                            });

                        }
                    },
                    error => { }
                );

        }
    }

    sendUnassignRequest = (admin_ids) => {
        this.setState({ preLoader: true });
        let id = this.props.match.params.id;
        var assigned = this.state.data.assigned;
        var unassigned = this.state.data.unassigned;
        if (id !== undefined) {
            service.post(`${process.env.REACT_APP_URL}/event/unassign/admin/${id}`, { keys: admin_ids })
                .then(
                    response => {
                        if (response.success) {

                            admin_ids.forEach((item, i) => {
                                var index = assigned.findIndex(x => x.id === item);
                                unassigned.push(assigned[index]);
                                assigned.splice(index, 1);
                            });

                            this.setState({
                                preLoader: false,
                                data: { assigned: assigned, unassigned: unassigned },
                                assigned_ids: [],
                                unassigned_ids: []
                            });

                        }
                    },
                    error => { }
                );
        }
    }


    render() {
        return (
            <Translation>
                {

                    t =>
                    
                        <div className="wrapper-content third-step">
                            <div className="btn-return-navigation"><NavLink to="/"><i className='material-icons'>arrow_back</i>{t('M_RETURN_TO_LIST')}</NavLink></div>

                            <div id="eb-assign-admin" style={{ paddingLeft: "15px" }}>
                                <div style={{ margin: 0 }} className="new-header clearfix">
                                    <div className="row">
                                        <div className="col-6">
                                            <h1 className="section-title">{t('EL_ASSIGN_ADMIN')}</h1>
                                        </div>
                                    </div>
                                </div>
                                {this.state.preLoader && <Loader />}
                                {!this.state.preLoader &&
                                    <div className="row d-flex">
                                        <div className="col-6">
                                            <div className="assign-column h-100 left-column">
                                                <div className="top-assign-panel clearfix">
                                                    <button className="btn float-left" onClick={this.bulkAssign()}>{t('EL_ASSIGN')}</button>
                                                    <div className="assign-counter float-right">{this.state.data.unassigned.length} {t('EL_ADMINS')}</div>
                                                </div>
                                                <div className="column-grid-box h-100">
                                                    <div className="column-search-box d-flex align-items-center">
                                                        <div className="search-label">
                                                            <label className="eb-custom-select">
                                                                <input type="checkbox" id="select-all" onChange={this.checkUnassignedAll()} />
                                                                <span></span>
                                                            </label>
                                                            <label htmlFor="select-all">{t('G_SELECT_ALL')}</label>
                                                        </div>
                                                        <div className="search-field">
                                                            <input type="text" placeholder="Search" value={this.state.unassigned_query} className="unassigned-search" onChange={this.onUnassingedSearchChange()}/>
                                                        </div>
                                                    </div>
                                                    <div className="eb-column-listing">
                                                        {this.state.data && this.state.data.unassigned.map((items, key) =>
                                                            <div className="eb-listing-item align-items-center d-flex" key={key}>
                                                                <label className="eb-custom-select">
                                                                    <input value={items.id} type="checkbox" className="unassigned_checkbox" onChange={this.handleUnassignChange(items.id)} />
                                                                    <span></span>
                                                                </label>

                                                                <div className="description-area">
                                                                    <h4>{items.first_name} {items.last_name}</h4>
                                                                    <p>{items.email}</p>
                                                                </div>
                                                                <div className="button-area">
                                                                    <button className="btn" onClick={() => this.sendAssignRequest([items.id])}>{t('EL_ASSIGN')}</button>
                                                                </div>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-6">
                                            <div className="assign-column h-100 right-column">
                                                <div className="top-assign-panel clearfix">
                                                    <button className="btn float-left" onClick={this.bulkUnassign()}>{t('EL_UNASSIGN')}</button>
                                                    <div className="assign-counter float-right">{this.state.data.assigned.length} {t('EL_ADMINS')}</div>
                                                </div>
                                                <div className="column-grid-box h-100">
                                                    <div className="column-search-box d-flex align-items-center">
                                                        <div className="search-label">
                                                            <label className="eb-custom-select">
                                                                <input type="checkbox" id="select-all-unassign" onChange={this.checkAssignedAll()} />
                                                                <span></span>
                                                            </label>
                                                            <label htmlFor="select-all">{t('G_SELECT_ALL')}</label>
                                                        </div>
                                                        <div className="search-field">
                                                            <input type="text" placeholder="Search" value={this.state.assigned_query} className="assigned-search" onChange={this.onAssingedSearchChange()}/>
                                                        </div>
                                                    </div>
                                                    <div className="eb-column-listing">
                                                        {this.state.data && this.state.data.assigned.map((items, key) =>
                                                            <div className="eb-listing-item align-items-center d-flex" key={key}>
                                                                <label className="eb-custom-select">
                                                                    <input value={items.id} type="checkbox" className="assigned_checkbox" onChange={this.handleAssignChange(items.id)} />
                                                                    <span></span>
                                                                </label>

                                                                <div className="description-area">
                                                                    <h4>{items.first_name} {items.last_name}</h4>
                                                                    <p>{items.email}</p>
                                                                </div>
                                                                <div className="button-area">
                                                                    <button className="btn" onClick={() => this.sendUnassignRequest([items.id])}>{t('EL_UNASSIGN')}</button>
                                                                </div>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                }
                            </div>
                        </div>
                }
            </Translation>
        )
    }
}
