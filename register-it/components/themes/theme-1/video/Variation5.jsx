import React from "react";
import { PortalWithState } from "react-portal";
import Videopopup from "components/Videopopup";
import HeadingElement from "components/ui-components/HeadingElement";
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


const Variation1 = ({ settings, videos, loadMore, eventUrl, home, siteLabels }) => {
  const bgStyle = (settings && settings.background_color !== "") ? { backgroundColor: settings.background_color} : {}

  return (
    <div style={bgStyle} className="module-section ebs-default-padding">
      {home && <div className="container">
        <HeadingElement dark={false} label={siteLabels.EVENTSITE_VIDEOS} align={'center'} />
      </div>}
      <div className="container">
        <div className="edgtf-image-gallery clearfix">
          <div className="edgtf-image-gallery-grid edgtf-gallery-columns-3">
            {videos &&
              videos.map((photo, i) => {
                return (
                  <div style={{ animationDelay: 50 * i + 'ms' }} key={i} className="edgtf-gallery-image ebs-animation-layer">
                    <PortalWithState closeOnOutsideClick closeOnEsc>
                      {({ openPortal, closePortal, isOpen, portal }) => (
                        <React.Fragment>
                          <span className="gallery-img-wrapper-rectangle" style={{ display: 'block', position: 'relative' }} onClick={openPortal} title={`${photo.info && photo.info.title !== undefined && photo.info.title}`}>
                            <div className="ebs-video-button-inner">
                              <i className="fa fa-play-circle" aria-hidden="true"></i>
                            </div>
                            {Number(photo.type) === 1 && <DailyMotion photo={photo} />}
                            {Number(photo.type) === 2 && <Vimeo photo={photo} />}
                            {Number(photo.type) === 3 && <YouTubeVideo photo={photo} />}
                            {Number(photo.type) === 4 || Number(photo.type) === 5  && <NormalVideo photo={photo} />}
                          </span>
                          {portal(
                             <Videopopup
                              photo={photo}
                              onClose={closePortal} />
                          )}
                        </React.Fragment>
                      )}
                    </PortalWithState>
                  </div>

                );
              })}
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

export default Variation1;
