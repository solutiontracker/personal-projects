"use client"; // this is a client component
import { useState } from "react";
import Image from 'next/image';
import Illustration from '@/app/assets/img/illustration.png'


const languages = [{ id: 1, name: "English" }, { id: 2, name: "Danish" }];

export default function Login() {
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
                      <h2 className="text-uppercase">Sign in</h2>
                      <form role="">
                      <div className="form-area-signup">
                          <label className="field-title">Email</label>
                          <div className='form-row-box'>
                            <input placeholder="demo123@email.com" className={email ? 'ieHack': ''} value={email} type="text" name="email" id="email" onChange={(e) => setEmail(e.target.value)}  />
                          </div>
                          <label className="field-title">Password</label>
                          <div className='form-row-box'>
                              <span className="icon-eye">
                                <Image onClick={handleShowPass} src={require(`@/app/assets/img/${passwordType ? 'icon-eye':'close-eye'}.svg`)} width="17" height="17" alt="" />
                              </span>
                              <input placeholder="Password" className={password ? 'ieHack': ''} type={passwordType ? 'password' : 'text'} value={password} id="password" onChange={(e) => setPassword(e.target.value)}  />
                          </div>
                          <div className="login-others clearfix">
                            {/* <label><i className={`material-icons`}>check_box_outline_blank</i>Remember me</label> */}
                              <a href="#!">Forgot Password?</a>
                          </div>
                          <div className="form-row-box button-panel">
                              <button className="btn btn-primary">Sign in</button>
                              <p>Don’t have an account? <a href="">Create account</a></p>
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
