import * as React from "react";
import ActiveLink from "components/atoms/ActiveLink";
import { Scrollbars } from "react-custom-scrollbars-2";
import MyProfileSidebar from "components/myAccount/profile/MyProfileSidebar";
import Image from 'next/image'

class Variation5 extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      module: false,
      showMenu: false,
      menus: this.props.event.header_data,
      topMenu: this.props.topMenu,
      menuresponsive: this.props.event.header_data,
      width: window.innerWidth,
      event:
        this.props.event !== undefined && this.props.event
          ? this.props.event
          : "",
    };
  }

  componentDidMount() {
    this._isMounted = true;
    window.addEventListener("scroll", this.handleScroll.bind(this), false);
  }

  componentWillUnmount() {
    this._isMounted = false;
    window.removeEventListener("scroll", this.handleScroll.bind(this));
  }

  componentDidUpdate(prevProps) {
    if (prevProps.loaded !== this.props.loaded && typeof window !== 'undefined') {
      this.handleFunction();
      document
        .getElementsByTagName("body")[0]
        .classList.remove("ebs-scroll-body-content");
      this.setState({
        showMenu: false,
      });
    }
  }

  handleScroll = () => {
    if (typeof window !== 'undefined') {
      const _app = document.getElementById("App");
      const _theme = document.getElementById("ebs-header-master")
        .classList.contains("ebs-fixed-header");
      if (window.scrollY > 350) {
        _app.classList.add("ebs-header-sticky");
        _app.style.paddingTop = _theme ? 0 : document.querySelectorAll("#App > .ebs-header-main-wrapper")[0].offsetHeight + "px";
      } else {
        _app.classList.remove("ebs-header-sticky");
        _app.style.paddingTop = 0 + "px";
      }
    }
  };

  accordionToggle = (e) => {
    if (typeof window !== 'undefined') {
      //variables
      var _this = e.target;
      var panel = _this.nextElementSibling;
      var panelParent = _this.parentElement.parentElement;
      var coursePanel = document.getElementsByClassName("ebs-accordion-dropdown");
      if (panel) {
        /*if pannel is already open - minimize*/
        if (panel.style.maxHeight) {
          panel.style.maxHeight = null;
          _this.classList.remove("active");
        } else {

          //opens the specified pannel
          panel.style.maxHeight = panel.scrollHeight + "px";

          for (var iii = 0; iii < coursePanel.length; iii++) {
            // coursePanel[iii].style.maxHeight = null;
            if (coursePanel[iii] === panelParent) {
              coursePanel[iii].style.maxHeight =
                coursePanel[iii].scrollHeight + panel.scrollHeight + "px";
            }
          }
          //adds the 'active' addition to the css.
          _this.classList.add("active");
        }
      }
    }
  };

  handleFunction = () => {
    if (typeof window !== 'undefined') {
      document
        .getElementById("ebs-header-master")
        .classList.remove("ebs-fixed-header");
      document
        .getElementById("ebs-header-master")
        .classList.remove("ebs-light-header");
      if (window.innerWidth >= 991 && document.getElementById("ebs-header-master").nextSibling.dataset) {
        var _nextSibling =
          document.getElementById("ebs-header-master").nextSibling.dataset.fixed;
        if (_nextSibling === "true") {
          document
            .getElementById("ebs-header-master")
            .classList.add("ebs-fixed-header");
        } else {
          document
            .getElementById("ebs-header-master")
            .classList.add("ebs-light-header");
        }
      }
    }
  };

  handleMenu = () => {
    if (typeof window !== 'undefined') {
      this.setState({ showMenu: !this.state.showMenu }, () => {
        const _body = document.getElementsByTagName("body")[0];
        const _scroll = document.body.classList.contains(
          "ebs-scroll-body-content"
        );
        if (_scroll) {
          _body.classList.remove("ebs-scroll-body-content");
        } else {
          _body.classList.add("ebs-scroll-body-content");
        }
      });
    }
  };

  render() {
    const { menus, event, topMenu } = this.state;
    if (menus.length === 0) return <div>Loading...</div>;
    return (
      <div
        style={{ transform: 'none', animation: 'none' }}
        id="ebs-header-master"
        className="ebs-main-header-v3 ebs-main-header-v7 ebs-header-main-wrapper ebs-header-shadow ebs-hide-header ebs-no-padding"
      >
        <div className="container">
          <div className="row d-flex align-items-center">
            <div className="col-lg-3 col-6">
              <div className="ebs-logo-main">
              <ActiveLink target={event.eventsiteSettings?.third_party_redirect === 0 ? `_self` : '_blank'} href={event.eventsiteSettings?.third_party_redirect === 0 ? `/${event.url}` : event.eventsiteSettings.third_party_redirect_url}>
                  {event.settings.header_logo ? (
                    <img
                      src={`${process.env.NEXT_APP_EVENTCENTER_URL}/assets/event/branding/${event.settings.header_logo}`}
                      alt=""
                    />
                  ) : (
                    <img
                      src={`${process.env.NEXT_APP_EVENTCENTER_URL}/_mobile_assets/images/logo-header@2x.png`}
                      alt=""
                    />
                  )}
                </ActiveLink>
              </div>
            </div>
            <div className="col-lg-9 col-6 d-flex align-items-center justify-content-end">
              {parseInt(event.eventsiteSettings.eventsite_menu) === 1 && <nav className="navbar navbar-expand-lg navbar-light">
                <button
                  className="navbar-toggler"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#navbarSupportedContentFixed"
                  aria-controls="navbarSupportedContentFixed"
                  aria-expanded="false"
                  style={{ display: "inline-block" }}
                  onClick={this.handleMenu.bind(this)}
                  aria-label="Toggle navigation"
                >
                  <span className="navbar-toggler-icon"></span>
                </button>
                <div
                  className={`collapse  ${this.state.showMenu ? "show" : ""}`}
                  id="navbarSupportedContentFixed"
                >
                  <div className="ebs-scroll-container">
                    <div
                      onClick={this.handleMenu.bind(this)}
                      id="btn-menu-close"
                    ></div>
                    <Scrollbars
                      autoHide
                      className="ebs-scorll"
                      style={{ width: "100%", height: "100%" }}
                    >
                      <div className="ebs-scorll-inner">
                        <ul className="nav navbar-nav m-0">
                          {topMenu.map((menu) => (
                            <li className="nav-item" key={menu.id}>
                              {(menu.alias === "gallery" ||
                                (menu.alias === "myaccount" && !this.props.userExist) ||
                                menu.alias === "practicalinformation" ||
                                menu.alias === "additional_information" ||
                                menu.alias === "general_information" ||
                                menu.alias === "info_pages" 
                                ) && (
                                  <>
                                  {menu.link_path == true ? 
                                  
                                  <ActiveLink
                                  className="nav-link" activeClassName="nav-link active"
                                        aria-current="page"
                                        target={menu.menu_url.indexOf("http") !== -1 ? "_blank" : ""}
                                        href={`${menu.menu_url}`}
                                      >
                                           <span className="ebs-nav-item">
                                      {menu.module}
                                    </span>
                                      </ActiveLink>
                                  
                                  : <span
                                    onClick={this.accordionToggle.bind(this)}
                                    className="nav-link ebs-accordion-button"
                                  >
                                    <span className="ebs-nav-item">
                                      {menu.module}
                                    </span>
                                  </span>}
                                  </>
                                )}
                              {menu.alias !== "gallery" &&
                                menu.alias !== "myaccount" &&
                                menu.alias !== "info_pages" &&
                                menu.alias !== "practicalinformation" &&
                                menu.alias !== "additional_information" &&
                                menu.alias !== "general_information" && (
                                  menu.alias === "custom" ? (
                                    menu.url !== "" ? (
                                      <a
                                        className="nav-link"
                                        aria-current="page"
                                        href={menu.url}
                                      >
                                        <span className="ebs-nav-item">
                                      {menu.module}
                                    </span>
                                      </a>
                                    ) : (
                                      <ActiveLink
                                        className="nav-link active"
                                        aria-current="page"
                                        href={`/${this.props.event.url}/cms/${menu.page_id}`}
                                      >
                                          <span className="ebs-nav-item">
                                      {menu.module}
                                    </span>
                                      </ActiveLink>
                                    )
                                  ) : (
                                    <ActiveLink
                                      className="nav-link" activeClassName="nav-link active"
                                      aria-current="page"
                                      href={`/${this.props.event.url}/${menu.alias}`}
                                    >
                                        <span className="ebs-nav-item">
                                      {menu.module}
                                    </span>
                                    </ActiveLink>
                                  )
                                )}
                              {menu.alias === "gallery" && (
                                <ul className="dropdown-menu ebs-accordion-dropdown">
                                  {menus["gallery_sub_menu"].map(
                                    (myaccount, k) => (
                                      <li className="nav-item" key={k}>
                                        <ActiveLink
                                          aria-current="page"
                                          className="nav-link" activeClassName="nav-link active"
                                          href={
                                            "/" +
                                            this.props.event.url +
                                            "/" +
                                            myaccount.alias
                                          }
                                          key={myaccount.id}
                                        >
                                          <span className="ebs-nav-item">
                                            {myaccount.module}
                                          </span>
                                        </ActiveLink>
                                      </li>
                                    )
                                  )}
                                </ul>
                              )}
                              {menu.alias === "myaccount" && !this.props.userExist && (
                                <ul className="dropdown-menu">
                                  {!this.props.userExist ? menus["my_account_sub_menu"].map(
                                    (myaccount, k) => (
                                      <li className="nav-item" key={k}>
                                        {myaccount.alias !== "login" ? (
                                        ((myaccount.alias === 'register' && this.props.registerDateEnd) || (myaccount.alias !== 'register')) ?
                                        <ActiveLink
                                          aria-current="page"
                                          className="nav-link" activeClassName="nav-link active"
                                          href={`${
                                            myaccount.alias === 'register' ? this.props.regisrationUrl :
                                            "/" +
                                            this.props.event.url +
                                            "/" +
                                            myaccount.alias
                                          }`}
                                          key={myaccount.id}
                                        >
                                          <span className="ebs-nav-item">
                                            {myaccount.module}
                                          </span>
                                        </ActiveLink>
                                        : null
                                        ) :
                                          <div className="nav-link" onClick={() => { this.props.setShowLogin(true) }}>
                                            <span className="ebs-nav-item">
                                              {myaccount.module}
                                            </span>
                                          </div>
                                        }
                                      </li>
                                    )
                                  ) : (<li className="nav-item">
                                    <ActiveLink
                                      aria-current="page"
                                      className="nav-link" activeClassName="nav-link active"
                                      href={`/${event.url}/profile`}
                                    >
                                      My Profile
                                    </ActiveLink>
                                  </li>
                                  )}
                                </ul>
                              )}

                              {(menu.alias === "practicalinformation" && (menus["practical_info_menu"].length > 1 || (menus["practical_info_menu"].length == 1 && event.header_data["practical_info_menu"][0].page_type === "menu"))) && (
                                <ul className="dropdown-menu ebs-accordion-dropdown">
                                  {menus["practical_info_menu"].map(
                                    (pItem, k) =>
                                      pItem.page_type &&
                                        pItem.page_type === "menu" ? (
                                        <li className="nav-item" key={pItem.id}>
                                          <span
                                            onClick={this.accordionToggle.bind(
                                              this
                                            )}
                                            className="nav-link ebs-accordion-button"
                                          >
                                            <span className="ebs-nav-item">
                                              {pItem.info.name}
                                            </span>
                                          </span>
                                          {pItem.submenu.length > 0 && (
                                            <ul className="dropdown-menu ebs-accordion-dropdown">
                                              {pItem.submenu.map(
                                                (subitem, k) => (
                                                  <li
                                                    className="nav-item"
                                                    key={k}
                                                  >
                                                    {subitem.page_type &&
                                                      subitem.page_type === 2 ? (
                                                      <a
                                                        className="nav-link" 
                                                        aria-current="page"
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        href={`${subitem.website_protocol}${subitem.url}`}
                                                      >
                                                        {subitem.info.name}
                                                      </a>
                                                    ) : (
                                                      <ActiveLink
                                                        aria-current="page"
                                                        className="nav-link" activeClassName="nav-link active"
                                                        href={
                                                          "/" +
                                                          this.props.event.url +
                                                          "/" +
                                                          menu.alias +
                                                          "/" +
                                                          subitem.id
                                                        }
                                                        key={subitem.id}
                                                      >
                                                        {subitem.info.name}
                                                      </ActiveLink>
                                                    )}
                                                  </li>
                                                )
                                              )}
                                            </ul>
                                          )}
                                        </li>
                                      ) : (
                                        <li className="nav-item" key={k}>
                                          {pItem.page_type &&
                                            pItem.page_type === 2 ? (
                                            <a
                                              className="nav-link"
                                              aria-current="page"
                                              target="_blank"
                                              rel="noreferrer"
                                              href={`${pItem.website_protocol}${pItem.url}`}
                                            >
                                              {pItem.info.name}
                                            </a>
                                          ) : (
                                            <ActiveLink
                                              aria-current="page"
                                              className="nav-link" activeClassName="nav-link active"
                                              href={
                                                "/" +
                                                this.props.event.url +
                                                "/" +
                                                menu.alias +
                                                "/" +
                                                pItem.id
                                              }
                                              key={pItem.id}
                                            >
                                              {pItem.info.name}
                                            </ActiveLink>
                                          )}
                                        </li>
                                      )
                                  )}
                                </ul>
                              )}
                              {(menu.alias === "additional_information" && (menus["additional_info_menu"].length > 1 ||  (menus["additional_info_menu"].length == 1 && event.header_data["additional_info_menu"][0].page_type === "menu"))) && (
                                <ul className="dropdown-menu ebs-accordion-dropdown">
                                  {menus["additional_info_menu"].map(
                                    (aItem, k) =>
                                      aItem.page_type &&
                                        aItem.page_type === "menu" ? (
                                        <li className="nav-item" key={aItem.id}>
                                          <span
                                            onClick={this.accordionToggle.bind(
                                              this
                                            )}
                                            className="nav-link ebs-accordion-button"
                                          >
                                            <span className="ebs-nav-item">
                                              {aItem.info.name}
                                            </span>
                                          </span>
                                          {aItem.submenu.length > 0 && (
                                            <ul className="dropdown-menu ebs-accordion-dropdown">
                                              {aItem.submenu.map(
                                                (subitem, k) => (
                                                  <li
                                                    className="nav-item"
                                                    key={k}
                                                  >
                                                    {(subitem.page_type &&
                                                      subitem.page_type === 2) ? (
                                                      <a
                                                        className="nav-link"
                                                        aria-current="page"
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        href={`${subitem.website_protocol}${subitem.url}`}
                                                      >
                                                        {subitem.info.name}
                                                      </a>
                                                    ) : (
                                                      <ActiveLink
                                                        aria-current="page"
                                                        className="nav-link" activeClassName="nav-link active"
                                                        href={
                                                          "/" +
                                                          this.props.event.url +
                                                          "/" +
                                                          menu.alias +
                                                          "/" +
                                                          subitem.id
                                                        }
                                                        key={subitem.id}
                                                      >
                                                        {subitem.info.name}
                                                      </ActiveLink>
                                                    )}
                                                  </li>
                                                )
                                              )}
                                            </ul>
                                          )}
                                        </li>
                                      ) : (
                                        <li className="nav-item" key={k}>
                                          {(aItem.page_type &&
                                            aItem.page_type === 2) ? (
                                            <a
                                              className="nav-link"
                                              aria-current="page"
                                              target="_blank"
                                              rel="noreferrer"
                                              href={`${aItem.website_protocol}${aItem.url}`}
                                            >
                                              {aItem.info.name}
                                            </a>
                                          ) : (
                                            <ActiveLink
                                              aria-current="page"
                                              className="nav-link" activeClassName="nav-link active"
                                              href={
                                                "/" +
                                                this.props.event.url +
                                                "/" +
                                                menu.alias +
                                                "/" +
                                                aItem.id
                                              }
                                              key={aItem.id}
                                            >
                                              {aItem.info.name}
                                            </ActiveLink>
                                          )}
                                        </li>
                                      )
                                  )}
                                </ul>
                              )}
                              {(menu.alias === "general_information" && (menus["general_info_menu"].length > 1 || (menus["general_info_menu"].length == 1 && event.header_data["general_info_menu"][0].page_type === "menu"))) && (
                                <ul className="dropdown-menu ebs-accordion-dropdown">
                                  {menus["general_info_menu"].map((gItem, k) =>
                                    gItem.page_type &&
                                      gItem.page_type === "menu" ? (
                                      <li className="nav-item" key={gItem.id}>
                                        <span
                                          onClick={this.accordionToggle.bind(
                                            this
                                          )}
                                          className="nav-link ebs-accordion-button"
                                        >
                                          <span className="ebs-nav-item">
                                            {gItem.info.name}
                                          </span>
                                        </span>
                                        {gItem.submenu.length > 0 && (
                                          <ul className="dropdown-menu ebs-accordion-dropdown">
                                            {gItem.submenu.map((subitem, k) => (
                                              <li className="nav-item" key={k}>
                                                {subitem.page_type &&
                                                  subitem.page_type === 2 ? (
                                                  <a
                                                    className="nav-link"
                                                    aria-current="page"
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    href={`${subitem.website_protocol}${subitem.url}`}
                                                  >
                                                    {subitem.info.name}
                                                  </a>
                                                ) : (
                                                  <ActiveLink
                                                    aria-current="page"
                                                    className="nav-link" activeClassName="nav-link active"
                                                    href={
                                                      "/" +
                                                      this.props.event.url +
                                                      "/" +
                                                      menu.alias +
                                                      "/" +
                                                      subitem.id
                                                    }
                                                    key={subitem.id}
                                                  >
                                                    {subitem.info.name}
                                                  </ActiveLink>
                                                )}
                                              </li>
                                            ))}
                                          </ul>
                                        )}
                                      </li>
                                    ) : (
                                      <li className="nav-item" key={k}>
                                        {gItem.page_type &&
                                          gItem.page_type === 2 ? (
                                          <a
                                            className="nav-link"
                                            aria-current="page"
                                            target="_blank"
                                            rel="noreferrer"
                                            href={`${gItem.website_protocol}${gItem.url}`}
                                          >
                                            {gItem.info.name}
                                          </a>
                                        ) : (
                                          <ActiveLink
                                            aria-current="page"
                                            className="nav-link" activeClassName="nav-link active"
                                            href={
                                              "/" +
                                              this.props.event.url +
                                              "/" +
                                              menu.alias +
                                              "/" +
                                              gItem.id
                                            }
                                            key={gItem.id}
                                          >
                                            {gItem.info.name}
                                          </ActiveLink>
                                        )}
                                      </li>
                                    )
                                  )}
                                </ul>
                              )}
                              
                              {(menu.alias === "info_pages" && menus["info_pages_menu"].find((p)=>p.id == menu.page_id) !== undefined && (  menus["info_pages_menu"].find((p)=>p.id == menu.page_id)['submenu'].length > 1 ||  (menus["info_pages_menu"].find((p)=>p.id == menu.page_id)['submenu'].length == 1 && menus["info_pages_menu"].find((p)=>p.id == menu.page_id)['submenu'][0].page_type === "menu"))) &&  (
                                <ul className="dropdown-menu ebs-accordion-dropdown">
                                  {menus["info_pages_menu"].find((item)=>(parseInt(item.id) === parseInt(menu.page_id))) !== undefined && menus["info_pages_menu"].find((item)=>(parseInt(item.id) === parseInt(menu.page_id))).submenu.map((gItem, k) =>
                                (gItem.page_type && gItem.page_type === 1  &&  gItem.submenu && gItem.submenu.length > 0) ? (
                                  <li className="nav-item" key={gItem.id}>
                                        <span
                                          onClick={this.accordionToggle.bind(
                                            this
                                            )}
                                            className="nav-link ebs-accordion-button"
                                            >
                                          <span className="ebs-nav-item">
                                            {gItem.info.name}
                                          </span>
                                        </span>
                                        {gItem.submenu.length > 0 && (
                                          <ul className="dropdown-menu ebs-accordion-dropdown">
                                            {gItem.submenu.map((subitem, k) => (
                                              <li className="nav-item" key={k}>
                                                {subitem.page_type &&
                                                  subitem.page_type === 3 ? (
                                                  <a
                                                    className="nav-link if1"
                                                    aria-current="page"
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    href={`${subitem.website_protocol}${subitem.url}`}
                                                  >
                                                    {subitem.info.name}
                                                  </a>
                                                ) : (
                                                  <ActiveLink
                                                    aria-current="page"
                                                    className="nav-link if2" activeClassName="nav-link active"
                                                    href={
                                                      "/" +
                                                      this.props.event.url +
                                                      "/" +
                                                      menu.alias +
                                                      "/" +
                                                      subitem.id
                                                    }
                                                    key={subitem.id}
                                                  >
                                                    {subitem.info.name}
                                                  </ActiveLink>
                                                )}
                                              </li>
                                            ))}
                                          </ul>
                                        )}
                                      </li>
                                    ) : (
                                      <li className="nav-item" key={k}>
                                        {gItem.page_type &&
                                          gItem.page_type === 3 ? (
                                          <a
                                            className="nav-link else1"
                                            aria-current="page"
                                            target="_blank"
                                            rel="noreferrer"
                                            href={`${gItem.website_protocol}${gItem.url}`}
                                          >
                                            {gItem.info.name}
                                          </a>
                                        ) : (
                                          gItem.page_type === 2 && <ActiveLink
                                            aria-current="page"
                                            className="nav-link else2" activeClassName="nav-link active"
                                            href={
                                              "/" +
                                              this.props.event.url +
                                              "/" +
                                              menu.alias +
                                              "/" +
                                              gItem.id
                                            }
                                            key={gItem.id}
                                          >
                                            {gItem.info.name}
                                          </ActiveLink>
                                        )}
                                      </li>
                                    )
                                  )}
                                </ul>
                              )}
                              
                            </li>
                          ))}
                           
                        </ul>
                      </div>
                    </Scrollbars>
                  </div>
                </div>
              </nav>}
              {this.props.userExist && <MyProfileSidebar />}
            </div>
          </div>
        </div>
      </div>
    );
  }
}

export default Variation5;
