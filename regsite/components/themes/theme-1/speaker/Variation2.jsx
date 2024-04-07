import React from "react";
import ActiveLink from "components/atoms/ActiveLink";
import HeadingElement from "components/ui-components/HeadingElement";
import Image from 'next/image'

const Variation2 = ({ speakers, listing, searchBar, loadMore, event, settings, siteLabels }) => {
  const bgStyle = (settings && settings.background_color !== "") ? { backgroundColor: settings.background_color } : {}

  return (
    <div
      style={bgStyle}
      className="edgtf-parallax-section-holder ebs-default-padding"
    >
      <div className="container">
        <HeadingElement dark={false} label={event.labels.EVENTSITE_SPEAKERS} desc={event.labels.EVENTSITE_AMAZING_SPEAKERS} align={settings.text_align} />
      </div>
      {listing && searchBar()}
      <div className="container">
        <div className={`row d-flex edgtf-team-list-holder edgtf-team-info-below-image ${!listing ? 'justify-content-center' : ''}`}>
          {/* Grid */}
          {speakers &&
            speakers.map((speaker, i) => {
              return (
                <div
                  className="col-12 col-sm-6 col-md-4 pl-0 pr-0 ebs-attendee-v2 ebs-dark-attendee"
                  key={i}
                >
                  <div style={{ animationDelay: 50 * i + 'ms' }} className="edgtf-team-list-holder-inner info_box ebs-animation-layer">
                    <div className="edgtf-team mb-3 w-100">
                      <div className="edgtf-team-inner">
                        <div className="edgtf-team-image">
                          <ActiveLink href={`/${event.url}/speakers/${speaker.id}`}>
                            <span className="gallery-img-wrapper-square">
                              {speaker.image && speaker.image !== "" ? (
                                <img
                                  onLoad={(e) => e.target.style.opacity = 1}
                                  src={
                                    process.env.NEXT_APP_EVENTCENTER_URL +
                                    "/assets/attendees/" +
                                    speaker.image
                                  }
                                  alt="g"
                                />
                              ) : (
                                <Image objectFit='contain' layout="fill"
                                  onLoad={(e) => e.target.style.opacity = 1}
                                  src={
                                    require("public/img/user-placeholder.jpg")
                                  }
                                  alt="g"
                                />
                              )}
                            </span>
                          </ActiveLink>
                        </div>
                        {/* Description */}
                        <div className="edgtf-team-info">
                          <div className="edgtf-team-title-holder">
                            {(speaker.first_name || speaker.last_name) && (
                              <ActiveLink href={`/${event.url}/speakers/${speaker.id}`}>
                                <h3 className="edgtf-team-name">
                                  {speaker.info &&
                                    speaker.info.initial && (
                                      <>
                                        {speaker.info.initial &&
                                          speaker.info.initial}&nbsp;
                                      </>
                                    )}
                                  {speaker.first_name && speaker.first_name}{" "}
                                  {speaker.last_name && speaker.last_name}
                                </h3>
                              </ActiveLink>
                            )}
                            {speaker.info &&
                              (speaker.info.company_name ||
                                speaker.info.title) && (
                                <div className="ebs-attendee-designation">
                                  {speaker.info.title && speaker.info.title}
                                  {speaker.info.title &&
                                    speaker.info.company_name &&
                                    ", "}
                                  {speaker.info.company_name &&
                                    speaker.info.company_name}
                                </div>
                              )}
                            {listing && <div className="ebs-border-wrapp">
                              {listing && speaker.email && (
                                <div className="ebs-email-phone">
                                  <a
                                    href={`mailto:${speaker.email}`}
                                    className="edgtf-team-position"
                                  >
                                    {speaker.email}
                                  </a>
                                </div>
                              )}
                              {listing && speaker.phone && (
                                <div className="ebs-email-phone">
                                  <a
                                    href={`tel:${speaker.phone}`}
                                    className="edgtf-team-position alt"
                                  >
                                    {speaker.phone}
                                  </a>
                                </div>
                              )}
                            </div>}
                          </div>
                          {listing &&
                            speaker.info &&
                            (speaker.info.facebook ||
                              speaker.info.twitter ||
                              speaker.info.linkedin ||
                              speaker.info.website) && (
                              <div className="edgtf-team-social-holder-between">
                                <div className="edgtf-team-social">
                                  <div className="edgtf-team-social-inner">
                                    <div className="edgtf-team-social-wrapp">
                                      <div className="social-icons">
                                        {speaker.info.facebook && (
                                          <a
                                            target="_blank" rel="noreferrer"
                                            href={`${speaker.info.facebook_protocol}${speaker.info.facebook}`}
                                          >
                                            <span data-icon="&#xe0aa;"></span>
                                          </a>
                                        )}
                                        {speaker.info.twitter && (
                                          <a
                                            target="_blank" rel="noreferrer"
                                            href={`${speaker.info.twitter_protocol}${speaker.info.twitter}`}
                                          >
                                            <span data-icon="&#xe0ab;"></span>
                                          </a>
                                        )}
                                        {speaker.info.linkedin && (
                                          <a
                                            target="_blank" rel="noreferrer"
                                            href={`${speaker.info.linkedin_protocol}${speaker.info.linkedin}`}
                                          >
                                            <span data-icon="&#xe0b4;"></span>
                                          </a>
                                        )}
                                        {speaker.info.website && (
                                          <a
                                            target="_blank" rel="noreferrer"
                                            href={`${speaker.info.website_protocol}${speaker.info.website}`}
                                          >
                                            <span data-icon="&#xe0e3;"></span>
                                          </a>
                                        )}
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            )}
                        </div>
                        {/* Description */}
                      </div>
                    </div>
                  </div>
                </div>
              );
            })}
        </div>
        {listing && speakers.length === 0 && <div>{siteLabels.GENERAL_NO_RECORD}</div>}
        {listing && speakers.length > 0 && loadMore()}
      </div>
    </div>
  );
};

export default Variation2;
