import * as React from "react";
import { Translation } from "react-i18next";
import Listing from "@/app/reports/Listing";
import { connect } from 'react-redux';

class ReportWidget extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      largeScreen: false,
      _url: `${process.env.REACT_APP_URL}/attendee/listing`,
      _export_url: `${process.env.REACT_APP_URL}/attendee/export`,
      status: '',
      type: 'registration-sign-ups',
      report: 'registration-sign-up-list',
      height: '550px'
    }
  }
  componentWillMount() {
    this.setState({ height: (window.innerHeight - 300) + 'px' });
  }
  handleLargeScreen = (_url, status, type, report, _export_url) => e => {
    e.preventDefault();
    this.setState({
      largeScreen: !this.state.largeScreen,
      _url: _url,
      _export_url: _export_url,
      status: status,
      type: type,
      report: report
    });
  }

  render() {
    return (
      <Translation>
        {(t) => (
          <div style={{minHeight: this.state.height}} className="wrapper-content third-step">
            {this.state.largeScreen ? (
              <div className="wrapper-import-file-wrapper">
                <div className="wrapper-import-file inline-popup-records">
                  <div style={{ height: '100%', display: 'flex', flexDirection: 'column' }}>
                    <div className="top-popuparea">
                      <Listing handleLargeScreen={this.handleLargeScreen} largeScreen={this.state.largeScreen} _url={this.state._url} status={this.state.status} type={this.state.type} report={this.state.report} _export_url={this.state._export_url} />
                    </div>
                  </div>
                </div>
              </div>
            ) : (
                <Listing handleLargeScreen={this.handleLargeScreen} largeScreen={this.state.largeScreen} _url={this.state._url} status={this.state.status} type={this.state.type} report={this.state.report} _export_url={this.state._export_url} />
              )}
          </div>
        )}
      </Translation>
    )
  }
}
function mapStateToProps(state) {
  const { event } = state;
  return {
    event
  };
}

export default connect(mapStateToProps)(ReportWidget);