import React from "react";
import HeadingElement from "components/ui-components/HeadingElement";
import {
  EmailIcon,
  FacebookIcon,
  LinkedinIcon,
  TwitterIcon,
  PinterestIcon,
  FacebookShareButton,
  TwitterShareButton,
  LinkedinShareButton,
  PinterestShareButton,
  EmailShareButton,
} from "react-share";

const Variation5 = ({ event, settings, socialMediaShare, labels }) => {
  const bgStyle = (settings && settings.background_color !== "") ? { backgroundColor: settings.background_color} : {}

  return (
    <div
      style={bgStyle}
      className="edgtf-parallax-section-holder ebs-default-padding">
      <div className="container">
        <HeadingElement dark={false} label={labels.SECTION_SOCIAL_FRONT_TITLE}  align={'center'} />
        <div className="ebs-social-share text-center pb-3">
          {socialMediaShare.Facebook == 1 && <FacebookShareButton url={`${window.location.origin.toString()}/${event.url}`}
          >
            <FacebookIcon size={120} borderRadius="6px" title="Facebook" />
          </FacebookShareButton>}
          {socialMediaShare.Linkedin == 1 && <LinkedinShareButton
            url={`${window.location.origin.toString()}/${event.url}`}
          >
            <LinkedinIcon size={120} borderRadius="6px" title="Linked In" />
          </LinkedinShareButton>}
          {socialMediaShare.Twitter && <TwitterShareButton
            url={`${window.location.origin.toString()}/${event.url}`}
          >
            <TwitterIcon size={120} borderRadius="6px" title="Twitter" />
          </TwitterShareButton>}
          {socialMediaShare.Pinterest == 1 && <PinterestShareButton
            url={`${window.location.origin.toString()}/${event.url}/`}
            media={
              event.settings.header_logo
                ? `${process.env.NEXT_APP_EVENTCENTER_URL}/assets/event/branding/${event.settings.header_logo}`
                : `${process.env.NEXT_APP_EVENTCENTER_URL}/_mobile_assets/images/logo-header@2x.png`
            }
          >
            <PinterestIcon size={120} borderRadius="6px" title="Pinterest" />
          </PinterestShareButton>}
          {socialMediaShare.Email == 1 && <EmailShareButton
            url={`${window.location.origin.toString()}/${event.url}`}
          >
            <EmailIcon size={120} borderRadius="6px" title="Facebook" />
          </EmailShareButton>}
        </div>
      </div>
    </div>
  );
};

export default Variation5;
