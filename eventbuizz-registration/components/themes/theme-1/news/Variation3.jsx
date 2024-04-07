import ActiveLink from "components/atoms/ActiveLink";
import React, { useState, useRef } from 'react';
import TruncateMarkup from 'react-truncate-markup';
import Image from 'next/image'

const Variation3 = ({ news, event_url, makeNewDetailURL, loadMore, newsSettings, siteLabels, homePage, moduleVariation }) => {
  const [height, setHeight] = useState(0);
  const iframe = useRef();
  const bgStyle = (moduleVariation && moduleVariation.background_color !== "") ? { backgroundColor: moduleVariation.background_color} : {}

  return (
    <div style={{ padding: '80px 0', ...bgStyle}} className='edgtf-container'>
      <div className="container">
        <div className={`${(!newsSettings.subscriber_id || homePage) ? 'edgtf-full-width-inner' : 'edgtf-two-columns-75-25'} clearfix`}>

          <div className="edgtf-column1 edgtf-content-left-from-sidebar">
            <div className="edgtf-column-inner">
              <div className="edgtf-blog-holder edgtf-blog-type-split-column ">
                {news.map((item, i) => (
                  <article style={{ animationDelay: 50 * i + 'ms' }} className="ebs-animation-layer" key={item.id}>
                    <div className="edgtf-post-content">
                      {item.image && <div className="edgtf-post-image">
                        <ActiveLink itemProp="url" href={makeNewDetailURL(event_url, item.id)}>
                          <span className="gallery-img-wrapper-rectangle-2">
                            {
                              item.image && item.image !== '' ? (
                                <img onLoad={(e) => e.target.style.opacity = 1} src={process.env.NEXT_APP_EVENTCENTER_URL + "/assets/eventsite_news/" + item.image} className="attachment-full size-full wp-post-image" alt="a" width="1500" height="500" />
                              ) : (
                                <Image objectFit='contain' layout="fill" onLoad={(e) => e.target.style.opacity = 1} src={""} className="attachment-full size-full wp-post-image" alt="a" width="1500" height="500" />
                              )
                            }
                          </span>
                        </ActiveLink>
                      </div>}
                      <div className="edgtf-post-text">
                        <div className="edgtf-post-text-inner">
                          <h3 itemProp="name" className="entry-title edgtf-post-title">
                            <ActiveLink itemProp="url" href={makeNewDetailURL(event_url, item.id)}>
                              {item.title}
                            </ActiveLink>
                          </h3>
                          <div className="edgtf-post-info">
                            <div itemProp="dateCreated" className="edgtf-post-info-date entry-date updated">
                              {item.created_at}
                            </div>
                          </div>

                          <TruncateMarkup lines={3}>
                            <p className="edgtf-post-excerpt">{item.body.replace(/<(.|\n)*?>/g, '')}</p>
                          </TruncateMarkup>
                        </div>
                      </div>
                    </div>
                  </article>
                ))}
              </div>
            </div>
            {(news.length > 0 && !homePage) && loadMore()}
          </div>
          {(news.length === 0 && !homePage) && <div>{siteLabels.GENERAL_NO_RECORD}</div>}
          {newsSettings.subscriber_id !== null && newsSettings.subscriber_id !== '' && !homePage && (
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
                  src={`${process.env.NEXT_APP_URL}/event/${event_url}/getMailingListSubscriberForm/${newsSettings.subscriber_id}`}
                />
              </div>
            </div>
          )}

        </div>
      </div>
    </div>
  )
}

export default Variation3
