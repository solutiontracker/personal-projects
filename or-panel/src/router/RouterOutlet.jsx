import * as React from 'react';
import { Switch, Redirect, Route } from 'react-router-dom';
import AuthLayoutRoute from "@/app/auth/layout/AuthLayoutRoute";
import DashboardLayoutRoute from "@/app/layout/DashboardLayoutRoute";
import MasterLayoutRoute from "@/app/layout/MasterLayoutRoute";
import EventFullScreenLayoutRoute from "@/app/layout/EventFullScreenLayoutRoute";
import OrganizerLayout from "@/app/layout/OrganizerLayout";
import Dashboard from "@/app/event/Dashboard";
import Events from "@/app/event/Events";
import asyncComponent from '../AsyncComponent';
import WaitingListOrders from "../app/components/app/event_site/billing/items/WaitingListOrders";

const Login = asyncComponent(() =>
  import('@/app/auth/Login').then(module => module.default)
)

const EventModuleOrder = asyncComponent(() =>
  import('@/app/settings/EventModuleOrder').then(module => module.default)
)

const EventSiteModuleOrder = asyncComponent(() =>
  import('@/app/settings/EventSiteModuleOrder').then(module => module.default)
)

const AttendeeWidget = asyncComponent(() =>
  import('@/app/attendee/AttendeeWidget').then(module => module.default)
)

const EventDocument = asyncComponent(() =>
  import('@/app/documents/EventDocument').then(module => module.default)
)

const ResetPasswordRequest = asyncComponent(() =>
  import('@/app/auth/ResetPasswordRequest').then(module => module.default)
)

const ResetPassword = asyncComponent(() =>
  import('@/app/auth/ResetPassword').then(module => module.default)
)

const Branding = asyncComponent(() =>
  import('@/app/settings/Branding').then(module => module.default)
)

const PageCrashed = asyncComponent(() =>
  import('@/PageCrashed').then(module => module.default)
)
const AssignEvent = asyncComponent(() =>
  import('@/app/event/components/AssignEvent').then(module => module.default)
)

const EventWrapper = asyncComponent(() =>
  import('@/app/event/EventWrapper').then(module => module.default)
)

const HotelManagement = asyncComponent(() =>
  import('@/app/hotel/HotelManagement').then(module => module.default)
)

const Survey = asyncComponent(() =>
  import('@/app/survey/Survey').then(module => module.default)
)

const SurveyQuestion = asyncComponent(() =>
  import('@/app/survey/SurveyQuestion').then(module => module.default)
)
const SurveyLeaderBoard = asyncComponent(() =>
  import('@/app/survey/SurveyLeaderBoard').then(module => module.default)
)
const SurveyLeaderBoardOuter = asyncComponent(() =>
  import('@/app/survey/SurveyLeaderBoardOuter').then(module => module.default)
)

const SurveyGroup = asyncComponent(() =>
  import('@/app/survey/SurveyGroup').then(module => module.default)
)

const SurveyQuestionFullScreenProjector = asyncComponent(() =>
  import('@/app/survey/SurveyQuestionFullScreenProjector').then(module => module.default)
)

const TemplateEditor = asyncComponent(() =>
  import('@/app/templates/TemplateEditor').then(module => module.default)
)

const ViewTemplateHistory = asyncComponent(() =>
  import('@/app/templates/ViewTemplateHistory').then(module => module.default)
)

const TemplateLogs = asyncComponent(() =>
  import('@/app/templates/Logs').then(module => module.default)
)

const AppPreview = asyncComponent(() =>
  import('@/app/AppPreview').then(module => module.default)
)

const ProgramWidget = asyncComponent(() =>
  import('@/app/program/ProgramWidget').then(module => module.default)
)
const AssignProgramSpeaker = asyncComponent(() =>
  import('@/app/program/AssignProgramSpeaker').then(module => module.default)
)

const SpeakerWidget = asyncComponent(() =>
  import('@/app/speaker/SpeakerWidget').then(module => module.default)
)

const RegistrationInvitation = asyncComponent(() =>
  import('@/app/attendee/RegistrationInvitation').then(module => module.default)
)

const AppInvitation = asyncComponent(() =>
  import('@/app/attendee/AppInvitation').then(module => module.default)
)

const AppInvitationNotSent = asyncComponent(() =>
  import('@/app/attendee/AppInvitationNotSent').then(module => module.default)
)

const NotRegisteredAttendees = asyncComponent(() =>
  import('@/app/attendee/NotRegisteredAttendees').then(module => module.default)
)

const NotAttendeesList = asyncComponent(() =>
  import('@/app/attendee/NotAttendeesList').then(module => module.default)
)

const RegistrationInvitationReminderLog = asyncComponent(() =>
  import('@/app/attendee/RegistrationInvitationReminderLog').then(module => module.default)
)

const AppInvitationReminderLog = asyncComponent(() =>
  import('@/app/attendee/AppInvitationReminderLog').then(module => module.default)
)

const RegistrationInvitationSetting = asyncComponent(() =>
  import('@/app/attendee/RegistrationInvitationSetting').then(module => module.default)
)

const InvitationProcess = asyncComponent(() =>
  import('@/app/attendee/InvitationProcess').then(module => module.default)
)

const PracticalInformation = asyncComponent(() =>
  import('@/app/event-information/PracticalInformation').then(module => module.default)
)

const AdditionalInformation = asyncComponent(() =>
  import('@/app/event-information/AdditionalInformation').then(module => module.default)
)

const InformationPages = asyncComponent(() =>
  import('@/app/event-information/InformationPages').then(module => module.default)
)

const GeneralInformation = asyncComponent(() =>
  import('@/app/event-information/GeneralInformation').then(module => module.default)
)

const SubRegistration = asyncComponent(() =>
  import('@/app/sub-registration/SubRegistration').then(module => module.default)
)

const Gdprcontainer = asyncComponent(() =>
  import('@/app/Gdprcontainer').then(module => module.default)
)

const Disclaimer = asyncComponent(() =>
  import('@/app/Disclaimer').then(module => module.default)
)

const GoogleMap = asyncComponent(() =>
  import('@/app/GoogleMap').then(module => module.default)
)

const CompanyDetails = asyncComponent(() =>
  import('@/app/registration-form/CompanyDetails').then(module => module.default)
)

const AttendeeType = asyncComponent(() =>
  import('@/app/registration-form/AttendeeType').then(module => module.default)
)

const CreateRegistrationForm = asyncComponent(() =>
  import('@/app/registration-form/CreateRegistrationForm').then(module => module.default)
)

const NewsWidget = asyncComponent(() =>
  import('@/app/news/NewsWidget').then(module => module.default)
)

const ReportWidget = asyncComponent(() =>
  import('@/app/reports/ReportWidget').then(module => module.default)
)

const PaymentProvider = asyncComponent(() =>
  import('@/app/event_site/billing/PaymentProvider').then(module => module.default)
)

const PaymentMethod = asyncComponent(() =>
  import('@/app/event_site/billing/PaymentMethod').then(module => module.default)
)

const EANInvoiceSetting = asyncComponent(() =>
  import('@/app/event_site/billing/EANInvoiceSetting').then(module => module.default)
)

const FIKSetting = asyncComponent(() =>
  import('@/app/event_site/billing/FIKSetting').then(module => module.default)
)

const BillingItem = asyncComponent(() =>
  import('@/app/event_site/billing/items/BillingItem').then(module => module.default)
)

const Voucher = asyncComponent(() =>
  import('@/app/event_site/billing/voucher/Voucher').then(module => module.default)
)

const Order = asyncComponent(() =>
  import('@/app/event_site/billing/order/Order').then(module => module.default)
)

const PurchasePolicy = asyncComponent(() =>
  import('@/app/event_site/billing/PurchasePolicy').then(module => module.default)
)

const Profile = asyncComponent(() =>
  import('@/app/organizer/Profile').then(module => module.default)
)

const Password = asyncComponent(() =>
  import('@/app/organizer/Password').then(module => module.default)
)

const eventInfo = localStorage.getItem('eventInfo');

class RouterOutlet extends React.Component {

  state = {
    event: (eventInfo && eventInfo !== undefined ? JSON.parse(eventInfo) : {}),
  };

  static getDerivedStateFromProps(props, state) {
    if (state.event !== JSON.parse(localStorage.getItem('eventInfo'))) {
      return {
        event: JSON.parse(localStorage.getItem('eventInfo'))
      };
    }
    // Return null to indicate no change to state.
    return null;
  }

  render() {
    return (
      <div>
        {!localStorage.getItem('eventBuizz') ? (
          <Switch>
            {/*Auth Routes*/}
            <AuthLayoutRoute path="/login" component={Login} exact />
            <AuthLayoutRoute path="/autoLogin/:token?" component={Login} exact />
            <AuthLayoutRoute path="/reset-password-request" component={ResetPasswordRequest} exact />
            <AuthLayoutRoute path="/reset-password" component={ResetPassword} exact />
            <Redirect from="*" to="/login" />
          </Switch>
        ) : (
            <Switch>
              {/*Auth Routes*/}
              <AuthLayoutRoute path="/autoLogin/:token?" component={Login} exact />
              
              {/*Dashboard Routes*/}
              <DashboardLayoutRoute name="home" path="/" component={Events} exact />
              <EventFullScreenLayoutRoute name="dashboard" path="/dashboard" component={Dashboard} exact />
              <DashboardLayoutRoute name="pagecrashed" path="/error" component={PageCrashed} exact />

              {/*Admin Routes*/}
              <OrganizerLayout path="/admin/assign-events/:id" component={AssignEvent} exact />

              {/*Event creation Routes*/}
              <MasterLayoutRoute component={EventWrapper} exact path="/event/create" />
              <MasterLayoutRoute path="/event/settings/branding" component={Branding} exact />
              <MasterLayoutRoute path="/event/edit/:id" component={EventWrapper} exact />

              {/*Event Registration form Routes*/}
              <MasterLayoutRoute path="/event/registration/basic-detail-form" component={CreateRegistrationForm} exact />
              <MasterLayoutRoute path="/event/registration/company-detail-form" component={CompanyDetails} exact />
              <MasterLayoutRoute path="/event/registration/attendee-type-form" component={AttendeeType} exact />
              <MasterLayoutRoute component={SubRegistration} exact path="/event/registration/sub-registration" />
              <MasterLayoutRoute path="/event/module/event-module-order" component={EventModuleOrder} exact />
              <MasterLayoutRoute path="/event/module/eventsite-module-order" component={EventSiteModuleOrder} exact />
              <MasterLayoutRoute path="/event/registration/manage/hotels" component={HotelManagement} exact />
              <MasterLayoutRoute path="/event/module/documents/:module?/:id?"  component={EventDocument} exact />

              {/*Event Modules Routes*/}
              <Route exact path="/event/module/attendees">
                {this.state.event && this.state.event.module_permissions && Number(this.state.event.module_permissions.attendees) === 0 ? <Redirect to="/event/module/programs" /> : <MasterLayoutRoute component={AttendeeWidget} exact />}
              </Route>
              <Route exact path="/event/module/programs">
                {this.state.event && this.state.event.module_permissions && Number(this.state.event.module_permissions.agendas) === 0 ? <Redirect to="/event/module/speakers" /> : <MasterLayoutRoute component={ProgramWidget} exact />}
              </Route>
              <Route exact path="/event/module/program/assign-speakers/:id">
                {this.state.event && this.state.event.module_permissions && Number(this.state.event.module_permissions.agendas) === 0 ? <Redirect to="/event/module/speakers" /> : <MasterLayoutRoute component={AssignProgramSpeaker} exact />}
              </Route>
              <Route exact path="/event/module/speakers">
                {this.state.event && this.state.event.module_permissions && Number(this.state.event.module_permissions.speakers) === 0 ? <Redirect to="/event/module/practical-information" /> : <MasterLayoutRoute component={SpeakerWidget} exact />}
              </Route>
              <Route exact path="/event/module/practical-information">
                {this.state.event && this.state.event.module_permissions && Number(this.state.event.module_permissions.infobooth) === 0 ? <Redirect to="/event/module/additional-information" /> : <MasterLayoutRoute component={PracticalInformation} exact />}
              </Route>
              <Route exact path="/event/module/additional-information">
                {this.state.event && this.state.event.module_permissions && Number(this.state.event.module_permissions.additional_info) === 0 ? <Redirect to="/event/module/general-information" /> : <MasterLayoutRoute component={AdditionalInformation} exact />}
              </Route>
              <Route exact path="/event/module/general-information">
                {this.state.event && this.state.event.module_permissions && Number(this.state.event.module_permissions.general_info) === 0 ? <Redirect to="/event/module/map" /> : <MasterLayoutRoute component={GeneralInformation} exact />}
              </Route>
              <Route exact path="/event/module/information-pages">
                {this.state.event && this.state.event.module_permissions && Number(this.state.event.module_permissions.information_pages) === 0 ? <Redirect to="/event/module/general-information" /> : <MasterLayoutRoute component={InformationPages} exact />}
              </Route>
              <Route exact path="/event/module/map">
                {this.state.event && this.state.event.module_permissions && Number(this.state.event.module_permissions.maps) === 0 ? <Redirect to="/event/manage/surveys" /> : <MasterLayoutRoute component={GoogleMap} exact />}
              </Route>
              <MasterLayoutRoute component={SubRegistration} exact path="/event/module/sub-registration" />
              <Route exact path="/event/manage/surveys">
                {this.state.event && this.state.event.module_permissions && Number(this.state.event.module_permissions.polls) === 0 ? <Redirect to={`/event/preview`} /> : <MasterLayoutRoute component={Survey} exact />}
              </Route>
              <MasterLayoutRoute path="/event/registration/tos" component={Disclaimer} exact />
              <MasterLayoutRoute path="/event/registration/gdpr" component={Gdprcontainer} exact />
              <MasterLayoutRoute path="/event/manage/survey/questions/:id" component={SurveyQuestion} exact />
              <MasterLayoutRoute path="/event/manage/survey/groups/:id" component={SurveyGroup} exact />
              <Route path="/event/manage/survey/question/full-screen-projector/:event_id/:id" component={SurveyQuestionFullScreenProjector} exact />
              {/* <MasterLayoutRoute path="/event/manage/survey/groups/:id" component={SurveyGroups} exact /> */}
              <MasterLayoutRoute path="/event/template/edit/:id?" component={TemplateEditor} exact />
              <MasterLayoutRoute path="/event/template/logs/:id" component={TemplateLogs} exact />
              <MasterLayoutRoute path="/event/template/history/view/:template_id/:id" component={ViewTemplateHistory} exact />
              <MasterLayoutRoute path="/event/preview" component={AppPreview} exact />
              <MasterLayoutRoute path="/event/invitation/report/registration" component={RegistrationInvitation} exact />
              <MasterLayoutRoute path="/event/invitation/report/not-registered-attendees" component={NotRegisteredAttendees} exact />
              <MasterLayoutRoute path="/event/invitation/report/not-attendees-list" component={NotAttendeesList} exact />
              <MasterLayoutRoute path="/event/invitation/report/registration-reminder-log" component={RegistrationInvitationReminderLog} exact />
              <MasterLayoutRoute path="/event/invitation/report/app-reminder-log" component={AppInvitationReminderLog} exact />
              <MasterLayoutRoute path="/event/invitation/report/registration/settings" component={RegistrationInvitationSetting} exact />
              <MasterLayoutRoute path="/event/invitation/report/app-invitation" component={AppInvitation} exact />
              <MasterLayoutRoute path="/event/invitation/report/app-invitation-not-sent" component={AppInvitationNotSent} exact />
              <MasterLayoutRoute path="/event/invitation/send-invitation/:step?" component={InvitationProcess} exact />
              <EventFullScreenLayoutRoute path="/event/news/alerts" component={NewsWidget} exact />
              <EventFullScreenLayoutRoute path="/event/reports" component={ReportWidget} exact />

              {/*Eventsite Billing module*/}
              <MasterLayoutRoute path="/event_site/billing-module/payment-providers" component={PaymentProvider} exact />
              <MasterLayoutRoute path="/event_site/billing-module/payment-methods" component={PaymentMethod} exact />
              <MasterLayoutRoute path="/event_site/billing-module/ean-invoice" component={EANInvoiceSetting} exact />
              <MasterLayoutRoute path="/event_site/billing-module/fik-setting" component={FIKSetting} exact />
              <MasterLayoutRoute path="/event_site/billing-module/items" component={BillingItem} exact />
              <MasterLayoutRoute path="/event_site/billing-module/waiting-list-orders" component={WaitingListOrders} exact />
              <MasterLayoutRoute path="/event_site/billing-module/purchase-policy" component={PurchasePolicy} exact />
              <MasterLayoutRoute path="/event_site/billing-module/voucher" component={Voucher} exact />
              <MasterLayoutRoute path="/event_site/billing-module/manage-orders" component={Order} exact />

              <Route path="/event/manage/survey/leaderboard/:event_id/:survey_id" component={SurveyLeaderBoard} exact />
              
              {/*Organizer routes*/}
              <MasterLayoutRoute path="/account/organizer/profile" component={Profile} exact />
              <MasterLayoutRoute path="/account/organizer/change-password" component={Password} exact />
              <Redirect from="*" to="/" />
            </Switch>
          )}
      </div>
    );
  }
}

export default RouterOutlet;
