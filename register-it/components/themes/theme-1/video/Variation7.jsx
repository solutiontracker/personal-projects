import React from "react";
import { PortalWithState } from "react-portal";
import Videopopup from "components/Videopopup";
import HeadingElement from 'components/ui-components/HeadingElement';
import ActiveLink from "components/atoms/ActiveLink";
import Image from 'next/image'


const Vimeo = ({photo}) => {
  const video =  /^(http\:\/\/|https\:\/\/)?(www\.)?(vimeo\.com\/)([0-9]+)$/;
  const match = photo.URL.match(video);
 if (photo.thumnail && photo.thumnail !== "") {
   return (
     <img
       onLoad={(e) => e.target.style.opacity = 1}
       style={{ width: "100%", height: '100%', objectFit: 'cover' }}
       src={process.env.NEXT_APP_EVENTCENTER_URL + "/assets/videos/" + photo.thumnail}
       alt={`${photo.info && photo.info.title !== undefined && photo.info.title}`}
     />
   );
 } else {
  return (
   <img
     onLoad={(e) => e.target.style.opacity = 1}
     style={{ width: "100%", height: '100%', objectFit: 'cover' }}
     src={`https://vumbnail.com/${match[4]}.jpg`}
     alt={`${photo.info && photo.info.title !== undefined && photo.info.title}`}
   />
  )
 }
};
const DailyMotion = ({photo}) => {
  const video =  /^(?:(?:https?):)?(?:\/\/)?(?:www\.)?(?:(?:dailymotion\.com(?:\/embed)?\/video)|dai\.ly)\/([a-zA-Z0-9]+)(?:_[\w_-]+)?$/;
  const match = photo.URL.match(video);
 if (photo.thumnail && photo.thumnail !== "") {
   return (
     <img
       onLoad={(e) => e.target.style.opacity = 1}
       style={{ width: "100%", height: '100%', objectFit: 'cover' }}
       src={process.env.NEXT_APP_EVENTCENTER_URL + "/assets/videos/" + photo.thumnail}
       alt={`${photo.info && photo.info.title !== undefined && photo.info.title}`}
     />
   );
 } else {
  return (
   <img
     onLoad={(e) => e.target.style.opacity = 1}
     style={{ width: "100%", height: '100%', objectFit: 'cover' }}
     src={`http://www.dailymotion.com/thumbnail/video/${match[1]}`}
     alt={`${photo.info && photo.info.title !== undefined && photo.info.title}`}
   />
  )
 }
};
const YouTubeVideo = ({photo}) => {
  const youtube =  /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
  const match = photo.URL.match(youtube);
 if (photo.thumnail && photo.thumnail !== "") {
   return (
     <img
       onLoad={(e) => e.target.style.opacity = 1}
       style={{ width: "100%", height: '100%', objectFit: 'cover' }}
       src={process.env.NEXT_APP_EVENTCENTER_URL + "/assets/videos/" + photo.thumnail}
       alt={`${photo.info && photo.info.title !== undefined && photo.info.title}`}
     />
   );
 } else {
  return (
   <img
     onLoad={(e) => e.target.style.opacity = 1}
     style={{ width: "100%", height: '100%', objectFit: 'cover' }}
     src={`https://img.youtube.com/vi/${match[2]}/maxresdefault.jpg`}
     alt={`${photo.info && photo.info.title !== undefined && photo.info.title}`}
   />
  )
 }
};
const NormalVideo = ({photo}) => {
 if (photo.thumnail && photo.thumnail !== "") {
   return (
     <img
       onLoad={(e) => e.target.style.opacity = 1}
       style={{ width: "100%", height: '100%', objectFit: 'cover' }}
       src={process.env.NEXT_APP_EVENTCENTER_URL + "/assets/videos/" + photo.thumnail}
       alt={`${photo.info && photo.info.title !== undefined && photo.info.title}`}
     />
   );
 } else {
  return (
   <Image objectFit='contain' layout="fill"
     onLoad={(e) => e.target.style.opacity = 1}
     style={{ width: "100%", height: '100%', objectFit: 'cover' }}
     src={require("public/img/gallery-not-found.png")}
     alt="g"
   />
  )
 }
};


const Variation7 = ({ settings, videos, home, eventUrl, loadMore, siteLabels }) => {
  const bgStyle = (settings && settings.background_color !== "") ? { backgroundColor: settings.background_color} : {}

  return (
    <div style={bgStyle} className="module-section ebs-default-padding">
      {home && <div className="container">
        <HeadingElement dark={false} label={siteLabels.EVENTSITE_VIDEOS} align={'center'} />
      </div>}
      <div className="container">
        <div className="edgtf-portfolio-list-holder-outer">
          <div className="edgtf-portfolio-list-holder">
            <div className="d-flex row">
              {videos &&
                videos.map((photo, i) => (
                  <div key={i} className="col-md-4 col-lg-3 col-sm-6">
                    <PortalWithState closeOnOutsideClick closeOnEsc>
                      {({ openPortal, closePortal, isOpen, portal }) => (
                        <React.Fragment>
                          <div style={{ animationDelay: 50 * i + 'ms', overflow: 'hidden' }} onClick={openPortal} className="edgtf-image-with-text edgtf-image-with-text-above mb-30px ebs-animation-layer">
                            <div className="ebs-video-button-inner ebs-right-top">
                              <i className="fa fa-play-circle" aria-hidden="true"></i>
                            </div>

                            <div className="edgtf-iwt-image gallery-img-wrapper-rectangle">
                              {Number(photo.type) === 1 && <DailyMotion photo={photo} />}
                              {Number(photo.type) === 2 && <Vimeo photo={photo} />}
                              {Number(photo.type) === 3 && <YouTubeVideo photo={photo} />}
                              {Number(photo.type) === 4 || Number(photo.type) === 5  && <NormalVideo photo={photo} />}
                            </div>
                            <div className="edgtf-iwt-text-holder">
                              <div className="edgtf-iwt-text-table">
                                <div className="edgtf-iwt-text-cell">
                                  {photo.info && (
                                    <h3 className="edgtf-iwt-title">
                                      {photo.info && photo.info.title !== undefined && photo.info.title}
                                    </h3>
                                  )}
                                </div>
                              </div>
                            </div>
                          </div>
                          {portal(
                           <Videopopup
                            photo={photo}
                            onClose={closePortal} />
                          )}
                        </React.Fragment>
                      )}
                    </PortalWithState>
                  </div>
                ))}
            </div>
          </div>
          {!home && loadMore()}
          {home && <div className="container p-0 pt-5 text-center">
            <ActiveLink href={`/${eventUrl}/videos`}>
              <button
                className="edgtf-btn edgtf-btn-medium edgtf-btn-outline edgtf-btn-custom-hover-bg edgtf-btn-custom-border-hover edgtf-btn-custom-hover-color"
              >
                Load More
              </button>
            </ActiveLink>
          </div>}
        </div>
      </div>
    </div>
  );
};

export default Variation7;
