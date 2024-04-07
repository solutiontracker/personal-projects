import React, { useState, useRef } from "react";
import ActiveLink from "components/atoms/ActiveLink";
import Image from 'next/image'
import PageHeader from "components/modules/PageHeader";
import HeadingElement from "components/ui-components/HeadingElement";

const arrayTraverse = (array, menu_id, currentPage, eventSiteModuleName, section_id) => {
  let returnArray = [{ id: "", name: eventSiteModuleName, type: "main_menu", section_id:section_id }];
  let toFolder = null;
  if (menu_id && menu_id !== array.id) {
    toFolder = array.find((item) => (item.id === parseFloat(menu_id)));
  }

  if (toFolder) {
    returnArray.push({ id: toFolder.id, name: toFolder.info.name, type: toFolder.page_type ? toFolder.page_type : 'menu', section_id:section_id });
  }

  returnArray.push({ id: currentPage.id, name: currentPage.name, type: 'page', section_id:section_id});
  return returnArray;
}



const CmsDetail = ({ detail, moduleName, breadCrumbData, eventSiteModuleName, eventUrl, eventsiteSettings }) => {
  const [breadCrumbs, setBreadCrumbs] = useState(arrayTraverse(breadCrumbData.submenu, detail.parent_id, detail, eventSiteModuleName, detail.section_id));
  const [height, setHeight] = useState(0);
  const [Loading, setLoading] = useState(true);
  const iframe = useRef();

  const informationModules = {
    additional_information: "additional_info",
    general_information: "general_info",
    practicalinformation: "event_info",
    info_pages: "information_pages",
  };

  return (
    <React.Fragment>
    <PageHeader label={detail.name}  align={'left'} showBreadcrumb={eventsiteSettings.show_eventsite_breadcrumbs} breadCrumbs={(type)=>{
      return ( <nav aria-label="breadcrumb" className={`ebs-breadcrumbs ${type !== "background" ? 'ebs-dark': ''}`}>
       <ul className="breadcrumb">
         {breadCrumbs.map((crumb, i) => (
           <li className="breadcrumb-item" key={i}>
             {
             crumb.id === detail.id ? 
             crumb.name :
             crumb.parent_id != (0 || "") ? 
             <ActiveLink href={`/${eventUrl}/${moduleName}?main_menu_id=${crumb.section_id}&menu_id=${crumb.id}`} >{crumb.name}</ActiveLink>:
             <ActiveLink href={`/${eventUrl}/${moduleName}?menu_id=${crumb.id}`} >{crumb.name}</ActiveLink>
             }
           </li>
         ))}
       </ul>
   </nav>)
    }} >       
      </PageHeader> 
      <div style={{ paddingTop: "30px" }} className="edgtf-container">
        <div className="edgtf-container-inner container">
          <div className={`${"edgtf-full-width-inner"} clearfix`}>
            
            <div className="edgtf-column1 edgtf-content-left-from-sidebar">
              <div className="edgtf-column-inner">
                <div className="edgtf-blog-holder edgtf-blog-type-standard">
                  <article>
                    <div className="edgtf-post-content">
                      {detail.image && detail.image_position === 'top' && (
                        <div className="edgtf-post-image">
                          <a itemProp="url" href="">
                            {detail.image && detail.image !== "" ? (
                              <img
                                onLoad={(e) => e.target.style.opacity = 1}
                                src={
                                  process.env.NEXT_APP_EVENTCENTER_URL +
                                  `/assets/${informationModules[moduleName]}/temp/` +
                                  detail.image
                                }
                                className="attachment-full size-full wp-post-image"
                                width="1500"
                                height="500"
                                alt="g"
                              />
                            ) : (
                              <Image objectFit='contain' layout="fill"
                                onLoad={(e) => e.target.style.opacity = 1}
                                src=""
                                alt="g"
                              />
                            )}
                          </a>
                        </div>
                      )}
                      <div className="edgtf-post-text">
                        <div className="edgtf-post-text-inner">
                          {detail.description && (
                            <div>
                              {Loading && 
                              <div className="d-flex justify-content-center"> 
                                <div style={{width: '6rem', height: '6rem'}} className="spinner-border"> <span className="sr-only">Loading...</span></div>
                              </div>}
                              <iframe
                                ref={iframe}
                                onLoad={() => {
                                  const obj = iframe.current;
                                  obj.contentWindow.document.body.style.fontFamily = '"Open Sans", sans-serif';
                                  setHeight(
                                    obj.contentWindow.document.body.scrollHeight +
                                    200
                                  );
                                  setLoading(false)
                                }}
                                width="100%"
                                height={height}
                                title="test"
                                itemProp="description"
                                className="edgtf-post-excerpt"
                                srcDoc={detail.description}
                              />
                            </div>
                          )}

                          {detail.pdf && (
                            <div className="infobooth-pdf">
                              <a
                                href={`${process.env.NEXT_APP_EVENTCENTER_URL}/assets/${informationModules[moduleName]}/${detail.pdf}`}
                                download
                                target="_blank" rel="noreferrer"
                                style={{
                                  border: "none !important",
                                  float: "left",
                                }}
                              >
                                <img
                                  alt=""
                                  className="infoBoothImage"
                                  src={`${process.env.NEXT_APP_EVENTCENTER_URL}/_mobile_assets/images/pdf.png`}
                                  width="40"
                                  style={{ border: "none !important" }}
                                />
                              </a>
                              <a
                                href={`${process.env.NEXT_APP_EVENTCENTER_URL}/assets/${informationModules[moduleName]}/${detail.pdf}`}
                                className="link_infobooth"
                                target="_blank" rel="noreferrer"
                                download
                              >
                                <span>View Document</span>
                              </a>
                            </div>
                          )}
                        </div>
                      </div>
                      {detail.image && detail.image_position !== 'top' && (
                        <div className="edgtf-post-image">
                          <a itemProp="url" href="">
                            {detail.image && detail.image !== "" ? (
                              <img
                                onLoad={(e) => e.target.style.opacity = 1}
                                src={
                                  process.env.NEXT_APP_EVENTCENTER_URL +
                                  `/assets/${informationModules[moduleName]}/temp/` +
                                  detail.image
                                }
                                className="attachment-full size-full wp-post-image"
                                width="1500"
                                height="500"
                                alt="g"
                              />
                            ) : (
                              <Image objectFit='contain' layout="fill"
                                onLoad={(e) => e.target.style.opacity = 1}
                                src=""
                                alt="g"
                              />
                            )}
                          </a>
                        </div>
                      )}
                    </div>
                  </article>
                </div>
              </div>
            </div>
          </div>
      </div>
      </div>
    </React.Fragment>
  );
};

export default CmsDetail;
