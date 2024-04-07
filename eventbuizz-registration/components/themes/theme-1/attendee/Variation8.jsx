import React from "react";
import ActiveLink from "components/atoms/ActiveLink";
import HeadingElement from "components/ui-components/HeadingElement";
import Image from 'next/image'

const Variation8 = ({ attendees, searchBar, loadMore, event, settings, siteLabels }) => {
  const bgStyle = (settings && settings.background_color !== "") ? { backgroundColor: settings.background_color } : {}

  return (
    <div style={bgStyle} className="module-section ebs-default-padding">
      <div className="container">
        <HeadingElement dark={false} label={event.labels.EVENTSITE_ATTENDEES} desc={event.labels.EVENT_ATTENDEES_LOWER_HEAD} align={settings.text_align} />
      </div>
      {searchBar()}
      <div className="container">
        <div className="row d-flex algin-items-center">
          {attendees &&
            attendees.map((attendee, i) => (
              <div
                key={i}
                style={{ marginBottom: "30px" }}
                className="col-md-4 col-sm-6"
              >
                <div style={{ height: "100%", marginBottom: 0, animationDelay: 50 * i + 'ms' }}
                  className="speakerv7-wrapper ebs-animation-layer">
                  <div className="speakerv7-image">
                    <span className="box">
                      <ActiveLink href={`/${event.url}/attendees/${attendee.id}`}>
                        <span className="gallery-img-wrapper-square">
                          {attendee.image && attendee.image !== "" ? (
                            <img
                              onLoad={(e) => e.target.style.opacity = 1}
                              src={
                                process.env.NEXT_APP_EVENTCENTER_URL +
                                "/assets/attendees/" +
                                attendee.image
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
                    </span>
                  </div>
                  <div className="attendeev7-caption">
                    {(attendee.first_name || attendee.last_name) && (
                      <ActiveLink href={`/${event.url}/attendees/${attendee.id}`}>
                        <h3>
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

                    {attendee.info &&
                      (attendee.info.company_name || attendee.info.title) && (
                        <p>
                          {attendee.info.title && attendee.info.title}
                          {attendee.info.company_name &&
                            attendee.info.company_name && <br />}
                          {attendee.info.company_name &&
                            attendee.info.company_name}
                        </p>
                      )}
                    {attendee.email && (
                      <div className="email">
                        <a href={`mailto:${attendee.email}`}>{attendee.email}</a>
                      </div>
                    )}

                    {attendee.phone && (
                      <div className="speakerv7-phone">
                        <a href={`tel:${attendee.phone}`}>{attendee.phone}</a>
                      </div>
                    )}
                    {
                      attendee.info &&
                      (attendee.info.facebook ||
                        attendee.info.twitter ||
                        attendee.info.linkedin ||
                        attendee.info.website) && (
                        <div className="d-flex">
                          <div className="social-icons">
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
                      )}
                  </div>
                </div>
              </div>
            ))}
        </div>
        {attendees.length === 0 && <div>{siteLabels.GENERAL_NO_RECORD}</div>}
        {attendees.length > 0 && loadMore()}
      </div>
    </div>
  );
};

export default Variation8;
