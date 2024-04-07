import React, { Component } from 'react';
import Img from 'react-image';
import { Translation } from "react-i18next";
import DropDown from '@/app/forms/DropDown';
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import { connect } from 'react-redux';

const in_array = require("in_array");

class SubRegistrationQuestions extends Component {

  constructor(props) {
    super(props);
    this.state = {
      questions: [],
      answers: [],
      question_id: '',
      question_type: '',
      event_total_submissions: '',
      sort_by: 'sort_order',
      order_by: 'ASC',

      //errors & loading
      preLoader: true,

      limit: 10,
    }

    this.onSorting = this.onSorting.bind(this);
  }

  componentDidMount() {
    this._isMounted = true;
    this.questions();
    document.body.addEventListener('click', this.removePopup.bind(this));
  }

  removePopup = e => {
    if (e.target.className !== 'btn active') {
      const items = document.querySelectorAll(".parctical-button-panel .btn");
      for (let i = 0; i < items.length; i++) {
        const element = items[i];
        element.classList.remove("active");
      }
    }
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  componentDidUpdate(prevProps, prevState) {
    if (prevState.question_id !== this.state.question_id || this.state.order_by !== prevState.order_by || this.state.sort_by !== prevState.sort_by || prevState.limit !== this.state.limit) {
      this.questions();
    }
  }

  questions = (loader = false) => {
    let questions = [];
    let question_id = '';
    let question_type = '';
    this.setState({ preLoader: (!loader ? true : false) });
    service.post(`${process.env.REACT_APP_URL}/sub-registration/questions`, this.state)
      .then(
        response => {
          if (response.success) {
            if (response.data) {
              if (this._isMounted) {
                if (!this.state.question_id) {
                  response.data.data.forEach(function (row, index, arr) {
                    if (index === 0) {
                      question_id = row.id;
                      question_type = row.question_type;
                    }
                    questions.push({ id: row.id, name: row.question });
                  });

                  this.setState({
                    question_id: question_id,
                    question_type: question_type,
                    questions: questions,
                    event_total_submissions: response.data.event_total_submissions,
                    preLoader: false
                  });
                } else {
                  this.setState({
                    question_type: response.data.question_type,
                    answers: response.data.data,
                    event_total_submissions: response.data.event_total_submissions,
                    preLoader: false,
                  });
                }
              }
            }
          }
        },
        error => { }
      );
  }

  handleDropdown = e => {
    e.stopPropagation();
    const items = document.querySelectorAll(".parctical-button-panel .btn");
    for (let i = 0; i < items.length; i++) {
      const element = items[i];
      if (element.classList === e.target.classList) {
        e.target.classList.toggle("active");
      } else {
        element.classList.remove("active");
      }
    }
  };

  handleChange = (input) => e => {
    this.setState({
      [input]: e.value
    })
  }

  onSorting(event) {
    this.setState({
      order_by: event.target.attributes.getNamedItem('data-order').value,
      sort_by: event.target.attributes.getNamedItem('data-sort').value,
    });
  }

  handleLimit = (limit) => e => {
    this.setState(prevState => ({
      limit: limit,
    }));
  };

  render() {
    this.getSelectedLabel = (item, id) => {
      if (item && item.length > 0 && id) {
        let obj = item.find(o => o.id.toString() === id.toString());
        return (obj ? obj.name : '');
      }
    }

    return (
      <Translation>
        {t => (
          <div className="data-popup-charts wrapper-import-file-wrapper">
            <div className="wrapper-import-file popup-subregistration">
              <div style={{ marginBottom: 0 }} className="header-popup-subregistration">
                <div className="row d-flex">
                  <div className="col-6 d-flex align-items-center">
                    <div className="heading-area-popup">
                      <h2>{t('DSB_SUB_REGISTRATION')}</h2>
                      <p>{this.props.event.name}</p>
                    </div>
                  </div>
                  <div className="col-6">
                    <div className="new-panel-buttons">
                      <button>
                        <Img src={require('img/ico-plus-lg.svg')} />
                      </button>
                      <button>
                        <Img src={require('img/ico-mail.svg')} />
                      </button>
                      <button>
                        <Img src={require('img/ico-excel.svg')} />
                      </button>
                      <button>
                        <Img src={require('img/ico-pdf.svg')} />
                      </button>
                    </div>
                  </div>
                </div>
                <div className="new-header">
                  <div className="row d-flex">
                    <div className="col-3">
                      <input name="query" type="text" placeholder="Search" value="" />
                    </div>
                    <div className="col-3">
                      <div className="right-top-header">
                        <label className="label-select-alt">
                          <DropDown
                            label={t("DSB_FILTER_BY")}
                            listitems={this.state.questions}
                            selected={this.state.question_id}
                            selectedlabel={this.getSelectedLabel(this.state.questions, this.state.question_id)}
                            onChange={this.handleChange('question_id')}
                            required={true}
                          />
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              {this.state.preLoader &&
                <Loader />
              }
              {!this.state.preLoader && (
                <React.Fragment>
                  <div className="wrapper-module">
                    <div className="top-section-wrapper row d-flex">
                      <div className="col-6">
                        {/* <div className="pnp-breadcrumb">
                          <ol className="breadcrumb">
                            <li onClick={this.props.close(false)} className="breadcrumb-item"><a href="#!">{t('DSB_HOME')}</a></li>
                          </ol>
                        </div> */}
                      </div>
                      <div className="col-6 d-flex align-items-center justify-content-end">
                        <p style={{ margin: '0 15px 0 0' }} className="text-right text-respondents">
                          {t('DSB_RESPONDENTS')} <a href="#!">{(this.state.event_total_submissions ? this.state.event_total_submissions : 0)}</a>.
                        </p>
                        <div className="panel-right-table d-flex justify-content-end">
                          <div className="parctical-button-panel">
                            <div className="dropdown">
                              <button
                                onClick={this.handleDropdown.bind(this)}
                                className="btn"
                                style={{ minWidth: '54px' }}
                              >
                                {this.state.limit}
                                <i className="material-icons">
                                  keyboard_arrow_down
                                                                  </i>
                              </button>
                              <div className="dropdown-menu">
                                {this.state.limit !== 10 && (
                                  <button className="dropdown-item" onClick={this.handleLimit(10)}>
                                    10
                                  </button>
                                )}
                                {this.state.limit !== 20 && (
                                  <button className="dropdown-item" onClick={this.handleLimit(20)}>
                                    20
                                  </button>
                                )}
                                {this.state.limit !== 50 && (
                                  <button className="dropdown-item" onClick={this.handleLimit(50)}>
                                    50
                                  </button>
                                )}
                                {this.state.limit !== 500 && (
                                  <button className="dropdown-item" onClick={this.handleLimit(500)}>
                                    500
                                  </button>
                                )}
                                {this.state.limit !== 1000 && (
                                  <button className="dropdown-item" onClick={this.handleLimit(1000)}>
                                    1000
                                  </button>
                                )}
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    {in_array(this.state.question_type, ["single", "multiple", "dropdown"]) && (
                      <React.Fragment>
                        {this.state.answers && this.state.answers.length > 0 && (
                          <div className="hotel-management-records attendee-records-template">
                            {}
                            <header className="header-records row d-flex">
                              <div className="col-6">
                                <strong>{t('DSB_ANSWER_OPTIONS')}</strong>
                                <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="value" onClick={this.onSorting} className="material-icons">
                                  {(this.state.order_by === "ASC" && this.state.sort_by === "value" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "value" ? "keyboard_arrow_up" : "unfold_more"))}
                                </i>
                              </div>
                              <div className="col-6">
                                <strong>{t('DSB_RESPONSES')}</strong>
                                <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="result_count" onClick={this.onSorting} className="material-icons">
                                  {(this.state.order_by === "ASC" && this.state.sort_by === "result_count" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "result_count" ? "keyboard_arrow_up" : "unfold_more"))}
                                </i>
                              </div>
                            </header>
                            {this.state.answers && this.state.answers.map((row, k) => {
                              return (
                                <React.Fragment key={row.id}>
                                  {row.answer && row.answer.map((option, k) => {
                                    return (
                                      <div onClick={this.props.openQuestionResults(row.id, option.id)} className="row d-flex" key={option.id}>
                                        <div className="col-6">
                                          <p>{option.value}</p>
                                        </div>
                                        <div className="col-6">
                                          <p>{option.result_count}</p>
                                        </div>
                                      </div>
                                    )
                                  })
                                  }
                                </React.Fragment>
                              )
                            })}
                          </div>
                        )}
                      </React.Fragment>
                    )}
                  </div>
                  <div className="bottom-component-panel bottom-panel-import">
                    <button onClick={this.props.close(false)} className="btn btn-import">Close</button>
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

function mapStateToProps(state) {
  const { event } = state;
  return {
    event
  };
}

export default connect(mapStateToProps)(SubRegistrationQuestions);
