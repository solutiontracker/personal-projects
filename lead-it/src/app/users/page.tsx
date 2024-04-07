'use client'
import React, { useEffect } from 'react';
import Dropdown from '@/app/components/DropDown';
import Template from '@/app/components/Template';
import Image from 'next/image';

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
            <div className="col-4">
              <div className="ebs-title-section">
                <h4>Lead users</h4>
              </div>
            </div>
            <div className="col-8">
              <div className="right-top-header">
                <button className="btn btn-default">
                  <i className="material-symbols-outlined">add</i> Add user
                </button>
              </div>
            </div>
          </div>
        </div>
        <div style={{ background: "#fff" }} className="main-data-table">
          <div className="ebs-order-list-section">
            <div className="ebs-order-header">
              <div className="row">
                <div className="col-8 d-flex">
                  <input type="text" className="ebs-search-area" defaultValue="Search" />
                  <label style={{ width: "250px" }} className="label-select-alt">
                    <Dropdown
                      label="Select type"
                      listitems={[
                        { id: "active_future", name: "Active and future events" },
                        { id: "active", name: "Active events" },
                        { id: "future", name: "Future events" },
                        { id: "expired", name: "Expired events" },
                        { id: "name", name: "All events" },
                      ]}
                    />
                  </label>
                </div>
                <div className="col-4 d-flex justify-content-end align-items-center">
                  <div onClick={(e) => e.stopPropagation()} className="ebs-dropdown-area">
                    <button onClick={handleToggle} className="ebs-btn-dropdown btn-select">
                      2 <i className="material-symbols-outlined">expand_more</i>
                    </button>
                    <div className="ebs-dropdown-menu">
                      <button className="dropdown-item">10</button>
                      <button className="dropdown-item">20</button>
                      <button className="dropdown-item">500</button>
                      <button className="dropdown-item">1000</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div className="ebs-data-table ebs-order-table">
              <div className="d-flex align-items-center ebs-table-header">
                <div className="ebs-table-box ebs-box-1"><strong>Name</strong></div>
                <div className="ebs-table-box ebs-box-4"><strong>Email</strong></div>
                <div className="ebs-table-box ebs-box-2"><strong>Type</strong></div>
                <div className="ebs-table-box ebs-box-2"><strong>Status</strong></div>
                <div className="ebs-table-box ebs-box-3"  />
              </div>
              {[...Array(20)].map((item,k) => 
              <div key={k} className="d-flex align-items-center ebs-table-content">
                <div className="ebs-table-box ebs-box-1"><div className="ebs-avatar-name d-flex align-items-center"><Image className='rounded' src={require('@/app/assets/img/logo-sm.svg')} alt="" width="35" height="35"  /><span>Judi Jane</span></div></div>
                <div className="ebs-table-box ebs-box-4"><p>demo456@eventbuizz.com</p></div>
                <div className="ebs-table-box ebs-box-2">
                  <div className="ebs-status ebs-status-success"><i className="material-icons">check_circle</i> Active</div>
                </div>
                <div className="ebs-table-box ebs-box-2">
                  <div className="ebs-status ebs-status-warning">Pending</div>
                </div>
                <div className="ebs-table-box ebs-box-3 d-flex justify-content-end">
                  <ul className='d-flex ebs-panel-list m-0'>
                    <li>
                      <button className='ebs-btn-panel'>
                        <span className="material-symbols-outlined">edit</span>
                      </button>
                    </li>
                    <li>
                      <button className='ebs-btn-panel'>
                        <span className="material-symbols-outlined">delete</span>
                      </button>
                    </li>
                    <li>
                      <button className='ebs-btn-panel ebs-button-more'>
                        <span className="material-symbols-outlined">chevron_right</span>
                      </button>
                    </li>
                  </ul>
                </div>
              </div>)}
            </div>
          </div>
        </div>
      </div>
    </Template>
  );
}
