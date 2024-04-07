import * as React from 'react';
import Img from 'react-image';
import { ReactSVG } from 'react-svg';
import Loader from '@/app/forms/Loader';
import { Link } from 'react-router-dom';
import { EventService } from 'services/event/event-service';
import Pagination from "react-js-pagination";
import DropDown from '@/app/forms/DropDown';
import Clone from '@/app/event/components/Clone';
import { confirmAlert } from 'react-confirm-alert'; // Import
import { EventAction } from 'actions/event/event-action';
import { GeneralAction } from 'actions/general-action';
import { connect } from 'react-redux';
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { Translation } from "react-i18next";
import { OverlayTrigger, Tooltip } from "react-bootstrap";
import DashboardTopBar from '@/app/layout/DashboardTopBar';

class Events extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      data: [],
      preLoader: false,
      sort_by: 'start_date',
      order_by: 'ASC',
      query: '',
      action: 'active_future',

      //pagination
      limit: 10,
      total: '',
      activePage: 1,

      typing: false,
      typingTimeout: 0,

      // POPUP
      clonePopup: false

    }

    //Event
    this.onFieldChange = this.onFieldChange.bind(this);
    this.onSorting = this.onSorting.bind(this);
  }

  componentDidMount() {
    this._isMounted = true;
    this.listing();
    document.body.addEventListener('click', this.removePopup.bind(this));
  }

  removePopup = e => {
    if (e.target.className !== 'btn btn_dots active') {
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

  handlePageChange = (activePage) => {
    this.listing(activePage);
  }

  listing = (activePage = 1) => {
    this.setState({ preLoader: true, clonePopup: false });
    EventService.listing(this.state, activePage).then(
      response => {
        if (response.success) {
          if (this._isMounted) {
            this.setState({
              data: response.data.result,
              activePage: response.data.current_page,
              total: response.data.total,
              preLoader: false
            });
          }
        }
      },
      error => { }
    );
  }

  editEvent = (event) => e => {
    const { dispatch } = this.props;
    dispatch(EventAction.eventState());
    dispatch(GeneralAction.step(1));
    dispatch(EventAction.eventInfo(event));
    dispatch(EventAction.template(''));
    this.props.history.push(`event/edit/${event.id}`);
  }

  openEvent = (event) => {
    const { dispatch } = this.props;
    dispatch(EventAction.eventState());
    dispatch(GeneralAction.step(1));
    dispatch(EventAction.eventInfo(event));
    dispatch(EventAction.template(''));
    this.props.history.push(`event/edit/${event.id}`);
  }

  sendEmail = (event) => e => {
    const { dispatch } = this.props;
    dispatch(EventAction.eventState());
    dispatch(GeneralAction.step(1));
    dispatch(EventAction.eventInfo(event));
    dispatch(EventAction.template(''));
    dispatch({ type: "invitation", invitation: null });
    this.props.history.push('/event/invitation/send-invitation');
  }

  openDashboard = (event) => e => {
    const { dispatch } = this.props;
    dispatch(EventAction.eventState());
    dispatch(GeneralAction.step(1));
    dispatch(EventAction.eventInfo(event));
    dispatch(EventAction.template(''));
    this.props.history.push(`dashboard`);
  }

  openEmailTemplate = (event) => e => {
    const { dispatch } = this.props;
    dispatch(EventAction.eventState());
    dispatch(GeneralAction.step(1));
    dispatch(EventAction.eventInfo(event));
    dispatch(EventAction.template(''));
    if (event.default_template_id !== undefined) this.props.history.push(`event/template/edit/${event.default_template_id}`);
  }


  openAssignEvent = (event) => e => {
    const { dispatch } = this.props;
    dispatch(EventAction.eventState());
    dispatch(GeneralAction.step(1));
    dispatch(EventAction.eventInfo(event));
    dispatch(EventAction.template(''));
    this.props.history.push(`/admin/assign-events/${event.id}`);
  }

  createEvent = () => e => {
    const { dispatch } = this.props;
    localStorage.removeItem('from_event_id');
    localStorage.removeItem('event_id');
    localStorage.removeItem('eventState');
    localStorage.removeItem('eventInfo');
    localStorage.removeItem('is_app');
    localStorage.removeItem('is_registration');
    dispatch(EventAction.eventState());
    dispatch(GeneralAction.step(1));
    dispatch(EventAction.eventInfo());
    dispatch(EventAction.template(''));
    this.props.history.push(`/event/create`);
  }

  handleDelete = (event_id) => e => {
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
                        EventService.destroy(event_id)
                          .then(
                            response => {
                              if (response.success) {
                                this.listing(this.state.activePage);
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
  }

  componentDidUpdate(prevProps: any, prevState: any) {
    const { action, order_by, sort_by } = this.state;
    if (action !== prevState.action || order_by !== prevState.order_by || sort_by !== prevState.sort_by) {
      this.listing(1);
    }
  }

  handleChange = input => e => {
    if(e.value === 'expired') {
      this.setState({
        [input]: e.value,
        'order_by': 'DESC'
      });
    } else {
      this.setState({
        [input]: e.value
      });
    }
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

  onSorting(event) {
    this.setState({
      order_by: event.target.attributes.getNamedItem('data-order').value,
      sort_by: event.target.attributes.getNamedItem('data-sort').value,
    });
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

  handleClose = () => {
    this.setState({
      clonePopup: false
    })
  }

  render() {

    this.getSelectedLabel = (item, id) => {
      if (item && item.length > 0 && id) {
        let obj = item.find(o => o.id.toString() === id.toString());
        return (obj ? obj.name : '');
      }
    }
    
    return (
      <Translation>

        {
          t =>
            <div className="container-box main-landing-page">
              <div>
                {this.state.clonePopup &&
                  <Clone
                    onClose={this.handleClose.bind(this)}
                    eventID={this.state.eventID}
                    openEvent={this.openEvent}
                  />
                }
                <div className="top-landing-page">
                  <div className="row d-flex">
                    <div className="col-2">
                      <div className="logo">
                        <Img width="180" src={require("img/logos.svg")} />
                      </div>
                    </div>
                    <div className="col-4">
                      <DashboardTopBar />
                    </div>
                    <div className="col-6">
                      <div className="right-top-header">
                        <input className="search-field" value={this.state.query} name="query" type="text"
                          placeholder={t('G_SEARCH')} onChange={this.onFieldChange} />
                        <label className="label-select-alt">
                          <DropDown
                            label={t('EL_FILTER_BY')}
                            listitems={[
                              { id: 'active_future', name: t('EL_ACTIVE_AND_FUTURE_EVENT') },
                              { id: 'active', name: t('EL_ACTIVE_EVENT') },
                              { id: 'future', name: t('EL_FUTURE_EVENT') },
                              { id: 'expired', name: t('EL_EXPIRED_EVENT') },
                              { id: 'name', name: t('EL_ALL_EVENT') }
                            ]}
                            selected={this.state.action}
                            selectedlabel={this.getSelectedLabel([{ id: 'active_future', name: t('EL_ACTIVE_AND_FUTURE_EVENT') },
                            { id: 'name', name: t('EL_ALL_EVENT') },
                            { id: 'active', name: t('EL_ACTIVE_EVENT') },
                            { id: 'expired', name: t('EL_EXPIRED_EVENT') },
                            { id: 'future', name: t('EL_FUTURE_EVENT') }], this.state.action)}
                            onChange={this.handleChange('action')}
                            required={true}
                          />
                        </label>
                        <a href="#!" className="btn" onClick={this.createEvent()}><i className="icons"><Img
                          src={require("img/ico-plus-white.svg")} /></i>{t('G_CREATE_EVENT')}</a>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="main-data-table">
                  {this.state.preLoader && <Loader />}
                  {!this.state.preLoader && (
                    <React.Fragment>
                      {this.state.data.length > 0 ? (
                        <div className="flex-data-table">
                          <div className="flex-header flex-row d-flex">
                            <div className="data-box box-1">
                              <span>{t('EL_ID')}</span><i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="id" onClick={this.onSorting} className="material-icons">
                                {(this.state.order_by === "ASC" && this.state.sort_by === "id" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "id" ? "keyboard_arrow_up" : "unfold_more"))}
                              </i>
                            </div>
                            <div className="data-box box-2">
                              <span>{t('EL_NAME')}</span><i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="name" onClick={this.onSorting} className="material-icons">
                                {(this.state.order_by === "ASC" && this.state.sort_by === "name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "name" ? "keyboard_arrow_up" : "unfold_more"))}
                              </i>
                            </div>
                            <div className="data-box box-3">
                              <span>{t('EL_DATE')}</span><i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="start_date" onClick={this.onSorting} className="material-icons">
                                {(this.state.order_by === "ASC" && this.state.sort_by === "start_date" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "start_date" ? "keyboard_arrow_up" : "unfold_more"))}
                              </i>
                            </div>
                            <div className="data-box box-4">
                              <span>{t('EL_REGISTERED')}</span>
                            </div>
                            <div className="data-box box-5">
                              <span>{t('EL_INVITED')}</span>
                            </div>
                            <div className="data-box box-6">
                              <span>{t('EL_OPEN_RATE')}</span>
                            </div>
                            <div className="data-box box-7">
                              <span>{t('EL_CANCELLED')}</span>
                            </div>
                            <div className="data-box box-8">
                              <span>{t('EL_WAITING_LIST')}</span>
                            </div>
                            <div className="data-box box-9">
                              <span>{t('EL_SEATS_REMAINING')}</span>
                            </div>
                            <div className="data-box box-10">
                              <span className="small-heading">
                                {`${this.state.total} ${t('EL_EVENTS')}`}
                              </span>
                            </div>
                          </div>
                          {this.state.data.map((item, key) => (
                            <div onClick={this.editEvent(item.event)} key={key} className="flex-row d-flex">
                              <div className="data-box box-1">{item.event.id}</div>
                              <div className="data-box box-2">{item.event.name}</div>
                              <div className="data-box box-3">{item.event.start_date_time}</div>
                              <div className="data-box box-4">{item.registered_attendees}</div>
                              <div className="data-box box-5">{item.invited}</div>
                              <div className="data-box box-6">{item.attendee_invitation_stats.opens}</div>
                              <div className="data-box box-7">{item.cancelled_orders}</div>
                              <div className="data-box box-8">{item.waiting_list_orders}</div>
                              <div className="data-box box-9">{item.seats_remaning}</div>
                              <div onClick={(e) => e.stopPropagation()} className="data-box box-10">
                                <div className="panel-nav">
                                  <OverlayTrigger overlay={<Tooltip>{t('EL_EDIT')}</Tooltip>}>
                                    <span onClick={this.editEvent(item.event)}><i
                                      className="icons"><ReactSVG className={`image-panel-${key}`} alt="" src={require("img/ico-edit-gray.svg")} /></i></span>
                                  </OverlayTrigger>
                                  <OverlayTrigger overlay={<Tooltip>{t('EL_SEND_MAIL')}</Tooltip>}>
                                    <span onClick={this.sendEmail(item.event)}>
                                      <i className="icons linestroke"><ReactSVG className={`image-panel-${key}`} alt="" src={require("img/ico-email-gray.svg")} /></i>
                                    </span>
                                  </OverlayTrigger>
                                  <OverlayTrigger overlay={<Tooltip>{t('EL_COPY')}</Tooltip>}>
                                    <span onClick={() => this.setState({ clonePopup: true, eventID: item.event.id })}><i className="icons"><ReactSVG className={`image-panel-${key}`} alt="" src={require("img/ico-copy-gray.svg")} /></i></span>
                                  </OverlayTrigger>
                                  <OverlayTrigger overlay={<Tooltip>{t('EL_DELETE')}</Tooltip>}>
                                    <span onClick={this.handleDelete(item.event.id)}><i
                                      className="icons"><ReactSVG className={`image-panel-${key}`} alt="" src={require("img/ico-delete-gray.svg")} /></i></span>
                                  </OverlayTrigger>
                                  <div className="parctical-button-panel">
                                    <div className="dropdown">
                                      <span onClick={this.handleDropdown.bind(this)} className="btn btn_dots">
                                      </span>
                                      <div className="dropdown-menu">
                                        <button className="dropdown-item">
                                          <ReactSVG wrapper="span" className='icons' alt="" src={require("img/ico-reports-gray.svg")} /> {t('EL_REPORTS')}
                                        </button>
                                        <button className="dropdown-item" onClick={this.openDashboard(item.event)}>
                                          <ReactSVG wrapper="span" className='icons' alt="" src={require("img/ico-analytic-gray.svg")} />{t('EL_ANALYTICS')}
                                        </button>
                                        <button className="dropdown-item" onClick={this.openEmailTemplate(item.event)}>
                                          <ReactSVG wrapper="span" className='icons' alt="" src={require("img/ico-envelop-gray.svg")} />{t('EL_EMAIL_TEMPLATES')}
                                        </button>
                                        <button className="dropdown-item">
                                          <ReactSVG wrapper="span" className='icons' alt="" src={require("img/ico-sendnew-gray.svg")} />{t('EL_SEND_NEWS')}
                                        </button>
                                       
                                       {this.props.auth.data !== undefined && (this.props.auth.data.user.parent_id === 0 || this.props.auth.data.user.id === item.event.owner_id) ? ( 
                                        <button className="dropdown-item" onClick={this.openAssignEvent(item.event)}>
                                          <ReactSVG wrapper="span" className='icons' alt="" src={require("img/admin.svg")} />{t('EL_ASSIGN_ADMIN')}
                                        </button>) : '' }
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          ))}
                        </div>
                      ) : (this.state.query === "" ? (
                        <Link className="start-creating-event" to="/event/create"><Img
                          src={require("img/start-creating-event.svg")} /></Link>
                      ) : (
                          <React.Fragment>
                            <div style={{ textAlign: 'center' }}>
                              <Img width="850px" src={require('img/noresult.svg')} alt="" />
                            </div>
                          </React.Fragment>
                        )
                        )}
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
                    </React.Fragment>
                  )}
                </div>
              </div>
            </div>
        }
      </Translation>
    );
  }
}

function mapStateToProps(state) {
  const { event, auth } = state;
  return {
    event, auth
  };
}

export default connect(mapStateToProps)(Events);
