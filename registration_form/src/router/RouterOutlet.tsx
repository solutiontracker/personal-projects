import React, { ReactElement, FC } from "react";
import { BrowserRouter, Route, Switch, Redirect } from 'react-router-dom';
import MasterLayout from "@/src/app/components/layout/MasterLayout";
import Registration from '@/src/app/components/registration/Registration';
import AddAttendee from '@/src/app/components/registration/AddAttendee';
import EventRegistrationCode from '@/src/app/components/EventRegistrationCode';
import ManageHotelBooking from '@/src/app/components/ManageHotelBooking';
import OrderSummary from '@/src/app/components/OrderSummary';
import PaymentInformation from '@/src/app/components/PaymentInformation';
import RegistrationSuccess from '@/src/app/components/RegistrationSuccess';
import Stripe from '@/src/app/components/payments/stripe/Index';
import Error404 from '@/src/app/components/Error404';
import OrderNotFound from '@/src/app/components/OrderNotFound';
import WaitingLinkExpired from '@/src/app/components/WaitingLinkExpired';
import AutoRegistrationSuccess from '@/src/app/components/AutoRegistrationSuccess';
import CancelWaitingListOrder from '@/src/app/components/CancelWaitingListOrder';
import CookiePolicy from '@/src/app/components/CookiePolicy';
import CustomForms from "@/src/app/components/customForms/CustomForms";
import in_array from "in_array";

const RouterOutlet: FC<any> = (): ReactElement => {

  return (
    <BrowserRouter>
      <Switch>
        <MasterLayout>
          <Route path="/:url/:provider(admin','sale|attendee|embed)/cookie-policy" component={CookiePolicy} />
          <Route path="/:url/event-registration-code" component={EventRegistrationCode} exact={true} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)" component={AddAttendee} exact={true} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/:section(manage-attendee|manage-items|manage-keywords|manage-sub-registrations|manage-documents)/:order_id?/:attendee_id?" component={Registration} exact={true} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/:section(registration-form)/:registration_form_id" component={Registration} exact={true} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/custom-forms/:order_id/:attendee_id/:form_id?" component={CustomForms} exact={true} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/:section(autoregister)/:ids" component={Registration} exact={true} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/manage-hotel-booking/:order_id/:attendee_id" component={ManageHotelBooking} exact={true} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/order-summary/:order_id/:is_waiting?" component={OrderSummary} exact={true} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/payment-information/:order_id" component={PaymentInformation} exact={true} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/registration-success/:order_id" component={RegistrationSuccess} exact={true} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/auto-registration-success/:order_id" component={AutoRegistrationSuccess} exact={true} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/payment/stripe/:order_id" component={Stripe} exact={true} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/404" component={Error404} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/no-order-found" component={OrderNotFound} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/waiting-link-expired" component={WaitingLinkExpired} />
          <Route path="/:url/:provider(admin|sale|attendee|embed)/cancel-waitinglist-order/:order_id" component={CancelWaitingListOrder} />
          <Route path="/:url/:provider/:param1?/:param2?" render={({ match }) => {
            const provider = match.params.provider;
            const param1 = match.params.param1;
            if (!in_array(provider, ['admin', 'sale', 'attendee', 'embed', 'event-registration-code']) || ((param1 !== undefined && !in_array(param1, ['cookie-policy', 'manage-attendee','manage-items','manage-keywords','manage-sub-registrations', 'registration-form', 'autoregister', 'manage-hotel-booking', 'order-summary', 'payment-information', 'registration-success', 'auto-registration-success', 'payment', '404', 'no-order-found', 'waiting-link-expired', 'cancel-waitinglist-order', 'manage-documents', 'custom-forms'])))) {
              return <Error404 />;
            }
          }} />
        </MasterLayout>
      </Switch>
    </BrowserRouter>
  );
};

export default RouterOutlet;