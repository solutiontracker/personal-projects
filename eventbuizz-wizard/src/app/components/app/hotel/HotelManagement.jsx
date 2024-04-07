import React, { Component } from 'react';
import Img from "react-image";
import { NavLink } from 'react-router-dom';
import HotelWidget from '@/app/hotel/forms/HotelWidget';
import { ReactSVG } from 'react-svg';
import Loader from '@/app/forms/Loader';
import { HotelService } from "services/hotel/hotel-service";
import { confirmAlert } from "react-confirm-alert";
import { Translation } from "react-i18next";
import { connect } from 'react-redux';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import { service } from 'services/service';
import moment from 'moment';


const in_array = require("in_array");

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/registration/sub-registration", "ddirectory": "/event/module/documents" };

// a little function to help us with reordering the result
const reorder = (list, startIndex, endIndex) => {
  const result = Array.from(list);
  const [removed] = result.splice(startIndex, 1);
  result.splice(endIndex, 0, removed);
  return result;
};

const getItemStyle = (isDragging, draggableStyle) => ({
  // some basic styles to make the items look a bit nicer
  userSelect: 'none',
  // change background colour if dragging
  // styles we need to apply on draggables
  ...draggableStyle,
  ...isDragging
});

const move = (source, destination, droppableSource, droppableDestination) => {
  const sourceClone = Array.from(source);
  const destClone = Array.from(destination);
  const [removed] = sourceClone.splice(droppableSource.index, 1);

  destClone.splice(droppableDestination.index, 0, removed);

  const result = {};
  result[droppableSource.droppableId] = sourceClone;
  result[droppableDestination.droppableId] = destClone;

  return result;
};

const getListStyle = () => ({
  width: '100%'
});

class HotelManagement extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      data: [],
      displayElement: false,
      editElement: false,
      editElementIndex: undefined,
      toggleList: false,
      //hotel price setting
      paidHotelBooking: false,
      next: false,

      //errors & loading
      preLoader: false,

      prev: "/event/registration/company-detail-form"
    }
  }

  componentDidMount() {
    this._isMounted = true;
    this.listing();

    //set next previous
    if (this.props.event.modules !== undefined && this.props.event.modules.length > 0) {
      let modules = this.props.event.modules.filter(function (module, i) {
        return in_array(module.alias, ["subregistration"]) && Number(module.status) === 1;
      });

      this.setState({
        prev: (modules[0] !== undefined && module_routes[modules[0]['alias']] !== undefined && Number(modules[0]['status']) === 1 ? module_routes[modules[0]['alias']] : "/event/registration/company-detail-form"),
      });

    }
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  continue = e => {
    e.preventDefault();
    this.props.nextStep();
  }

  back = e => {
    e.preventDefault();
    this.props.prevStep();
  }

  getHotelSettings = () => {
    HotelService.getHotelPriceSetting()
      .then(
        response => {
          if (response.success) {
            this.setState({
              paidHotelBooking: response.data.show_hotel_prices === 1 ? true : false,
              preLoader: false
            });
          }
        },
        error => { }
      );
  }

  updateHotelSettings = () => {
    const request_data = {};
    request_data.show_hotel_prices = this.state.paidHotelBooking === true ? 0 : 1;
    HotelService.updateHotelPriceSetting(request_data)
      .then(
        response => {
          if (response.success) {
          }
        },
        error => { }
      );
  }

  listing = (activePage = 1, loader = false, type = "save") => {
    this.setState({ preLoader: (!loader ? true : false) });
    HotelService.listing(activePage, this.state).then(
      response => {
        if (response.success) {
          if (this._isMounted) {
            this.setState({
              data: response.data,
              next: false,
              displayElement: (type === "save-new" ? true : false),
              editElement: false
            });
          }
        }
      },
      error => { });
    this.getHotelSettings();
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
                        HotelService.destroy(id).then(
                          response => {
                            if (response.success) {
                              this.listing(1, true);
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
    });
  }

  toggleDates = e => {
    const next = e.target.nextSibling;
    next.style.display = next.style.display === 'block' ? 'none' : 'block';
  }

  handleDateChange = input => e => {
    if (e !== undefined) {
      var month = e.getMonth() + 1;
      var day = e.getDate();
      var year = e.getFullYear();
      var daydigit = (day.toString().length === 2) ? day : '0' + day;
      var date = month + '/' + daydigit + '/' + year;
      this.setState({ [input]: date });
    } else {
      this.setState({ [input]: '' });
    }
  }

  updateHotelPriceSetting = () => {
    this.setState({
      paidHotelBooking: this.state.paidHotelBooking === true ? false : true
    });
    this.updateHotelSettings();
  }

  handleChange = input => e => {
    if (e.hex) {
      this.setState({ [input]: e.hex });
    } else {
      if (e.target.type === 'checkbox') {
        this.setState({ [input]: e.target.checked });
      } else if (e.target.type === 'file') {
        if (e.target.getAttribute('multiple') !== null) {
          if (this.state[input].length === 0) {
            this.setState({ [input]: e.target.files });
          } else {
            let fileName = e.target.files;
            this.setState(prevstate => ({ [input]: [...prevstate[input], ...fileName] }))
          }

        } else {
          this.setState({ [input]: e.target.files });
        }
      } else if (e.target.getAttribute('data-value') === 'remove-file') {
        if (this.state[input].length > 1) {
          var array = [...this.state[input]];
          var index = 1;
          if (index !== -1) {
            array.splice(index, 1);
            this.setState({ [input]: array });
          }
        } else {
          this.setState({ [input]: [] });
        }
      } else {
        this.setState({ [input]: e.target.value });
      }
    }
  };

  /**
     * A semi-generic way to handle multiple lists. Matches
     * the IDs of the droppable container to the names of the
     * source arrays stored in the state.
     */
  id2List = {
    droppable: 'data',
  };

  getList = id => this.state[this.id2List[id]];

  onDragStart = result => {

    this.setState({
      editIndex: false,
      activeState: false
    })
  }

  onDragEnd = result => {
    const { source, destination } = result;
    // dropped outside the list
    if (!destination) {
      return;
    }
    if (source.droppableId === destination.droppableId) {
      const data = reorder(
        this.getList(source.droppableId),
        source.index,
        destination.index
      );
      this.setState({ data });
      service.put(`${process.env.REACT_APP_URL}/hotel/sorting`, { items: data })
        .then(
          response => { },
          error => { }
        );
    } else {
      const result = move(
        this.getList(source.droppableId),
        this.getList(destination.droppableId),
        source,
        destination
      );
      this.setState({
        data: result.droppable,
      });
    }
  };

  updateStatus = (id, value, index) => {
    let data = this.state.data;
    data[index].status = value;
    service.put(`${process.env.REACT_APP_URL}/update-column-status`, { id: id, table: 'conf_event_hotels', column: 'status', value: value })
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

  export = () => {
    service.download(`${process.env.REACT_APP_URL}/hotel/export`)
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

  render() {
    const HotelRecords = ({ data }) => {
      return (
        <Translation>
          {
            t =>
              <DragDropContext onDragEnd={this.onDragEnd}>
                <Droppable droppableId="droppable">
                  {(provided, snapshot) => (
                    <div
                      className="draggable-left customize-page new-style hotel-management"
                      ref={provided.innerRef}
                      style={getListStyle(snapshot.isDraggingOver)}>
                      {data.length && data.map((data, key) => (
                        <Draggable
                          key={`item-${data.id}`}
                          draggableId={`item-${data.id}`}
                          index={key}>
                          {(provided, snapshot) => (
                            <div
                              className="input-list-item"
                              data-index={key}
                              ref={provided.innerRef}
                              {...provided.draggableProps}
                              style={getItemStyle(
                                snapshot.isDragging,
                                provided.draggableProps.style
                              )}>
                              <div className="row d-flex">
                                <span
                                  {...provided.dragHandleProps}
                                  className="list-drag material-icons">more_vert more_vert</span>
                                <div className="col-4">
                                  {data.name && <h5>{data.name}</h5>}
                                  {data.description && <p>{data.description}</p>}
                                </div>
                                <div className="col-8">
                                  <div className="row">
                                    <div className="col-5"> 
                                      {data.room_range.length > 0 && <h5>{data.room_range.length} {t('HM_NIGHTS')}</h5>}
                                      {data.from_date && <p>{moment(data.from_date).format('DD/MM/YYYY')} to {moment(data.to_date).format('DD/MM/YYYY')}</p>}
                                      {data.room_range.length > 0 && (
                                        <div className="">
                                          <span onClick={this.toggleDates} className="btn-list-detail"><i
                                            className='material-icons'>chevron_right</i>{t('HM_VIEW_DETAIL')}</span>
                                          <ul className="list-dates">
                                            {data.room_range.map((date, k) => {
                                              return <li key={k}>{date.room_date} - {`${date.no_of_rooms} ${t('HM_ROOM')}`}</li>
                                            })}
                                          </ul>
                                        </div>
                                      )}
                                    </div>
                                    <div className="col-4">
                                      {data.price && (
                                        <div>
                                          <h5>{`${data.price} ${data.currency}`} </h5>
                                          <p>{t('HM_PER_PRICE')}</p>
                                        </div>
                                      )}
                                    </div>
                                    <div className="col-3">
                                      <ul className="panel-actions">
                                        <li><span className={(data.status === 1 ? "active" : "deactive")} onClick={() => this.updateStatus(data.id, (data.status === 1 ? 0 : 1), key)}><ReactSVG wrapper='span' className="icons" src={require(`img/ico-feathereye${data.status !== 1 ? '-alt' : ''}.svg`)} /></span></li>
                                        <li><span onClick={() => this.handleEditElement(key)}><ReactSVG wrapper='span' className="icons" src={require("img/ico-edit.svg")} /></span></li>
                                        <li><span onClick={() => this.handleDeleteElement(key, data.id)}><ReactSVG wrapper='span' className="icons" src={require("img/ico-delete.svg")} /></span></li>
                                      </ul>
                                    </div>
                                  </div>
                                </div>
                                {this.state.editElement && this.state.editElementIndex === key ?
                                  <HotelWidget data={this.listing} editdata={data} editdataindex={key}
                                    datacancel={this.handleCancel} /> : ''}
                              </div>
                            </div>
                          )}
                        </Draggable>
                      ))}
                      {provided.placeholder}
                    </div>
                  )}
                </Droppable>
              </DragDropContext>
          }
        </Translation>
      )
    }
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
                  <header className="new-header clearfix">
                    <div className="row">
                      <div className="col-6 accomodation-section">
                        <h1 className="section-title">{t('HM_ACCOMODATION')}</h1>
                        <p>{t('HM_ACCOMODATION_HEADING_TOP')} </p>
                        <h5>{t('HM_PAID_ACCOMODATION')}</h5>
                        <div className="button-header d-flex clearfix">
                          <p style={{ paddingRight: '10px' }}>{t('HM_ACCOMODATION_HEADING_BOTTOM')} </p>
                          <label className="custom-checkbox-toggle float-right"><input onClick={this.updateHotelPriceSetting}
                            defaultChecked={this.state.paidHotelBooking}
                            type="checkbox"
                            name="" /><span></span></label>
                        </div>
                      </div>
                      <div className="col-6">
                        {!this.state.displayElement && !this.state.editElement && (
                          <div className="new-right-header new-panel-buttons float-right">
                            <button onClick={this.handleAddElement} className="btn_addNew">
                              <ReactSVG wrapper="span" className="icons" src={require('img/ico-plus-lg.svg')} />
                            </button>
                            <button onClick={this.export.bind(this)} style={{ marginLeft: '3px' }} className="btn_addNew_main">
                              <span className="icons">
                                <Img src={require("img/export-csv.svg")} />
                              </span>
                            </button>
                          </div>
                        )}
                      </div>
                    </div>
                  </header>
                  <div className="hotel-management-section">
                    {this.state.displayElement && (
                      <HotelWidget data={this.listing} datacancel={this.handleCancel} />
                    )}
                    {this.state.data.length > 0 ? (
                      <div key='0' className="hotel-management-records">
                        <HotelRecords data={this.state.data} />
                      </div>
                    ) : ''}
                  </div>
                  <div className="bottom-component-panel clearfix">
                    <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                      <i className='material-icons'>remove_red_eye</i>
                      {t("G_PREVIEW")}
                    </NavLink>
                    {this.state.prev && (
                      <NavLink className="btn btn-prev-step" to={this.state.prev}><span className="material-icons">
                        keyboard_backspace</span></NavLink>
                    )}
                    <NavLink className="btn btn-next-step" to={`/event/registration/gdpr`}>{t('G_NEXT')}</NavLink>
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

export default connect(mapStateToProps)(HotelManagement);