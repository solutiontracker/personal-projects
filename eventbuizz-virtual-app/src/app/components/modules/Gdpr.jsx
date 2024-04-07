import React, { Component } from 'react';
import ReactHtmlParser from 'react-html-parser';
class Gdpr extends Component {
  render() {
    return (
      <div className="app-popup-wrapper">
        <div className="app-popup-container">
          <header
            style={{ backgroundColor: this.props.event.settings.primary_color }}
            className="app-popup-header"
          >
            {this.props.event.gdpr.subject}
          </header>
          <div className="app-popup-pane">
            <div className="gdpr-popup-sec">
              {ReactHtmlParser(this.props.event.gdpr.description)}
            </div>
          </div>
          <div className="app-popup-footer">
            <button onClick={this.props.handleClick('cancel')} style={{ backgroundColor: this.props.event.settings.primary_color }} className="btn">{this.props.event.labels.GDPR_CANCEL || 'Cancel'}</button>
            <button onClick={this.props.handleClick('accept')} style={{ backgroundColor: this.props.event.settings.primary_color }} className="btn">{this.props.event.labels.GDPR_ACCEPT || 'Accept'}</button>
          </div>
        </div>
      </div>
    )
  }
}

export default Gdpr;
