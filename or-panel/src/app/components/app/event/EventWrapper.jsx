import * as React from 'react';
import { connect } from 'react-redux';
import EventDetail from '@/app/event/EventDetail';
import EventDateTime from '@/app/event/EventDateTime';
import Template from '@/app/event/Template';
import { EventService } from 'services/event/event-service';
import { EventAction } from 'actions/event/event-action';
import { GeneralAction } from 'actions/general-action';
import { withRouter } from 'react-router-dom';
import { withTranslation } from "react-i18next";
import ConfirmationModal from "@/app/forms/ConfirmationModal";

class EventWrapper extends React.Component {
  state = {
    id: null,
    step: 1,
    event_id: localStorage.getItem('event_id'),

    //event create / update operation
    eventInfo: {
      detail: [],
      duration: [],
      errors: {}
    },

    //errors & loading
    message: false,
    success: true,
    errors: {},
    isLoader: false
  }

  componentDidMount() {
    this.setState({
      step: this.props.eventStep
    });
    this.props.dispatch(GeneralAction.update(false));
  }

  save = e => {
    e.preventDefault();
    const type = e.target.getAttribute('data-type');
    this.setState({ isLoader: type });
    setTimeout(() => {
      if (this.props.eventState.editData) {
        EventService.updateEvent(this.props.eventState, this.props.eventState.detail.event_id)
          .then(
            response => {
              if (response.success) {
                this.setState({
                  message: response.message,
                  success: true,
                  isLoader: false
                });
                this.props.eventState.errors = {};
                this.props.dispatch(EventAction.eventInfo(response.data.event));
                if (type === "save-next") {
                  this.props.history.push('/event/settings/branding');
                }
                this.props.dispatch(GeneralAction.update(false));
              } else {
                this.setState({
                  message: response.message,
                  success: false,
                  isLoader: false
                });
                this.props.eventState.errors = response.errors;
                if (response.errors && (response.errors.name !== undefined || response.errors.sms_organizer_name !== undefined || response.errors.organizer_name !== undefined || response.errors.email !== undefined)) { this.props.dispatch(GeneralAction.step(2)); }
                else if (response.errors && (response.errors.start_date !== undefined || response.errors.end_date !== undefined || response.errors.start_time !== undefined || response.errors.end_time !== undefined || response.errors.registration_end_date !== undefined || response.errors.cancellation_date !== undefined)) { this.props.dispatch(GeneralAction.step(3)); }
              }
              this.props.dispatch(EventAction.eventState(this.props.eventState));
            },
            error => { }
          );
      } else {
        EventService.createEvent(this.props.eventState)
          .then(
            response => {
              if (response.success) {
                this.setState({
                  message: response.message,
                  success: true,
                  isLoader: false,
                  id: response.data.event.id
                });
                this.props.eventState.errors = {};
                this.props.dispatch(EventAction.eventInfo(response.data.event));
                this.props.dispatch(GeneralAction.step(1));
                this.props.dispatch(GeneralAction.update(false));
                this.props.dispatch(GeneralAction.redirect(!this.props.redirect));
                this.props.history.push((type === "save-next" ? '/event/settings/branding' : `/event/edit/${response.data.event.id}`));
              } else {
                this.setState({
                  message: response.message,
                  success: false,
                  isLoader: false
                });
                this.props.eventState.errors = response.errors;
                if (response.errors && (response.errors.name !== undefined || response.errors.sms_organizer_name !== undefined || response.errors.organizer_name !== undefined || response.errors.email !== undefined)) { this.props.dispatch(GeneralAction.step(2)); }
                else if (response.errors && (response.errors.start_date !== undefined || response.errors.end_date !== undefined || response.errors.start_time !== undefined || response.errors.end_time !== undefined || response.errors.registration_end_date !== undefined || response.errors.cancellation_date !== undefined)) { this.props.dispatch(GeneralAction.step(3)); }
              }
              this.props.dispatch(EventAction.eventState(this.props.eventState));
            },
            error => { }
          );
      }
    }, 500);
  }

  static getDerivedStateFromProps(props, state) {
    if (state.step !== props.eventStep) {
      return {
        step: props.eventStep,
        message: false
      };
    }
    // Return null to indicate no change to state.
    return null;
  }

  showStep = () => {
    const { step, eventInfo, message, success, isLoader } = this.state;
    if (step === 1)
      return (
        <React.Fragment>
          <ConfirmationModal update={this.props.update} />
          <Template
            eventInfo={eventInfo}
            message={message}
            success={success}
            isLoader={isLoader}
            save={this.save}
            id={this.state.id}
            change={this.props.update}
          />
        </React.Fragment>
      )
    if (step === 2)
      return (
        <React.Fragment>
          <ConfirmationModal update={this.props.update} />
          <EventDetail
            eventInfo={eventInfo}
            message={message}
            success={success}
            isLoader={isLoader}
            save={this.save}
            change={this.props.update}
          />
        </React.Fragment>
      )
    if (step === 3)
      return (
        <React.Fragment>
          <ConfirmationModal update={this.props.update} />
          <EventDateTime
            save={this.save}
            message={message}
            success={success}
            isLoader={isLoader}
            change={this.props.update}
          />
        </React.Fragment>
      )
  }

  componentWillUnmount() {
    if (this.props.eventState.editData) {
      this.props.dispatch(EventAction.eventInfo(this.props.event));
    }
  }

  render() {
    return (
      this.showStep()
    );
  }
}

function mapStateToProps(state) {
  const { eventStep, eventState, update, event, redirect } = state;
  return {
    eventStep, eventState, update, event, redirect
  };
}

export default connect(mapStateToProps)(withRouter(withTranslation()(EventWrapper)));