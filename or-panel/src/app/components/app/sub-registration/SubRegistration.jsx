import * as React from "react";
import { NavLink } from 'react-router-dom';
import Img from 'react-image';
import { ReactSVG } from 'react-svg';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import FormWidget from '@/app/sub-registration/forms/FormWidget';
import { SubRegistrationService } from 'services/sub-registration/sub-registration-service';
import Loader from '@/app/forms/Loader';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { confirmAlert } from 'react-confirm-alert'; // Import
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { Translation } from "react-i18next";
import { connect } from 'react-redux';
import { service } from 'services/service';

const in_array = require("in_array");

// a little function to help us with reordering the result
const reorder = (list, startIndex, endIndex) => {
  const result = Array.from(list);
  const [removed] = result.splice(startIndex, 1);
  result.splice(endIndex, 0, removed);
  return result;
};

const Matrix = ({column,row}) => {
  console.log(column);
  console.log(row);
  return (
    <div className="op-matrix-table">
      <header className="d-flex">
        <div className="box title"></div>
        {column && column.map((title,k) => 
          <div key={k} className="box text-center">
            <p title={title.name}>{title.name}</p>
          </div>
         )}
      </header>
      {row && row.map((val,k) =>
        <div className="d-flex">
          <div title={val.value} className="box title"><p>{val.value}</p></div>
            {column && column.map((title,k) => 
              <div key={k} className="box text-center">
                <i className="material-icons">radio_button_unchecked</i>
              </div>
            )}
        </div>
      )}
    </div>
  )
}
const Datalist = ({ data }) => {
  if (data.question_type === "single" || data.question_type === "multiple" || data.question_type === "dropdown") {

    return (
      <Translation>
        {
          t =>
            <div className="listdata">
              <h4>{data.question}</h4>
              {data.question_type === "single" || data.question_type === "multiple" ? (
                <ul className="list-listdata">
                  {data.answer && data.answer.map((options, k) => {
                    return (
                      <li key={k}>
                        <i className='material-icons'>
                          {data.question_type === "single" && 'radio_button_unchecked'}
                          {data.question_type === "multiple" && 'check_box_outline_blank'}
                        </i>
                        {options.value}
                      </li>
                    )
                  })}
                </ul>
              ) : ''}
              {data.question_type === "dropdown" && (
                <label className="label-select">
                  <select name="">
                    {data.answer && data.answer.map((options, k) => {
                      return <option key={k} value="">{options.value}</option>
                    })}
                  </select>
                  <span>{t('SR_QUESTION_DROPDOWN')}</span>
                </label>
              )}

            </div>
        }
      </Translation>
    )
  } else {
    return (
      <Translation>
        {
          t =>
            <div className="listdata">
              <h4>{data.question}</h4>
              {data.question_type === "matrix" && <Matrix column={data.matrix} row={data.answer} />}
              {data.question_type === "open" &&
                <input type="text" className="textfield" placeholder={t('SR_YOUR_ANSWER')} disabled />}
              {data.question_type === "number" &&
                <input type="text" className="textfield" placeholder={t('SR_YOUR_VALUE')} disabled />}
              {data.question_type === "date" &&
                <input type="text" className="textfield date" placeholder={t('SR_YOUR_MONTH_AND_DAY')} disabled />}
              {data.question_type === "date_time" &&
                <input type="text" className="textfield datetime" placeholder={t('SR_YOUR_MONTH_AND_DAY_WITH_TIME')}
                  disabled />}
            </div>
        }
      </Translation>
    )
  }
}

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/module/sub-registration", "ddirectory": "/event/module/documents" };

class SubRegistration extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      data: [],
      displayElement: false,
      editData: false,
      editDataIndex: undefined,
      toggleList: false,
      sub_registration_id: undefined,
      listing: false,
      moduleStatus: false,

      //errors & loading
      preLoader: true,
      message: false,
      success: true,
      errors: {},
      isLoader: false
    }

    this.onDragEnd = this.onDragEnd.bind(this);
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
        return module.alias === "subregistration";
      });

      this.setState({
        next: (modules[index + 1] !== undefined && module_routes[modules[index + 1]['alias']] !== undefined ? module_routes[modules[index + 1]['alias']] : "/event/manage/surveys"),
        prev: (modules[index - 1] !== undefined && module_routes[modules[index - 1]['alias']] !== undefined ? module_routes[modules[index - 1]['alias']] : (Number(this.props.event.is_registration) === 1 ? "/event/module/eventsite-module-order" : (Number(this.props.event.is_app) === 1 ? "/event/module/event-module-order" : "/event_site/billing-module/manage-orders")))
      });

    }
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  listing = (loader = false) => {
    this.setState({ preLoader: (!loader ? true : false) });
    SubRegistrationService.listing(this.state)
      .then(
        response => {
          if (response.success) {
            if (response.data) {
              if (this._isMounted) {
                this.setState({
                  data: response.data.data,
                  sub_registration_id: response.data.sub_registration_id,
                  editDataIndex: undefined,
                  editData: false,
                  preLoader: false,
                  listing: response.data.settings && Number(response.data.settings.listing) === 1 ? true : false,
                  moduleStatus: response.data.module_setting && Number(response.data.module_setting.status) === 1 ? true : false,
                });
              }
            }
          }
        },
        error => { }
      );
  }

  updateSubregistrationSettings = () => {
    const request_data = {};
    request_data.listing = this.state.listing === true ? 0 : 1;
    service.put(`${process.env.REACT_APP_URL}/sub-registration/settings`, request_data)
      .then(
        response => {
          if (response.success) { }
        },
        error => { }
      );
  }

  updateListing = () => {
    this.setState({
      listing: this.state.listing === true ? false : true
    });
    this.updateSubregistrationSettings();
  }

  updateModule = () => {
    this.setState({
      moduleStatus: this.state.moduleStatus === true ? false : true
    }, () => {
      service.put(`${process.env.REACT_APP_URL}/sub-registration/update-module-setting`, this.state)
        .then(
          response => {
            if (response.success) { }
          },
          error => { }
        );
    });
  }

  deleteQuestion = id => e => {
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
                        SubRegistrationService.destroy(this.state, id, 'question')
                          .then(
                            response => {
                              if (response.success) {
                                this.setState({ 'message': response.message });
                                this.listing();
                              }
                            },
                            error => {
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

    e.preventDefault();
  }

  updateQuestionOrder = (items) => {
    SubRegistrationService.updateQuestionOrder(items)
      .then(
        response => {
          if (response.success) {
            this.listing(false);
          }
        },
        error => { }
      );
  }

  onDragEnd(result) {
    if (!result.destination) {
      return;
    }
    const sourceIndex = result.source.index;
    const destIndex = result.destination.index;
    if (result.type === "droppableItem") {
      const data = reorder(this.state.data, sourceIndex, destIndex);
      this.setState({ data });
      this.updateQuestionOrder(data);
    }
  }

  questionSave = (question, index, type) => {
    this.setState({ isLoader: type });
    if (this.state.editData) {
      SubRegistrationService.update(this.state.editData.id, question, this.state.sub_registration_id)
        .then(
          response => {
            if (response.success) {
              this.setState({
                'message': response.message,
                'success': true,
                isLoader: false,
                errors: {},
                displayElement: (type === "save-new" ? true : false),
              });
              this.listing(true);
            } else {
              this.setState({
                'message': response.message,
                'success': false,
                'isLoader': false,
                'errors': response.errors
              });
            }
          },
          error => { }
        );
    } else {
      SubRegistrationService.create(question, this.state.sub_registration_id)
        .then(
          response => {
            if (response.success) {
              this.setState({
                'message': response.message,
                'success': true,
                isLoader: false,
                errors: {},
                displayElement: (type === "save-new" ? true : false),
              });
              this.listing(false);
            } else {
              this.setState({
                'message': response.message,
                'success': false,
                'isLoader': false,
                'errors': response.errors
              });
            }
          },
          error => { }
        );
    }
  }

  addQuestionElement = () => {
    this.setState({
      displayElement: true,
      editData: false,
      editDataIndex: undefined,
    });
  }

  cancelQuestionElement = () => {
    this.setState({
      editData: false,
      editDataIndex: undefined,
      displayElement: false
    });
  }

  editQuestionElement = id => e => {
    e.preventDefault();
    const data = [...this.state.data];
    this.setState({
      editDataIndex: id,
      displayElement: false,
      editData: data[id],
    })

  }

  render() {

    let module = this.props.event.modules.filter(function (module, i) {
      return in_array(module.alias, ["subregistration"]);
    });

    return (
      <Translation>
        {
          t =>
            <div className="wrapper-content third-step">
              {this.state.preLoader &&
                <Loader />
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
                  <div style={{ height: '100%' }}>
                    <header style={{ marginBottom: 0 }} className="new-header clearfix">
                      <div className="row d-flex">
                        <div className="col-6">
                          <h1 className="section-title">{(module[0]['value'] !== undefined ? module[0]['value'] : t('SR_CUSTOM_QUESTIONS'))}
                            <label className="custom-checkbox-toggle float-right"><input onClick={this.updateModule}
                              defaultChecked={this.state.moduleStatus}
                              type="checkbox" /><span></span></label></h1>
                          <p>{t('SR_SUB_HEADING')}</p>
                          <h4 style={{ marginBottom: '2px' }} className="tooltipHeading">
                            {t('SR_ENABLE')}
                            <label className="custom-checkbox-toggle float-right"><input onClick={this.updateListing}
                              defaultChecked={this.state.listing}
                              type="checkbox" /><span></span></label>
                            {/* <em className="app-tooltip"><i className="material-icons">info</i><div className="app-tooltipwrapper">{t('SR_ENABLE_TOOLTIP')}</div></em> */}
                          </h4>
                          <div className="button-header d-flex clearfix">
                            <p style={{ paddingRight: '10px' }}>{t('SR_ENABLE_NOTE')} </p>
                          </div>
                        </div>
                        <div className="col-6">
                          <div className="new-right-header new-panel-buttons float-right">
                            {!this.state.displayElement && !this.state.editData && (
                              <button onClick={this.addQuestionElement} className="btn_addNew">
                                <ReactSVG wrapper="span" className="icons" src={require('img/ico-plus-lg.svg')} />
                              </button>
                            )}
                          </div>
                        </div>
                      </div>
                    </header>
                    {this.state.displayElement && (
                      <FormWidget
                        questionSave={this.questionSave}
                        errors={this.state.errors}
                        cancelQuestionElement={this.cancelQuestionElement}
                        isLoader={this.state.isLoader}
                      />
                    )}
                    {this.state.data.length !== 0 && (
                      <div className="subregistration-widget">
                        <DragDropContext onDragEnd={this.onDragEnd}>
                          <Droppable droppableId="droppable" type="droppableItem">
                            {(provided, snapshot) => (
                              <div
                                ref={provided.innerRef}
                                className="add-question-wrapper"
                              >
                                {this.state.data.map((list, index) => (
                                  <Draggable key={index} draggableId={`item-${index}`} index={index}>
                                    {(provided, snapshot) => (
                                      <div
                                        className="datalist"
                                        ref={provided.innerRef}
                                        {...provided.draggableProps}
                                      >
                                        <span
                                          {...provided.dragHandleProps}
                                          className={!this.state.displayElement && !this.state.editData ? 'handle-drag' : 'd-none'}
                                        >
                                          <i className="material-icons">more_vert more_vert</i>
                                        </span>
                                        <Datalist
                                          data={list}
                                        />
                                        {!this.state.displayElement && !this.state.editData && (
                                          <ul className="panel-actions">
                                            <li><span onClick={this.editQuestionElement(index)}><i className="icons"><Img src={require("img/ico-edit.svg")} /></i></span></li>
                                            <li><span onClick={this.deleteQuestion(list.id)}><i className="icons"><Img src={require("img/ico-delete.svg")} /></i></span></li>
                                          </ul>
                                        )}
                                        {this.state.editDataIndex === index && (
                                          <FormWidget
                                            questionSave={this.questionSave}
                                            errors={this.state.errors}
                                            cancelQuestionElement={this.cancelQuestionElement}
                                            editIndex={this.state.editDataIndex}
                                            editData={this.state.editData}
                                            isLoader={this.state.isLoader}
                                          />
                                        )}
                                        {provided.placeholder}
                                      </div>
                                    )}
                                  </Draggable>
                                ))}
                                {provided.placeholder}
                              </div>
                            )}
                          </Droppable>
                        </DragDropContext>
                      </div>
                    )}
                  </div>
                  <div className="bottom-component-panel clearfix">
                    <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                      <i className='material-icons'>remove_red_eye</i>
                      {t('G_PREVIEW')}
                    </NavLink>
                    {window.location.pathname.includes('event/module') ? (
                      <React.Fragment>
                        {this.state.prev !== undefined && (
                          <NavLink className="btn btn-prev-step" to={this.state.prev}><span className="material-icons">
                            keyboard_backspace</span></NavLink>
                        )}
                        {this.state.next !== undefined && (
                          <NavLink className="btn btn-next-step" to={this.state.next}>{t('G_NEXT')}</NavLink>
                        )}
                      </React.Fragment>
                    ) : (
                        <React.Fragment>
                          <NavLink className="btn btn-prev-step" to={`/event/registration/company-detail-form`}><span className="material-icons">
                            keyboard_backspace</span></NavLink>
                          <NavLink className="btn btn-next-step" to={`/event/registration/manage/hotels`}>{t('G_NEXT')}</NavLink>
                        </React.Fragment>
                      )}
                  </div>
                </React.Fragment>
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

export default connect(mapStateToProps)(SubRegistration);
