import * as React from 'react';
import { NavLink } from 'react-router-dom';
import { connect } from 'react-redux';
import { GeneralAction } from 'actions/general-action';
import { withRouter } from 'react-router-dom';
import { Translation } from "react-i18next";
import { service } from 'services/service';
import { EventAction } from 'actions/event/event-action';
import { AuthAction } from 'actions/auth/auth-action';

const in_array = require("in_array");

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/module/sub-registration", "ddirectory": "/event/module/documents" };

class SideBar extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            default_template_id: (this.props.event !== undefined ? this.props.event.default_template_id : null),
            templateModules: '',
            event_id: localStorage.getItem('event_id'),
            editData: (window.location.pathname.includes('/event/edit/') ? true : false),
            completed_modules: [],
            defaultModuleRoute: "/event/module/attendees",
            invitationStep: this.props.invitation.step,
            current_path: this.props.match.path
        };
    }

    componentDidMount() {
        this._isMounted = true;
        if (this.state.event_id) {
            this.templates();
            this.progress();
            this.updateDefaultModuleRoute();
        }
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    static getDerivedStateFromProps(props, state) {
        if (state.current_path !== props.match.path) {
            return {
                event_id: (props.event.id !== undefined ? props.event.id : ''),
                editData: props.match.path.includes('/event/edit/'),
                invitationStep: props.invitation.step,
                current_path: props.match.path
            };
        } else if (state.invitationStep !== props.invitation.step) {
            return {
                invitationStep: props.invitation.step
            };
        }
        // Return null to indicate no change to state.
        return null;
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.current_path !== this.state.current_path || prevProps.redirect !== this.props.redirect) {
            if (this.state.event_id) {
                this.progress();
            }
            if (prevState.event_id !== this.state.event_id && this.state.event_id) {
                this.templates();
            }
            this.updateDefaultModuleRoute();
        }
    }

    templates() {
        service.get(`${process.env.REACT_APP_URL}/template/listing`)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                templateModules: response.data,
                                default_template_id: response.data.default_template_id,
                            }, () => {
                                this.props.event.templateIds = response.data.templateIds;
                                this.props.dispatch(EventAction.eventInfo(this.props.event));
                            });
                        }
                    }
                },
                error => { }
            );
    }

    updateDefaultModuleRoute() {
        if (this.state.event_id) {
            //set next previous
            if (this.props.event.modules !== undefined && this.props.event.modules.length > 0) {
                let modules = this.props.event.modules.filter(function (module, i) {
                    return ((!in_array(module.alias, ["polls"]) && Number(module.status) === 1) || (in_array(module.alias, ["subregistration"])));
                });
                if (Number(this.props.event.is_app) === 1) {
                    this.setState({
                        defaultModuleRoute: "/event/module/event-module-order"
                    });
                } else if (Number(this.props.event.is_registration) === 1) {
                    this.setState({
                        defaultModuleRoute: "/event/module/eventsite-module-order"
                    });
                } else {
                    this.setState({
                        defaultModuleRoute: (modules[0] !== undefined && module_routes[modules[0]['alias']] !== undefined ? module_routes[modules[0]['alias']] : "/event/manage/surveys")
                    });
                }
            }
        }
    }

    progress() {
        service.get(`${process.env.REACT_APP_URL}/event/progress`)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            if (response.data) {
                                this.setState({
                                    completed_modules: response.data.modules
                                });
                            }
                        }
                    }
                },
                error => { }
            );
    }

    render() {
        const { event_id } = this.state;
        return (
            <Translation>
                {
                    t => <div className="row d-flex">
                        <div className="col-3">
                            {!window.location.pathname.includes('/event/template/') && !window.location.pathname.includes('/event/invitation/send-invitation') && !window.location.pathname.includes('/account/organizer/') && !window.location.pathname.includes('/account/change-password') && <div className="app-side-application">
                                <div className="btn-return-navigation"><NavLink to="/"><i className='material-icons'>arrow_back</i><p>{t('M_RETURN_TO_LIST')}</p></NavLink></div>
                                <ul className={window.location.pathname.includes('/event/create') ? 'create-event' : ''}>
                                    <React.Fragment>
                                        <li className={`${window.location.pathname.includes('/event/create') || window.location.pathname.includes('event/edit') ? 'bold activeList' : ''} ${in_array('event', this.state.completed_modules) ? 'completed' : ''}`}>
                                            {window.location.pathname.includes('event/create') ? (
                                                <span><NavLink to='/event/create' onClick={() => { this.props.dispatch(GeneralAction.step(1)); }}>{t('G_CREATE_EVENT')}</NavLink></span>) : (<span><NavLink to={`/event/edit/${event_id}`} onClick={() => { this.props.dispatch(GeneralAction.step(1)); }}>{t('M_ENTER_EVENT_DETAILS')}</NavLink></span>)}
                                            <ul>
                                                <li>
                                                    {this.props.eventStep === 1 ? (
                                                        <span className="active">{t('M_EVENT_SETUP')}</span>
                                                    ) : (
                                                            <span onClick={() => this.props.dispatch(GeneralAction.step(1))} className="activeCompleted">{t('M_EVENT_SETUP')}</span>
                                                        )}
                                                </li>
                                                <li>
                                                    {this.props.eventStep === 2 ? (
                                                        <span className="active">{t('M_EVENT_DETAILS')}</span>
                                                    ) : this.props.eventStep > 2 ? (
                                                        <span style={{ cursor: 'pointer' }} onClick={() => this.props.dispatch(GeneralAction.step(2))} className="activeCompleted">{t('M_EVENT_DETAILS')}</span>
                                                    ) : this.state.editData ? (
                                                        <span style={{ cursor: 'pointer' }} onClick={() => this.props.dispatch(GeneralAction.step(2))}>{t('M_EVENT_DETAILS')}</span>
                                                    ) : <span>{t('M_EVENT_DETAILS')}</span>}
                                                </li>
                                                <li>
                                                    {this.props.eventStep === 3 ? (
                                                        <span className="active">{t('M_EVENT_DATE_AND_LOCATION')}</span>
                                                    ) : this.props.eventStep > 3 ? (
                                                        <span style={{ cursor: 'pointer' }} onClick={() => this.props.dispatch(GeneralAction.step(3))} className="activeCompleted">{t('M_EVENT_DATE_AND_LOCATION')}</span>
                                                    ) : this.state.editData ? (
                                                        <span style={{ cursor: 'pointer' }} onClick={() => this.props.dispatch(GeneralAction.step(3))}>{t('M_EVENT_DATE_AND_LOCATION')}</span>
                                                    ) : <span>{t('M_EVENT_DATE_AND_LOCATION')}</span>}
                                                </li>
                                            </ul>
                                        </li>
                                        <li className={`${window.location.pathname.includes('/event/settings/branding') ? 'bold activeList' : ''} ${in_array('branding', this.state.completed_modules) ? 'completed' : ''}`}>
                                            <span><NavLink to="/event/settings/branding">{t('M_EVENT_BRANDING')}</NavLink></span>
                                        </li>
                                        {this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 1 && (
                                            <li className={`${window.location.pathname.includes('event_site/billing-module') ? 'bold activeList' : ''}`}>
                                                <span><NavLink to="/event_site/billing-module/payment-methods">{t('BILLING_BILLING_SETUP_MENU')}</NavLink></span>
                                                <ul>
                                                    <li><NavLink to="/event_site/billing-module/payment-methods"><span className='active'>{t('BILLING_PAYMENT_METHOD_MENU')}</span></NavLink></li>
                                                    <li className={this.props.event && this.props.event.eventsite_secion_fields && Number(this.props.event.eventsite_secion_fields.company_detail.credit_card_payment.status) === 1 ? "" : 'lockscreen'}><NavLink to="/event_site/billing-module/payment-providers"><span className='active'>{t('BILLING_PAYMENT_PROVIDERS_MENU')}</span></NavLink></li>
                                                    {this.props.event && this.props.event.eventsite_payment_setting && Number(this.props.event.eventsite_payment_setting.billing_type) === 1 && (
                                                        <React.Fragment>
                                                            <li className={this.props.event && this.props.event.eventsite_secion_fields && Number(this.props.event.eventsite_secion_fields.company_detail.company_public_payment.status) === 1 ? "" : 'lockscreen'}><NavLink to="/event_site/billing-module/ean-invoice"><span className='active'>{(this.props.event && this.props.event.eventsite_secion_fields && this.props.event.eventsite_secion_fields.company_detail.company_public_payment.name ? this.props.event.eventsite_secion_fields.company_detail.company_public_payment.name : t('BILLING_EAN_MENU'))}</span></NavLink></li>
                                                            <li className={this.props.event && this.props.event.eventsite_payment_setting && Number(this.props.event.eventsite_payment_setting.eventsite_billing_fik) === 1 ? "" : 'lockscreen'}><NavLink to="/event_site/billing-module/fik-setting"><span className='active'>{t('BILLING_FIK_MENU')}</span></NavLink></li>
                                                        </React.Fragment>
                                                    )}
                                                    <li><NavLink to="/event_site/billing-module/items"><span className='active'>{t('BILLING_ITEMS_MENU')}</span></NavLink>
                                                    </li>
                                                    <li><NavLink to="/event_site/billing-module/voucher"><span className='active'>{t('BILLING_VOUCHER_MENU')}</span></NavLink></li>
                                                    <li><NavLink to="/event_site/billing-module/purchase-policy"><span className='active'>{t('BILLING_PURCHSE_POLICY_MENU')}</span></NavLink>
                                                    </li>
                                                    <li><NavLink to="/event_site/billing-module/manage-orders"><span className='active'>{t('BILLING_VOUCHER_ORDERS')}</span></NavLink></li>
                                                    {/*waiting list orders menu*/}
                                                    <li>
                                                        <NavLink to="/event_site/billing-module/waiting-list-orders">
                                                            <span className="active">
                                                                {t("BILLING_WAITING_LIST_ORDERS")}
                                                            </span>
                                                        </NavLink>
                                                    </li>
                                                </ul>
                                            </li>
                                        )}
                                        {this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 0 && (
                                            <li className={`${window.location.pathname.includes('event_site/billing-module') ? 'bold activeList' : ''}`}>
                                                <span>
                                                    <NavLink to={this.props.event.eventsite_payment_setting && Number(this.props.event.eventsite_payment_setting.is_item) === 1 ? "/event_site/billing-module/items" : "/event_site/billing-module/manage-orders"}>
                                                        {t('BILLING_BILLING_FREE_SETUP_MENU')}
                                                    </NavLink>
                                                </span>
                                                <ul>
                                                    {this.props.event.eventsite_payment_setting && Number(this.props.event.eventsite_payment_setting.is_item) === 1 && (
                                                        <li>
                                                            <NavLink to="/event_site/billing-module/items">
                                                                <span className='active'>{t('BILLING_ITEMS_MENU')}
                                                                </span>
                                                            </NavLink>
                                                        </li>
                                                    )}
                                                    <li>
                                                        <NavLink to="/event_site/billing-module/manage-orders">
                                                            <span className='active'>
                                                                {t('BILLING_VOUCHER_ORDERS')}
                                                            </span>
                                                        </NavLink>
                                                    </li>

                                                    {/*waiting list orders menu*/}
                                                    <li>
                                                        <NavLink to="/event_site/billing-module/waiting-list-orders">
                                                            <span className="active">
                                                                {t("BILLING_WAITING_LIST_ORDERS")}
                                                            </span>
                                                        </NavLink>
                                                    </li>

                                                </ul>
                                            </li>
                                        )}
                                        <React.Fragment>
                                            {(Number(this.props.event.is_registration) === 1 || window.location.pathname.includes('/event/create')) && (
                                                <React.Fragment>
                                                    <li className={`${window.location.pathname.includes('event/registration') ? 'bold activeList' : ''} ${in_array('sub-registration', this.state.completed_modules) && in_array('hotel', this.state.completed_modules) && in_array('disclaimer', this.state.completed_modules) ? 'completed' : ''}`}>
                                                        <span><NavLink to="/event/registration/basic-detail-form">{t('M_REGISTRATION_FORM_FIELDS')}</NavLink></span>
                                                        <ul>
                                                            <li>
                                                                <NavLink to="/event/registration/basic-detail-form"><span className='active'>{t('M_ATTENDEE_FIELDS')}</span></NavLink>
                                                            </li>
                                                            <li>
                                                                <NavLink to="/event/registration/attendee-type-form"><span>{t('M_ATTENDEE_TYPES')}</span></NavLink>
                                                            </li>
                                                            <li>
                                                                <NavLink to="/event/registration/company-detail-form"><span>{t('M_COMPANY_FIELDS')}</span></NavLink>
                                                            </li>
                                                            {this.props.event.modules !== undefined && this.props.event.modules.length > 0 && this.props.event.modules.map((module, i) => {
                                                                return (
                                                                    <React.Fragment key={i}>
                                                                        {module.alias === "subregistration" && (
                                                                            <li>
                                                                                <NavLink to="/event/registration/sub-registration"><span>{module.value}</span></NavLink>
                                                                            </li>
                                                                        )}
                                                                    </React.Fragment>
                                                                )
                                                            })}
                                                            <li>
                                                                <NavLink to="/event/registration/manage/hotels"><span>{t('M_ACCOMODATION')}</span></NavLink>
                                                            </li>
                                                            <li>
                                                                <NavLink to="/event/registration/gdpr"><span>{t('M_GDPR_POLICY')}</span></NavLink>
                                                            </li>
                                                            <li>
                                                                <NavLink to="/event/registration/tos"><span>{t('M_TERMS_CONDITIONS')}</span></NavLink>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </React.Fragment>
                                            )}
                                            <li className={`${window.location.pathname.includes('event/module') ? 'bold activeList' : ''} ${in_array('attendee', this.state.completed_modules) && in_array('program', this.state.completed_modules) && in_array('speaker', this.state.completed_modules) && in_array('additional-info', this.state.completed_modules) && in_array('practical-info', this.state.completed_modules) && in_array('additional-info', this.state.completed_modules) && in_array('general-info', this.state.completed_modules) ? 'completed' : ''}`}>
                                                <span><NavLink to={this.state.defaultModuleRoute}>{t('M_UPLOAD_CONTENT')}</NavLink></span>
                                                <ul>
                                                    <li className={Number(this.props.event.is_app) === 0 ? "lockscreen" : ''}><NavLink to="/event/module/event-module-order"><span className='active'>{t('M_CUSTOMIZE_APP_MENU')}</span></NavLink>
                                                    </li>
                                                    <li className={Number(this.props.event.is_registration) === 0 ? "lockscreen" : ''}><NavLink to="/event/module/eventsite-module-order"><span className='active'>{t('M_CUSTOMIZE_WEBSITE_MENU')}</span></NavLink>
                                                    </li>
                                                    {this.props.event.modules !== undefined && this.props.event.modules.length > 0 && this.props.event.modules.map((module, i) => {
                                                        return (
                                                            <React.Fragment key={i}>
                                                                {module.alias === "attendees" && (
                                                                    <li className={Number(module.status) === 0 ? "lockscreen" : ''}>
                                                                        <NavLink to="/event/module/attendees"><span className='active'>{module.value}</span></NavLink>
                                                                    </li>
                                                                )}
                                                                {module.alias === "agendas" && (
                                                                    <li className={Number(module.status) === 0 ? "lockscreen" : ''}>
                                                                        <NavLink to="/event/module/programs"><span>{module.value}</span></NavLink>
                                                                    </li>
                                                                )}
                                                                {module.alias === "speakers" && (
                                                                    <li className={Number(module.status) === 0 ? "lockscreen" : ''}>
                                                                        <NavLink to="/event/module/speakers"><span>{module.value}</span></NavLink>
                                                                    </li>
                                                                )}
                                                                {module.alias === "subregistration" && (
                                                                    <li>
                                                                        <NavLink to="/event/module/sub-registration"><span>{module.value}</span></NavLink>
                                                                    </li>
                                                                )}
                                                                {module.alias === "infobooth" && (
                                                                    <li className={Number(module.status) === 0 ? "lockscreen" : ''}>
                                                                        <NavLink to="/event/module/practical-information"><span>{module.value}</span></NavLink>
                                                                    </li>
                                                                )}
                                                                {module.alias === "additional_info" && (
                                                                    <li className={Number(module.status) === 0 ? "lockscreen" : ''}>
                                                                        <NavLink to="/event/module/additional-information"><span>{module.value}</span></NavLink>
                                                                    </li>
                                                                )}
                                                                
                                                                {module.alias === "general_info" && (
                                                                    <li className={Number(module.status) === 0 ? "lockscreen" : ''}>
                                                                        <NavLink to="/event/module/general-information"><span>{module.value}</span></NavLink>
                                                                    </li>
                                                                )}

                                                                {module.alias === "general_info" && (
                                                                    <li className={Number(module.status) === 0 ? "lockscreen" : ''}>
                                                                        <NavLink to="/event/module/information-pages"><span>{t('INFORMATION_PAGES_SIDEBAR_MENU_TITLE')}</span></NavLink>
                                                                    </li>
                                                                )}

                                                                {module.alias === "maps" && (
                                                                    <li className={Number(module.status) === 0 ? "lockscreen" : ''}>
                                                                        <NavLink to="/event/module/map"><span>{module.value}</span></NavLink>
                                                                    </li>
                                                                )}
                                                                {module.alias === "ddirectory" && (
                                                                    <React.Fragment>
                                                                        {this.props.event !== undefined && this.props.event.defaultDirectory && this.props.event.directory_sub_modules !== undefined && this.props.event.directory_sub_modules.length > 0 && (
                                                                            <li className={`${window.location.pathname.includes('event/module/documents') ? 'bold activeList' : ''} ${Number(module.status) === 0 ? "lockscreen" : ''}`}>
                                                                                <span style={{ lineHeight: 'inherit' }}><NavLink to={`/event/module/documents/agendas/${this.props.event.defaultDirectory.id}`}>{module.value}</NavLink></span>
                                                                                <ul>
                                                                                    {this.props.event.directory_sub_modules !== undefined && this.props.event.directory_sub_modules.length > 0 && this.props.event.directory_sub_modules.map((menu, i) => {
                                                                                        return (
                                                                                            <React.Fragment key={i}>
                                                                                                {menu.alias === "agendas" && (
                                                                                                    <li className={Number(menu.status) === 0 ? "lockscreen" : ''}>
                                                                                                        <NavLink isActive={(match, location) => {
                                                                                                            if (location.pathname === `/event/module/documents/agendas/${menu.id}`) {
                                                                                                                return true;
                                                                                                            }
                                                                                                            return false;
                                                                                                        }} to={`/event/module/documents/agendas/${menu.id}`}>{menu.name}</NavLink>
                                                                                                    </li>
                                                                                                )}
                                                                                                {menu.alias === "speakers" && (
                                                                                                    <li className={Number(menu.status) === 0 ? "lockscreen" : ''}>
                                                                                                        <NavLink isActive={(match, location) => {
                                                                                                            if (location.pathname === `/event/module/documents/speakers/${menu.id}`) {
                                                                                                                return true;
                                                                                                            }
                                                                                                            return false;
                                                                                                        }} to={`/event/module/documents/speakers/${menu.id}`}>{menu.name}</NavLink>
                                                                                                    </li>
                                                                                                )}
                                                                                                {menu.alias === "other" && (
                                                                                                    <li className={Number(menu.status) === 0 ? "lockscreen" : ''}>
                                                                                                        <NavLink isActive={(match, location) => {
                                                                                                            if (location.pathname === `/event/module/documents/other/${menu.id}`) {
                                                                                                                return true;
                                                                                                            }
                                                                                                            return false;
                                                                                                        }} to={`/event/module/documents/other/${menu.id}`}>{menu.name}</NavLink>
                                                                                                    </li>
                                                                                                )}
                                                                                            </React.Fragment>
                                                                                        )
                                                                                    })}
                                                                                </ul>
                                                                            </li>
                                                                        )}
                                                                    </React.Fragment>
                                                                )}
                                                            </React.Fragment>
                                                        )
                                                    })}
                                                </ul>
                                            </li>
                                            {this.props.event.modules !== undefined && this.props.event.modules.length > 0 && this.props.event.modules.map((module, i) => {
                                                return (
                                                    <React.Fragment key={i}>
                                                        {module.alias === "polls" && (
                                                            <li className={`${(window.location.pathname.includes('event/manage/surveys') || window.location.pathname.includes('event/manage/survey/questions') ? 'bold activeList' : "")} ${(Number(module.status) === 0 ? "lockscreen" : '')} ${in_array('survey', this.state.completed_modules) ? 'completed' : ''}`}>
                                                                <span><NavLink to="/event/manage/surveys">{module.value}</NavLink></span>
                                                            </li>
                                                        )}
                                                    </React.Fragment>
                                                )
                                            })}
                                            <li className={window.location.pathname.includes('/event/preview') ? 'bold activeList lastItem' : 'lastItem'}>
                                                <span><NavLink to="/event/preview">{t('PRV_PREVIEW')}</NavLink></span>
                                            </li>
                                        </React.Fragment>
                                    </React.Fragment>

                                </ul>
                            </div>}
                            {window.location.pathname.includes('/event/template/') && <div className="app-side-application">
                                <div className="btn-return-navigation"><NavLink to={`/event/edit/${event_id}`} onClick={() => { this.props.dispatch(GeneralAction.step(1)); }}><i className='material-icons'>arrow_back</i>{t('M_RETURN_TO_EDIT_EVENT')}</NavLink></div>
                                <ul className="sidebarTemplate">
                                    {this.state.templateModules && (
                                        <li className={`${window.location.pathname === `/event/template/edit/${this.props.match.params.id}` || window.location.pathname === `/event/template/logs/${this.props.match.params.id}` || window.location.pathname === `/event/template/history/view/${this.props.match.params.template_id}/${this.props.match.params.id}` ? 'bold activeList' : ''}`}>
                                            <span><NavLink to={`/event/template/edit/${this.state.default_template_id}`} >{t('T_EMAIL_TEMPLATES')}</NavLink></span>
                                            <ul>
                                                {this.state.templateModules.email && this.state.templateModules.email.sub && (
                                                    <React.Fragment>
                                                        {Object.values(this.state.templateModules.email.sub).map(row =>
                                                        (
                                                            Object.values(row.sub).map(row =>
                                                            (
                                                                <li key={row.display}>
                                                                    <span> <NavLink isActive={(match, location) => {
                                                                        if (location.pathname === `/event/${row.url}` || location.pathname === `/event/template/logs/${row.id}` || location.pathname === `/event/template/history/view/${row.id}/${this.props.match.params.id}`) {
                                                                            return true;
                                                                        }

                                                                        return false;
                                                                    }} to={`/event/${row.url}`}>{t(`T_${row.alias}`)}</NavLink></span>
                                                                </li>
                                                            )
                                                            )
                                                        )
                                                        )}
                                                    </React.Fragment>
                                                )}
                                            </ul>
                                        </li>
                                    )}
                                    {/* End */}
                                </ul>
                            </div>}
                            {window.location.pathname.includes('/event/invitation/send-invitation') && (
                                <div className="app-side-application">
                                    <div className="btn-return-navigation"><NavLink to={`/event/edit/${event_id}`} onClick={() => { this.props.dispatch(GeneralAction.step(1)); }}><i className='material-icons'>arrow_back</i>{t('M_RETURN_TO_EDIT_EVENT')}</NavLink></div>
                                    <ul>
                                        <li className={`${!this.props.invitation.module ? 'bold activeList' : ''} ${this.state.invitationStep >= 1 ? 'completed' : ''}`}>
                                            <span><a style={{ cursor: 'pointer' }} onClick={() => { this.props.dispatch(EventAction.invitation(null)); this.props.history.push(`/event/invitation/send-invitation`); }}>{t('M_CREATE_INVITE')}</a></span>
                                            <ul>
                                                <li>
                                                    <span onClick={() => this.props.dispatch(EventAction.invitation(null))} className="activeCompleted">{t('M_SELECT_INVITE_TYPE')}</span>
                                                </li>
                                            </ul>
                                        </li>

                                        <li className={`${this.state.invitationStep === 1 ? 'bold activeList' : ''} ${this.state.invitationStep >= 2 ? 'completed' : ''}`}>
                                            {this.state.invitationStep > 1 ? (
                                                <span><a style={{ cursor: 'pointer' }} onClick={() => { this.props.invitation.step = 1; this.props.dispatch(EventAction.invitation(this.props.invitation)); this.props.history.push(`/event/invitation/send-invitation/1`); }}>{t('M_EDIT_EMAIL')}</a></span>
                                            ) : (
                                                    <span>{t('M_EDIT_EMAIL')}</span>
                                                )}
                                        </li>
                                        <li className={`${this.state.invitationStep === 2 ? 'bold activeList' : ''} ${this.state.invitationStep >= 3 ? 'completed' : ''}`}>
                                            {this.state.invitationStep > 2 ? (
                                                <span><a style={{ cursor: 'pointer' }} onClick={() => { this.props.invitation.step = 2; this.props.dispatch(EventAction.invitation(this.props.invitation)); this.props.history.push(`/event/invitation/send-invitation/2`); }}>{t('M_UPLOAD_UPDATE_GUEST_LIST')}</a></span>
                                            ) : (
                                                    <span>{t('M_UPLOAD_UPDATE_GUEST_LIST')}</span>
                                                )}
                                        </li>
                                        <li className={`lastItem ${this.state.invitationStep === 3 ? 'bold activeList' : ''} ${this.state.invitationStep >= 4 ? 'completed' : ''}`}>
                                            <span style={{ cursor: 'pointer' }}>{t('M_PREVIEW_SEND_EMAIL')}</span>
                                        </li>
                                    </ul>
                                </div>
                            )}
                            {window.location.pathname.includes('/account/organizer/') && (
                                <div className="app-side-application app-application-setting">
                                    <div className="btn-return-navigation"><NavLink to={`/`} onClick={() => { this.props.dispatch(GeneralAction.step(1)); }}><i className='material-icons'>arrow_back</i>{t('M_RETURN_TO_MAIN')}</NavLink></div>
                                    <ul>
                                        <li>
                                            <NavLink to="/account/organizer/profile"><img alt="" src={require('img/ico-user.svg')} /><span> {t('M_MY_ACCOUNT')}</span></NavLink>
                                        </li>
                                        <li>
                                            <NavLink to="/account/organizer/change-password"><img alt="" src={require('img/ico-lock-lg.svg')} /><span>{t('M_CHANGE_PASSWORD')}</span></NavLink>
                                        </li>
                                        <li>
                                            <a href="#!" onClick={(e) => { e.preventDefault(); AuthAction.logout() }} >
                                                <span className="icons">
                                                    <img alt="" src={require('img/ico-logout.svg')} />
                                                </span>{t('LOGOUT')}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            )}
                        </div>
                        <div className="col-9">
                            {this.props.children}
                        </div>
                    </div>

                }
            </Translation>
        );
    }
}

function mapStateToProps(state) {
    const { eventStep, event, invitation, eventState, redirect } = state;
    return {
        eventStep, event, invitation, eventState, redirect
    };
}

export default connect(mapStateToProps)(withRouter(SideBar));