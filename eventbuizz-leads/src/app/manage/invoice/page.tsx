'use client'
import React, { useEffect } from 'react';
import Image from 'next/image'
import Dropdown from '@/app/components/DropDown';

export default function Invoice() {
  
  return (
    <>
      <header className="header">
        <div className="container">
          <div className="row bottom-header-elements">
            <div className="col-8">
              <div className="ebs-bottom-header-left">
                <p>
                  <a href="#!">
                    <i className="material-icons">arrow_back</i> Return to list
                  </a>
                </p>
                <h3>
                  <a href="#!">Parent event lead 2.0</a>
                </h3>
                <ul>
                  <li>
                    <i className="material-symbols-outlined">calendar_month</i>16 Oct 2023 - 20 Oct 2023
                  </li>
                  <li>
                    <i className="material-symbols-outlined">place</i>Pakistan, Lahore
                  </li>
                </ul>
              </div>
            </div>
            <div className="col-4 d-flex justify-content-end">
              <ul className="main-navigation">
                <li>
                  Irfan Danish <i className="material-icons">expand_more</i>
                  <ul>
                    <li>
                      <a href="">My account</a>
                    </li>
                    <li>
                      <a href=""> Change password</a>
                    </li>
                    <li>
                      <a href="">Logout</a>
                    </li>
                  </ul>
                </li>
                <li>
                  English <i className="material-icons">expand_more</i>
                  <ul>
                    <li>
                      <a href="">English</a>
                    </li>
                    <li>
                      <a href=""> Danish</a>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </header>
      <main className="main-section" role="main">
        <div className="container">
          <div className="wrapper-box">
            <div className="container-box main-landing-page">
              <div className="top-landing-page">
                <div className="row d-flex">
                  <div className="col-4">
                    <div className="logo">
                      <a href="">
                        <Image
                          src={require("@/app/assets/img/logo.svg")}
                          alt=""
                          width="200"
                          height="29"
                          className="logos"
                        />
                      </a>
                    </div>
                  </div>
                  <div className="col-8">
                    <div className="right-top-header">
                      <button className="btn btn-default">
                        <i className="material-symbols-outlined">sim_card_download</i> PDF
                      </button>
                      <button className="btn btn-default btn-send-order">
                        <i className="material-symbols-outlined">send</i> Send Order
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div style={{ background: "#fff" }} className="main-data-table">
                <p>Invoice</p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </>
  );
}
