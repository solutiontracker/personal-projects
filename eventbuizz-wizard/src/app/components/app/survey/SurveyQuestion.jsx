import * as React from "react";
import { NavLink } from 'react-router-dom';
import Img from 'react-image';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import { withRouter } from 'react-router-dom';
import { Link } from 'react-router-dom';
import QuestionFormWidget from '@/app/survey/forms/QuestionFormWidget';
import { SurveyService } from 'services/survey/survey-service';
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { confirmAlert } from 'react-confirm-alert'; // Import
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { Translation } from "react-i18next";
import { connect } from 'react-redux';
import { ReactSVG } from 'react-svg';

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
  console.log(data);
  if (data.question_type === "single" || data.question_type === "multiple" || data.question_type === "dropdown") {

    return (
      <Translation>
        {
          t =>
            <div className="listdata">
              <h4>{data.question} <p className="markButton">{data.q_responses}</p></h4>
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
                        <p className="markButton">{options.a_responses}</p>
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
                  <span>Question Dropdown</span>
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
              <h4>{data.question}<p className="markButton">{data.q_responses}</p></h4>
              {data.question_type === "matrix" && <Matrix column={data.matrix} row={data.answer} />}
              {data.question_type === "open" && <input type="text" className="textfield" placeholder={t('ES_YOUR_ANSWER')} disabled />}
              {data.question_type === "number" && <input type="text" className="textfield" placeholder={t('ES_YOUR_VALUE')} disabled />}
              {data.question_type === "date" && <input type="text" className="textfield date" placeholder={t('ES_YOUR_MONTH_AND_DAY')} disabled />}
              {data.question_type === "date_time" && <input type="text" className="textfield datetime" placeholder={t('ES_YOUR_MONTH_AND_DAY_WITH_TIME')} disabled />}
            </div>
        }
      </Translation>
    )
  }
}

class SurveyQuestion extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      data: [],
      displayElement: false,
      editData: false,
      editDataIndex: undefined,
      toggleList: false,
      survey_id: this.props.match.params.id,
      survey_name: '',

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
    this.survey();
    this.listing();
    this._isMounted = true;
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  survey = () => {
    let id = this.props.match.params.id;
    if (id !== undefined) {
      service.get(`${process.env.REACT_APP_URL}/survey/fetch/${id}`)
        .then(
          response => {
            if (response.success) {
              if (response.data.result) {
                if(this._isMounted){
                  this.setState({
                    survey_name: (response.data.result.info.name !== undefined ? response.data.result.info.name : '')
                  });
                }
              }
            }
          },
          error => { }
        );
    }
  }

  listing = (loader = false) => {
    this.setState({ preLoader: (!loader ? true : false) });
    SurveyService.listing(this.state, 'question')
      .then(
        response => {
          if (response.success) {
            if (response.data) {
              if (this._isMounted) {
                this.setState({
                  data: response.data.data,
                  editDataIndex: undefined,
                  editData: false,
                  preLoader: false
                });
              }
            }
          }
        },
        error => { }
      );
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
                        SurveyService.destroy(this.state, id, 'question')
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
    SurveyService.updateQuestionOrder(items)
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
      SurveyService.update(question, 'question', this.state.editData.id, this.state.survey_id)
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
      SurveyService.create(question, 'question', this.state.survey_id)
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


  updateStatus = (id, value, index) => {
    let data = this.state.data;
    data[index].status = value;
    service.put(`${process.env.REACT_APP_URL}/survey/question/update-status/` + id, { value: value })
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

  openFullScreenProjector = (question_id) => e => {
    window.open(`${process.env.REACT_APP_BASE_URL}/event/manage/survey/question/full-screen-projector/${this.props.event.id}/${question_id}`)
  }


  clearQuestionResults = (question_id) => e => {
    this.setState({ preLoader: true });
    service.get(`${process.env.REACT_APP_URL}/survey/question/clear-results/${question_id}`)
      .then(
        response => {
          if (response.success) {
            this.listing();
          }
        },
        error => { }
      );
  }

  render() {
    return (
      <Translation>
        {
          t =>
            <div className="wrapper-content third-step wrapper-survey-main">
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
                      icon={this.state.success ? 'check' : 'info'}
                    />
                  }
                  <div style={{ height: '100%' }}>
                    <header style={{ marginBottom: 0 }} className="new-header clearfix">
                      <div className="row">
                        <div className="col-6">
                          <h1 className="section-title"><Link to="/event/manage/surveys"><i
                            className="material-icons">arrow_back_ios</i></Link>{this.state.survey_name}</h1>
                        </div>
                        <div className="col-6">
                          <div className="new-right-header new-panel-buttons float-right">
                            <button onClick={this.addQuestionElement} className="btn_addNew">
                              <Img src={require('img/ico-plus-lg.svg')} alt="" />
                            </button>
                          </div>
                        </div>
                      </div>
                    </header>
                    <p style={{ marginLeft:"20px" }}>
                    {t("SURVEY_DETAIL_DESCRIPTION")}
                    </p>
                    {this.state.displayElement ? (
                      <div style={{ marginBottom: '15px' }}>
                        <QuestionFormWidget
                          questionSave={this.questionSave}
                          errors={this.state.errors}
                          cancelQuestionElement={this.cancelQuestionElement}
                          isLoader={this.state.isLoader}
                        />
                      </div>
                    ) : ''}
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
                                            <li><span className={(list.status === 1 ? "active" : "deactive")} onClick={() => this.updateStatus(list.id, (list.status === 1 ? 0 : 1), index)}><i className="icons"><Img src={require(`img/ico-feathereye${list.status !== 1 ? '-alt' : ''}.svg`)} /></i></span></li>
                                            <li><span onClick={this.editQuestionElement(index)}><i className="icons"><Img src={require("img/ico-edit.svg")} /></i></span></li>
                                            <li><span onClick={this.deleteQuestion(list.id)}><i className="icons"><Img src={require("img/ico-delete.svg")} /></i></span></li>
                                            <li>
                                              <div className="parctical-button-panel button-panel-list">
                                                <div className="dropdown">
                                                  <span onClick={this.handleDropdown.bind(this)} className="btn btn_dots">
                                                    <ReactSVG style={{ pointerEvents: 'none' }} wrapper="span" className='icons' alt="" src={require("img/ico-dots-gray.svg")} />
                                                  </span>
                                                  <div className="dropdown-menu">
                                                    {(list.question_type === "single" || list.question_type === "multiple") && <button className="dropdown-item" onClick={this.openFullScreenProjector(list.id)}>
                                                      {t('ES_FULL_SCREEN_VIEW')}
                                                    </button>
                                                    }
                                                    <button className="dropdown-item" onClick={this.clearQuestionResults(list.id)}>
                                                      {t('ES_CLEAR_RESULT')}
                                                    </button>
                                                  </div>
                                                </div>
                                              </div>
                                            </li>
                                          </ul>
                                        )}
                                        {this.state.editDataIndex === index && (
                                          <QuestionFormWidget
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
                    <NavLink className="btn btn-prev-step" to={`/event/manage/surveys`}><span className="material-icons">
                      keyboard_backspace</span></NavLink>
                    {this.props.event.default_template_id && (
                      <NavLink className="btn btn-next-step" to={`/event/template/edit/${this.props.event.default_template_id}`}>{t('G_NEXT')}</NavLink>
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

export default connect(mapStateToProps)(withRouter(SurveyQuestion));
