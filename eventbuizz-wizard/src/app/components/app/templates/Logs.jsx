import React, { Component } from "react";
import { Link } from 'react-router-dom';
import { service } from "services/service";
import Pagination from "react-js-pagination";
import Loader from "@/app/forms/Loader";
import "react-confirm-alert/src/react-confirm-alert.css"; // Import css
import { Translation } from "react-i18next";
import AlertMessage from "@/app/forms/alerts/AlertMessage";

class Logs extends Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            id: this.props.match.params.id,
            records: [],

            //pagination
            limit: 25,
            total: null,
            activePage: 1,

            //errors & loading
            preLoader: true,

            message: false,
            success: true
        };
    }

    componentDidMount() {
        this._isMounted = true;
        this.listing();
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    handlePageChange = activePage => {
        this.listing(activePage);
    };

    listing = (activePage = 1, loader = false) => {
        this.setState({ preLoader: !loader ? true : false });
        service
            .post(`${process.env.REACT_APP_URL}/template/logs/${this.state.id}/${activePage}`, this.state)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                records: response.data.data,
                                activePage: response.data.current_page,
                                total: response.data.total,
                                preLoader: false
                            });
                        }
                    }
                },
                error => { }
            );
    };

    render() {
        const Records = ({ data }) => {
            return (
                <React.Fragment>
                    <Translation>
                        {
                            t =>
                                <header className="header-records row d-flex">
                                    <div className="col-10">
                                        <strong>{t("T_HISTORY")}</strong>
                                    </div>
                                    <div className="col-2">
                                        <strong>{t("G_DETAIL")}</strong>
                                    </div>
                                </header>
                        }
                    </Translation>
                    {data.map((data, key) => {
                        return (
                            <Translation key={key}>
                                {
                                    t =>
                                        <div className="row d-flex" key={key}>
                                            <div className="col-10">
                                                {data.created_at}
                                            </div>
                                            <div className="col-2">
                                                <Link to={`/event/template/history/view/${data.template_id}/${data.id}`}>{t('T_VIEW_DETAIL')}</Link>
                                            </div>
                                        </div>
                                }
                            </Translation>
                        )
                    })}
                </React.Fragment>
            )
        }

        return (
            <Translation>
                {t => (
                    <div>
                        <div className="wrapper-content third-step">
                            {this.state.preLoader && <Loader />}
                            {!this.state.preLoader && (
                                <React.Fragment>
                                    {this.state.message && (
                                        <AlertMessage
                                            className={`alert  ${
                                                this.state.success ? "alert-success" : "alert-danger"
                                                }`}
                                            title={`${this.state.success ? "" : t("EE_OCCURRED")}`}
                                            content={this.state.message}
                                            icon={this.state.success ? "check" : "info"}
                                        />
                                    )}
                                    <header className="new-header d-flex clearfix">
                                        <h1
                                            style={{ whiteSpace: "nowrap" }}
                                            className="section-title float-left"
                                        >
                                            <Link to={`/event/template/edit/${this.state.id}`}><i
                                                className="material-icons">arrow_back_ios</i></Link> {t("T_TEMPLATE_VERSION_HISTORY")}
                                        </h1>
                                    </header>

                                    <div
                                        style={{ paddingTop: "15px" }}
                                        className="attendee-management-section"
                                    >
                                        {this.state.records.length > 0 ? (
                                            <div className="hotel-management-records attendee-records-template">
                                                <Records data={this.state.records} />
                                            </div>
                                        ) : (
                                                ""
                                            )}
                                        {this.state.total > this.state.limit ? (
                                            <nav
                                                className="page-navigation"
                                                aria-label="navigation"
                                            >
                                                <Pagination
                                                    hideFirstLastPages={true}
                                                    prevPageText="keyboard_arrow_left"
                                                    linkClassPrev="material-icons"
                                                    nextPageText="keyboard_arrow_right"
                                                    linkClassNext="material-icons"
                                                    innerClass="pagination"
                                                    itemClass="page-item"
                                                    linkClass="page-link"
                                                    activePage={this.state.activePage}
                                                    itemsCountPerPage={this.state.limit}
                                                    totalItemsCount={this.state.total}
                                                    pageRangeDisplayed={5}
                                                    onChange={this.handlePageChange}
                                                />
                                            </nav>
                                        ) : (
                                                ""
                                            )}
                                    </div>
                                </React.Fragment>
                            )}
                        </div>
                    </div>
                )}
            </Translation>
        );
    }
}

export default Logs;
