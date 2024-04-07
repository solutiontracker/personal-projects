"use client"; // this is a client component
import { useState } from "react";
import Image from 'next/image';

const InputField = ({title}) => {
  const [password, setPassword] = useState('')
  const [passwordType, setpasswordType] = useState(true)
  const handleShowPass = (e:any) => {
    e.stopPropagation();
    setpasswordType(!passwordType)
  } 
  return (
    <>
      <label className="field-title">{title}</label>
      <div className='form-row-box'>
          <span className="icon-eye">
            <Image onClick={handleShowPass} src={require(`@/app/assets/img/${passwordType ? 'close-eye':'icon-eye'}.svg`)} width="17" height="17" alt="" />
          </span>
          <input placeholder="Password" className={password ? 'ieHack': ''} type={passwordType ? 'password' : 'text'} value={password} id="password" onChange={(e) => setPassword(e.target.value)}  />
      </div>
    </>
  )
}

export default function forgetPassword() {
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
                      <h2 className="text-uppercase">Forget Password</h2>
                      <div className="alert alert-success d-flex align-items-center gx-3" role="alert">
                        <span className="material-symbols-outlined">lock</span>
                        <div style={{paddingLeft: '10px', fontSize: '14px', maxWidth: '395px'}}>
                          Please create new strong password that includes numbers, letters and punctuation marks.
                        </div>
                      </div>
                      <form role="">
                      <div className="form-area-signup">
                          <InputField title="Create new password" />
                          <InputField title="Confirm new password" />
                          <div className="form-row-box button-panel">
                              <button className="btn btn-primary">Reset password</button>
                              <p>Have an account? <a href="">Login</a></p>
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
