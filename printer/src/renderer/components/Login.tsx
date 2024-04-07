/* eslint-disable global-require */
import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';

const Login = () => {

  const [email, setEmail] = useState('');

  const [erroremail, setErroremail] = useState(false);

  const [loading, setLoading] = useState(false);

  const [server, setServer] = useState(false);

  const [password, setPassword] = useState('');

  const [errorpassword, setErrorpassword] = useState(false);

  const [eventID, setEventID] = useState('');

  const [erroreventID, setErroreventID] = useState(false);

  const navigate = useNavigate();

  const signIn = () => {

    if (email === '') {
      setErroremail(true);
    } else {
      setErroremail(false);
    }

    if (password === '') {
      setErrorpassword(true);
    } else {
      setErrorpassword(false);
    }

    if (eventID === '') {
      setErroreventID(true);
    } else {
      setErroreventID(false);
    }

    if (
      !erroremail &&
      email !== '' &&
      !errorpassword &&
      password !== '' &&
      !erroreventID &&
      eventID !== ''
    ) {
      setLoading(true);
      const requestOptions = {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          event_id: eventID,
          email,
          password,
        }),
      };
      fetch(
        // `${process.env.REACT_APP_API_URL}/api/v2/auth/badges/login`,
        `https:apidev.eventbuizz.com/api/v2/auth/badges/login`,
        requestOptions
      )
        .then((response) => response.json())
        .then((data) => {
          if (data.status === 0) {
            setServer(data.message);
          } else {
            localStorage.setItem('data', JSON.stringify(data));
            setServer(false);
            const path = `/dashboard`;
            navigate(path);
          }
          setLoading(false);
        })
        .catch((error) => {
          console.log(error);
        });
    }
  };

  useEffect(() => {
    const tok = JSON.parse(localStorage.getItem('data') || '{}');
    if (tok && tok.status === 1) {
      const path = `/dashboard`;
      navigate(path);
    }
  }, [navigate]);

  const handleFunction = (type: string, val: string) => {
    if (type === 'email') {
      setEmail(val);
    } else if (type === 'password') {
      setPassword(val);
    } else if (type === 'event-id') {
      setEventID(val);
    }
  };

  return (
    <div className="wrapper">
      <div className="login-screen">
        <div className="left-login">
          <img id="logo" src={require('../img/logo.svg')} alt="" />
          <div className="login-caption">
            <h3>With Eventbuizz get your events Reinvented.</h3>
            <p>One app for everything</p>
          </div>
          <div className="bottom-login">
            <ul>
              <li>&copy; 2022 Eventbuizz </li>
              <li>
                <a href="#!">Privacy policy</a>
              </li>
            </ul>
          </div>
        </div>
        <div className="right-login">
          <div className="login-form">
            <h2>Login Account</h2>
            {server && (
              <p style={{ textAlign: 'center' }} className="error">
                {server}
              </p>
            )}
            <div className="login-item">
              <label htmlFor="login-email">
                <span>Email</span>
                <input
                  onChange={(e) => handleFunction('email', e.target.value)}
                  type="email"
                  value={email}
                  name=""
                  id="login-email"
                />
              </label>
              {erroremail && (
                <div className="error">Please Enter a valid email address</div>
              )}
            </div>
            <div className="login-item">
              <label htmlFor="login-password">
                <span>Password</span>
                <input
                  onChange={(e) => handleFunction('password', e.target.value)}
                  value={password}
                  type="password"
                  name=""
                  id="login-password"
                />
              </label>
              {errorpassword && (
                <div className="error">Please Enter a password</div>
              )}
            </div>
            <div className="login-item">
              <label htmlFor="login-text">
                <span>Event code</span>
                <input
                  onChange={(e) => handleFunction('event-id', e.target.value)}
                  value={eventID}
                  type="text"
                  name=""
                  id="login-text"
                />
              </label>
              {erroreventID && (
                <div className="error">Please Enter a valid event id</div>
              )}
            </div>
            <div className="login-item">
              <button onClick={signIn} type="button">
                {loading ? 'Loading...' : 'SIGN IN '}
              </button>
            </div>
            <div className="login-item">
              <p className="ebs-need-help">
                Trouble singing in ? <a href="#!">need help</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );

};
export default Login;
