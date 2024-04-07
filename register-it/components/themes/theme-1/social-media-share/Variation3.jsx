import React, { useEffect, useRef } from "react";
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

const Variation3 = ({ event, settings, socialMediaShare, labels }) => {

  const _parallax = useRef(null);

  useEffect(() => {
    window.addEventListener("scroll", scollEffect);
    return () => {
      window.removeEventListener("scroll", scollEffect);
    }
  }, [])

  function scollEffect() {
    const scrolled = window.pageYOffset;
    const itemOffset = _parallax.current.offsetTop;
    const itemHeight = _parallax.current.getBoundingClientRect();
    if (scrolled < (itemOffset - window.innerHeight) || scrolled > (itemOffset + itemHeight.height)) return false;
    const _scroll = (scrolled - itemOffset) + itemHeight.height;
    _parallax.current.style.backgroundPosition = `50%  -${(_scroll * 0.1)}px`;
  };

  const WrapperLayout = ({ children }) => {

    const _bgimage = `${process.env.NEXT_APP_EVENTCENTER_URL}/assets/variation_background/${settings.background_image}`;

    if (settings && settings.background_image !== "") {
      return (
        <div style={{ backgroundImage: `url(${_bgimage})`}}
          className="edgtf-parallax-section-holder ebs-bg-holder ebs-default-padding"
          ref={_parallax}>
          {children}
        </div>
      );
    } else {
      return (
        <div className="edgtf-parallax-section-holder ebs-bg-holder ebs-default-padding"
          ref={_parallax}>
          {children}
        </div>
      );
    }

  }

  return (
    <WrapperLayout>
      <div className="container">
        <HeadingElement dark={true} label={labels.SECTION_SOCIAL_FRONT_TITLE} align={'center'} />
        <div className="ebs-social-share text-center pb-3">
          {socialMediaShare.Facebook == 1 && <FacebookShareButton url={`${window.location.origin.toString()}/${event.url}`}
          >
            <FacebookIcon size={120} bgStyle={{ fill: 'transparent' }}
              round={true} title="Facebook" />
          </FacebookShareButton>}
          {socialMediaShare.Linkedin == 1 && <LinkedinShareButton
            url={`${window.location.origin.toString()}/${event.url}`}
          >
            <LinkedinIcon size={120} bgStyle={{ fill: 'transparent' }}
              round={true} title="Linked In" />
          </LinkedinShareButton>}
          {socialMediaShare.Twitter == 1 && <TwitterShareButton
            url={`${window.location.origin.toString()}/${event.url}`}
          >
            <TwitterIcon size={120} bgStyle={{ fill: 'transparent' }}
              round={true} title="Twitter" />
          </TwitterShareButton>}
          {socialMediaShare.Pinterest == 1 && <PinterestShareButton
            url={`${window.location.origin.toString()}/${event.url}/`}
            media={
              event.settings.header_logo
                ? `${process.env.NEXT_APP_EVENTCENTER_URL}/assets/event/branding/${event.settings.header_logo}`
                : `${process.env.NEXT_APP_EVENTCENTER_URL}/_mobile_assets/images/logo-header@2x.png`
            }
          >
            <PinterestIcon size={120} bgStyle={{ fill: 'transparent' }}
              round={true} title="Pinterest" />
          </PinterestShareButton>}
          {socialMediaShare.Email == 1 && <EmailShareButton
            url={`${window.location.origin.toString()}/${event.url}`}
          >
            <EmailIcon size={120} bgStyle={{ fill: 'transparent' }}
              round={true} title="Facebook" />
          </EmailShareButton>}
        </div>
      </div>
    </WrapperLayout>
  );
};

export default Variation3;
