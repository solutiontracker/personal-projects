import SliderBanner from "./components/SliderBanner";
import moment from "moment";
import React from "react";

const Variation4 = ({ banner, event, countdown, regisrationUrl, settings, registerDateEnd }) => {

  const WrapperLayout = (props) => {
  const _bgLayer = (props.slides.info?.title.length > 0 && settings.title === 1) || (props.slides.info?.message.length > 0 && settings.caption === 1) || (settings.register_button === 1);

    if (props.slides && Number(props.slides.video_type) === 1) {
      return (
        <div
          style={{
            backgroundImage: `url(${process.env.NEXT_APP_EVENTCENTER_URL + props.slides.image
              })`,
            backgroundPosition: "50% 0"
            , backgroundBlendMode: _bgLayer ? 'overlay' : 'normal'
          }}
          className="background parallax-backgroud"
        >
          {props.slides.url ? <a href={props.slides.url} target="_blank" rel="noreferrer">
              {props.children}
            </a >: props.children}
        </div>
      );
    } else {
      return (
        <div
          style={{
            backgroundPosition: "50% 0"
            , backgroundBlendMode: _bgLayer ? 'overlay' : 'normal'
          }}
          className="background parallax-backgroud"
        >
          {props.slides.url ? <a href={props.slides.url} target="_blank" rel="noreferrer">
              {props.children}
            </a >: props.children}
        </div>
      );
    }

  }

  return (
    <div className="container">
      <div className="main-slider-wrapper">
        {banner && (
          <SliderBanner
            countdown={countdown}
            registerDateEnd={registerDateEnd}
            eventsiteSettings={event.eventsiteSettings}
            event={event}
          >
            {banner.map((slides, i) => (
              <div key={i} className="slide-wrapper">
                <WrapperLayout
                  slides={slides}
                >
                  {Number(slides.video_type) === 2 && (
                    <div className="video-fullscreen">
                      <video autoPlay playsInline muted loop src={`${process.env.NEXT_APP_EVENTCENTER_URL}/${slides.image}`} type="video/mp4"></video>
                    </div>
                  )}
                  <div className="caption-wrapp">
                    <div className="col-12 align-items-center justify-content-center d-flex inner-caption-wrapp">
                      <div
                        style={{ position: "relative" }}
                        className="parallax-text"
                      >
                        {slides.info.title && settings.title === 1 && (
                          <div
                            className="edgtf-custom-font-holder text-center ebs-banner-title"
                            style={{
                              fontFamily: "Rubik",
                              fontSize: "80px",
                              lineHeight: "100px",
                              fontWeight: "400",
                              textTransform: "uppercase",
                              textAlign: "left",
                              color: "#ec008c",
                            }}
                          >
                            <span style={{ color:  slides?.title_color ? slides?.title_color : "#fff" }}>
                              {slides.info.title}
                            </span>
                          </div>
                        )}
                        {slides.info.message && settings.caption === 1 && (
                          <div
                            className="edgtf-custom-font-holder text-center ebs-banner-subtitle"
                            style={{
                              margin: "10px auto 0",
                              fontSize: "26px",
                              lineHeight: "37px",
                              fontWeight: "400",
                              letterSpacing: "0px",
                              maxWidth: 900,
                              textAlign: "left",
                              color:  slides?.sub_title_color ? slides?.sub_title_color : "#fff"
                            }}
                          >
                            {slides.info.message}
                          </div>
                        )}
                        {settings.register_button && registerDateEnd && <div
                          className="edgtf-custom-font-holder text-center ebs-custom-button-holder"
                          style={{
                            marginTop: "40px",
                            fontSize: "26px",
                            lineHeight: "37px",
                            fontWeight: "400",
                            letterSpacing: "0px",
                            textAlign: "left",
                            color: "#ffffff",
                          }}
                        >
                          <a href={regisrationUrl} style={{ fontFamily: 'Rubik', marginRight: '0', fontSize: '15px', fontWeight: '500', background: 'transparent', border: '2px solid #fff', color: '#fff', padding: '17px 48px 15px' }} className="edgtf-btn edgtf-btn-huge edgtf-btn-custom-border-hover edgtf-btn-custom-hover-bg edgtf-btn-custom-hover-color">{event.labels.EVENTSITE_REGISTER_NOW2 ? event.labels.EVENTSITE_REGISTER_NOW2 : 'Register Now'}</a>
                        </div>}
                      </div>
                    </div>
                  </div>
                </WrapperLayout>
              </div>
            ))}
          </SliderBanner>
        )}
      </div>
    </div>
  );
};

export default Variation4;
