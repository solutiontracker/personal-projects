import React, { Component } from 'react';
import { NavLink, Link } from 'react-router-dom';
import Img from 'react-image';
import { ReactSVG } from 'react-svg';
import ImportCSV from '@/app/forms/ImportCSV';
import FormWidget from '@/app/program/forms/FormWidget';
import { ProgramService } from "services/program/program-service";
import Pagination from "react-js-pagination";
import Loader from '@/app/forms/Loader';
import { confirmAlert } from 'react-confirm-alert'; // Import
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { Translation } from "react-i18next";
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { connect } from 'react-redux';
import { service } from 'services/service';
import moment from 'moment';

const in_array = require("in_array");

const labelArray = [
    {
        value: 'Do not map this field',
        name: '-1'
    },
    {
        value: 'Topic',
        name: 'topic',
    },
    {
        value: 'Description',
        name: 'description'
    },
    {
        value: 'Date (dd-mm-yyyy)',
        name: 'date'
    },
    {
        value: 'Start time (hh:mm)',
        name: 'start_time'
    },
    {
        value: 'End time (hh:mm)',
        name: 'end_time'
    },
    {
        value: 'Location',
        name: 'location'
    },
    {
        value: 'Track id',
        name: 'track_id'
    },
    {
        value: 'Speaker id',
        name: 'speaker_id'
    },
    {
        value: 'Group id',
        name: 'group_id'
    },
    {
        value: 'Attendee to program',
        name: 'attendee_to_program'
    },
    {
        value: 'Workshop id',
        name: 'workshop_id'
    },
    {
        value: 'Enable check-in',
        name: 'enable_checkin'
    }
];

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/module/sub-registration", "ddirectory": "/event/module/documents" };

class ProgramWidget extends Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            query: '',
            data: [],
            displayElement: false,
            editElement: false,
            editElementIndex: undefined,
            toggleList: false,
            importCSVcontainer: false,

            //pagination
            limit: 10,
            total: '',

            //errors & loading
            preLoader: true,
            downloadLoader: false,

            typing: false,
            typingTimeout: 0,

            message: false,
            success: true,

            request_data: {}
        }

        document.body.addEventListener('click', this.handleDocument.bind(this));
    }

    handleDocument = e => {
        var query = document.querySelector('.btn_addmore');
        if (query !== null && query.classList !== null && query.classList.contains('active') && e.target.classList !== null && !e.target.classList.contains('btn_addmore')) {
          query.classList.remove('active');
        }
    }

    componentDidMount() {
        this._isMounted = true;
        this.listing();

        //set next previous
        if (this.props.event.modules !== undefined && this.props.event.modules.length > 0) {
            let modules = this.props.event.modules.filter(function (module, i) {
                return ((!in_array(module.alias, ["polls"]) && Number(module.status) === 1) || (in_array(module.alias, ["subregistration"])));
            });

            let index = modules.findIndex(function (module, i) {
                return module.alias === "agendas";
            });

            this.setState({
                next: (modules[index + 1] !== undefined && module_routes[modules[index + 1]['alias']] !== undefined  ? module_routes[modules[index + 1]['alias']] : "/event/manage/surveys"),
                prev: (modules[index - 1] !== undefined && module_routes[modules[index - 1]['alias']] !== undefined  ? module_routes[modules[index - 1]['alias']] : (Number(this.props.event.is_registration) === 1 ? "/event/module/eventsite-module-order" : (Number(this.props.event.is_app) === 1 ? "/event/module/event-module-order" : "/event_site/billing-module/manage-orders")))
            });

        }
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    handlePageChange = (activePage) => {
        this.listing(activePage);
    }

    listing = (activePage = 1, loader = false, type = "save", request_data = {}) => {
        this.setState({ preLoader: (!loader ? true : false) });
        ProgramService.listing(activePage, this.state).then(
            response => {
                if (response.success) {
                    if (this._isMounted) {
                        this.setState({
                            data: response.data.data,
                            activePage: response.data.current_page,
                            total: response.data.total,
                            displayElement: (type === "save-new" ? true : false),
                            editElement: false,
                            preLoader: false,
                            request_data: request_data
                        });
                    }
                }
            },
            error => { });
    }

    onFieldChange(event) {
        const self = this;
        if (self.state.typingTimeout) {
            clearTimeout(self.state.typingTimeout);
        }
        self.setState({
            query: event.target.value,
            typing: false,
            typingTimeout: setTimeout(function () {
                self.listing(1)
            }, 1000)
        });
    }

    handleEditElement = (index) => {
        this.setState({
            editElement: true,
            editElementIndex: index,
            displayElement: false,
        });
    }

    handleDeleteElement = (index, id) => {
        confirmAlert({
            customUI: ({ onClose }) => {
                return (
                    <Translation>
                        {
                            t =>
                                <div className='app-main-popup'>
                                    <div className="app-header">
                                        <h4>{t('G_DELETE')}</h4>
                                    </div>
                                    <div className="app-body">
                                        <p>{t('EE_ON_DELETE_ALERT_MSG')}</p>
                                    </div>
                                    <div className="app-footer">
                                        <button className="btn btn-cancel" onClick={onClose}>{t('G_CANCEL')}</button>
                                        <button className="btn btn-success"
                                            onClick={() => {
                                                onClose();
                                                ProgramService.destroy(id).then(
                                                    response => {
                                                        if (response.success) {
                                                            this.listing(1, false);
                                                        } else {
                                                            this.setState({
                                                                'message': response.message,
                                                                'success': false
                                                            });
                                                        }
                                                    }, error => {

                                                    }
                                                );
                                            }}
                                        >
                                            {t('G_DELETE')}
                                        </button>
                                    </div>
                                </div>
                        }
                    </Translation>
                );
            }
        });
    }

    handleCancel = () => {
        this.setState({
            editElement: false,
            editElementIndex: undefined,
            displayElement: false
        });
    }

    handleAddElement = () => {
        this.setState({
            displayElement: true,
            request_data: {}
        });
    }

    importCSVFile = () => {
        this.setState({
            importCSVcontainer: true,
        });
    }

    handleClose = () => {
        this.setState({
            importCSVcontainer: false,
        });
        this.listing(1, false);
    }

    downloadPDF = () => {
        this.setState({ downloadLoader: true });
        service.download(`${process.env.REACT_APP_URL}/program/download-pdf`)
            .then(response => {
                response.blob().then(blob => {
                    let url = window.URL.createObjectURL(blob);
                    let a = document.createElement('a');
                    a.href = url;
                    a.download = 'export.pdf';
                    a.click();
                    this.setState({
                        downloadLoader: false
                    });
                });
            });
    }

    handleDropdown = e => {
        e.preventDefault();
        if (e.target.classList.contains('active')) {
            e.target.classList.remove('active');
        } else {

            var query = document.querySelectorAll('.btn_addmore');
            for (var i = 0; i < query.length; ++i) {
                query[i].classList.remove('active');
            }
            e.target.classList.add('active');
        }
    }

    render() {

        let module = this.props.event.modules.filter(function (module, i) {
            return in_array(module.alias, ["agendas"]);
        });

        const Records = ({ data }) => {
            return (
                <Translation>
                {
                    t =>
                    data.map((data, key) => {
                        console.log(data)
                        return (
                            <div style={{ paddingTop: 0 }} className="row d-flex" key={key}>
                                {data.heading_date && (
                                    <h4 style={{ width: '100%', marginTop: '20px' }} className="program-date-heading">{data.heading_date}</h4>
                                )}
                                <div className="col-10">
                                    <div className="row">
                                        <div className="col-3">
                                            <div className="date-box-item">
                                                {data.start_time && <p><ReactSVG wrapper="span" className="icons" src={require('img/ico-clock.svg')} />{moment(data.start_time, "HH:mm:ss", true).format('HH:mm') + ' - ' + moment(data.end_time, "HH:mm:ss", true).format('HH:mm')}</p>}
                                            </div>
                                        </div>
                                        <div className="col-6">
                                            <div className="data-desciption">
                                                <h5>{data.topic}</h5>
                                                {data.description && <p style={{ maxWidth: '100%' }} dangerouslySetInnerHTML={{ __html: data.description }}></p>}
                                            </div>
                                        </div>
                                        <div className="col-3 text-center">
                                            <div className="date-box-item">
                                                <p>{(data.location && data.location !== "undefined") ? data.location : ''}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-2">
                                    <ul className="panel-actions">
                                        <li><span onClick={() => this.handleEditElement(key)}><i className="icons"><Img src={require("img/ico-edit.svg")} /></i></span></li>
                                        <li><span onClick={() => this.handleDeleteElement(key, data.id)}><i className="icons"><Img src={require("img/ico-delete.svg")} /></i></span></li>
                                        <li>
                                        <div style={{ verticalAlign: 'unset' }} className="wrapp_add_button">
                                            <span 
                                            className="btn_addmore" onClick={this.handleDropdown.bind(this)}><i className="icons"><Img
                                                src={require("img/ico-dots.svg")} /></i></span>
                                            <div className="drop_down_panel">
                                            <Link className="btn" to={`/event/module/program/assign-speakers/${data.id}`}>{t('PROGRAM_ASSIGN_SPEAKERS')}</Link>
                                            </div>
                                        </div>
                                        </li>
                                    </ul>
                                </div>
                                {this.state.editElement && !this.state.displayElement && this.state.editElementIndex === key ? <FormWidget data={this.listing} editdata={data} editdataindex={key} datacancel={this.handleCancel} editElement={this.state.editElement} /> : ''}
                            </div>
                        )
                    })
                }
                </Translation>
            )
        }
        return (
            <Translation>
                {
                    t =>

                        <div>
                            {this.state.importCSVcontainer !== false ? (
                                <ImportCSV
                                    apiUrl={`${process.env.REACT_APP_URL}/general/import/program`}
                                    downloadFile='/samples/program_template.csv'
                                    onClick={this.handleClose}
                                    element={labelArray}
                                    validate={['topic', 'date', 'start_time', 'end_time']}
                                    compName={t('PROGRAM_NAME')}
                                />
                            ) : (
                                    <div className="wrapper-content third-step">
                                        {this.state.preLoader &&
                                            <Loader />
                                        }
                                        {this.state.downloadLoader &&
                                            <Loader fixed="true" />
                                        }
                                        {!this.state.preLoader && (
                                            <React.Fragment>
                                                {this.state.message &&
                                                    <AlertMessage
                                                        className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                                                        title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                                                        content={this.state.message}
                                                        icon={this.state.success ? "check" : "info"}
                                                    />
                                                }
                                                <header style={{ margin: 0 }} className="new-header clearfix">
                                                    <div className="row">
                                                        <div className="col-12">
                                                            <h1 className="section-title float-left">{(module[0]['value'] !== undefined ? module[0]['value'] : t('PROGRAM_NAME'))}</h1>
                                                            <div className="new-right-header new-panel-buttons float-right">
                                                                <button onClick={() => this.downloadPDF()} className="btn_addNew">
                                                                    <Img width="20px" src={require('img/printer.png')} />
                                                                </button>
                                                                <button onClick={this.handleAddElement} className="btn_addNew">
                                                                    <Img width="20px" src={require('img/ico-plus-lg.svg')} />
                                                                </button>
                                                                <button onClick={this.importCSVFile} className="btn_csvImport">
                                                                    {/* {t('G_IMPORT_CSV')} */}
                                                                    <Img width="30px" src={require('img/ico-csvimport-lg.svg')} />
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div className="col-6">
                                                            <p>{t('PROGRAM_SUB_HEADING')}</p>
                                                        </div>
                                                    </div>
                                                </header>
                                                <div className="attendee-management-section">
                                                    {this.state.displayElement ? (
                                                        <div style={{ marginBottom: '15px' }}>
                                                            <FormWidget data={this.listing} datacancel={this.handleCancel} _location={this.state.request_data.location} date={this.state.request_data.date} end_time={this.state.request_data.end_time} />
                                                        </div>
                                                    ) : ''}
                                                    <div style={{ margin: 0 }} className="new-header">
                                                        <div className="d-flex">
                                                            <input value={this.state.query} name="query" type="text"
                                                                placeholder={t('G_SEARCH')} onChange={this.onFieldChange.bind(this)}
                                                            />
                                                        </div>
                                                    </div>
                                                    {this.state.data.length > 0 ? (
                                                        <div className="hotel-management-records program-widget-records">
                                                            <Records data={this.state.data} />
                                                        </div>
                                                    ) : ''}
                                                    {this.state.total > this.state.limit ? (
                                                        <nav className="page-navigation" aria-label="navigation">
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
                                                    ) : ''}
                                                </div>
                                                <div className="bottom-component-panel clearfix">
                                                    <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                                                        <i className='material-icons'>remove_red_eye</i>
                                                        {t('G_PREVIEW')}
                                                    </NavLink>
                                                    {this.state.prev !== undefined && (
                                                        <NavLink className="btn btn-prev-step" to={this.state.prev}><span className="material-icons">
                                                            keyboard_backspace</span></NavLink>
                                                    )}
                                                    {this.state.next !== undefined && (
                                                        <NavLink className="btn btn-next-step" to={this.state.next}>{t('G_NEXT')}</NavLink>
                                                    )}
                                                </div>
                                            </React.Fragment>
                                        )}
                                    </div>
                                )}
                        </div>
                }
            </Translation>
        )
    }
}

function mapStateToProps(state) {
    const { event, update } = state;
    return {
        event, update
    };
}

export default connect(mapStateToProps)(ProgramWidget);