import * as React from "react";
import { NavLink } from 'react-router-dom';
import Input from '@/app/forms/Input';
import { service } from 'services/service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Loader from '@/app/forms/Loader';
import { Translation } from "react-i18next";
import CKEditor from 'ckeditor4-react';
import ConfirmationModal from "@/app/forms/ConfirmationModal";

class PurchasePolicy extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            type: 'purchase-policy',
            purchase_policy_inline_text: '',
            purchase_policy: '',

            //errors & loading
            message: false,
            success: true,
            errors: {},
            isLoader: false,
            preLoader: false,

            prev: '/event_site/billing-module/voucher',
            next: '/event_site/billing-module/manage-orders',

            change: false
        };

        this.config = {
            htmlRemoveTags: ['script'],
        };
    }

    componentDidMount() {
        this._isMounted = true;
        this.setState({ preLoader: true });
        service.get(`${process.env.REACT_APP_URL}/eventsite/billing/purchase-policy`)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            if (response.data) {
                                this.setState({
                                    purchase_policy_inline_text: response.data.purchase_policy_inline_text,
                                    purchase_policy: response.data.purchase_policy,
                                    preLoader: false
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

    save = e => {
        e.preventDefault();
        const type = e.target.getAttribute('data-type');
        this.setState({ isLoader: type });
        service.put(`${process.env.REACT_APP_URL}/eventsite/billing/purchase-policy`, this.state)
            .then(
                response => {
                    if (response.success) {
                        this.setState({
                            message: response.message,
                            success: true,
                            isLoader: false,
                            errors: {},
                            change: false
                        });
                        if (type === "save-next") this.props.history.push(this.state.next);
                    } else {
                        this.setState({
                            message: response.message,
                            success: false,
                            isLoader: false,
                            errors: response.errors
                        });
                    }
                },
                error => { }
            );
    }

    handleEditorChange = (e) => {
        this.setState({
            purchase_policy: e.editor.getData(),
            change: true
        });
    }

    handleChange = input => e => {
        this.setState({
            [input]: e.target.value,
            change: true
        })
    };

    render() {
        return (
            <Translation>
                {
                    t =>
                        <div className="wrapper-content third-step">
                            <ConfirmationModal update={this.state.change} />
                            {this.state.message &&
                                <AlertMessage
                                    className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                                    title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                                    content={this.state.message}
                                    icon={this.state.success ? "check" : "info"}
                                />
                            }
                            {this.state.preLoader && <Loader />}
                            {!this.state.preLoader && (
                                <React.Fragment>
                                    <div style={{ height: '100%' }}>
                                        <div className="row">
                                            <div className="col-6">
                                                <h1 className="section-title">{t("BILLING_PURCHASE_POLICY_MAIN_HEADING")}</h1>
                                                <p>{t("BILLING_PURCHASE_POLICY_SUB_HEADING")}</p>
                                                <Input
                                                    type='text'
                                                    label={t('BILLING_PURCHASE_POLICY_INLINE_TEXT')}
                                                    name='content'
                                                    value={this.state.purchase_policy_inline_text}
                                                    onChange={this.handleChange('purchase_policy_inline_text')}
                                                    required={true}
                                                />
                                                {this.state.errors.content && <p className="error-message">{this.state.errors.content}</p>}
                                                <p><em>{t("BILLING_PURCHASE_POLICY_INLINE_TEXT_ALERT")}</em></p>
                                                <h5>{t("BILLING_PURCHASE_POLICY_DESC")}</h5>
                                                <CKEditor
                                                    data={this.state.purchase_policy}
                                                    config={{
                                                        enterMode: CKEditor.ENTER_BR,
                                                        fullPage: true,
                                                        allowedContent: true,
                                                        extraAllowedContent: 'style[id]',
                                                        htmlEncodeOutput: false,
                                                        entities: false,
                                                        height: 250,
                                                    }}
                                                    onChange={this.handleEditorChange}
                                                />
                                                {this.state.errors.purchase_policy &&
                                                    <p className="error-message">{this.state.errors.purchase_policy}</p>}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="bottom-component-panel clearfix">
                                        <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                                            <i className='material-icons'>remove_red_eye</i>
                                            {t('G_PREVIEW')}
                                        </NavLink>
                                        <NavLink className="btn btn-prev-step" to={this.state.prev}><span className="material-icons">
                                            keyboard_backspace</span></NavLink>
                                        <button data-type="save" disabled={this.state.isLoader ? true : false} className="btn btn btn-save" onClick={this.save}>{this.state.isLoader === "save" ?
                                            <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE')}
                                        </button>
                                        <button data-type="save-next" disabled={this.state.isLoader ? true : false} className="btn btn-save-next" onClick={this.save}>{this.state.isLoader === "save-next" ?
                                            <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE_NEXT')}
                                        </button>
                                    </div>
                                </React.Fragment>
                            )}
                        </div>
                }
            </Translation>
        )
    }
}
export default PurchasePolicy;