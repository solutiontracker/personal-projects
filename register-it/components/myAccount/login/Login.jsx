import React, {useState, useEffect, useRef} from 'react'
import SimpleReactValidator from "simple-react-validator";
import AlertMessage from './AlertMessage';
import Image from 'next/image'

const Login = ({setStep, onCancel, onformSubmit, event, error, loading}) => {
    const [, forceUpdate] = useState(0);
    const simpleValidator = useRef(new SimpleReactValidator({
      element: (message) => <p className="error-message">{message}</p>,
      messages: {
        required: event.labels.REGISTRATION_FORM_FIELD_REQUIRED,
        email:"Enter a valid email address",
        min:'Minimum 6 characters are required'
      },
      autoForceUpdate: { forceUpdate: () => forceUpdate(1) }
    }))
    const [valid, setValid] = useState(false);
    const [popup, setPopup] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [formData, setFormData] = useState({
      email:'',
      password:'',
      acceptTermsConditions:false,
    });
useEffect(() => {
    setValid(simpleValidator.current.allValid())  
    }, [formData])

const onChange=(e)=>{
    if(e.target.name !== "acceptTermsConditions"){
        setFormData({...formData, [e.target.name]:e.target.value});
    }else{
        setFormData({...formData, [e.target.name]:e.target.checked});
    }
}

const handleTermsCondition = (e) =>{
  e.preventDefault();
  e.stopPropagation();
  if(event.disclaimer.length > 0){
    setPopup(!popup)
  }
}
const onSubmit = (e) =>{
    e.preventDefault();
    const formValid = simpleValidator.current.allValid()
    if (!formValid) {
      simpleValidator.current.showMessages()
    }else{
      onformSubmit(formData);
    }
  }
  return (
    <React.Fragment>
      {popup && <div className="ebs-terms-conditon">
        <span onClick={handleTermsCondition} className="btn-inner-close">
          <Image objectFit='contain' layout="fill" src={require('public/img/remove-icon-x2.png')} alt="" />
        </span>
        <div className="ebs-content-terms" dangerouslySetInnerHTML={{__html:event.disclaimer[0]['disclaimer']}}>

        </div>
      </div>}
      <form style={{display: popup ? 'none' : ''}} id="loginForm" onSubmit={(e)=>{onSubmit(e)}}>
          <div className="ebs-login-wrapp-inner">
            <span onClick={() => onCancel()} className="btn-inner-close">
              <Image objectFit='contain' layout="fill" src={require('public/img/remove-icon-x2.png')} alt="" />
            </span>
            <h2 className="ebs-login-title">{event.labels.EVENTSITE_LOGIN !== undefined ? event.labels.EVENTSITE_LOGIN : "Login"}</h2>
            <p className="ebs-login-desc">{event.labels.EVENTSITE_ENTER_EMAIL !== undefined ? event.labels.EVENTSITE_ENTER_EMAIL : "Enter to continue and explore within your grasp."}</p>
            {error && <AlertMessage message={error}/>}
            {Number(event.attendee_settings.email_enable) === 1 && <div className="ebs-login-from">
              <label className="ebs-label-input">
                <span className="ebs-label-title">{event.labels.GENERAL_EMAIL !== undefined ? event.labels.GENERAL_EMAIL : "Email"}</span>
                <input className="ebs-input" name="email" type="email" autoComplete="false" placeholder={event.labels.GENERAL_EMAIL !== undefined ? event.labels.GENERAL_EMAIL : "Email Address"} value={formData.email} onChange={(e)=>{onChange(e)}} onBlur={()=>simpleValidator.current.showMessageFor('email')}/>
                {simpleValidator.current.message('email', formData.email, 'required|email')}
              </label>
            {Number(event.attendee_settings.hide_password) === 0 && Number(event.attendee_settings.registration_password) === 0 && Number(event.attendee_settings.authentication) === 0 && <label className="ebs-label-input" >
                <span className="ebs-label-title">{event.labels.GENERAL_PASSWORD}</span>
                <input className="ebs-input" name="password" type={showPassword ? "text" : "password"} autoComplete="false" placeholder={event.labels.GENERAL_PASSWORD} value={formData.password} onChange={(e)=>{onChange(e)}} onBlur={()=>simpleValidator.current.showMessageFor('password')} />
                <span className="ebs-show-password">
                  <div style={{width: 17, height: 12, position: 'relative'}}>
                    <Image objectFit='contain' layout="fill" src={showPassword ? require('public/img/icon-eye-close.svg'): require('public/img/icon-eye.svg')} onClick ={()=>{setShowPassword(!showPassword)}} alt="" />
                  </div>
                </span>
                {simpleValidator.current.message('password', formData.password, 'required|min:6')}
              </label>}
            {Number(event.attendee_settings.hide_password) === 0 && Number(event.attendee_settings.forgot_link) === 0 && Number(event.attendee_settings.authentication) === 0 && <div className="ebs-forgot-password"><span onClick={() => setStep("requestResetPassword") }>{event.labels.EVENTSITE_FORGOT_PASSWORD !== undefined ? event.labels.EVENTSITE_FORGOT_PASSWORD : "Forgot your password?"}</span></div>}
              <div className="ebs-form-accept">
                <label className="ebs-label-accept">
                  <input type="checkbox" name="acceptTermsConditions" value={formData.acceptTermsConditions} onChange={(e)=>{onChange(e)}} onBlur={()=>simpleValidator.current.showMessageFor('acceptTermsConditions')} /> <span className="ebs-accept-text">{event.labels.EVENTSITE_TERM_AGREE ? event.labels.EVENTSITE_TERM_AGREE : null} <span onClick={handleTermsCondition}>{event.labels.EVENTSITE_TERMANDCONDITIONS !== undefined ? event.labels.EVENTSITE_TERMANDCONDITIONS : "term and conditions"}</span>.</span>
                    {simpleValidator.current.message('acceptTermsConditions', formData.acceptTermsConditions, 'accepted')}
                </label>
              </div>
              <div className="ebs-btn-wrapp">
                <button className="btn btn-default" type="submit" disabled={(!valid || loading) ? true : false}>{ event.labels.GENERAL_SIGN_IN !== undefined ? event.labels.GENERAL_SIGN_IN : "Sign in"}</button>
              </div>
            </div>}
            {(Number(event.attendee_settings.linkedin_registration) === 1 || Number(event.attendee_settings.facebook_enable) === 1) && <div className="ebs-social-meida-login">
              <p>Or login with</p> 
              <div className="d-flex align-items-center justify-content-center">
                {Number(event.attendee_settings.facebook_enable) === 1 && <div className="ebs-ico-social">
                  <div className="ebs-inner-wrapper">
                    <Image objectFit='contain' layout="fill" src={require('public/img/ico-facebook.svg')} alt="" />
                  </div>
                </div>}
                {Number(event.attendee_settings.linkedin_registration) === 1 && <div className="ebs-ico-social">
                  <div className="ebs-inner-wrapper">
                    <Image objectFit='contain' layout="fill" src={require('public/img/ico-linkedin.svg')} alt="" />
                  </div>
                </div>}
              </div>
            </div>}
          </div>
      </form>
    </React.Fragment>
  )
}

export default Login