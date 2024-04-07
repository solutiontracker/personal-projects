import * as React from 'react';
import { withRouter } from "react-router-dom";
import RouterOutlet from 'router/RouterOutlet.jsx';
import 'bootstrap/dist/js/bootstrap';
import { Preloader, Placeholder } from 'react-preloading-screen';
import HttpsRedirect from 'react-https-redirect';
import ReactGA from 'react-ga';

class App extends React.Component {
  render() {
    if (process.env.REACT_APP_ENVIRONMENT === "live") {
      ReactGA.initialize('UA-72012828-4');
      ReactGA.pageview(window.location.pathname + window.location.search);
    }
    return (
      <HttpsRedirect disabled={process.env.REACT_APP_SSL === 'false' ? true : false}>
        <Preloader>
          <RouterOutlet />
          <Placeholder>
            <span>Loading...</span>
          </Placeholder>
        </Preloader>
      </HttpsRedirect>
    );
  }
}
export default withRouter(App);

