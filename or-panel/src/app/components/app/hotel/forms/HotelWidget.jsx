import React, { Component } from 'react';
import Input from '@/app/forms/Input';
import TextArea from '@/app/forms/TextArea';
import DateRange from '@/app/forms/DateRange';
import moment from 'moment';
import { HotelService } from "services/hotel/hotel-service";
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import { connect } from 'react-redux';
import { withRouter } from "react-router-dom";
import ConfirmationModal from "@/app/forms/ConfirmationModal";

class HotelWidget extends Component {
  constructor(props) {
    super(props);
    this.state = {
      id: '',
      dates: undefined,
      from_date: (this.props.event ? this.props.event.start_date : ''),
      to_date: (this.props.event ? this.props.event.end_date : ''),
      qty: '',
      roomName: '',
      priceNight: '',
      roomDescription: "",
      roomsDates: [],
      display: true,

      //errors & loading
      message: false,
      success: true,
      errors: {},
      isLoader: false,

      //Valdiations
      room_name_validate: '',
      price_validate: '',
      from_date_validate: '',
      to_date_validate: '',
      validatePrice: '',

      change: false

    }
  }

  componentDidMount() {
    const { editdata } = this.props;
    if (this.props.editdata) {
      let roomArray = [];
      let dateArray = [];
      this.props.editdata.room_range.forEach(room => {
        roomArray.push(room.no_of_rooms);
        dateArray.push(moment(room.room_date).format('MM/DD/YYYY'));
      });
      this.setState({
        id: editdata.id,
        dates: dateArray,
        qty: editdata.qty,
        roomName: editdata.name,
        priceNight: editdata.price,
        roomsDates: roomArray,
        roomDescription: editdata.description,
        from_date: editdata.from_date,
        to_date: editdata.to_date,
      })
    }
  }

  dateList = (from, to) => {
    if (from && to) {
      var startDate = moment(from);
      var endDate = moment(to);
      var roomDate = this.state.roomsDates;
      var now = startDate, dates = [], roomDates = [];
      var i = 0;
      while (now.isBefore(endDate)) {
        dates.push(now.format('MM/DD/YYYY'));
        now.add(1, 'days');
        if (roomDate[i] !== undefined) {
          roomDates.push(roomDate[i]);
        } else {
          if (this.props.editdata) {
            roomDates.push('');
          } else {
            roomDates.push(this.state.qty);
          }
        }
        i++;
      }
      this.setState({
        dates: dates.length > 0 ? dates : undefined,
        roomsDates: roomDates,
        from_date: from,
        to_date: to,
        change: true
      });
    }
  }

  handleChange = input => e => {
    const pattern = e.target.getAttribute('pattern');
    if (pattern === '[0-9]*') {
      const validate = (e.target.validity.valid) ? e.target.value : this.state[input];
      this.setState({
        [input]: validate,
        change: true
      })
    } else if (pattern === '^[0-9]+([\\.\\-]?[0-9]+)?$') {
      const validate = (e.target.validity.valid) ? false : true;
      this.setState({
        [input]: e.target.value,
        validatePrice: validate,
        change: true
      })
    }
    else {
      this.setState({
        [input]: e.target.value,
        change: true
      })
    }
  }

  updateDateRooms = input => e => {
    let roomArray = [], dateArray = [];
    const validate = (e.target.validity.valid) ? e.target.value : this.state[input];
    if (this.state.dates !== undefined) {
      this.state.dates.forEach(room => {
        roomArray.push(validate);
        dateArray.push(room);
      });
    }
    this.setState({
      [input]: validate,
      roomsDates: roomArray,
      dates: dateArray.length > 0 ? dateArray : undefined,
      change: true
    })
  }

  updateRoomQty = key => e => {
    const pattern = e.target.getAttribute('pattern');
    if (pattern === '[0-9]*') {
      let roomsDates = this.state.roomsDates;
      const validate = (e.target.validity.valid) ? e.target.value : roomsDates[key];
      roomsDates[key] = validate;
      this.setState({
        roomsDates: roomsDates,
        change: true
      });
    }
  }

  toggleButton = () => {
    this.setState({
      display: this.state.display === true ? false : true
    })
  };

  saveData = (e) => {
    const type = e.target.getAttribute('data-type');
    var items = document.querySelectorAll('.advance-options input');
    var itemsArray = [];
    items.forEach(item => {
      itemsArray.push(item.value);
    });
    this.setState({ isLoader: type, roomsDates: itemsArray });
    if (this.props.editdata) {
      const request_data = this.state;
      const id = this.state.id;
      HotelService.update(request_data, id)
        .then(
          response => {
            if (response.success) {
              this.setState({
                'message': response.message,
                'success': true,
                isLoader: false,
                errors: {},
                change: false
              });
              this.props.data(1, true, type);
            } else {
              this.setState({
                'message': response.message,
                'success': false,
                'isLoader': false,
                'errors': response.errors
              });
            }
          },
          error => { });
    } else {
      const request_data = this.state;
      HotelService.create(request_data).then(
        response => {
          if (response.success) {
            this.setState({
              displayElement: false,
              editElement: false,
              'message': response.message,
              'success': true,
              isLoader: false,
              errors: {},
              change: false
            });
            this.props.data(1, false, type);
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

  render() {

    return (
      <Translation>
        {
          t =>

            <div className={`hotel-add-item ${this.props.editdata ? 'isGray' : ''}`}>
              <ConfirmationModal update={this.state.change} />
              {this.state.message &&
                <AlertMessage
                  className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                  title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                  content={this.state.message}
                  icon={this.state.success ? "check" : "info"}
                />
              }
              <h4 className="component-heading">{(this.props.editdata !== undefined ? t('HM_EDIT_ROOM') : t('HM_ADD_ROOM'))}</h4>
              <div className="row d-flex">
                <div className="col-3">
                  <Input
                    className={this.state.room_name_validate}
                    type='text'
                    label={t('HM_ROOM_NAME')}
                    value={this.state.roomName}
                    name='RoomName'
                    onChange={this.handleChange('roomName')}
                    required={true}
                  />
                  {this.state.errors.name && <p className="error-message">{this.state.errors.name}</p>}
                </div>
                {!this.props.editdata && (
                  <div className="col-2">
                    <Input
                      type='text'
                      label={t('HM_Quantity')}
                      name='Qty'
                      value={this.state.qty}
                      onChange={this.updateDateRooms('qty')}
                      pattern='[0-9]*'
                      required={false}
                    />
                  </div>
                )}
                <div className="col-3 PriceNight">
                  <Input
                    type='text'
                    label={t('HM_PRICE_PER_NIGHT')}
                    name='PriceNight'
                    value={this.state.priceNight}
                    onChange={this.handleChange('priceNight')}
                    pattern='^[0-9]+([\.\-]?[0-9]+)?$'
                    required={true}
                  />
                  {this.state.errors.price && <p className="error-message">{this.state.errors.price}</p>}
                  {this.state.validatePrice && <p className="error-message">{t('EE_VALID_NUMBER')}</p>}
                </div>
                <div className="col-4">
                  <DateRange
                    type='text'
                    name='DateRange'
                    dateList={this.dateList}
                    required={true}
                    datavalue={this.state.dates}
                    from_date={this.state.from_date}
                    to_date={this.state.to_date}
                    disabledDays={true}
                  />
                  {this.state.errors.from_date && <p className="error-message">{this.state.errors.from_date}</p>}
                  {this.state.errors.to_date && <p className="error-message">{this.state.errors.to_date}</p>}
                  {this.state.errors.room_range && <p className="error-message">{this.state.errors.room_range}</p>}
                </div>
              </div>
              <div className="row d-flex">
                <div className="col-12">
                  <TextArea
                    label={t('HM_DESCRIPTION')}
                    height={70}
                    required={false}
                    value={this.state.roomDescription}
                    onChange={this.handleChange('roomDescription')}
                  />
                </div>
              </div>
              {this.state.dates && (
                <div className="advance-options">
                  <p className="btn-advance-options"><span onClick={this.toggleButton.bind(this)}
                    className={this.state.display === true ? 'active' : ''}><i
                      className='material-icons'>chevron_right</i>{t('HM_ADVANCED_OPTIONS')}</span></p>
                  <div className={this.state.display === true ? "row d-flex" : "row d-flex display-none"}>
                    {this.state.dates.map((date, k) => {
                      return (
                        <div className='col-2' key={k}>
                          <Input
                            type='text'
                            label={`${t('HM_ROOM')} ${date}`}
                            value={this.state.roomsDates[k]}
                            name='roomsDates'
                            onChange={this.updateRoomQty(k)}
                            pattern='[0-9]*'
                            required={false}
                          />
                        </div>
                      )
                    })}
                  </div>
                </div>
              )}
              <div className="bottom-panel-button">
                <button disabled={this.state.isLoader ? true : false} onClick={this.saveData} data-type="save" className="btn">{this.state.isLoader === "save" ?
                  <span className="spinner-border spinner-border-sm"></span> : (this.props.editdata ? t('G_SAVE') : t('G_SAVE'))}</button>
                {!this.props.editdata && (
                  <button disabled={this.state.isLoader ? true : false} onClick={this.saveData} data-type="save-new" className="btn save-new">{this.state.isLoader === "save-new" ?
                    <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE_AND_ADD_ANOTHER')}</button>
                )}
                <button className="btn btn-cancel" onClick={this.props.datacancel}>{t('G_CANCEL')}</button>
              </div>
            </div>
        }
      </Translation>
    )
  }
}

function mapStateToProps(state) {
  const { alert, event } = state;
  return {
    alert, event
  };
}

export default connect(mapStateToProps)(withRouter(HotelWidget));