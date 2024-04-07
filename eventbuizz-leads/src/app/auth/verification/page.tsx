"use client"; // this is a client component
import { useState } from "react";
import Image from 'next/image';


const languages = [{ id: 1, name: "English" }, { id: 2, name: "Danish" }];

export default function Verification() {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [passwordType, setpasswordType] = useState(true)
  const handleShowPass = (e:any) => {
    e.stopPropagation();
    setpasswordType(!passwordType)
  } 
  return (
    <div className="signup-wrapper">
      <main className="main-section" role="main">
        <div className="container-fluid">
          <div className="wrapper-box">
            <div className="container-box">
              <div className="row">
                <div className="col-6">
                  <div className="left-signup d-flex align-items-center justify-content-center">
                    <div className="text-block text-center">
                      <Image src={require('@/app/assets/img/ico-team-logo.svg')} alt="" width="120" height="120" className='logos' />
                      <h4 className="text-uppercase">Leads User</h4>
                      <p> Leads user portal is an application that helps businesses generate and manage leads for their products or services.</p>
                    </div>
                  </div>
                </div>
                <div className="col-6">
                  <div className="right-section-blank">
                    <div className="right-formarea">
                      <div className="text-center ebs-verification-code">
                        <h2 className="text-uppercase">Verification Code</h2>
                        <p>Enter verification code below we sent to email</p>
                        <p><strong>demo123@eventbuizz.com</strong></p>
                      </div>
                      <form role="">
                        <div className="form-area-signup">
                          <div className="d-flex align-items-center ebs-verification-code-fields">
                            <input type="text" />
                            <input type="text" />
                            <input type="text" />
                            <input type="text" />
                          </div>
                          <div className="text-center ebs-count-down">
                            <div className="ebs-timer">04:34</div>
                            <span className="btn-resend">Resend Code</span>
                          </div>
                          <div className="form-row-box button-panel">
                              <button className="btn btn-primary">Verify</button>
                              <button className="btn btn-bordered">Cancel</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
