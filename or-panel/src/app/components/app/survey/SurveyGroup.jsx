import * as React from "react";
import { NavLink } from 'react-router-dom';
import { withRouter } from 'react-router-dom';
import { Link } from 'react-router-dom';
import QuestionFormWidget from '@/app/survey/forms/QuestionFormWidget';
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { connect } from 'react-redux';
import { Translation } from "react-i18next";
class SurveyGroup extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      data: [],
      survey_id: this.props.match.params.id,
      survey_name: '',

      //errors & loading
      preLoader: true,
      message: false,
      success: true,
      errors: {},
      isLoader: false,
      prev: (Number(this.props.event.is_registration) === 1 ? "/event/registration/tos" : "/event_site/billing-module/manage-orders")

    }

  }

  componentDidMount() {
    this.surveyGroup();
    this._isMounted = true;
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  surveyGroup = () => {
    let id = this.props.match.params.id;
    if (id !== undefined) {
      service.get(`${process.env.REACT_APP_URL}/survey/group/${id}`)
        .then(
          response => {
            if (response.success) {
              if (response.data) {
                this.setState({
                  data: response.data,
                  preLoader: false
                });
              }
            }
          },
          error => { }
        );
    }
  }

  handleUpdate = () => {
    this.setState({ isLoader: true });
    let id = this.props.match.params.id;
    if (id !== undefined) {
      service.post(`${process.env.REACT_APP_URL}/survey/group/${id}`, { groups: this.state.data.assigned_ids })
        .then(
          response => {
            this.setState({ isLoader: false });
            if (response.success) {

            }
          },
          error => { }
        );
    }

  }

  handleChange = e => {
    const id = e.target.value;
    var data = this.state.data;

    if (e.target.checked) {
      data.assigned_ids.push(id);
    } else {
      var ind = data.assigned_ids.indexOf(id);
      data.assigned_ids.splice(ind, 1);
    }
    console.log(data.assigned_ids);
    this.setState({
      data: data,
      change: true
    });
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
                      </div>
                    </header>
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
                    {this.state.data.groups.length !== 0 && (
                      <div className="subregistration-widget">

                        {this.state.data.groups.map((list, index) => {
                          return (
                            <div key={index}>
                              <h4>{list.info.value}</h4>
                              {list.children.map((item, i) => {
                                return (
                                  <div style={{ marginLeft: "30px", marginBottom: "5px" }}>
                                    <input type="checkbox" defaultChecked={this.state.data.assigned_ids && this.state.data.assigned_ids.includes(item.id) ? true : false} value={item.id} onChange={this.handleChange.bind(this)} />
                                    <label style={{ marginLeft: "5px" }}>{item.children_info[0].value}
                                    </label>
                                  </div>
                                )
                              })}
                            </div>)
                        })
                        }
                        <div class="bottom-panel-button">
                          <button disabled={this.state.isLoader ? true : false} class="btn" onClick={this.handleUpdate}>{this.state.isLoader ?
                            <span className="spinner-border spinner-border-sm"></span> : (this.props.editdata ? t('G_SAVE') : t('G_SAVE'))}</button><Link to="/event/manage/surveys" class="btn btn-cancel">{t('G_CANCEL')}</Link></div>
                      </div>
                    )}
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

export default connect(mapStateToProps)(withRouter(SurveyGroup));
