import React, { ReactElement, FC } from "react";
import { BrowserRouter, Route, Switch } from 'react-router-dom';
import Sidebar from "../app/Sidebar";
import PollsSurvey from "../app/PollsSurvey";
import FutureComponent from "../app/FutureComponent";

const RouterOutlet: FC<any> = (): ReactElement => {

  return (
    <BrowserRouter>
      <Sidebar />
      <Switch>
        <Route path="/" component={PollsSurvey} exact />
        <Route path="/speaker-list" component={FutureComponent} exact />
      </Switch>
    </BrowserRouter>
  );
};

export default RouterOutlet;