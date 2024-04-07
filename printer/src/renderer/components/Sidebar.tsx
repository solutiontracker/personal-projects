/* eslint-disable global-require */
import {  useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { LoginResponseData } from '../types/LoginDataTypes';

const Sidebar = ({connectToServer}:{connectToServer:boolean}) => {
  const navigate = useNavigate();
  const [info, setInfo] = useState<null|LoginResponseData >(null);
  useEffect(() => {
    const tok = JSON.parse(localStorage.getItem('data') || '{}');
    console.log(tok)
    if (tok && tok.status === 1) {
      setInfo(tok.data);
    }
  }, []);

  const onLogout= (event:React.MouseEvent<HTMLAnchorElement, MouseEvent>) =>{
    event.preventDefault();
    localStorage.removeItem('data');
    localStorage.removeItem('terminal');
    localStorage.removeItem('printer');
    localStorage.removeItem('queueRange');
    const path = `/`;
    navigate(path);
  }

  return (
    <>
      <div className="info-section">
        <div className="top-logo">
          <img src={require('../img/logo-dark.svg')} alt="" />
        </div>
        <div className="user-info">
          <div className="info-name">
            {info && <div className="name">{info.user && info.user.name}</div>}
            {connectToServer ? (
              <img src={require('../img/connected.svg')} alt="connected" />
            ) : (
              <img src={require('../img/disconnected.svg')} alt="connected" />
            )}
          </div>
        </div>
        <div className="navigation">
          <ul>
            <li>
              <Link className="print" to="/dashboard">
                Badge printing
              </Link>
            </li>
            <li>
              <Link className="history" to="/dashboard/history">
                Printing Histrory
              </Link>
            </li>
            <li>
              <Link className="fonts" to="/dashboard/fonts">
                Fonts
              </Link>
            </li>
          </ul>
        </div>
        <div className="bottom-logout">
          <a
            href="#!"
            onClick={(event) => {
              onLogout(event);
            }}
          >
            Logout
          </a>
        </div>
        <div className='version-bottom'>
          <p className='list'>Version: 1.0.9</p>
        </div>
      </div>
    </>
  );
};

export default Sidebar;
