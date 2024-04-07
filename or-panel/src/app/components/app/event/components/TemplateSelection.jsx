import React, { Component } from "react";
import Img from 'react-image';
import { Translation } from "react-i18next";
import { GeneralService } from 'services/general-service';
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import 'sass/TemplateSelection.scss';

export default class TemplateSelection extends Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            templates: [],
            stateTab: 1,
            isOpen: false,
            state: false,
            paymentTypes: this.props.paymentTypes,
            filterLanguages: this.props.filterLanguages,
            selectedTemplate: this.props.selectedTemplate,
            languages: {},
            from_event_id: this.props.from_event_id,

            //loading
            preLoader: false,
        }
    }

    componentDidMount() {
        this._isMounted = true;
        this.languages();
        this.templates();
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.state !== this.state.state) {
            this.templates();
        }
    }

    languages = () => {
        GeneralService.languages()
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                languages: response.data.records.languages,
                            });
                        }
                    }
                },
                error => { }
            );
    }

    templates = () => {
        this.setState({ preLoader: true });
        service.post(`${process.env.REACT_APP_URL}/event/templates`, this.state)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            if (response.data) {
                                this.setState({
                                    templates: response.data.results,
                                    preLoader: false,
                                    selectedTemplate: response.data.selectedTemplate,
                                });
                            }
                        }
                    }
                },
                error => { }
            );
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    handleSelect = (id, name, language_id) => e => {
        e.preventDefault();
        this.setState({
            isOpen: !this.state.isOpen,
            selectedTemplate: {
                id: id,
                name: name,
                language_id: language_id
            }
        }, () => {
            this.props.updateTemplate(id, language_id, this.state.paymentTypes, this.state.filterLanguages, this.state.selectedTemplate);
        });
    }

    handleCheck = e => {
        var array = []
        var checkboxes = document.querySelectorAll('.checklist-eventype:checked')
        for (var i = 0; i < checkboxes.length; i++) {
            array.push(checkboxes[i].value)
        }
        this.setState({
            paymentTypes: array,
            state: !this.state.state,
        }, () => { })
    }

    changeLanguage = e => {
        var array = []
        var checkboxes = document.querySelectorAll('.checklist-eventLang:checked')
        for (var i = 0; i < checkboxes.length; i++) {
            array.push(checkboxes[i].value)
        }
        this.setState({
            filterLanguages: array,
            state: !this.state.state,
        }, () => { })
    }

    handleSearch = e => {
        var b = document.querySelectorAll(".select-list-style");
        b.forEach(function (obj, index) {
            var text = obj.innerHTML.toLowerCase();
            let cond = text.includes(e.target.value.toLowerCase());
            if (cond) {
                obj.style.display = "block";
            } else {
                obj.style.display = "none";
            }
        });
    }

    handleReset = e => {
        e.preventDefault();
        this.setState({
            state: !this.state.state,
            selectedTemplate: [],
            filterLanguages: [],
            paymentTypes: [],
            from_event_id: '',
        }, () => {
            localStorage.removeItem('from_event_id');
            localStorage.removeItem('language_id');
            this.props.updateTemplate(this.state.from_event_id, '', [], [], []);
        });
    }

    render() {
        return (
            <Translation>
                {
                    t =>
                        <React.Fragment>
                            {this.state.preLoader && <Loader fixed="true" />}
                            {!this.state.preLoader && (
                                <React.Fragment>
                                    <div className={`${this.state.isOpen && 'isOpen'} wrapper-select`}>
                                        <label onClick={() => this.setState({ isOpen: !this.state.isOpen })} className="label-wrapper-select">
                                            <div className="btn-wrapper">
                                                {this.state.selectedTemplate.name ? this.state.selectedTemplate.name : t('ED_SELECT_EVENT_TEMPLATE')}   <em className="req">*</em>
                                            </div>
                                            <i className="icon-right material-icons">keyboard_arrow_down</i>
                                        </label>
                                        {this.state.isOpen &&
                                            <div className="wrapper-select-container">
                                                <div className="wrapper-select-navigation">
                                                    <span onClick={() => this.setState({ stateTab: 1 })} className={this.state.stateTab === 1 ? 'active' : ''}>{t('ED_TEMPLATE')}</span>
                                                    <span onClick={() => this.setState({ stateTab: 2 })} className={this.state.stateTab === 2 ? 'active' : ''}>{t('ED_EVENT_TYPE')}</span>
                                                    <span onClick={() => this.setState({ stateTab: 3 })} className={this.state.stateTab === 3 ? 'active' : ''}>{t('ED_LANGUAGES')}</span>
                                                </div>
                                                {this.state.stateTab === 1 &&
                                                    <div className="wrapper-tab-content">
                                                        <div className="search-list">
                                                            <input type="text" placeholder={t('ED_SEARCH')} onKeyUp={this.handleSearch.bind(this)} />
                                                        </div>
                                                        {this.state.templates && this.state.templates.length > 0 && (
                                                            this.state.templates.map((list, i) => {
                                                                return (
                                                                    <div onClick={this.handleSelect(list.id, list.name, list.language_id)} className={`${list.id === this.state.selectedTemplate.id ? 'selected' : ''} select-list-style`} key={i}>
                                                                        {list.name}
                                                                    </div>
                                                                )
                                                            })
                                                        )
                                                        }
                                                    </div>
                                                }
                                                {this.state.stateTab === 2 &&
                                                    <div className="wrapper-tab-content">
                                                        <div className={`select-list-checkbox ${this.state.paymentTypes.includes('1') ? 'selected' : ''}`}>
                                                            <label><input type="checkbox" className={`checklist-eventype`} onChange={this.handleCheck.bind(this)} value="1" checked={this.state.paymentTypes.includes('1') ? true : false} />
                                                                <span>{t('ED_PAID')}</span>
                                                            </label>
                                                        </div>
                                                        <div className={`select-list-checkbox ${this.state.paymentTypes.includes('0') ? 'selected' : ''}`}>
                                                            <label><input type="checkbox" className={`checklist-eventype`} onChange={this.handleCheck.bind(this)} value="0" checked={this.state.paymentTypes.includes('0') ? true : false} />
                                                                <span>{t('ED_FREE')}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                }
                                                {this.state.stateTab === 3 &&
                                                    <div className="wrapper-tab-content">
                                                        {
                                                            this.state.languages && this.state.languages.length > 0 && (
                                                                this.state.languages.map((item, k) => {
                                                                    return (
                                                                        <div className={`select-list-checkbox ${this.state.filterLanguages.includes(item.id.toString()) ? 'selected' : ''}`} key={k}>
                                                                            <label>
                                                                                <input type="checkbox" className="checklist-eventLang" onChange={this.changeLanguage.bind(this)} value={item.id.toString()}
                                                                                    checked={this.state.filterLanguages.includes(item.id.toString()) ? true : false}
                                                                                />
                                                                                <span>{item.name}</span>
                                                                            </label>
                                                                        </div>
                                                                    )
                                                                })
                                                            )
                                                        }
                                                    </div>
                                                }
                                                <div className="wrapper-tab-footer d-flex">
                                                    {this.state.paymentTypes.length > 0 || this.state.filterLanguages.length > 0 || this.state.selectedTemplate.id ?
                                                        <button onClick={this.handleReset.bind(this)} className="btn btn-reset"><Img src={require('img/ico-close-circle.svg')} />{t('G_RESET')}</button> : ''
                                                    }
                                                    <div className="tab-right">
                                                        <button onClick={() => this.setState({ isOpen: !this.state.isOpen })} className="btn btn_cancel">{t('G_CANCEL')}</button>
                                                        {/* {this.state.selectedTemplate.id && <button className="btn btn_apply">{t('G_APPLY')}</button>} */}
                                                    </div>
                                                </div>
                                            </div>
                                        }
                                    </div>
                                    {this.state.isOpen && <div onClick={() => this.setState({ isOpen: !this.state.isOpen })} className="blanket-wrapper"></div>}
                                </React.Fragment>
                            )}
                        </React.Fragment>
                }
            </Translation>
        );
    }
}
