import * as React from 'react';
import ReCAPTCHA from "react-google-recaptcha";
import PhoneInput from 'react-phone-input-2'
import Img from 'react-image';
import 'react-phone-input-2/dist/style.css'

class Signup extends React.Component {

    render() {
        return (
            <div className="container-box">
                <Img src="images/logos.svg" className="logos" />
                <div className="row">
                    <div className="col-6">
                        <div className="left-signup">
                            <div className="text-block">
                                <h4>WELCOME TO  PLUG’N’PLAY</h4>
                                <p>Minimize your efforts. Maximize the results.</p>
                                <ul>
                                    <li>Create your own event in a few clicks</li>
                                    <li>Sort out event registration in no time</li>
                                    <li>Get your own customized event app</li>
                                    <li>Feel safe with our step by step navigation</li>
                                </ul>
                            </div>
                            <Img src="images/illustration.png" className="illustration" />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="right-formarea">
                            <h2>GET A FREE 14 DAYS TRIAL</h2>
                            <p>No commitment. No credit card.</p>

                            <div className="form-area-signup">
                                <div className="form-row-box">
                                    <input type="text" placeholder="" name="" />
                                    <label className="title">First name</label>
                                </div>
                                <div className="form-row-box">
                                    <input type="text" placeholder="" name="" />
                                    <label className="title">Last name</label>
                                </div>
                                <div className="form-row-box">
                                    <input type="text" placeholder="" name="" />
                                    <label className="title">Company</label>
                                </div>
                                <div className="form-row-box">
                                    <PhoneInput defaultCountry={'dk'} />
                                </div>
                                <div className="form-row-box">
                                    <input type="email" placeholder="" name="" />
                                    <label className="title">Enter business Email</label>
                                </div>
                                <div className="form-row-box">
                                    <ReCAPTCHA
                                        sitekey="Your client site key"

                                    />
                                </div>
                                <div className="form-row-box button-panel">
                                    <button className="btn btn-primary">START 14 DAY FREE TRIAL</button>
                                    <div className="other-link">Already have an account?  <a href="">Login here</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default Signup;
