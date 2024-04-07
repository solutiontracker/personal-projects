import React from "react";

const Variation1 = ({map, siteLabels}) => {
  return (
    <div style={{ paddingTop: "80px" }} className="edgtf-container">
      <div className="edgtf-container-inner container">
        <div className="edgtf-title-section-holder text-center pb-3">
          <h2 className="edgtf-title-with-dots edgtf-appeared">{siteLabels.EVENTSITE_MAP}</h2>
          <span className="edge-title-separator edge-enable-separator" />
          <h6 style={{ marginBottom: 0 }} className="edgtf-section-subtitle">
            {siteLabels.EVENTSITE_MAP_DETAIL}
          </h6>
          <p
            style={{ marginTop: 0, fontSize: 14, color: "#555" }}
            className="edgtf-section-subtitle"
            >
            {siteLabels.EVENTSITE_MAP_ADDRESS}
          </p>
        </div>
      </div>
      <div className="ebs-google-map">
        {map && map.info.url && <iframe
          title="google map"
          src={map.info.url ? map.info.url : "https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d13603.145696954229!2d74.3470055!3d31.5300254!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2s!4v1640081607060!5m2!1sen!2s"}          
          width="600"
          height="450"
          allowFullScreen=""
          loading="lazy"
        />}
        {(map && map.info.image && map.info.url === "") && 
          <img src={`${process.env.NEXT_APP_EVENTCENTER_URL + '/assets/maps/'}${map.info.image}`} alt=""  style={{width:"100%"}}/>
        }
      </div>
    </div>
  );
};

export default Variation1;
