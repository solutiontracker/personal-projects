import React, { Component } from 'react';
import Input from '@/app/forms/Input';
import DateTime from '@/app/forms/DateTime';
import DropDown from '@/app/forms/DropDown';
import TextArea from '@/app/forms/TextArea';
import CheckFields from '@/app/forms/CheckFields';


export class RegistrationInformation extends Component {


  render() {
    const { mainTitle, switchItem} = this.props;
    return (
      <div className="wrapper-box">
        <header className="header-section">
          <h3> 1. {mainTitle} <i className='material-icons'> keyboard_arrow_up</i></h3>
        </header>
        {switchItem === 1 &&
          <div>
            <div className="row d-flex justify-content-center mb-3">
              <div className="col-6 ">
                <div className="header-box clearfix">
                  <h4 className="float-left">Attendee information</h4>
                  <span className="required-field float-right"> <em className='req'>*</em> required fields</span>
                </div>
                <Input
                  type='text'
                  label='Attendee Type'
                  inputValue='Visitor'
                  required={true}
                  />
                <Input
                  type='text'
                  label='Title'
                  inputValue='Director'
                  required={false}
                  />
                <Input
                  type='text'
                  label='First Name'
                  inputValue=''
                  required={true}
                  />
                <Input
                  type='text'
                  label='Last Name'
                  inputValue=''
                  required={false}
                />
                <Input
                  type='email'
                  label='Email'
                  inputValue=''
                  required={true}
                  />
                <Input
                  type='email'
                  label='Confirm Email'
                  inputValue=''
                  required={true}
                />
                <DateTime label="Birth Date" />
                <DropDown
                  label='City'
                  listitems={['Denmark', 'Pakistan', 'England']}
                  required={false}
                />
                <DropDown
                  label='Country'
                  listitems={['Denmark','Pakistan','England']}
                  required={false}
                />
                <TextArea
                  label='About'
                  required={true}
                />
                <CheckFields
                  label='Are you a member?'
                  display='inline'
                  type="radio"
                  fields={['Male','Female']}
                  checked={['Female']}
                />
                <CheckFields
                  label='Are you a member?'
                  display='inline'
                  type="checkbox"
                  fields={['Male', 'Female']}
                  checked={['Male', 'Female']}
                />
              </div>
            </div>
            <div className="row d-flex justify-content-center">
              <div className="col-6 ">
                <div className="header-box clearfix">
                  <h4 className="float-left">Membership information</h4>
                </div>
              </div>
            </div>
          </div> 
        }
      </div>
    )
  }
}

export default RegistrationInformation
