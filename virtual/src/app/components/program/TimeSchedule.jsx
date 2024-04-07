import React, { Component } from 'react';
import { ReactSVG } from 'react-svg'
import moment from 'moment';
import { connect } from 'react-redux';
import { service } from 'services/service';
import Loader from '@app/modules/Loader';
import { GeneralAction } from 'actions/general-action';
import { store } from 'helpers';

const _multiplyer = 500;
var clearTime;
function itemSorting(data) {
  var itemArray = [];
  var i = 0;
  var _lastTime = '00:00';
  data.program_array.forEach((items, k) => {
    const _end = moment(items.start_time, 'HH:mm');
    const _last = moment(_lastTime, 'HH:mm');
    const eventduration = moment.duration(_last.diff(_end));
    _lastTime = items.end_time;
    if (eventduration.asMinutes() <= 0) {
      if (k === 0) {
        const obj = [items];
        itemArray.push(obj);
      } else {
        itemArray[i].push(items);
      }
    } else {
      const obj = [items];
      itemArray.push(obj);
      i = i + 1;
    }
  });
  data.program_array = itemArray;
  return data;
}

const VerticalView = ({ openProgram, data, program_setting }) => {
  if (data) {
    return (
      <div className="timelinecontent">
        {data.map((items, k) => (
          <div key={k} className="timelinebox" onClick={openProgram(items.id, items.video)}>
            {Number(program_setting.agenda_display_time) === 1 && (
              <span className="time">
                {items.start_time} - {items.end_time}
              </span>
            )}
            <div>
              {items.name && <strong>{items.name}</strong>}
              {items.video > 0 && <span className="video"><ReactSVG wrapper='span' src={require('images/ico-video.svg')} /> {items.video}</span>}
              {items.tracks && <span className="tracks">
                {items.tracks.map((track, k) =>
                  <span key={k}>{track}</span>
                )}
              </span>}
              {items.workshop && <span className="tag">{items.workshop}</span>}
            </div>
          </div>
        ))}
      </div>
    )
  }
}

const TimelineHeader = () => {
  var Numbers = Array.from({ length: 48 }, (v, k) => k + 1);
  var date = moment('00:00', 'HH:mm');
  const _width = (24 * _multiplyer) / 48;
  return (
    <div id="timelineheader">
      {Numbers.map(numbers => {
        if (numbers > 1) date = moment(date).add(30, 'm').toDate()
        return (
          <span style={{ width: _width }} key={numbers}>{moment(date).format('HH:mm')}</span>)
      }
      )}
    </div>
  )
}

const DataItem = ({ openProgram, items, program_setting }) => {
  const startTime = moment(items.start_time, 'HH:mm')
  const endTime = moment(items.end_time, 'HH:mm')
  const _time = moment.duration(startTime.diff(moment('00:00', 'HH:mm')));
  const hours = _time.asHours();
  const eventduration = moment.duration(endTime.diff(startTime));
  var _wrappWidth = (_multiplyer / 60) * eventduration.asMinutes()
  _wrappWidth = Math.round(_wrappWidth);
  return (
    <div style={{ left: hours * _multiplyer, width: _wrappWidth }} className="datawrapp" onClick={openProgram(items.id, items.video)}>
      <strong className="title">{items.name}</strong>
      {Number(program_setting.agenda_display_time) === 1 && (
        <span className="time">{items.start_time} - {items.end_time}</span>
      )}
      {items.video > 0 && <span className="video"><ReactSVG wrapper='span' src={require('images/ico-video.svg')} /> {items.video}</span>}
      {items.tracks && <span className="tracks">
        {items.tracks.map((track, k) =>
          <span key={k}>{track}</span>
        )}
      </span>}
      {items.workshop && <span className="tag">{items.workshop}</span>}
    </div>
  )
}

const TimelineContent = ({ openProgram, data, program_setting }) => {
  return (
    <div id="timelinecontent">
      {data && data.map((items, k) => (
        <React.Fragment key={k}>
          <div className="datarow">
            {
              items.map((item, key) => (
                <DataItem openProgram={openProgram} key={key} items={item} program_setting={program_setting} />
              ))
            }
          </div>
        </React.Fragment>
      ))}
    </div>
  )
}

function currentTimerBar(data) {
  var _current = moment(data.current_time, 'HH:mm');
  var _time = moment('00:00:00', 'HH:mm');
  var _postion = 0;
  const eventduration = moment.duration(_current.diff(_time));
  const _difference = eventduration.asMinutes();
  const _timelinewrapp = document.getElementById('timelinewrapp');
  const _currentTimeline = document.getElementById('currentTimeline');
  const _timelindeschdle = document.getElementById('timelindeschdle');
  if (_difference >= 0) {
    _postion = (_multiplyer / 60) * _difference;
    if (_postion <= _timelinewrapp.offsetWidth) {
      _currentTimeline.style.left = _postion + 'px';
      _currentTimeline.style.display = 'block';
      _timelindeschdle.scrollLeft = _postion - 150;
      clearTime = setInterval(function () {
        _postion = (_postion + (_multiplyer / 3600))
        if (_postion <= _timelinewrapp.offsetWidth) {
          _currentTimeline.style.left = _postion + 'px'
        } else {
          _currentTimeline.style.display = 'none'
        }
      }, 1000)
    }
  }
}
const ThemeStyle = ({ primary_color }) => {
  return (
    <style dangerouslySetInnerHTML={{
      __html: `
    #range:focus::-webkit-slider-runnable-track {
        background: ${primary_color} !important;
        border-radius: 20px;
      }
      #range:active::-moz-range-track {
        background:${primary_color} !important;
        border-radius: 20px;
      }
      #range::-webkit-slider-runnable-track {
        background:${primary_color} !important;
      }
      #range::-webkit-slider-thumb {
        -webkit-appearance:none;
        border:1px solid ${primary_color} !important;
      }
      #range::-moz-range-track {
        background:${primary_color} !important;
      }
      #range::-moz-range-thumb {
        border:1px solid ${primary_color} !important;
      }
      #range::-ms-fill-lower {
        background:${primary_color} !important;
      }
      #range::-ms-fill-upper {
        background:${primary_color} !important;
      }
      #range::-ms-thumb {
        border:1px solid ${primary_color} !important;
      }
      #range:focus::-ms-fill-lower {
        background:${primary_color} !important;
      }
      #range:focus::-ms-fill-upper {
        background:${primary_color} !important;
      }
      #weekdays ul li.active, #weekdays ul li.active a {
          color: ${primary_color} !important;
        }
    
    `}} />
  )
}
class TimeSchedule extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      event: this.props.event,
      isLoading: false,
      date: '',
      data: false,
      filter_workshop: 'all',
      filter_tracks: 'all',
      range: 0,
      preLoader: true,
      program_array: [],
      program_setting: {},
      layoutView: this.props.event.settings.program_view === "vertical"
    }
    this.carouselLoader = this.carouselLoader.bind(this);
  }

  carouselLoader() {
    const carousel = document.querySelectorAll('#theCarousel ul')[0];
    const carousel_li = document.querySelectorAll('#theCarousel ul li');
    if (carousel_li.length > 6) {
      carousel.style.width = carousel_li.length * 75 + 'px';
      document.getElementById('weekdays').classList.remove('initialized');
      const parent = document.getElementById('theCarousel');
      const _visible = Math.round(Math.min(parent.getBoundingClientRect().width / 75));
      const _width = 75;
      carousel_li.forEach((element, i) => {
        var _item = element.classList.contains('active');
        if (_item && i > (_visible - 1)) {
          carousel.style.left = -(_width * (i - (_visible - 1))) + 'px';
        }
      })
    }
  }

  loadData() {
    this._isMounted = true;
    this.setState({ preLoader: true });
    service.post(`${process.env.REACT_APP_URL}/${this.state.event.url}/program/timetable`, this.state)
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              this.setState({
                isLoading: true,
                preLoader: false,
                program_array: response.data.program_array,
                program_setting: response.data.program_setting,
                data: this.state.layoutView ? response.data : itemSorting(response.data),
                event: (response.event ? response.event : this.state.event),
              }, () => {
                if (!this.state.layoutView) {
                  currentTimerBar(this.state.data);
                }
                const _end = moment(this.state.data.current_time, 'HH:mm:ss');
                const _last = moment('00:00:00', 'HH:mm:ss');
                const eventduration = moment.duration(_end.diff(_last));
                this.setState({
                  range: Number(Math.floor(Math.max(0, eventduration.asHours() - 1)))
                },() => {
                  window.addEventListener('resize', this.carouselLoader, false);
                  this.carouselLoader();
                })
              });
            }
          }
        },
        error => { }
      );
  }

  componentWillUnmount() {
    this._isMounted = false;
    window.removeEventListener('resize', this.carouselLoader, false);
  }

  componentDidMount() {
    this.loadData();
  }

  handleChange = (stateData) => (e) => {
    e.preventDefault();
    const _target = e.target.value;
    let newArray = this.state.data;
    newArray.program_array = [...this.state.program_array];
    var _filter = [];
    this.state.program_array.forEach(element => {
      if (stateData === 'filter_workshop') {
        if (this.state.filter_tracks === 'all') {
          if (element.workshop === _target || _target === 'all') {
            _filter.push(element);
          }
        } else {
          if ((element.workshop === _target || _target === 'all') && (element.tracks && element.tracks.indexOf(this.state.filter_tracks) > -1)) {
            _filter.push(element);
          }
        }
      } else {
        if (this.state.filter_workshop === 'all') {
          if ((element.tracks && element.tracks.indexOf(_target) > -1) || _target === 'all') {
            _filter.push(element);
          }
        } else {
          if (((element.tracks && element.tracks.indexOf(_target) > -1) || _target === 'all') && element.workshop === this.state.filter_workshop) {
            _filter.push(element);
          }
        }

      }
    });
    newArray = this.state.data;
    newArray.program_array = _filter;

    this.setState({
      data: this.state.layoutView ? newArray : itemSorting(newArray),
      [stateData]: _target
    })
  }

  handleCarousel = (position) => e => {
    e.preventDefault();
    const _width = 75;
    const carousel = document.querySelectorAll('#theCarousel ul')[0];
    const carousel_li = document.querySelectorAll('#theCarousel ul li');
    const parent = document.getElementById('theCarousel');
    parent.classList.add('disbaled');
    const _visible = Math.round(Math.min(parent.getBoundingClientRect().width / 75));;
    var style = getComputedStyle(carousel);
    var left = style.getPropertyValue("left").replace('px', '');
    var number = (carousel_li.length * _width) - (_width * _visible);
    if (position === 'back') {
      if (number - Number(left * -1) !== 0)
        carousel.style.left = (Number(left) - _width) + 'px';
    } else {
      if (Number(left) !== 0) {
        carousel.style.left = (Number(left) + _width) + 'px';
      }
    }
    setTimeout(() => {
      parent.classList.remove('disbaled');
    }, 100);
  }

  handleSlider = e => {
    this.setState({
      range: Number(e.target.value)
    })
    const _timelindeschdle = document.getElementById('timelindeschdle');
    _timelindeschdle.scrollLeft = e.target.value * _multiplyer
  }

  handleRangeButton = value => e => {
    e.preventDefault();
    const _timelindeschdle = document.getElementById('timelindeschdle');
    if (value === 'back' && this.state.range !== 0) {
      this.setState({
        range: this.state.range - 1
      }, () => {
        _timelindeschdle.scrollLeft = this.state.range * _multiplyer
      })
    } else if (value === 'forward' && this.state.range !== 23) {
      this.setState({
        range: this.state.range + 1
      }, () => {
        _timelindeschdle.scrollLeft = this.state.range * _multiplyer
      })
    }
  }

  handleClick = (selected_date) => (e) => {
    e.preventDefault();
    clearInterval(clearTime);
    this.setState({
      date: selected_date,
    });
  }

  componentDidUpdate(prevProps, prevState) {
    const { date } = this.state;
    if (date !== prevState.date) {
      this.loadData();
    }
  }

  openProgram = (program_id, count) => (e) => {
    if (Number(count) > 0) {
      store.dispatch(GeneralAction.video({ url: '', is_iframe: this.props.video.is_iframe ? 1 : 0, popover: false, current_video: this.props.video.current_video, agenda_id: this.props.video.agenda_id }));
      this.props.history.push(`/event/${this.state.event.url}/streaming/${Number(program_id)}`);
    }
  }

  render() {
    const _width = this.state.layoutView ? '100%' : 24 * _multiplyer;
    const _primaryColor = this.state.event.settings.primary_color;
    return (
      <React.Fragment>
        {this.state.preLoader && <Loader fixed="true" />}
        <div id="timelinearea" className="h-100 w-100">
          <ThemeStyle primary_color={_primaryColor} />
          <div id="timelinepanel">
            <div className="row d-flex">
              <div className="col-5">
                <div className="initialized" id="weekdays">
                  <div id="theCarousel">
                    <ul id="carouselInner" className="carousel-inner">
                      {this.state.data && this.state.data.schedules.map((date, k) => (
                        <li onClick={this.handleClick(date)} key={k} style={{ color: date === this.state.data.selected_date ? _primaryColor : '' }} className={date === this.state.data.selected_date ? 'item active' : 'item'}>
                          <strong>{moment(date).format("ddd")}</strong>
                          <span>{moment(date).format("DD/MM")}</span>
                        </li>
                      ))}
                    </ul>
                  </div>
                  <div className="arrowscarousel">
                    <button onClick={this.handleCarousel('forward')} className="range-slider-arrow-item back blur">&lt;</button>
                    <button onClick={this.handleCarousel('back')} className="range-slider-arrow-item forward">&gt;</button>
                  </div>
                </div>
                <div className="d-flex">
                  {this.state.data.program_workshops &&
                    <div style={{ marginRight: 20 }} className="filterarea d-flex">
                      <label>{this.state.event.labels.DESKTOP_APP_LABEL_FILTER_BY_WORKSHOPS}</label>
                      <select onChange={this.handleChange('filter_workshop')} autoComplete="off" className="custom-select" value={this.state.filter_workshop}>
                        <option value="all">{this.state.event.labels.DESKTOP_APP_LABEL_SEE_ALL}</option>
                        {this.state.data.program_workshops.map((option, k) => (
                          <option key={k} value={option}>{option}</option>
                        ))}
                      </select>
                    </div>}
                  {this.state.data.program_tracks &&
                    <div className="filterarea d-flex">
                      <label>{this.state.event.labels.DESKTOP_APP_LABEL_FILTER_BY_TRACKS}</label>
                      <select onChange={this.handleChange('filter_tracks')} autoComplete="off" className="custom-select" value={this.state.filter_tracks}>
                        <option value="all">{this.state.event.labels.DESKTOP_APP_LABEL_SEE_ALL}</option>
                        {this.state.data.program_tracks.map((option, k) => (
                          <option key={k} value={option.name}>{option.name}</option>
                        ))}
                      </select>
                    </div>}
                </div>
              </div>
              <div className="col-7">
                <div id="timeslider">
                  <div className={this.state.layoutView ? 'disabled' : ''} id="rangeslider">
                    <ul>
                      <li>02</li>
                      <li>05</li>
                      <li>08</li>
                      <li>11</li>
                      <li>14</li>
                      <li>17</li>
                      <li>20</li>
                      <li>23</li>
                    </ul>
                    <input onChange={this.handleSlider.bind(this)} type="range" id="range" min="0" max="23" value={this.state.range} />
                    <div className="range-slider-arrow">
                      <button onClick={this.handleRangeButton('back')} className="range-slider-arrow-item back">&lt;</button>
                      <button onClick={this.handleRangeButton('forward')} className="range-slider-arrow-item forward">&gt;</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="timelindeschdle">
            <div style={{ width: _width }} className={this.state.layoutView ? 'timeline-vertical' : ''} id="timelinewrapp">
              {this.state.layoutView ? (
                this.state.data.program_array && this.state.isLoading && <VerticalView openProgram={this.openProgram} data={this.state.data.program_array} program_setting={this.state.program_setting} />
              ) : (
                  <React.Fragment>
                    <div style={{ background: _primaryColor }} id="currentTimeline"></div>
                    <TimelineHeader />
                    {this.state.data.program_array && this.state.isLoading && <TimelineContent openProgram={this.openProgram} data={this.state.data.program_array} program_setting={this.state.program_setting} />}
                  </React.Fragment>
                )}
            </div>
          </div>
        </div>
      </React.Fragment>
    )
  }
}

function mapStateToProps(state) {
  const { event, video } = state;
  return {
    event, video
  };
}

export default connect(mapStateToProps)(TimeSchedule);