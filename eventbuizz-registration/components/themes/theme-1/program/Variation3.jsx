import moment from "moment";
import React, {useState} from "react";
import Image from 'next/image'

const Variation3 = ({programs}) => {
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
          <div className="schedulev3-wrapper">
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
                        {moment(new Date(element[0].date)).format(
                          "DD MMM"
                        )}
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
                          onClick={() => setTabIndex(0)}
                          className="sc-accordion-header"
                        >
                          <div className="sc-time">
                            {moment(element.start_time, "HH:mm:ss").format(
                              "HH:mm"
                            )}
                          </div>
                          {element.topic && <h4>{element.topic}</h4>}
                        </div>
                        {k === tabIndex && (
                          <div
                            style={{ display: "block" }}
                            className="sc-accordion-content"
                          >
                            <div className="row">
                              <div className="col-lg-2 col-md-2 col-sm-2">
                                <div className="sc-speaker-container">
                                  <Image objectFit='contain' layout="fill" src={require("public/img/square.jpg")} alt="" />
                                </div>
                              </div>
                              <div className="col-lg-7 col-md-7 col-sm-10">
                                <div className="sc-left-description">
                                  <h4>Mauris rhoncus scelerisque lacus</h4>
                                  <p>
                                    Sed facilisis justo vitae risus viverra
                                    vulputate. Mauris vel ipsum dignissim diam
                                    viverra condimentum. Donec sodales, diam
                                    eget mattis condimentum, quam neque tempus
                                    purus, dictum viverra risus nisl quis metus.
                                  </p>
                                </div>
                              </div>
                              <div className="col-lg-3 col-md-3 col-sm-10">
                                <div className="sc-right-description">
                                  <h5>Stanley Willis</h5>
                                  <p>
                                    Duis porttitor magna id arcu varius, a
                                    facilisis sem rutrum. Etiam auctor urna non
                                    elit sagittis, vehicula malesuada quam
                                    sollicitudin. Phasellus gravida placerat
                                    nisl ac convallis.{" "}
                                  </p>
                                </div>
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

export default Variation3;
