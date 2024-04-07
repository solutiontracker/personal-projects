import React from "react";
import DocumentsListing from "components/ui-components/DocumentsListing";
import Image from 'next/image'

const Variation1 = ({ exhibitor, labels, documents, moduleName, eventTimezone }) => {
  
  return (
    <div data-fixed="false" className="ebs-transparent-box">
      {/* <div
        style={{
          minHeight: 250,
        }}
        className="edgtf-title edgtf-standard-type edgtf-has-background edgtf-content-left-alignment edgtf-title-large-text-size edgtf-animation-no edgtf-title-image-not-responsive edgtf-title-with-border"
      >
        <div className="edgtf-title-holder d-flex align-items-center justify-content-center">
          <div className="container">
            <div className="edgtf-title-subtitle-holder">
              <div className="edgtf-title-subtitle-holder-inner">
                <h1 style={{ color: "white" }}>
                  <span>{moduleName}</span>
                </h1>
              </div>
            </div>
          </div>
        </div>
        <div></div>
      </div> */}
      <div className="single-team-member">
        <div className="edgtf-container-inner container clearfix">
          <div className="edgtf-team-single-holder">
            <div className="edge-team-single-holder">
              <div className="edge-grid-row">
                <div className="edge-grid-col-12 edgtf-team-list-single-image">
                  <span style={{ border: '1px solid #ccc' }} className="gallery-img-wrapper-square">
                    {
                      exhibitor?.logo && exhibitor?.logo !== '' ? (
                        <img
                          style={{ maxWidth: '90%', width: 'auto' }}
                          onLoad={(e) => e.target.style.opacity = 1}
                          onClick={e => handleOnClick(e, exhibitor)}
                          src={

                            process.env.NEXT_APP_EVENTCENTER_URL + "/assets/exhibitors/" + exhibitor?.logo
                          }
                          alt="Client 11"
                        />
                      ) : (
                        <Image objectFit='contain' layout="fill"
                          onLoad={(e) => e.target.style.opacity = 1}
                          src={require('public/img/exhibitors-default.png')}
                          className="vc_single_image-img attachment-full"
                          alt="x"
                        />
                      )
                    }
                  </span>
                </div>
                <div className="edge-grid-col-12 edgtf-team-list-single-info">
                  {exhibitor?.name &&
                    <h2 className="edge-name">
                      {exhibitor?.name}
                    </h2>}
                  <div className="edge-grid-row edge-info">
                    <div className="edge-grid-col-12">
                      {exhibitor?.description && (
                        <div
                          style={{ paddingBottom: 10 }}
                          className="edge-team-single-content"
                        >
                          <h4 className="info">{labels.EVENTSITE_ABOUT_LABEL !== undefined ? labels.EVENTSITE_ABOUT_LABEL :"ABOUT"} </h4>
                          <div className="ebs-detail-single-content" dangerouslySetInnerHTML={{ __html: exhibitor?.description }} ></div>
                        </div>
                      )}
                      {exhibitor?.email && (
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
                            {labels.GENERAL_EMAIL !== undefined ? labels.GENERAL_EMAIL :"Email"}
                          </h4>
                          <p>
                            <a
                              style={{ color: "#000" }}
                              href={`mailto:${exhibitor?.email}`}
                            >
                              {exhibitor?.email}
                            </a>
                          </p>
                        </div>
                      )}
                      {exhibitor?.phone_number && (
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
                            {labels.EVENTSITE_PHONE_LABEL !== undefined ? labels.EVENTSITE_PHONE_LABEL :"Phone"}
                          </h4>
                          <p>
                            <a
                              style={{ color: "#000" }}
                              href={`tel:${exhibitor?.phone_number}`}
                            >
                              {exhibitor?.phone_number}
                            </a>
                          </p>
                        </div>
                      )}
                      {exhibitor.booth && (
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
                            booth{" "}
                          </h4>
                          <p>
                            {exhibitor.booth}
                          </p>
                        </div>
                      )}
                      <div
                        style={{ marginBottom: 20 }}
                        className="edge-info-row"
                      >
                        <div className="social-icons">
                          {exhibitor.facebook.replace(/^https?:\/\//, "") && (
                            <a
                              style={{ fontSize: "30px" }}
                              target="_blank" rel="noreferrer"
                              href={`${exhibitor?.facebook}`}
                            >
                              <span data-icon="&#xe0aa;"></span>
                            </a>
                          )}
                          {exhibitor.twitter.replace(/^https?:\/\//, "") && (
                            <a
                              style={{ fontSize: "30px" }}
                              target="_blank" rel="noreferrer"
                              href={`${exhibitor?.twitter}`}
                            >
                              <span data-icon="&#xe0ab;"></span>
                            </a>
                          )}
                          {exhibitor.linkedin.replace(/^https?:\/\//, "")  && (
                            <a
                              style={{ fontSize: "30px" }}
                              target="_blank" rel="noreferrer"
                              href={`${exhibitor?.linkedin}`}
                            >
                              <span data-icon="&#xe0b4;"></span>
                            </a>
                          )}
                          {exhibitor.website.replace(/^https?:\/\//, "") && (
                            <a
                              style={{ fontSize: "30px" }}
                              target="_blank" rel="noreferrer"
                              href={`${exhibitor?.website}`}
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

      {exhibitor?.exhibitors_attendee?.length > 0 && <div style={{ paddingBottom: 50 }} className="">
        <div className="container">
          <div className="edgtf-title-section-holder pb-1">
            <h3 className="edgtf-title-with-dots edgtf-appeared pb-2">{labels.EVENTSITE_CONTACT_PERSON_LABEL !== undefined ? labels.EVENTSITE_CONTACT_PERSON_LABEL :"Contacts"}</h3>
          </div>
          <div className="row d-flex ebs-program-speakers">
            {exhibitor.exhibitors_attendee?.map((attendee, o) =>
              <div key={o} style={{ animationDelay: 50 * o + 'ms' }} className="col-md-3 col-sm-4 col-lg-2 col-6 ebs-speakers-box ebs-detail-image-sponsors ebs-animation-layer">
                <span style={{ marginBottom: 20 }} className="gallery-img-wrapper-square">
                  {
                    attendee?.image && attendee?.image !== "" ? (
                      <img
                        onLoad={(e) => e.target.style.opacity = 1}
                        style={{ width: '90%' }}
                        src={
                          process.env.NEXT_APP_EVENTCENTER_URL +
                          "/assets/attendees/" +
                          attendee?.image
                        }
                        alt="Client 11"
                      />
                    ) : (
                      <Image objectFit='contain' layout="fill"
                        onLoad={(e) => e.target.style.opacity = 1}
                        src={require('public/img/user-placeholder.jpg')}
                        alt="x"
                      />
                    )
                  }
                </span>
                <h4>{attendee?.first_name} {attendee?.last_name}</h4>
                <p>{attendee?.info?.title && (attendee?.info?.title)} {attendee?.info?.company_name && (attendee?.info?.company_name)}</p>
                <p>{attendee?.email && (attendee?.email)}</p>
                <p>{attendee?.info?.phone && (attendee?.info?.phone)}</p>
                <div
                  style={{ marginBottom: 20 }}
                  className="edge-info-row"
                >
                  <div className="social-icons">
                    {attendee?.info?.facebook && (
                      <a
                        style={{ fontSize: "30px" }}
                        target="_blank" rel="noreferrer"
                        href={`${attendee?.info?.facebook_protocol}${attendee?.info?.facebook}`}
                      >
                        <span data-icon="&#xe0aa;"></span>
                      </a>
                    )}
                    {attendee?.info?.twitter && (
                      <a
                        style={{ fontSize: "30px" }}
                        target="_blank" rel="noreferrer"
                        href={`${attendee?.info?.twitter_protocol}${attendee?.info?.twitter}`}
                      >
                        <span data-icon="&#xe0ab;"></span>
                      </a>
                    )}
                    {attendee?.info?.linkedin && (
                      <a
                        style={{ fontSize: "30px" }}
                        target="_blank" rel="noreferrer"
                        href={`${attendee?.info?.linkedin_protocol}${attendee?.info?.linkedin}`}
                      >
                        <span data-icon="&#xe0b4;"></span>
                      </a>
                    )}
                    {attendee?.info?.website && (
                      <a
                        style={{ fontSize: "30px" }}
                        target="_blank" rel="noreferrer"
                        href={'https://'+`${attendee?.info?.website}`}
                      >
                        <span data-icon="&#xe0e3;"></span>
                      </a>
                    )}
                  </div>
                </div>
              </div>
              
            )}
          </div>
        </div>
      </div>}
      {documents && documents.length > 0 && <div style={{ paddingBottom: 80 }} className="edgtf-full-width">
        <div className="edgtf-container-inner container">
          <div className="edgtf-title-section-holder pb-1">
            <h3 className="edgtf-title-with-dots edgtf-appeared mb-0 pb-2">{labels.DOCUMENT_HEADING_DOCUMENT ? labels.DOCUMENT_HEADING_DOCUMENT : labels.GENERAL_DOCUMENT}</h3>
          </div>
          <DocumentsListing documents={documents} page={'exhibitor'} labels={labels} eventTimezone={eventTimezone} />
        </div>
      </div>}
    </div>
  );
};

export default Variation1;
