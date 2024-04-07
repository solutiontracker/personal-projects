import React, {useState, useRef} from 'react';
import HeadingElement from 'components/ui-components/HeadingElement';
import SimpleReactValidator from "simple-react-validator";

const Variation2 = (props) =>  {
  const [email, setEmail] = useState('');
  const [first_name, setFirstName] = useState('');
  const [last_name, setLastName] = useState('');
  const [is_checked, setIsChecked] = useState(false);

    const _parallax = React.useRef(null);
    React.useEffect(() => {
      window.addEventListener("scroll",scollEffect);
      return () => {
        window.removeEventListener("scroll",scollEffect);
      }
    }, [])
    
     function scollEffect () {
      if (props.settings) {
        const scrolled = window.pageYOffset;
        const itemOffset = _parallax.current.offsetTop;
        const itemHeight = _parallax.current.getBoundingClientRect();
        if (scrolled < (itemOffset - window.innerHeight) || scrolled > (itemOffset + itemHeight.height)) return false;
        const _scroll = (scrolled - itemOffset) + itemHeight.height;
        _parallax.current.style.backgroundPosition = `50%  -${(_scroll * 0.1)}px`;
      }
    };
    
    const _bgimage = `${process.env.NEXT_APP_EVENTCENTER_URL}/assets/variation_background/${props.moduleVariation.background_image}`;
    
    const bgStyle = (props.moduleVariation && props.moduleVariation.background_image !== "") ? { backgroundImage: `url(${_bgimage})`, backgroundPosition: "center top", backgroundSize: 'cover' } : { backgroundPosition: "center top", backgroundSize: 'cover' }
  
    const onSubmit =() =>{
          props.handleSubmit({email,first_name,last_name,is_checked});
  
    }

    return (
          <React.Fragment>
            {props.settings && <div className="module-section">
              <div ref={_parallax} style={bgStyle} className="edgtf-parallax-section-holder ebs-bg-holder ebs-default-padding">
                  <div className="container">
                    <HeadingElement dark={true} label={props.event.labels.EVENTSITE_NEWSLETTER_SUBSCRIBE_HEADING ? props.event.labels.EVENTSITE_NEWSLETTER_SUBSCRIBE_HEADING : "Subscribe to our newsletter "}  align={'center'} />
                    {<p style={{color:"#fff", textAlign:"center"}} dangerouslySetInnerHTML={{__html: props.settings.content}} />}
                  </div>
                  <div className="ebs-sub-newsletter-sec">
                    <div className="container">
                      {props.alert !== "" &&<p style={{color:"green"}}>
                          {props.alert}
                      </p>}
                      <form onSubmit={(e)=>{ e.preventDefault(); onSubmit(); }}>
                        <div className="row d-flex">
                          <div className="col-md-4">
                            <input style={{color: '#fff',padding: 15}} name="email" className="wpcf7-form-control wpcf7-text" value={email} required onChange={(e)=>{setEmail(e.currentTarget.value)}} type="email" placeholder={props.settings.email_label} />
                            {props.errors.email && props.errors.email.map((error,i)=>(
                              <p key={i} className='error-message'>{error}</p>
                            ))}
                          </div>
                          <div className="col-md-4">
                            <input style={{color: '#fff',padding: 15}} name="first_name" className="wpcf7-form-control wpcf7-text" value={first_name} required onChange={(e)=>{setFirstName(e.currentTarget.value)}} type="text" placeholder={props.settings.first_name_label} />
                            {props.errors.first_name && props.errors.first_name.map((error,i)=>(
                              <p key={i} className='error-message'>{error}</p>
                            ))}
                          </div>
                          <div className="col-md-4">
                            <input style={{color: '#fff',padding: 15}} name="last_name" className="wpcf7-form-control wpcf7-text" value={last_name}  onChange={(e)=>{setLastName(e.currentTarget.value)}} type="text" placeholder={props.settings.last_name_label} />
                          </div>
                          {props.settings.show_checkbox !== "0" && <div className="col-md-12 mb-5">
                            <label className="ebs-accept-terms">
                              <span className="ebs-custom-check">
                                  <input type="checkbox" name="is_checked" required onChange={(e)=>{setIsChecked(e.currentTarget.checked)}} checked={is_checked ? true : false}  />
                                  <i className="material-icons"></i>
                                </span>
                                <p dangerouslySetInnerHTML={{__html: props.settings.checkbox_content}} />
                            </label>
                            </div>}
                          <div className="col-md-12 text-center">
                          <button style={{border: '2px solid #fff', color: '#fff',  fontWeight: 500,  backgroundColor: 'transparent'}} type="submit"  disabled={props.loading ? true : false} className="edgtf-btn edgtf-btn-huge edgtf-btn-custom-border-hover edgtf-btn-custom-hover-bg edgtf-btn-custom-hover-color">
                            {props.settings.button_label}
                            {props.loading && <em style={{verticalAlign: 'bottom',marginLeft: 4,fontSize: 24}} className="fa fa-pulse fa-spinner"></em>}
                          </button> 
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                  </div>
            </div>}
          </React.Fragment>
    );
  }


export default Variation2;
