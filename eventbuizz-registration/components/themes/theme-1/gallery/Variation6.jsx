
import React from "react";
import Masonry from "react-masonry-css";
import { Gallery, Item } from 'react-photoswipe-gallery'
import HeadingElement from "components/ui-components/HeadingElement";
import ActiveLink from "components/atoms/ActiveLink";
import Image from 'next/image'
import { getMeta } from 'helpers/helper';

const Variation6 = ({ photos, settings, loadMore, eventUrl, home, sitelabels, totalPages }) => {

  const imgUrl = (photo) => {
    if (photo.image && photo.image !== "") {
      return process.env.NEXT_APP_EVENTCENTER_URL + "/assets/photos/" + photo.image
    } else {
      return "img/home-2-gallery-img-1-480x400.jpg"
    }
  };

  const breakpointColumnsObj = {
    default: 3,
    1100: 3,
    700: 2,
    500: 1,
  };
  const bgStyle = (settings && settings.background_color !== "") ? { backgroundColor: settings.background_color} : {}

  return (
    <div style={bgStyle} className="module-section ebs-default-padding">
      <div className="container">
        {home && <HeadingElement dark={false} label={sitelabels.EVENTSITE_PHOTOS} desc={sitelabels.EVENTSITE_PHOTOS_SUB} align={settings.text_align} />}
        <div className="gallerMasonry">
          {photos && (
            <Gallery shareButton={false} id="my-gallery" withCaption>
              <Masonry
                breakpointCols={breakpointColumnsObj}
                className="my-masonry-grid"
                columnClassName="my-masonry-grid_column"
              >
                {photos &&
                  photos.map((photo, i) => (
                    <div style={{ animationDelay: 50 * i + 'ms' }} key={i} className="gallerMasonry ebs-animation-layer">
                      <Item
                        original={imgUrl(photo)}
                        thumbnail={imgUrl(photo)}
                        caption={photo.info && photo.info.title !== undefined ? photo.info.title : 'Photo'}
                        title={`${(photo.info && photo.info.title !== undefined) && photo.info.title}`}
                        width={getMeta(imgUrl(photo), 'width') !== 0 ? getMeta(imgUrl(photo), 'width') : 1000}
                        height={getMeta(imgUrl(photo), 'height') !== 0 ? getMeta(imgUrl(photo), 'height') : 665}
                      >
                        {({ ref, open }) => (
                          <figure className="gallery-img-wrapper-rectangle" ref={ref} onClick={open}>
                            {photo.image && photo.image !== "" ? (
                              <img
                                onLoad={(e) => e.target.style.opacity = 1}
                                src={process.env.NEXT_APP_EVENTCENTER_URL + "/assets/photos/" + photo.image}
                                alt={`${(photo.info && photo.info.title !== undefined) && photo.info.title}`}
                              />
                            ) : (
                              <Image objectFit='contain' layout="fill"
                                onLoad={(e) => e.target.style.opacity = 1}
                                src={require("public/img/gallery-not-found.png")}
                                alt="g"
                              />
                            )}

                            <figcaption>
                              {photo.info && (
                                <div
                                  className="icon"
                                  style={{
                                    border: "none",
                                    padding: "10px",
                                    textAlign: "center",
                                    fontSize: "20px",
                                    lineHeight: "1.2",
                                  }}
                                >
                                  {`${photo.info.title !== undefined && photo.info.title}`}
                                </div>
                              )}
                            </figcaption>
                          </figure>
                        )}
                      </Item>
                    </div>
                  ))}
              </Masonry>
            </Gallery>
          )}
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
  );
};

export default Variation6;
