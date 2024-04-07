import React from "react";
import Image from 'next/image'

const Variation1 = ({ attendee, labels }) => {
  return (
    <div data-fixed="false" className="ebs-transparent-box">
      <div className="single-team-member">
        <div className="edgtf-container-inner container clearfix">
          <div className="edgtf-team-single-holder">
            <div className="edge-team-single-holder">
              <div className="edge-grid-row">
                <div className="edge-grid-col-12 edgtf-team-list-single-image">
                  <span style={{ border: '1px solid #ccc' }} className="gallery-img-wrapper-square">
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
                </div>
                <div className="edge-grid-col-12 edgtf-team-list-single-info">
                  <h2 className="edge-name">
                    {attendee.info &&
                      attendee.info.initial &&
                      `${attendee.info.initial} `}
                    {attendee.first_name && attendee.first_name}{" "}
                    {attendee.last_name && attendee.last_name}
                  </h2>
                  {attendee.info &&
                    (attendee.info.company_name || attendee.info.title) && (
                      <div className="edge-info-row">
                        <p className="info">
                          {attendee.info.title &&
                            `${attendee.info.title},`}{" "}
                          {attendee.info.company_name &&
                            attendee.info.company_name}
                        </p>
                      </div>
                    )}
                  <div className="edge-grid-row edge-info">
                    <div className="edge-grid-col-12">
                      {attendee.info && attendee.info.about && (
                        <div
                          style={{ paddingBottom: 10 }}
                          className="edge-team-single-content"
                        >
                          <h4 className="info">{attendee.labels.about !== undefined ? attendee.labels.about : "ABOUT"} </h4>
                          <p dangerouslySetInnerHTML={{ __html: attendee.info.about }}></p>
                        </div>
                      )}
                      {attendee.email && (
                        <div
                          style={{ marginBottom: 20 }}
                          className="edge-info-row"
                        >
                          <h4
                            style={{
                              textTransform: "uppercase",
                              marginBottom: 10,
                            }}
                            className="info"
                          >
                            {attendee.labels.email !== undefined ? attendee.labels.email : 'Email'}
                          </h4>
                          <p>
                            <a
                              style={{ color: "#000" }}
                              href={`mailto:${attendee.email}`}
                            >
                              {attendee.email}
                            </a>
                          </p>
                        </div>
                      )}
                      {attendee.phone && (
                        <div
                          style={{ marginBottom: 20 }}
                          className="edge-info-row"
                        >
                          <h4
                            style={{
                              textTransform: "uppercase",
                              marginBottom: 10,
                            }}
                            className="info"
                          >
                            {attendee.labels.phone !== undefined ? attendee.labels.phone : 'Email'}
                          </h4>
                          <p>
                            <a
                              style={{ color: "#000" }}
                              href={`tel:${attendee.phone}`}
                            >
                              {attendee.phone}
                            </a>
                          </p>
                        </div>
                      )}
                      <div
                        style={{ marginBottom: 20 }}
                        className="edge-info-row"
                      >
                        <div className="social-icons">
                          {attendee.info && attendee.info.facebook && (
                            <a
                              style={{ fontSize: "30px" }}
                              target="_blank" rel="noreferrer"
                              href={`${attendee.info.facebook_protocol}${attendee.info.facebook}`}
                            >
                              <span data-icon="&#xe0aa;"></span>
                            </a>
                          )}
                          {attendee.info && attendee.info.twitter && (
                            <a
                              style={{ fontSize: "30px" }}
                              target="_blank" rel="noreferrer"
                              href={`${attendee.info.twitter_protocol}${attendee.info.twitter}`}
                            >
                              <span data-icon="&#xe0ab;"></span>
                            </a>
                          )}
                          {attendee.info && attendee.info.linkedin && (
                            <a
                              style={{ fontSize: "30px" }}
                              target="_blank" rel="noreferrer"
                              href={`${attendee.info.linkedin_protocol}${attendee.info.linkedin}`}
                            >
                              <span data-icon="&#xe0b4;"></span>
                            </a>
                          )}
                          {attendee.info && attendee.info.website && (
                            <a
                              style={{ fontSize: "30px" }}
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
              </div>
            </div>
          </div>
        </div>
      </div>
      {/* <div style={{ paddingBottom: 80 }} className="edgtf-full-width">
        <div className="edgtf-container-inner container">
          <div className="edgtf-title-section-holder pb-1">
            <h2 className="edgtf-title-with-dots edgtf-appeared">Programes</h2>
            <span className="edge-title-separator edge-enable-separator"></span>
            <h6>
              Reminder for developer: Needed to implement programme sections
              variations
            </h6>
          </div>
        </div>
      </div> */}
    </div>
  );
};

export default Variation1;
