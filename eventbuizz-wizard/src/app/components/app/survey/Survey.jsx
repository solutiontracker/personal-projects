import React, { Component } from 'react';
import { NavLink } from 'react-router-dom';
import Img from 'react-image';
import FormWidget from '@/app/survey/forms/FormWidget';
import { SurveyService } from "services/survey/survey-service";
import Pagination from "react-js-pagination";
import Loader from '@/app/forms/Loader';
import { OverlayTrigger, Tooltip } from "react-bootstrap";
import { confirmAlert } from 'react-confirm-alert'; // Import
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { Translation } from "react-i18next";
import { connect } from 'react-redux';
import { service } from 'services/service';
import { ReactSVG } from 'react-svg';

const in_array = require("in_array");

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/module/sub-registration", "ddirectory": "/event/module/documents" };

class SurveyWidget extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      data: [],
      displayElement: false,
      editElement: false,
      editElementIndex: undefined,

      //pagination
      limit: 5,
      total: '',

      //errors & loading
      preLoader: true,

      prev: (Number(this.props.event.is_registration) === 1 ? "/event/registration/tos" : "/event_site/billing-module/manage-orders")
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

      this.setState({
        prev: (modules[modules.length - 1] !== undefined && module_routes[modules[modules.length - 1]['alias']] !== undefined ? module_routes[modules[modules.length - 1]['alias']] : (Number(this.props.event.is_registration) === 1 ? "/event/module/eventsite-module-order" : (Number(this.props.event.is_app) === 1 ? "/event/module/event-module-order" : "/event_site/billing-module/manage-orders"))),
      });

    }
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  handlePageChange = (activePage) => {
    this.listing(activePage);
  }

  listing = (activePage = 1, loader = false) => {
    this.setState({ preLoader: (!loader ? true : false) });
    SurveyService.listing(this.state, 'survey', activePage).then(
      response => {
        if (response.success) {
          if (this._isMounted) {
            this.setState({
              data: response.data.data,
              activePage: response.data.current_page,
              total: response.data.total,
              displayElement: false,
              editElement: false,
              preLoader: false
            });
          }
        }
      },
      error => {

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
          <Translation >
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
                        SurveyService.destroy(this.state, id, 'survey').then(
                          response => {
                            if (response.success) {
                              this.listing(1, true);
                            }
                          }, error => {

                          }
                        );
                      }}
                    >{t('G_DELETE')}
                    </button>
                  </div>
                </div>
            }
          </Translation>
        );
      }
    });
  }


  updateStatus = (id, value, index) => {
    let data = this.state.data;
    data[index].status = value;
    service.put(`${process.env.REACT_APP_URL}/survey/update-status/${id}`, { value: value })
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              this.setState({ data: data });
            }
          }
        },
        error => { }
      );
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
    });
  }

  openSurvey = (survey_id) => e => {
    this.props.history.push(`/event/manage/survey/questions/${survey_id}`);
  }


  openLeaderBoard = (survey_id) => e => {
    this.props.history.push(`/event/manage/survey/leaderboard/${survey_id}`);
  }
  openSurveyGroups = (survey_id) => e => {
    this.props.history.push(`/event/manage/survey/groups/${survey_id}`);
  }


  clearSurveyResults = (survey_id) => e => {
    this.setState({ preLoader: true });
    service.get(`${process.env.REACT_APP_URL}/survey/clear-results/${survey_id}`)
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              this.listing()

            }
          }
        },
        error => { }
      );
  }

  export = (survey_id) => e => {
    service.download(`${process.env.REACT_APP_URL}/survey/export_single_result/${survey_id}`)
      .then(response => {
        response.blob().then(blob => {
          if (window.navigator && window.navigator.msSaveOrOpenBlob) { // for IE
            var csvData = new Blob([blob], { type: 'text/csv' });
            window.navigator.msSaveOrOpenBlob(csvData, "export.csv");
          } else {
            let url = window.URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = 'export.csv';
            a.click();
          }
        });
      });
  }
  exportByPoints = () => e => {
    const dateTime = Date.now();
    const timestamp = Math.floor(dateTime / 1000);
    service.download(`${process.env.REACT_APP_URL}/survey/export_by_points`)
      .then(response => {
        response.blob().then(blob => {
          if (window.navigator && window.navigator.msSaveOrOpenBlob) { // for IE
            var csvData = new Blob([blob], { type: 'text/csv' });
           
            window.navigator.msSaveOrOpenBlob(csvData, "survey_results_points_"+timestamp+".csv");
          } else {
            let url = window.URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = "survey_results_points_"+timestamp+".csv";
            a.click();
          }
        });
      });
  }

  render() {

    let module = this.props.event.modules.filter(function (module, i) {
      return in_array(module.alias, ["polls"]);
    });

    const Records = ({ data }) => {
      return (
        data.map((data, key) => {
          return (
            <Translation key={key}>
              {
                t =>
                  <React.Fragment key={key}>
                    <div className="row d-flex">
                      <div className="col-9" onClick={this.openSurvey(data.id)}>
                        <div className="row">
                          <div className="col-12 d-flex">
                            <h5>{data.info.name}</h5>
                            <p className="markButton">{data.results}/{data.total_attendees}</p>
                          </div>
                        </div>
                      </div>
                      <div className="col-3">
                        <ul className="panel-actions">
                          <li>
                            <OverlayTrigger overlay={<Tooltip>{data.status === 1 ? t('G_HIDE') : t('G_SHOW')}</Tooltip>}>
                              <span className={(data.status === 1 ? "active" : "deactive")} onClick={() => this.updateStatus(data.id, (data.status === 1 ? 0 : 1), key)}><i className="icons"><Img src={require(`img/ico-feathereye${data.status !== 1 ? '-alt' : ''}.svg`)} /></i></span>
                            </OverlayTrigger></li>

                          <li>
                            <OverlayTrigger overlay={<Tooltip>{t('T_VIEW_QUESTION')}</Tooltip>}>
                              <span onClick={this.openSurvey(data.id)}><i className="icons"><Img src={require(data.question.length > 0 ? "img/ico-detail.svg" : "img/ico-plus-lg.svg")} /></i></span>
                            </OverlayTrigger>
                          </li>

                          <li>
                            <OverlayTrigger overlay={<Tooltip>{t('EL_EDIT')}</Tooltip>}>
                              <span onClick={() => this.handleEditElement(key)}><i className="icons"><Img src={require("img/ico-edit.svg")} /></i></span>
                            </OverlayTrigger>
                          </li>

                          <li>
                            <OverlayTrigger overlay={<Tooltip>{t('ES_RESULT')}</Tooltip>}>
                              <span><i className="icons" onClick={this.export(data.id)}><Img src={require("img/ico-download.svg")} /></i></span>
                            </OverlayTrigger>
                          </li>

                          <li>
                            <OverlayTrigger overlay={<Tooltip>{t('EL_DELETE')}</Tooltip>}><span onClick={() => this.handleDeleteElement(key, data.id)}><i className="icons"><Img src={require("img/ico-delete.svg")} /></i></span>
                            </OverlayTrigger>
                          </li>

                          <li>
                            <div className="parctical-button-panel button-panel-list">
                              <div className="dropdown">
                                <span onClick={this.handleDropdown.bind(this)} className="btn btn_dots">
                                  <ReactSVG style={{ pointerEvents: 'none' }} wrapper="span" className='icons' alt="" src={require("img/ico-dots-gray.svg")} />
                                </span>
                                <div className="dropdown-menu">
                                <a href={`/event/manage/survey/leaderboard/${this.props.event.id}/${data.id}`} target="blank" className="dropdown-item">
                                    {t('ES_LEADERBOARD')}
                                  </a>
                                {/* <button onClick={this.openLeaderBoard(data.id)} className="dropdown-item">
                                    {t('ES_LEADERBOARD')}
                                  </button> */}
                                  <button onClick={this.openSurveyGroups(data.id)} className="dropdown-item">
                                    {t('ES_ASSIGN_GROUP')}
                                  </button>
                                  <button onClick={this.clearSurveyResults(data.id)} className="dropdown-item">
                                    {t('ES_CLEAR_RESULT')}
                                  </button>
                                </div>
                              </div>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                    {this.state.editElement && this.state.editElementIndex === key ?
                      <FormWidget listing={this.listing} editdata={data} editdataindex={key}
                        datacancel={this.handleCancel} editElement={this.state.editElement} /> : ''}
                  </React.Fragment>
              }
            </Translation>
          )
        })
      )
    }

    return (
      <Translation>
        {
          t =>
            <div>
              <div className="wrapper-content third-step wrapper-survey-main">
                {this.state.preLoader &&
                  <Loader />
                }
                {!this.state.preLoader && (
                  <React.Fragment>
                    <header style={{ marginBottom: 0 }} className="new-header clearfix">
                      <div className="row">
                        <div className="col-6">
                          <h1 className="section-title">{(module[0]['value'] !== undefined ? module[0]['value'] : t('ES_NAME'))}</h1>
                          <p>{t('ES_TOP_HEADLINE')} </p>
                        </div>
                        <div className="col-6">
                          <div className="new-right-header new-panel-buttons float-right">
                            <button onClick={this.exportByPoints()} className="btn_addNew">
                              <Img src={require("img/ico-download.svg")} alt="" />
                            </button>
                          </div>
                          <div className="new-right-header new-panel-buttons float-right">
                            <button onClick={this.handleAddElement} className="btn_addNew">
                              <Img src={require('img/ico-plus-lg.svg')} alt="" />
                            </button>
                          </div>
                        </div>
                      </div>
                    </header>
                    {this.state.displayElement ? (
                      <div style={{ marginBottom: '15px' }}>
                        <FormWidget listing={this.listing} datacancel={this.handleCancel} />
                      </div>
                    ) : ''}
                    <div className="attendee-management-section">
                      {this.state.data.length > 0 ? (
                        <div className="hotel-management-records evolution-survey">
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
                      {this.state.prev && (
                        <NavLink className="btn btn-prev-step" to={this.state.prev}><span className="material-icons">
                          keyboard_backspace</span></NavLink>
                      )}
                      <NavLink className="btn btn-next-step" to={`/event/preview`}>{t('G_NEXT')}</NavLink>
                    </div>
                  </React.Fragment>
                )}
              </div>
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

export default connect(mapStateToProps)(SurveyWidget);