import moment from "moment";
import React, { useState } from "react";
import Image from 'next/image'

const Variation2 = ({ programs }) => {
  const [activeIndex, setActiveIndex] = useState(0);
  const [tabIndex, setTabIndex] = useState(0);
  return (
    <div className="module-section ebs-default-padding">
      <div className="container">
        <div className="row">
          <div className="col-md-8 offset-md-2 text-center">
            <div
              style={{ marginBottom: "30px" }}
              className="edgtf-title-section-holder"
            >
              <h2 className="edgtf-title-with-dots edgtf-appeared">
                Event Schedule{" "}
              </h2>
              <h6
                style={{ fontSize: "16px", lineHeight: "1.5" }}
                className="edgtf-section-subtitle"
              >
                A schedule at a glance is listed below. Check the program for
                this year\'s conference and learn about the speakers and sessions
                in store for tech enthusiasts.
              </h6>
            </div>
          </div>
        </div>
      </div>
      <div className="container">
        {programs && (
          <div className="schedulev2-wrapper">
            <div className="schedule-tab-wrapper">
              <ul>
                {programs &&
                  programs.length > 0 &&
                  programs.map((element, k) => (
                    <li key={k}>
                      <a
                        style={{
                          pointerEvents: k === activeIndex ? "none" : "",
                        }}
                        onClick={() => {
                          setActiveIndex(k);
                          setTabIndex(0);
                        }}
                        className={k === activeIndex ? "active" : ""}
                        href="javascript:void(0)"
                      >
                        {moment(new Date(element[0].date)).format("DD MMM")}
                      </a>
                    </li>
                  ))}
              </ul>
            </div>
            <div className="schedule-content-wrapper">
              <div className="schdedule-target">
                <div className="schdedule-accordion">
                  {programs[activeIndex] &&
                    programs[activeIndex].map((element, k) => (
                      <div key={k} className="schdedule-accordion-wrapper">
                        <div
                          onClick={() => setTabIndex(k)}
                          className="sc-accordion-header"
                        >
                          <div className="row align-item-center">
                            <div className="col-2">
                              <div className="sc-time">
                                <i className="fa fa-clock-o"></i>{" "}
                                {moment(element.start_time, "HH:mm:ss").format(
                                  "HH:mm"
                                )}
                                –
                                {moment(element.end_time, "HH:mm:ss").format(
                                  "HH:mm"
                                )}
                              </div>
                            </div>
                            <div className="col-10">
                              <h4>
                                {element.topic}
                                <i
                                  className={
                                    k === tabIndex
                                      ? "fa fa-angle-up"
                                      : "fa fa-angle-down"
                                  }
                                ></i>
                              </h4>
                            </div>
                          </div>
                        </div>
                        {k === tabIndex && (
                          <div
                            style={{ display: "block" }}
                            className="sc-accordion-content"
                          >
                            <div className="row">
                              <div className="col-2"></div>
                              <div className="col-10">
                                {element.description && (
                                  <div
                                    dangerouslySetInnerHTML={{
                                      __html: element.description,
                                    }}
                                  />
                                )}
                                {element.speakers &&
                                  element.speakers.length > 0 && (
                                    <div className="d-flex row mt-4">
                                      <div className="col-12 mb-3">
                                        <h5>SPEAKERS</h5>
                                      </div>
                                      {element.speakers.map((speaker, k) => (
                                        <div
                                          key={k}
                                          className="sc-speaker-container col-md-3 col-sm-4 col-xs-6"
                                        >
                                          {speaker.image &&
                                            speaker.image !== "" ? (
                                            <img
                                              src={
                                                process.env
                                                  .NEXT_APP_EVENTCENTER_URL +
                                                "/assets/attendees/" +
                                                speaker.image
                                              }
                                              alt=""
                                            />
                                          ) : (
                                            <Image objectFit='contain' layout="fill"
                                              src={
                                                require("public/img/square.jpg")
                                              }
                                              alt=""
                                            />
                                          )}
                                          <div className="cs-speaker-name">
                                            {speaker.first_name &&
                                              speaker.first_name}{" "}
                                            {speaker.last_name &&
                                              speaker.last_name}
                                          </div>
                                          {speaker.email && (
                                            <div className="cs-speaker-description">
                                              {speaker.email}
                                            </div>
                                          )}
                                        </div>
                                      ))}
                                    </div>
                                  )}
                              </div>
                            </div>
                          </div>
                        )}
                      </div>
                    ))}
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default Variation2;
