import React from "react";
import { Gallery, Item } from 'react-photoswipe-gallery'
import HeadingElement from "components/ui-components/HeadingElement";
import ActiveLink from "components/atoms/ActiveLink";
import Image from 'next/image'
import { getMeta } from 'helpers/helper';

const Variation7 = ({ photos, settings, loadMore, eventUrl, home, sitelabels, totalPages }) => {

  const imgUrl = (photo) => {
    if (photo.image && photo.image !== "") {
      return process.env.NEXT_APP_EVENTCENTER_URL + "/assets/photos/" + photo.image
    } else {
      return "img/home-2-gallery-img-1-480x400.jpg"
    }
  };
  const bgStyle = (settings && settings.background_color !== "") ? { backgroundColor: settings.background_color} : {}

  return (
    <div style={bgStyle} className="module-section ebs-default-padding">
      <div className="container">
        {home && <HeadingElement dark={false} label={sitelabels.EVENTSITE_PHOTOS} desc={sitelabels.EVENTSITE_PHOTOS_SUB} align={settings.text_align} />}
        <div className="edgtf-portfolio-list-holder-outer">
          <div className="edgtf-portfolio-list-holder">
            <div className="d-flex row">
              <Gallery shareButton={false} id="my-gallery" withCaption>
                {photos &&
                  photos.map((photo, i) => (
                    <div key={i} className="col-lg-3 col-md-4 col-sm-6">
                      <Item
                        key={i}
                        original={imgUrl(photo)}
                        thumbnail={imgUrl(photo)}
                        caption={photo.info && photo.info.title !== undefined ? photo.info.title : 'Photo'}
                        title={`${photo.info && photo.info.title !== undefined && photo.info.title}`}
                        width={getMeta(imgUrl(photo), 'width') !== 0 ? getMeta(imgUrl(photo), 'width') : 1000}
                        height={getMeta(imgUrl(photo), 'height') !== 0 ? getMeta(imgUrl(photo), 'height') : 665}
                      >
                        {({ ref, open }) => (
                          <div style={{ animationDelay: 50 * i + 'ms' }} ref={ref} onClick={open} className="edgtf-image-with-text edgtf-image-with-text-above mb-30px ebs-animation-layer">
                            <div className="edgtf-link-holder">
                              <div className="edgtf-iwt-image gallery-img-wrapper-rectangle">
                                {photo.image && photo.image !== "" ? (
                                  <img
                                    onLoad={(e) => e.target.style.opacity = 1}
                                    style={{ width: "100%" }}
                                    src={process.env.NEXT_APP_EVENTCENTER_URL + "/assets/photos/" + photo.image}
                                    alt={`${photo.info && photo.info.title !== undefined && photo.info.title}`}
                                  />
                                ) : (
                                  <Image objectFit='contain' layout="fill"
                                    onLoad={(e) => e.target.style.opacity = 1}
                                    style={{ width: "100%" }}
                                    src={require("public/img/gallery-not-found.png")}
                                    alt="g"
                                  />
                                )}
                              </div>
                            </div>
                            <div className="edgtf-iwt-text-holder">
                              <div className="edgtf-iwt-text-table">
                                <div className="edgtf-iwt-text-cell">
                                  {photo.info && (
                                    <h3 className="edgtf-iwt-title">
                                      {photo.info.title !== undefined && photo.info.title}
                                    </h3>
                                  )}
                                </div>
                              </div>
                            </div>
                          </div>
                        )}
                      </Item>
                    </div>
                  ))}
              </Gallery>
            </div>
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

export default Variation7;
