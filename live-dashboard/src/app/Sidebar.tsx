import React, { ReactElement, FC } from 'react';
import { NavLink  } from 'react-router-dom';

type Props = Record<string, never>;

const App: FC<Props> = (): ReactElement => {
  return (
    <div className="ebs-main-sidebar">
      <div className="ebs-sidebar-top">
        <div className="ebs-logo">
          <img src={require('../img/img-logo.svg')} alt="" />
        </div>
        <div className="ebs-mode">
          <span className="ebs-mode-title">Event mode</span>
        </div>
      </div>
      <div className="ebs-sidebar-bottom">
        <ul>
          <li>
            <NavLink exact to="/" activeClassName="selected">Polls and Surveys</NavLink>
          </li>
          <li>
            <NavLink exact to="/speaker-list" activeClassName="selected">Speakers list</NavLink>
          </li>
        </ul>
      </div>      
    </div>
  );
};

export default App;