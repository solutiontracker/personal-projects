import React, { useEffect } from 'react';
import Header from "components/modules/Header";
import {
  subRegistrationSelector,
} from "store/Slices/myAccount/subRegistrationSlice";
import { useRouter } from 'next/router';
import { useSelector } from "react-redux";
import {
  eventSelector
} from "store/Slices/EventSlice";
import AfterLoginSubRegistration from "components/myAccount/profile/AfterLoginSubRegistration";
import Footer from "../modules/Footer";
import CookiePolicy from 'components/ui-components/CookiePolicy';


const MasterLayoutMyAccount = (props) => {

  const { event } = useSelector(eventSelector);

  const { skip } = useSelector(subRegistrationSelector);

  const isAuthenticated = localStorage.getItem(`event${event.id}User`);
  const sub_reg_skip = localStorage.getItem(`${event.url}_sub_reg_skip`);

  const router = useRouter();

  useEffect(() => {
    if (!isAuthenticated) {
      router.push(`/${event.url}`);
    }
  }, [isAuthenticated]);

  return (
    <>
      <Header />
      {(skip || sub_reg_skip) ? props.children : <AfterLoginSubRegistration {...props} />}
      <Footer /> 
      <CookiePolicy/>
    </>
  )
}

export default MasterLayoutMyAccount