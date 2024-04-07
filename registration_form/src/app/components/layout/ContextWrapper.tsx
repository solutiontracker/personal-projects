import React, { FC, ReactNode, useContext, useEffect, useRef } from "react";
import Header from '@/src/app/components/layout/Header';
import Footer from '@/src/app/components/layout/Footer';
import WaitingListAlert from '@/src/app/components/layout/WaitingListAlert';
import { Scrollbars } from 'react-custom-scrollbars';
import ReactTooltip from "react-tooltip";
import Theme from '@/src/app/themes/Theme';
import CookiePolicy from '@/src/app/components/forms/CookiePolicy';
import RegSteps from "@/src/app/components/layout/Steps";
import { EventContext } from "@/src/app/context/event/EventProvider";
import in_array from "in_array";
import { postMessage as postMessageScript } from "@/src/app/helpers";

interface Props {
    children: ReactNode;
}

// functional component
const ContextWrapper: FC<Props> = ({ children }) => {

    const [toggle, setToggle] = React.useState(false);

    const { routeParams } = useContext<any>(EventContext);

    const handleToggle = () => {
        setToggle(!toggle);
    }
    const appRef = useRef<any>(null);
    useEffect(() => {
    if (!appRef?.current) return;
    const resizeObserver = new ResizeObserver(() => {
        console.log(appRef?.current?.scrollHeight)
        postMessageScript({contentHeight:appRef?.current?.scrollHeight});
    });
    resizeObserver.observe(appRef?.current);
    return () => resizeObserver.disconnect(); // clean up 
    }, []);

    return (
        <React.Fragment>
            <Theme />
            {(window.location.pathname.includes('/attendee') || window.location.pathname.includes('/cookie-policy')) && (
                <Header />
            )}
            <main role="main" className={`main-section ${in_array(routeParams?.provider, ["sale", "embed"]) ? 'main-section-embed' : 'ebs-section-popup-top'} ${window.location.pathname.includes('order-summary') || window.location.pathname.includes('registration-success') ? 'review-order-section' : ''} ${toggle && 'ebs-toggle-wrapper'} ${in_array(routeParams?.page, ['registration-information', 'hotel-booking']) && !in_array(routeParams?.provider, ["embed"]) && 'ebs-has-steps'}`}>
                {!in_array(routeParams?.provider, ["embed"]) && <RegSteps handleToggle={handleToggle} toggle={toggle} />}
                <div
                    id="ebs-element-scroll"
                    style={{overflowY: 'auto', width: '100%',minHeight: '100%'}}
                    // onScrollStart={() => { ReactTooltip.hide(); }}
                    // renderView={props => (<div {...props} style={{ ...props.style, overflowX: 'hidden' }} />)}
                    // width='100%'
                    // height='100%'
                >
                    {!window.location.pathname.includes('/cookie-policy') && (
                        <WaitingListAlert />
                    )}
                    <div className="container master-container" ref={appRef}>
                        {children}
                    </div>
                    {(window.location.pathname.includes('/attendee') || window.location.pathname.includes('/cookie-policy')) && (
                        <Footer />
                    )}
                </div>
            </main>
            {in_array(routeParams?.provider, ["attendee"]) && (
                <CookiePolicy />
            )}
        </React.Fragment>
    );
};

export default ContextWrapper;