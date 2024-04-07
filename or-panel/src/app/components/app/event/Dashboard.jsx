import React, { Component } from 'react'
import CircularProgressBar from "@/app/event/stats/CircularProgressBar";
import { Translation } from "react-i18next";
import ChartWidget from "@/app/event/stats/ChartWidget";
import ChartWidgetBar from "@/app/event/stats/ChartWidgetBar";
import SubRegistrationQuestions from "@/app/event/stats/SubRegistrationQuestions";
import SubRegistrationQuestionResults from "@/app/event/stats/SubRegistrationQuestionResults";
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import { connect } from 'react-redux';

class Dashboard extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      data: [],
      preLoader: false,
      popup: false,
      question_id: '',
      answer_id: '',
    }
  }

  componentDidMount() {
    this._isMounted = true;
    this.stats();
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  stats = () => {
    this.setState({ preLoader: true });
    service.post(`${process.env.REACT_APP_URL}/dashboard`, this.state)
      .then(
        response => {
          if (response.success) {
            if (response.data) {
              if (this._isMounted) {
                this.setState({
                  data: response.data,
                  preLoader: false
                });
              }
            }
          }
        },
        error => { }
      );
  }

  close = (type = false) => e => {
    this.setState({ popup: type });
  }

  openQuestionResults = (question_id, answer_id) => e => {
    this.setState({
      popup: 'sub-registration-result',
      question_id: question_id,
      answer_id: answer_id,
    });
  }

  render() {
    return (
      <Translation>
        {t => (
          <div className="container-box main-landing-page">
            {this.state.preLoader && <Loader />}
            {!this.state.preLoader && (
              <React.Fragment>
                <div className="main-data-table-inner">
                  <div className="row">
                    <div className="col-6">
                      <ChartWidget data={this.state.data} event={this.props.event} />
                    </div>
                    <div className="col-6">
                      <ChartWidgetBar data={this.state.data} />
                    </div>
                  </div>
                  <div className="row">
                    <div className="col-6">
                      <div className="row">
                        <div className="col-4">
                          <CircularProgressBar
                            value={(this.state.data.total_tickets ? Number(Math.round((this.state.data.total_signups / this.state.data.total_tickets) * 100)) : '...')}
                            total={(this.state.data.total_signups ? this.state.data.total_signups : '')}
                            label={t('DSB_SIGNUPS')}
                            border="#25AC35"
                            hidepercentage={Number(this.state.data.total_tickets) === 0 ? true : false}
                          />
                        </div>
                        <div className="col-4">
                          <CircularProgressBar
                            value={(this.state.data.total_tickets ? Number(Math.round((this.state.data.tickets_left / this.state.data.total_tickets) * 100)) : '...')}
                            total={(this.state.data.tickets_left ? this.state.data.tickets_left : '')}
                            label={t('DSB_SEATS_LEFT')}
                            border="#FF8F00"
                            hidepercentage={Number(this.state.data.total_tickets) === 0 ? true : false}
                          />
                        </div>
                        <div className="col-4">
                          <CircularProgressBar
                            value={(this.state.data.totalOrders ? Number(Math.round((this.state.data.cancelled_orders / this.state.data.totalOrders) * 100)) : '0')}
                            total={(this.state.data.cancelled_orders ? this.state.data.cancelled_orders : '')}
                            label={t('DSB_CANCEL_ORDERS')}
                            border="#EB0813"
                            hidepercentage={Number(this.state.data.totalOrders) === 0 ? true : false}
                          />
                        </div>
                      </div>
                    </div>
                    {/* <div className="col-6">
                      <div className="wrapper-unknown-widget">
                        <div className="row d-flex align-items-center">
                          <div className="col-6">
                            <div className="counter-box">
                              <h4>HEADING 01</h4>
                              <big>000</big>
                            </div>
                          </div>
                          <div className="col-6">
                            <button onClick={() => this.setState({ popup: "sub-registration" })} className="btn btn-subregistration">{t('DSB_SUB_REGISTRATION')}</button>
                          </div>
                        </div>
                      </div>
                    </div> */}
                    <div className="col-6">
                      <div className="row">
                        <div className="col-4">
                          <CircularProgressBar
                            value={(this.state.data.total_signups ? Number(Math.round((this.state.data.event_sub_registration_responses / this.state.data.total_signups) * 100)) : '0')}
                            total={(this.state.data.event_sub_registration_responses ? this.state.data.event_sub_registration_responses : '')}
                            label={t('DSB_SUB_REGISTRATION')}
                            border="#25AC35"
                            hidepercentage={Number(this.state.data.event_sub_registration_responses) === 0 ? true : false}
                          />
                        </div>
                        <div className="col-4">
                          <CircularProgressBar
                            value={(this.state.data.invited ? Number(Math.round((this.state.data.not_registered / this.state.data.invited) * 100)) : '0')}
                            total={(this.state.data.not_registered ? this.state.data.not_registered : '')}
                            label={t('DSB_NO_RESPONSE')} 
                            border="#FF8F00"
                            hidepercentage={Number(this.state.data.not_registered) === 0 ? true : false}
                          />
                        </div>
                        <div className="col-4">
                          <CircularProgressBar
                            value={(this.state.data.totalOrders ? Number(Math.round((this.state.data.waitingListOrders / this.state.data.totalOrders) * 100)) : '0')}
                            total={(this.state.data.waitingListOrders ? this.state.data.waitingListOrders : '')}
                            label={t('DSB_WAITING_LIST_ORDERS')}
                            border="#EB0813"
                            hidepercentage={Number(this.state.data.totalOrders) === 0 ? true : false}
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                {this.state.popup === "sub-registration" && <SubRegistrationQuestions close={this.close} openQuestionResults={this.openQuestionResults} />}
                {this.state.popup === "sub-registration-result" && <SubRegistrationQuestionResults close={this.close} question_id={this.state.question_id} answer_id={this.state.answer_id} />}
              </React.Fragment>
            )}
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

export default connect(mapStateToProps)(Dashboard);