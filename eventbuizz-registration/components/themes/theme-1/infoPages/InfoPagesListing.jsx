import React, { useState, useEffect } from "react";
import HeadingElement from "components/ui-components/HeadingElement";
import ActiveLink from "components/atoms/ActiveLink";
import PageHeader from "components/modules/PageHeader";
const InfoPagesListing = ({ listing, moduleName, breadCrumbData, eventSiteModuleName, eventUrl, menu_id, main_menu_id, eventsiteSettings }) => {

  const [breadCrumbs, setBreadCrumbs] = useState(arrayTraverse(breadCrumbData, menu_id, eventSiteModuleName, main_menu_id));

  const [cmsListing, setCmsListing] = useState(getListing(listing, menu_id, main_menu_id));

  const [currentMenu, setCurrentMenu] = useState(menu_id);

  const onCrumbClick = (e, crumb) => {
    e.preventDefault();
    if (crumb.id !== currentMenu) {
      if (parseInt(crumb.id) === parseInt(main_menu_id)) {
        setBreadCrumbs([{ id: main_menu_id, name: eventSiteModuleName, type: "main_menu" }]);
        setCmsListing(getListing(listing, crumb.id, main_menu_id));
      } else {
        setCurrentMenu(crumb.id);
        setBreadCrumbs([{ id: main_menu_id, name: eventSiteModuleName, type: "main_menu" }, { id: crumb.id, name: crumb.info.name, type: "menu" }]);
        setCmsListing(getListing(listing, crumb.id, main_menu_id));
      }
    }
  }

  useEffect(() => {
    setBreadCrumbs(arrayTraverse(breadCrumbData, menu_id, eventSiteModuleName, main_menu_id));
    setCmsListing(getListing(listing, menu_id ,main_menu_id));
    setCurrentMenu(menu_id);
  }, [listing, breadCrumbData, eventSiteModuleName, menu_id])

  return (
   <React.Fragment>
       <PageHeader label={eventSiteModuleName} showBreadcrumb={eventsiteSettings.show_eventsite_breadcrumbs} breadCrumbs={(type) => {
            return (<nav aria-label="breadcrumb" className={`ebs-breadcrumbs ${type !== "background" ? "ebs-dark": ""}`}>
            <ul className="breadcrumb">
              {breadCrumbs.map((crumb, i) => (
                <li className="breadcrumb-item" key={i}>
                  {(crumb.id === currentMenu) ? crumb.name : <a href="javascript:void(0)" onClick={(e) => { onCrumbClick(e, crumb) }}>{crumb.name}</a>}
                </li>
              ))}
            </ul>
          </nav>)
    }} />
    <div
      className="edgtf-parallax-section-holder ebs-default-padding">
      <div className="container">        
        <div className="ebs-inner-page-wrapper">
          <ul>
            {cmsListing && cmsListing.map((item, i) => (
              <li key={i}>
                {item.page_type === 2 &&
                  <ActiveLink href={`/${eventUrl}/info_pages/${item.id}`}>
                    {item.info.name}
                  </ActiveLink>
                }
                {item.page_type === 3 &&
                  <a href={`${item.website_protocol}${item.url}`} target="_blank" rel="noreferrer"  >{item.info.name}</a>
                }
                {item.page_type === 1 &&
                  <a href="javascript:void(0)" onClick={(e) => { onCrumbClick(e, item) }}>
                    {item.info.name}
                  </a>
                }
                {item.submenu &&
                  <ul>
                    {item.submenu.map((subitem, j) => (
                      <li key={j}>
                        {subitem.page_type === 2 &&
                          <ActiveLink href={`/${eventUrl}/info_pages/${subitem.id}`}>
                            {subitem.info.name}
                          </ActiveLink>
                        }
                        {subitem.page_type === 3 &&
                          <a href={`${subitem.website_protocol}${subitem.url}`} target="_blank" rel="noreferrer"  >{subitem.info.name}</a>
                        }
                      </li>
                    ))}
                  </ul>
                }
              </li>
            ))
            }
          </ul>
        </div>
      </div>
    </div>
   </React.Fragment>
  );
};


export default InfoPagesListing;


const arrayTraverse = (array, menu_id, eventSiteModuleName, main_menu_id) => {
  let returnArray = [{ id: main_menu_id, name: eventSiteModuleName, type: "main_menu" }];
  let toFolder = array.find((item) => (item.id === parseFloat(main_menu_id))) !== (undefined || null) ? array.find((item) => (item.id === parseFloat(main_menu_id))) : [];
  if (menu_id && menu_id !== main_menu_id) {
    toFolder = toFolder.submenu.find((item) => (item.id === parseFloat(menu_id)));
  }
  if (toFolder) {
    returnArray.push({ id: toFolder.id, name: toFolder.info.name, type: toFolder.page_type ? toFolder.page_type : 'menu' });
  }
  return returnArray;
}

const getListing = (array, menu_id, main_menu_id) => {
    let arr = array.find((item) => (item.id === parseFloat(main_menu_id))) != (undefined || null) ? array.find((item) => (item.id === parseFloat(main_menu_id))).submenu : [];
    if (menu_id && parseFloat(menu_id) !== parseFloat(main_menu_id)) {     
        arr = arr.find((item) => (item.id === parseFloat(menu_id))) != (null || undefined) ? arr.find((item) => (item.id === parseFloat(menu_id))).submenu : [];
    }
    return arr;
}



