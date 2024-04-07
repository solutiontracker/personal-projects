'use client'
import React, { useEffect } from 'react';
import Image from 'next/image'
import Dropdown from '@/app/components/DropDown';
import Header from '@/app/components/Header';
import Template from '@/app/components/Template';

export default function OrderListing() {
  
  useEffect(() => {
    document.body.addEventListener('click',handleBody,false)
    return () => {
      document.body.removeEventListener('click',handleBody,false)
    }
  }, [])
  
  const handleBody = (e:any) => {
    var _items = document.querySelectorAll('.ebs-btn-dropdown');
    _items.forEach(element => {
      element.classList.remove('ebs-active')
    });
  }
  const handleToggle = (e:any) => {
    e.stopPropagation();
    e.preventDefault();
    e.target.classList.toggle('ebs-active');
  }
  return (
    <Template>
      <div className="container-box main-landing-page">
        <div className="top-landing-page">
          <div className="row d-flex">
            <div className="col-12">
              <div className="ebs-title-section">
                <h4>Create event</h4>
              </div>
            </div>
          </div>
        </div>
          <div className="ebs-create-order-sec">
            <div className="ebs-create-order-form">
              <div className="row d-flex align-items-center">
                <div className="col-label-field"><label className='ebs-label'>Event name</label></div>
                <div className="col-input-field"><input type="text"  /></div>
              </div>
              <div className="row d-flex align-items-center">
                <div className="col-label-field"><label className='ebs-label'>Start date</label></div>
                <div className="col-input-field"><input type="text"  /></div>
              </div>
              <div className="row d-flex align-items-center">
                <div className="col-label-field"><label className='ebs-label'>End date</label></div>
                <div className="col-input-field"><input type="text"  /></div>
              </div>
              <div className="row d-flex align-items-center">
                <div className="col-label-field"><label className='ebs-label'>Location</label></div>
                <div className="col-input-field"><input type="text"  /></div>
              </div>
            </div>
            <div className="ebs-button-panel">
              <button className='btn btn-cancel'>Cancel</button>
              <button className='btn'>Save</button>
            </div>
        </div>
      </div>
    </Template>
  );
}
