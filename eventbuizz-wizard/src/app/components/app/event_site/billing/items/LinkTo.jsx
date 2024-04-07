import React, { Component } from "react";
import 'sass/billing.scss';
import { service } from "services/service";
import { Translation } from "react-i18next";
import Loader from '@/app/forms/Loader';

const in_array = require("in_array");

const _links = ["program", "track", "workshop", "attendee_group"];

export default class LinkTo extends Component {

  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      query: '',
      link_to_id: this.props.link_to_id,
      billing_item_type: this.props.billing_item_type,
      data: [],

      //pagination
      limit: 10,
      total: '',

      //errors & loading
      preLoader: true,

      typing: false,
      typingTimeout: 0,

      message: false,
      success: true,
    }
  }

  componentDidMount() {
    const _body = document.getElementsByTagName('body');
    _body[0].classList.add('noscroll');
    this._isMounted = true;
    this.listing();
  }

  componentWillUnmount() {
    const _body = document.getElementsByTagName('body');
    _body[0].classList.remove('noscroll');
    this._isMounted = false;
  }

  handlePageChange = (activePage) => {
    this.listing(activePage);
  }

  listing = (activePage = 1, loader = false, type = "save") => {
    this.setState({ preLoader: (!loader ? true : false) });
    service.post(`${process.env.REACT_APP_URL}/eventsite/billing/items/link-to-search`, this.state).then(
      response => {
        if (response.success) {
          if (this._isMounted) {
            this.setState({
              data: response.data,
              activePage: response.data.current_page,
              total: response.data.total,
              preLoader: false
            });
          }
        }
      },
      error => { });
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

  handleClick = (input, value, type) => e => {
    if (type === "radio") {
      this.setState({
        [input]: (this.state[input] === value ? 0 : value)
      });
    } else {
      if (in_array(value, this.state.link_to_id)) {
        const link_to_id = this.state.link_to_id;
        var index = link_to_id.indexOf(value);
        link_to_id.splice(index, 1);
        this.setState({ link_to_id });
      } else {
        const link_to_id = this.state.link_to_id;
        link_to_id.push(value);
        this.setState({ link_to_id });
      }
    }
  };

  apply = e => {
    var link_to_id = this.state.link_to_id;
    var link_to_names = "";
    var link_to = "";
    if (Number(this.props.billing_item_type) === 3) {
      link_to_names = this.state.data.filter(function (row, i) {
        return in_array(row.id, link_to_id);
      });
      link_to = (link_to_id.length > 0 ? _links[Number(this.props.billing_item_type)] : "none");
      link_to_names = Array.prototype.map.call(link_to_names, function (item) { return item.name; }).join(",");
    } else {
      link_to_names = this.state.data.filter(function (row, i) {
        return Number(row.id) === Number(link_to_id);
      });
      link_to = (link_to_id && Number(link_to_id) !== 0 ? _links[Number(this.props.billing_item_type)] : "none");
      if (Number(this.props.billing_item_type) === 0) {
        link_to_names = Array.prototype.map.call(link_to_names, function (item) { return item.topic; }).join(",");
      } else {
        link_to_names = Array.prototype.map.call(link_to_names, function (item) { return item.name; }).join(",");
      }
    }
    this.props.linkTo(this.state.link_to_id, link_to_names, link_to);
  };

  render() {
    const Programs = ({ data }) => {
      return (
        data.map((data, key) => {
          return (
            <React.Fragment key={key}>
              {data.heading_date && (
                <div className="program-by-date">
                  <h4>{data.heading_date}</h4>
                </div>
              )}
              <div className="program-by-date">
                <div className="list-program">
                  <div className={`icons ${Number(data.disabled) === 1 && Number(data.id) !== Number(this.props.link_to_id) ? "disabled" : ""}`} style={{ cursor: "pointer" }} onClick={this.handleClick('link_to_id', data.id.toString(), "radio")}>
                    <i className="material-icons">{Number(this.state.link_to_id) === Number(data.id) ? 'radio_button_checked' : 'radio_button_unchecked'}</i>
                  </div>
                  {data.start_time && <div className="program-time">{data.start_time + ' - ' + data.end_time}</div>}
                  <div className="program-title">{data.topic}</div>
                </div>
              </div>
            </React.Fragment >
          )
        })
      )
    }

    const Tracks = ({ data }) => {
      return (
        data.map((data, key) => {
          return (
            <React.Fragment key={key}>
              <div className="program-by-date">
                {data.parent ? (
                  <React.Fragment>
                    <div style={{ cursor: "pointer" }} className={`icons ${Number(data.disabled) === 1 && Number(data.id) !== Number(this.props.link_to_id) ? "disabled" : ""}`} onClick={this.handleClick('link_to_id', data.id.toString(), "radio")}>
                      <i className="material-icons">{Number(this.state.link_to_id) === Number(data.id) ? 'radio_button_checked' : 'radio_button_unchecked'}</i>
                    </div>
                    <h4>{data.name}</h4>
                  </React.Fragment>
                ) : (
                    <div className="list-program">
                      <div style={{ cursor: "pointer" }} className={`icons ${Number(data.disabled) === 1 && Number(data.id) !== Number(this.props.link_to_id) ? "disabled" : ""}`} onClick={this.handleClick('link_to_id', data.id.toString(), "radio")}>
                        <i className="material-icons">{Number(this.state.link_to_id) === Number(data.id) ? 'radio_button_checked' : 'radio_button_unchecked'}</i>
                      </div>
                      <div className="program-title">{data.name}</div>
                    </div>
                  )}
              </div>
            </React.Fragment>
          )
        })
      )
    }

    const Workshops = ({ data }) => {
      return (
        data.map((data, key) => {
          return (
            <React.Fragment key={key}>
              <div className="program-by-date">
                <div className="list-program">
                  <div style={{ cursor: "pointer" }} className={`icons ${Number(data.disabled) === 1 && Number(data.id) !== Number(this.props.link_to_id) ? "disabled" : ""}`} onClick={this.handleClick('link_to_id', data.id.toString(), "radio")}>
                    <i className="material-icons">{Number(this.state.link_to_id) === Number(data.id) ? 'radio_button_checked' : 'radio_button_unchecked'}</i>
                  </div>
                  <div className="program-title">{data.name}</div>
                </div>
              </div>
            </React.Fragment>
          )
        })
      )
    }

    const AttendeeGroups = ({ data }) => {
      return (
        data.map((data, key) => {
          return (
            <React.Fragment key={key}>
              <div className="program-by-date">
                {Number(data.disabled) === 1 ? (
                  <h4>{data.name}</h4>
                ) : (
                    <div className="list-program">
                      <div style={{ cursor: "pointer" }} className="icons" onClick={this.handleClick('link_to_id', data.id.toString(), "checkbox")}>
                        <i className="material-icons">{(in_array(data.id, this.state.link_to_id) ? "radio_button_checked" : "radio_button_unchecked")}</i>
                      </div>
                      <div className="program-title">{data.name}</div>
                    </div>
                  )}
              </div>
            </React.Fragment>
          )
        })
      )
    }

    return (
      <Translation>
        {
          t =>
            <div className="wrapper-popup popup-program-session">
              <div className="wrapper-sidebar">
                {this.state.preLoader &&
                  <Loader />
                }
                {!this.state.preLoader && (
                  <React.Fragment>
                    <header>
                      <h3>
                        {
                          (() => {
                            if (Number(this.state.billing_item_type) === 0)
                              return t("BILLING_ITEMS_SELECT_PROGRAM");
                            else if (Number(this.state.billing_item_type) === 1)
                              return t("BILLING_ITEMS_SELECT_TRACK");
                            else if (Number(this.state.billing_item_type) === 2)
                              return t("BILLING_ITEMS_SELECT_WORKSHOP");
                            else if (Number(this.state.billing_item_type) === 3)
                              return t("BILLING_ITEMS_SELECT_ATTENDEE_GROUP");
                          })()
                        }
                      </h3>
                    </header>
                    <div className="bottom-content">
                      <div className="new-header">
                        <input value={this.state.query} name="query" type="text"
                          placeholder={t('G_SEARCH')} onChange={this.onFieldChange.bind(this)}
                        />
                      </div>
                      <div className="select-program-sessions">
                        {this.state.data.length > 0 && Number(this.state.billing_item_type) === 0 && (
                          <Programs data={this.state.data} />
                        )}
                        {this.state.data.length > 0 && Number(this.state.billing_item_type) === 1 && (
                          <Tracks data={this.state.data} />
                        )}
                        {this.state.data.length > 0 && Number(this.state.billing_item_type) === 2 && (
                          <Workshops data={this.state.data} />
                        )}
                        {this.state.data.length > 0 && Number(this.state.billing_item_type) === 3 && (
                          <AttendeeGroups data={this.state.data} />
                        )}
                      </div>
                    </div>
                    <div className="bottom-component-panel clearfix">
                      <button onClick={this.props.onClose} style={{ minWidth: '124px' }} data-type="save" className="btn btn btn-save">
                        {t("G_CANCEL")}
                      </button>
                      <button data-type="save-next" className="btn btn-save-next" onClick={this.apply}>
                        {t("G_SAVE")}
                      </button>
                    </div>
                  </React.Fragment>
                )}
              </div>
            </div>
        }
      </Translation>
    );
  }
}
