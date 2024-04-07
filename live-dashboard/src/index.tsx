import React from 'react';
import ReactDOM from 'react-dom';
import '@/src/sass/app.scss';
import {BrowserRouter} from 'react-router-dom';
import App from '@/src/app/App';

ReactDOM.render(<BrowserRouter><App /></BrowserRouter>,document.getElementById('root'));
