import React, {Suspense, lazy} from "react";
import { globalSelector } from "store/Slices/GlobalSlice";
import { eventSelector } from "store/Slices/EventSlice";
import { useSelector } from "react-redux";
import PageLoader from "./ui-components/PageLoader";
import EventDescription from "./modules/EventDescription";
import PageContent from "./modules/manage-packages/PageContent";
const Speaker = lazy(() => import("components/modules/speakers/Speaker"));
const Gallery = lazy(() => import("components/modules/Gallery"));
const Video = lazy(() => import("components/modules/Video"));
const Sponsor = lazy(() => import("components/modules/sponsor/Sponsor"));
const Program = lazy(() => import("components/modules/program/Program"));
const Exhibitor = lazy(() => import("components/modules/exhibitor/Exhibitor"));
const SocialShare = lazy(() => import("components/modules/SocialShare"));
const RegisterNow = lazy(() => import("components/modules/RegisterNow"));
const Banner = lazy(() => import("components/modules/Banner"));
const Map = lazy(() => import("components/modules/Map"));
const Footer = lazy(() => import("components/modules/Footer"));
const EventInformation = lazy(() => import("components/modules/EventInformation"));
const NewsLetterSubscription = lazy(() => import("components/modules/NewsLetterSubscription"));
const CustomSection1 = lazy(() => import("components/modules/CustomSection1"));
const CustomSection2 = lazy(() => import("components/modules/CustomSection2"));
const CustomSection3 = lazy(() => import("components/modules/CustomSection3"));
const SortableBanner = lazy(() => import("components/modules/SortableBanner"));
const News = lazy(() => import("components/modules/news/News"));

const Index = () => {
  
  const { event } = useSelector(eventSelector);
  const { loadedSections, loadCount } = useSelector(globalSelector);
  const { layoutSections } = event;
  return (
    <Suspense fallback={<PageLoader/>}>
      <React.Fragment>
        {loadedSections !== loadCount && <PageLoader className="fixed" />}    
        {event &&
          layoutSections &&
          layoutSections.map((section, i) => {
            return (() => {
   
              if (section.module_alias === "social_media_share" && section.status === 1)
                return <SocialShare key={i} />;
              else if (section.module_alias === "event_description" && section.status === 1)
                return <EventDescription event={event} key={i} />;
              else if (section.module_alias === "custom_html2" && section.status === 1)
                return <CustomSection2 key={i}/>;
              else if (section.module_alias === "custom_html1" && section.status === 1)
                return <CustomSection1 key={i} />;
              else if (section.module_alias === "custom_html3" && section.status === 1)
                return <CustomSection3 key={i} />;
              else if (section.module_alias === "event_info" && section.status === 1) return <EventInformation key={i}/>;
              else if (section.module_alias === "newsletter_subscription" && section.status === 1) return <NewsLetterSubscription key={i}/>;
              else if (section.module_alias === "agenda" && section.status === 1)
                return <Program homePage={true} key={i} />;
              else if (section.module_alias === "sponsor" && section.status === 1)
                return <Sponsor homePage={true} key={i} />;
              else if (section.module_alias === "top_banner" && section.status === 1) return <Banner key={i} />;
              else if (section.module_alias === "banner_sort" && section.status === 1) return <SortableBanner key={i} />;
              else if (section.module_alias === "register_now" && section.status === 1)
                return <RegisterNow key={i}/>;
              else if (section.module_alias === "exhibitor" && section.status === 1)
                return <Exhibitor homePage={true} key={i} />;
              else if (section.module_alias === "speaker" && section.status === 1)
                return <Speaker homePage={true} key={i} />;
              else if (section.module_alias === "map" && section.status === 1) 
              return <Map key={i} />;
              else if (section.module_alias === "video" && section.status === 1)
                return <Video homePage={true} key={i} />;
              else if (section.module_alias === "gallery" && section.status === 1)
                return <Gallery homePage={true} key={i} />;
              else if (section.module_alias === "streaming" && section.status === 1) return <div key={i}></div>;
              else if (section.module_alias === "waiting_list" && section.status === 1)
                return <div key={i}></div>;
              else if (section.module_alias === "registration_packages" && section.status === 1)
                return <PageContent isHome={true} key={i} />;
              else if (section.module_alias === "news" && section.status === 1)
                return <News key={i} homePage={true} />;
            })();
          })}
          
      </React.Fragment>
    </Suspense>

  );
};

export default Index;
