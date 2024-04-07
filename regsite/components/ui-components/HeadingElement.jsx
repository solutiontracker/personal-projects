import React from 'react'

 const HeadingElement = ({dark,align,label,desc, page_header, breakheading}) => {
  return (
  <div className={`row d-flex ${!page_header ? 'mb-4':''} `}>
            {align === 'center' && <div className="col-md-8 offset-md-2 text-center">
              <div className="edgtf-title-section-holder">
                <h2
                  style={{ color: dark ? '#fff' :'#313131' }}
                  className="edgtf-title-with-dots edgtf-appeared"
                >
                  {label}
                </h2>
                <span className="edge-title-separator edge-enable-separator"></span>
              </div>
              {desc && <div className="edgtf-title-section-holder">
                <h6 style={{ color: dark ? '#fff' :'#888' }} className="edgtf-section-subtitle">{desc}</h6>
              </div>}
            </div>}
            {align === 'left' && 
              <React.Fragment>
                <div className={desc && !breakheading ? "col-md-4" : "col-md-12"}>
                  <div className="edgtf-title-section-holder">
                    <h2
                      style={{ color: dark ? '#fff' :'#313131' }}
                      className="edgtf-title-with-dots edgtf-appeared"
                    >
                      {label}
                    </h2>
                    <span className="edge-title-separator edge-enable-separator"></span>
                  </div>
                </div>
                {desc && <div className={breakheading ? 'col-md-12' : 'col-md-8'}>
                 <div className="edgtf-title-section-holder">
                    <h6 style={{ color: dark ? '#fff' :'#888',marginTop: breakheading ? 0 : 15 }} className="edgtf-section-subtitle">{desc}</h6>
                  </div>
                </div> }
              </React.Fragment>
            }
          </div>
  )
};
export default HeadingElement;
