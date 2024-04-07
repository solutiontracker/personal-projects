import React from "react";
import { Gallery, Item } from 'react-photoswipe-gallery'
import HeadingElement from "components/ui-components/HeadingElement";
import ActiveLink from "components/atoms/ActiveLink";
import Image from 'next/image'
import { getMeta } from 'helpers/helper';

const Variation5 = ({ photos, settings, loadMore, home, eventUrl, sitelabels, totalPages }) => {

  const imgUrl = (photo) => {
    if (photo.image && photo.image !== "") {
      return process.env.NEXT_APP_EVENTCENTER_URL + "/assets/photos/" + photo.image
    } else {
      return "public/img/gallery-not-found.png"
    }
  };
  const bgStyle = (settings && settings.background_color !== "") ? { backgroundColor: settings.background_color} : {}

  return (
    <div style={bgStyle} className="module-section ebs-default-padding">
      {home && <div className="container">
      <HeadingElement dark={false} label={sitelabels.EVENTSITE_PHOTOS} desc={sitelabels.EVENTSITE_PHOTOS_SUB} align={settings.text_align} />
      </div>}
      <div className="container">
        <div className="edgtf-image-gallery clearfix">
          <div className="edgtf-image-gallery-grid edgtf-gallery-columns-3">
            <Gallery shareButton={false} id="my-gallery" withCaption>
              {photos &&
                photos.map((photo, i) => {
                  return (
                    <Item
                      key={i}
                      original={imgUrl(photo)}
                      caption={photo.info && photo.info.title !== undefined ? photo.info.title : 'Photo'}
                      thumbnail={imgUrl(photo)}
                      title={`${photo.info && photo.info.title !== undefined && photo.info.title}`}
                      width={getMeta(imgUrl(photo), 'width') !== 0 ? getMeta(imgUrl(photo), 'width') : 1000}
                      height={getMeta(imgUrl(photo), 'height') !== 0 ? getMeta(imgUrl(photo), 'height') : 665}
                    >
                      {({ ref, open }) => (
                        <div style={{ animationDelay: 50 * i + 'ms' }} ref={ref} onClick={open} className="edgtf-gallery-image ebs-animation-layer" >
                          <span title="home-2-gallery-img-1" className="gallery-img-wrapper-rectangle">
                            {photo.image && photo.image !== "" ? (
                              <img
                                onLoad={(e) => e.target.style.opacity = 1}
                                src={process.env.NEXT_APP_EVENTCENTER_URL + "/assets/photos/" + photo.image}
                                alt={`${photo.info && photo.info.title !== undefined && photo.info.title}`}
                              />
                            ) : (
                              <Image objectFit='contain' layout="fill"
                                onLoad={(e) => e.target.style.opacity = 1}
                                src={require("public/img/gallery-not-found.png")}
                                alt="g"
                              />
                            )}
                          </span>
                        </div>
                      )}
                    </Item>
                  );
                })}
            </Gallery>
          </div>
          {!home && loadMore()}
          {home && totalPages > 1 && <div className="container p-0 pt-5 text-center">
            <ActiveLink href={`/${eventUrl}/gallery`}>
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

export default Variation5;
