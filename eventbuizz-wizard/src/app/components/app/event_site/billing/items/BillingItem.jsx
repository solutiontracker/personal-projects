import * as React from "react";
import { Translation } from "react-i18next";
import Listing from "@/app/event_site/billing/items/Listing";
import { connect } from 'react-redux';
import 'sass/billing.scss';

class BillingItem extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      largeScreen: false,
    }
  }

  handleLargeScreen = e => {
    e.preventDefault();
    this.setState({
      largeScreen: !this.state.largeScreen
    })
  }

  render() {
    return (
      <Translation>
        {(t) => (
          <div className="wrapper-content third-step main-billing-page">
            {this.state.largeScreen ? (
              <div className="wrapper-import-file-wrapper">
                <div className="wrapper-import-file inline-popup-records">
                  <div style={{ height: '100%', display: 'flex', flexDirection: 'column' }}>
                    <div className="top-popuparea">
                      <Listing handleLargeScreen={this.handleLargeScreen} largeScreen={this.state.largeScreen} />
                    </div>
                  </div>
                </div>
              </div>
            ) : (
                <Listing handleLargeScreen={this.handleLargeScreen} largeScreen={this.state.largeScreen} />
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

export default connect(mapStateToProps)(BillingItem);