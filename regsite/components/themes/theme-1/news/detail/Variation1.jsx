import React, { useState, useRef } from "react";
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
import Image from 'next/image'

const Variation1 = ({ event, news, sidebar, newsSettings }) => {
  const [height, setHeight] = useState(0);
  const iframe = useRef();
  return (
    <div style={{ paddingLeft: 0,paddingRight: 0 }} className="edgtf-container ebs-default-padding">
      <div className="container">
        <div className={`${newsSettings.subscriber_id ? "edgtf-two-columns-75-25" : "edgtf-full-width-inner"} clearfix`}>
          <div className="edgtf-column1 edgtf-content-left-from-sidebar">
            <div className="edgtf-column-inner">
              <div className="edgtf-blog-holder edgtf-blog-type-standard">
                <article>
                  <div className="blog-post-social">
                    <FacebookShareButton
                      url={`${window.location}`}
                    >
                      <FacebookIcon size={32} round={true} title="Facebook" />
                    </FacebookShareButton>
                    <LinkedinShareButton
                      url={`${window.location}`}
                    >
                      <LinkedinIcon size={32} round={true} title="Linked In" />
                    </LinkedinShareButton>
                    <TwitterShareButton
                      url={`${window.location}`}
                    >
                      <TwitterIcon size={32} round={true} title="Twitter" />
                    </TwitterShareButton>
                    <PinterestShareButton
                      url={`${window.location}`}
                      media={
                        news.image
                          ? `${process.env.NEXT_APP_EVENTCENTER_URL}/assets/eventsite_news/${news.image}`
                          : `${process.env.NEXT_APP_EVENTCENTER_URL}/_admin_assets/images/header_logo_size_image.jpg`
                      }
                    >
                      <PinterestIcon size={32} round={true} title="Pinterest" />
                    </PinterestShareButton>
                    <EmailShareButton
                      url={`${window.location}`}
                    >
                      <EmailIcon size={32} round={true} title="Facebook" />
                    </EmailShareButton>
                  </div>
                  <div className="edgtf-post-content">
                    {news.image && <div className="edgtf-post-image">
                      <span className="gallery-img-wrapper-rectangle-2">
                        {news.image && news.image !== "" ? (
                          <img
                            onLoad={(e) => e.target.style.opacity = 1}
                            src={
                              process.env.NEXT_APP_EVENTCENTER_URL +
                              "/assets/eventsite_news/" +
                              news.image
                            }
                            className="attachment-full size-full wp-post-image"
                            alt="a"
                            width="1500"
                            height="500"
                          />
                        ) : (
                          <Image objectFit='contain' layout="fill"
                            onLoad={(e) => e.target.style.opacity = 1}
                            src={
                              ""
                            }
                            className="attachment-full size-full wp-post-image"
                            alt="a"
                            width="1500"
                            height="500"
                          />
                        )}
                      </span>
                    </div>}
                    <div className="edgtf-post-text">
                      <div className="edgtf-post-text-inner">
                        <h2
                          itemProp="name"
                          className="entry-title edgtf-post-title"
                        >
                          <a
                            itemProp="url"
                            href="javascript:void(0)"
                            title="Web Analytics Made Easy"
                          >
                            {news.title}
                          </a>
                        </h2>
                        <div className="edgtf-post-info">
                          <div
                            style={{ fontSize: 15 }}
                            itemProp="dateCreated"
                            className="edgtf-post-info-date entry-date updated"
                          >
                            {news.created_at}
                          </div>
                        </div>
                        <div
                          style={{ marginBottom: 40, marginTop: 0 }}
                          className="edgtf-post-info-bottom"
                        ></div>
                        <p
                          itemProp="description"
                          className="edgtf-post-excerpt"
                          dangerouslySetInnerHTML={{ __html: news.body }}
                        />
                      </div>
                    </div>
                  </div>
                </article>
              </div>
            </div>
          </div>
          {newsSettings.subscriber_id !== null && typeof window !== 'undefined' && (
            <div className="edgtf-column2">
              <div className="edgtf-sidebar">
                <iframe
                  ref={iframe}
                  onLoad={() => {
                    setHeight(iframe.current.contentWindow.window.top.document.body.scrollHeight - window.innerHeight > 400 ? iframe.current.contentWindow.window.top.document.body.scrollHeight - 200 : window.innerHeight);
                  }}
                  width="100%"
                  height={height > 0 ? height : 400}
                  title="test"
                  itemProp="description"
                  className="edgtf-post-excerpt"
                  src={`${process.env.NEXT_APP_URL}/event/${event.url}/getMailingListSubscriberForm/${newsSettings.subscriber_id}`}
                />
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default Variation1;
