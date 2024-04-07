/* eslint-disable */
// @ts-ignore
import React, { useState } from 'react';
import { HashRouter as Router, Routes, Route } from 'react-router-dom';
import './sass/main.scss';
import Login from './components/Login';
import BadgePrinting from './components/BadgePrinting';
import InstallFonts from './components/InstallFonts';
import PrintingHistory from './components/PrintingHistory';
import Dashboard from './components/Dashboard';

export default function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Login />} />
        <Route path="/dashboard" element={<Dashboard />} >
          <Route path="" element={<BadgePrinting />} />
          <Route path="fonts" element={<InstallFonts />} />
          <Route path="history" element={<PrintingHistory />} />
        </Route>
      </Routes>
    </Router>
  );
}
