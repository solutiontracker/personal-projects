import React from "react";
import ActiveLink from "components/atoms/ActiveLink";
import HeadingElement from "components/ui-components/HeadingElement";
import Image from 'next/image'

const Variation16 = ({ attendees, searchBar, loadMore, event, settings, siteLabels }) => {
    const _bgimage = `${process.env.NEXT_APP_EVENTCENTER_URL}/assets/variation_background/${settings.background_image}`;

  const bgStyle = (settings && settings.background_image !== "") ? { backgroundImage: `url(${_bgimage})` } : {}

  return (
    <div
      style={bgStyle}
      className="edgtf-parallax-section-holder ebs-bg-holder ebs-default-padding"
    >
      <div className="container">
        <HeadingElement dark={true} label={event.labels.EVENTSITE_ATTENDEES} desc={event.labels.EVENT_ATTENDEES_LOWER_HEAD} align={settings.text_align} />
      </div>
      {searchBar()}
      <div className="container">
        <div className="edgtf-team-list-holder clearfix">
          {/* Grid */}
          {attendees &&
            attendees.map((attendee, i) => (
              <div
                key={i}
                className="ebs-attendees-list ebs-attendees-list-dark"
              >
                <div style={{ animationDelay: 50 * i + 'ms' }} className="edgtf-team-list-holder-inner info_box ebs-animation-layer">
                  <div className="edgtf-team w-100 p-0 mb-4 border-top lh-base">
                    <div className="edgtf-team-inner w-auto row d-flex align-items-center">
                      {/* Description */}
                      <div className="edgtf-team-info text-start text-xs-center col-12">
                        <div className="row d-flex align-items-center">
                          <div className="col-lg-5">
                             <div className="edgtf-team-title-holder m-0">
                              {(attendee.first_name || attendee.last_name) && (
                                <ActiveLink href={`/${event.url}/attendees/${attendee.id}`}>
                                  <h3 className="edgtf-team-name mt-0 mb-1">
                                    {attendee.info &&
                                      attendee.info.initial && (
                                        <>
                                          {attendee.info.initial &&
                                            attendee.info.initial}&nbsp;
                                        </>
                                      )}
                                    {attendee.first_name && attendee.first_name}{" "}
                                    {attendee.last_name && attendee.last_name}
                                  </h3>
                                </ActiveLink>
                              )}
                             </div>
                          </div>
                          <div className="col-lg-7">
                            <div className="row d-flex align-items-center">
                              <div className="col-lg-4">
                                {attendee.info &&
                                (attendee.info.title) && (
                                  <div  className="ebs-attendee-designation mb-1">
                                    {attendee.info.title && attendee.info.title}
                                  </div>
                                )}
                              </div>
                              <div className="col-lg-4">
                                 {attendee.info &&
                                (attendee.info.company_name) && (
                                  <div  className="ebs-attendee-designation mb-1">
                                    {attendee.info.company_name && attendee.info.company_name}
                                  </div>
                                )}
                              </div>
                              <div className="col-lg-4 d-flex justify-content-lg-end">
                                {
                                  attendee.info &&
                                  (attendee.info.facebook ||
                                    attendee.info.twitter ||
                                    attendee.info.linkedin ||
                                    attendee.info.website) && (
                                    <div className="edgtf-team-social-holder-between">
                                      <div className="edgtf-team-social">
                                        <div className="edgtf-team-social-inner">
                                          <div className="edgtf-team-social-wrapp">
                                            <div className="social-icons pt-1 text-start">
                                              {attendee.info.facebook && (
                                                <a
                                                  target="_blank" rel="noreferrer"
                                                  href={`${attendee.info.facebook_protocol}${attendee.info.facebook}`}
                                                >
                                                  <span data-icon="&#xe0aa;"></span>
                                                </a>
                                              )}
                                              {attendee.info.twitter && (
                                                <a
                                                  target="_blank" rel="noreferrer"
                                                  href={`${attendee.info.twitter_protocol}${attendee.info.twitter}`}
                                                >
                                                  <span data-icon="&#xe0ab;"></span>
                                                </a>
                                              )}
                                              {attendee.info.linkedin && (
                                                <a
                                                  target="_blank" rel="noreferrer"
                                                  href={`${attendee.info.linkedin_protocol}${attendee.info.linkedin}`}
                                                >
                                                  <span data-icon="&#xe0b4;"></span>
                                                </a>
                                              )}
                                              {attendee.info.website && (
                                                <a
                                                  target="_blank" rel="noreferrer"
                                                  href={`${attendee.info.website_protocol}${attendee.info.website}`}
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
                            </div>
                          </div>
                        </div>
                      </div>
                      {/* Description */}
                    </div>
                  </div>
                </div>
              </div>
            ))}
          {/* Grid */}
        </div>
        {attendees.length === 0 && <div>{siteLabels.GENERAL_NO_RECORD}</div>}
        <div className="border-top d-flex mb-5"></div>
        {attendees.length > 0 && loadMore()}
      </div>
    </div>
  );
};

export default Variation16;
