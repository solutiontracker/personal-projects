import * as React from "react";
import { Link } from 'react-router-dom';
import Input from '@/app/forms/Input';
import { service } from 'services/service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Loader from '@/app/forms/Loader';
import { Translation } from "react-i18next";

class ViewTemplateHistory extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            id: this.props.match.params.id,
            title: '',
            subject: '',
            template: '',

            //errors & loading
            message: false,
            success: true,
            errors: {},
            isLoader: false,
            preLoader: false,
        };

        this.config = {
            htmlRemoveTags: ['script'],
        };

        this.handleEditorChange = this.handleEditorChange.bind(this);
    }

    componentDidMount() {
        this._isMounted = true;
        if (this.state.id !== undefined) {
            this.getTemplate(this.state.id);
        }
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    getTemplate(id) {
        this.setState({ preLoader: true });
        service.get(`${process.env.REACT_APP_URL}/template/view/history/${id}`)
            .then(
                response => {
                    if (response.success) {
                        if (response.data) {
                            if (this._isMounted) {
                                this.setState({
                                    subject: response.data.subject,
                                    title: response.data.alias,
                                    template: response.data.style + response.data.template,
                                    template_id: response.data.template_id,
                                    preLoader: false
                                });

                                var templateHtml = new Blob([response.data.style + response.data.template], {
                                    type: "text/html"
                                });

                                const templateIframe = document.getElementById("template");
                                templateIframe.src = URL.createObjectURL(templateHtml);
                            }
                        }
                    }
                },
                error => { }
            );
    }


    static getDerivedStateFromProps(props, state) {
        if (state.id !== props.match.params.id && props.match.params.id !== undefined) {
            return {
                id: props.match.params.id,
                message: ""
            };
        }
        // Return null to indicate no change to state.
        return null;
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.id !== this.state.id) {
            this.getTemplate(this.state.id);
        }
    }

    handleChange = input => e => {
        this.setState({
            [input]: e.target.value
        })
    };

    handleEditorChange = (e) => {
        this.setState({
            template: e.editor.getData()
        });
    }

    render() {
        return (
            <Translation key="template">
                {
                    t =>
                        <div className="wrapper-content third-step">
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
                                    <header className="new-header d-flex clearfix">
                                        <h1 className="section-title float-left"><Link to={`/event/template/edit/${this.state.template_id}`}><i
                                            className="material-icons">arrow_back_ios</i></Link> {t(`T_${this.state.title}`)}</h1>
                                    </header>
                                    <Input
                                        type='text'
                                        label={t('T_SUBJECT')}
                                        name='subject'
                                        value={this.state.subject}
                                        onChange={this.handleChange('subject')}
                                        required={true}
                                    />
                                    {this.state.errors.subject && <p className="error-message">{this.state.errors.subject}</p>}
                                    <iframe
                                        title="template"
                                        frameBorder="0"
                                        width="100%"
                                        height="450px"
                                        id="template"
                                    ></iframe>
                                    {this.state.errors.template &&
                                        <p className="error-message">{this.state.errors.template}</p>}
                                </React.Fragment>
                            )}
                        </div>
                }
            </Translation>
        )
    }
}
export default ViewTemplateHistory;