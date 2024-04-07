import * as React from "react";
import { NavLink } from 'react-router-dom';
import Img from 'react-image';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import { withRouter } from 'react-router-dom';
import { Link } from 'react-router-dom';
import { SurveyService } from 'services/survey/survey-service';
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { confirmAlert } from 'react-confirm-alert'; // Import
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { Translation } from "react-i18next";
import { connect } from 'react-redux';
import { ReactSVG } from 'react-svg';
import socketIOClient from "socket.io-client";
const socket = socketIOClient(process.env.REACT_APP_SOCKET_SERVER);
`${process.env.NEXT_APP_EVENTCENTER_URL}/_mobile_assets/images/logo-header@2x.png`
class SurveyLeaderBord extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      data: [],
      eventData:[],
      displayElement: false,
      editData: false,
      editDataIndex: undefined,
      toggleList: false,
      event_id: this.props.match.params.event_id,
      survey_id: this.props.match.params.survey_id,
      survey: {},
      eventSettings:{},
      //errors & loading
      gdprSettings:{},
      preLoader: true,
      message: false,
      success: true,
      errors: {},
      isLoader: false,
    }
  }

  componentDidMount() {
    console.log('hello');
    this.getLeaderBoard();
    this.socket();
    this._isMounted = true;
  }
  socket() {
    let _this = this;
    socket.off(`event-buizz:leader_board_survey_block_${this.state.event_id}_${this.state.survey_id}`);
    socket.on(`event-buizz:leader_board_survey_block_${this.state.event_id}_${this.state.survey_id}`, function (data) {
      console.log(data);
        if (data.html) {
            _this.getLeaderBoard(true);
        };
    });
}
  componentWillUnmount() {
    this._isMounted = false;
  }

  getLeaderBoard = (loader = false) => {
    this.setState({ preLoader: (!loader ? true : false) });
 
    SurveyService.getLeaderBoard(this.state)
      .then(
        response => {
          if (response.success) {
            console.log(response.data)
            if (response.data) {
              if (this._isMounted) {
                this.setState({
                  data: response.data.attendee_survey_score,
                  eventData: response.data.event,
                  eventSettings: response.data.event_settings,
                  gdprSettings: response.data.gdpr_settings,
                  survey:response.data.survey[0],
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
  lastScore = 0;
  render() {
    return (
      <React.Fragment>
      {this.state.preLoader &&
        <Loader />
      }
        {this.state.eventSettings && Object.keys(this.state.eventSettings).length > 0 && <div className="ebs-leaderboard-container">
        <div className="ebs-leaderboard-header">
          <div className="ebs-container">
            <div className="row">
              <div className="col-8">
                <div className="d-flex align-items-center">
                  <div className="ebs-leaderboard-logo">
                    <a href="#!">
                      <img src={this.state.eventSettings && this.state.eventSettings.header_logo && this.state.eventSettings.header_logo !== '' ? `${process.env.REACT_APP_EVENTCENTER_URL}/assets/event/branding/${this.state.eventSettings.header_logo}` : `${process.env.REACT_APP_EVENTCENTER_URL}/_mobile_assets/images/logo-header@2x.png`} hight="85" alt="" width="250" /></a>
                  </div>
                  <div className="ebs-slogan">
                    <strong>{this.state.survey && this.state.survey.info && this.state.survey.info.length > 0  && this.state.survey.info[0].value }</strong>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
          <div className="ebs-leaderboard-listing">
            <div className="ebs-container">
              {this.state.data && this.state.gdprSettings && this.state.data.length > 0 &&  this.state.data.filter((item)=>(this.state.gdprSettings.enable_gdpr !== 1 || (this.state.gdprSettings.enable_gdpr ===  1 && item.attendee.event_attendees[0].gdpr === 1) || (this.state.gdprSettings.enable_gdpr === 1 && item.attendee.event_attendees[0].gdpr === 0 && this.state.gdprSettings.attendee_invisible === 0))).map((item, index)=>{
                let classData = (index === 0) || (item.total_score >= this.lastScore) ? 'active' : '';
                this.lastScore = item.total_score >= this.lastScore ? item.total_score : this.lastScore; 
                return (<div className={`ebs-leaderboard-info ${classData}`} key={index}>
                      <div className="ebs-user-info">
                        <div className="row">
                          <div className="col-8 d-flex">
                            <div className="ebs-media">
                              <img src= {item.attendee && item.attendee.image && item.attendee.image  !== ''  && item.attendee.event_attendees[0].gdpr === 1 ? `${process.env.REACT_APP_EVENTCENTER_URL}/assets/attendees/${item.attendee.image}` : `${process.env.REACT_APP_EVENTCENTER_URL}/images/speakers/no-img.jpg`} alt="" />
                            </div>
                            <div className="ebs-user-title">
                              <strong className="ebs-name">{item.attendee && `${item.attendee.first_name} ${item.attendee.last_name}`} </strong>
                              <span className="ebs-designation"></span>
                            </div>
                          </div>
                          <div className="col-4 d-flex justify-content-end">
                            <div className="ebs-like">{item.total_score && item.total_score}</div>
                          </div>
                        </div>
                      </div>
                  </div>)
              })}
            </div>
          </div>
        </div>
        }
      </React.Fragment>
    )
  }
}

function mapStateToProps(state) {
  const { event, update } = state;
  return {
    event, update
  };
}

export default connect(mapStateToProps)(withRouter(SurveyLeaderBord));
