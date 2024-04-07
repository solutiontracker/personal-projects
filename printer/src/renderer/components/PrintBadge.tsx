/* eslint-disable */
// @ts-ignore
import React, { FC, useState, useEffect } from 'react';
import '../App.css';
const PrintBadge = ({data} :{ data:any}) => {
  return (
    <>
      <div className="App">
        <div id="body" style={{ display: 'inline-block', background: 'white' }} className="printElement1" dangerouslySetInnerHTML={{ __html: data }} />
      </div>
    </>
  );
};
export default PrintBadge;
