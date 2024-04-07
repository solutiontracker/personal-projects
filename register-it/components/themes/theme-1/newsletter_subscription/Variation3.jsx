import React, {useState} from 'react';
import HeadingElement from 'components/ui-components/HeadingElement';
const Variation3 = (props) =>  {
  const [email, setEmail] = useState('');
  const [first_name, setFirstName] = useState('');
  const [last_name, setLastName] = useState('');
  const [is_checked, setIsChecked] = useState(false);
  const onSubmit =() =>{
    props.handleSubmit({email,first_name,last_name,is_checked});

  }
  const bgStyle = (props.moduleVariation && props.moduleVariation.background_color !== "") ? { backgroundColor: props.moduleVariation.background_color} : { backgroundColor: '#F2F2F2' }

    return (
      <React.Fragment>
        {props.settings && <div className="module-section">
          <div style={bgStyle} className="ebs-default-padding">
          <div className="container-fluid">
            <HeadingElement dark={false} label={props.event.labels.EVENTSITE_NEWSLETTER_SUBSCRIBE_HEADING ? props.event.labels.EVENTSITE_NEWSLETTER_SUBSCRIBE_HEADING : "Subscribe to our newsletter "}  align={'left'} />
            <p style={{color:"#fff"}} dangerouslySetInnerHTML={{__html: props.settings.content}} />
          </div>
            <div className="ebs-sub-newsletter-sec ebs-dark-variation">
              {props.alert !== "" &&<p style={{color:"green"}}>
                          {props.alert}
              </p>}
              <form onSubmit={(e)=>{ e.preventDefault(); onSubmit(); }} >
                <div className="container-fluid">
                  <div className="ebs-contain-fluid d-flex">
                    <div className="ebs-left-fluid">
                      <div className="row d-flex">
                      <div className="col-md-4">
                      <input style={{color: '#313131',padding: 15,backgroundColor: '#fff'}} name="email" className="wpcf7-form-control wpcf7-text" value={email} required onChange={(e)=>{setEmail(e.currentTarget.value)}} type="email" placeholder={props.settings.email_label} />
                      {props.errors.email && props.errors.email.map((error,i)=>(
                                <p key={i} className='error-message'>{error}</p>
                              ))}  
                      </div>
                        <div className="col-md-4">
                          <input style={{color: '#313131',padding: 15,backgroundColor: '#fff'}} name="first_name" className="wpcf7-form-control wpcf7-text" value={first_name} required onChange={(e)=>{setFirstName(e.currentTarget.value)}} type="text" placeholder={props.settings.first_name_label} />
                          {props.errors.first_name && props.errors.first_name.map((error,i)=>(
                                <p key={i} className='error-message'>{error}</p>
                              ))}
                        </div>
                        <div className="col-md-4">
                          <input style={{color: '#313131',padding: 15,backgroundColor: '#fff'}} name="last_name" className="wpcf7-form-control wpcf7-text" value={last_name}  onChange={(e)=>{setLastName(e.currentTarget.value)}} type="text" placeholder={props.settings.last_name_label} />
                        </div>
                      </div>
                    </div>
                    <div className="ebs-right-fluid">
                    <button style={{border: '2px solid #313131', color: '#313131',  fontWeight: 500}} type="submit"  disabled={props.loading ? true : false} className="edgtf-btn edgtf-btn-huge edgtf-btn-custom-border-hover edgtf-btn-custom-hover-bg edgtf-btn-custom-hover-color">
                      {props.settings.button_label}
                      {props.loading && <em style={{verticalAlign: 'bottom',marginLeft: 4,fontSize: 24}} className="fa fa-pulse fa-spinner"></em>}
                    </button> 
                    </div>
                  </div>
                  {props.settings.show_checkbox !== "0" && <div className="row d-flex">
                    <div className="col-md-12 mb-5">
                      <label className="ebs-accept-terms">
                        <span className="ebs-custom-check">
                          <input type="checkbox" name="is_checked" required onChange={(e)=>{setIsChecked(e.currentTarget.checked)}} checked={is_checked ? true : false} />
                          <i className="material-icons"></i>
                          </span>
                          <p dangerouslySetInnerHTML={{__html: props.settings.checkbox_content}} />
                      </label>
                    </div>
                  
                  </div>}
                </div>
              </form>
            </div>
        </div>
        </div>}
      </React.Fragment>
    );
  }


export default Variation3;
