import { configureStore } from "@reduxjs/toolkit";
import { setupListeners } from "@reduxjs/toolkit/query";
import eventReducer from "./Slices/EventSlice";
import globalReducer from "./Slices/GlobalSlice";
import speakerReducer from "./Slices/SpeakerSlice";
import attendeeReducer from "./Slices/AttendeeSlice";
import attendeeDetailReducer from "./Slices/AttendeeDetailSlice";
import speakerDetailReducer from "./Slices/SpeakerDetailSlice";
import mapReducer from "./Slices/MapSlice";
import newsDetailReducer from "./Slices/NewsDetailSlice";
import newsReducer from "./Slices/NewsSlice";
import profileReducer from "./Slices/myAccount/profileSlice";
import interestReducer from "./Slices/myAccount/networkInterestSlice";
import newsletterReducer from "./Slices/myAccount/newsletterSlice";
import subRegistrationReducer from "./Slices/myAccount/subRegistrationSlice";
import mySubRegistrationReducer from "./Slices/myAccount/mysubRegistrationSlice";
import surveyListReducer from "./Slices/myAccount/surveyListSlice";
import surveyReducer from "./Slices/myAccount/surveySlice";
import userReducer from "./Slices/myAccount/userSlice";
import sponsorReducer from "./Slices/SponsorSlice";
import exhibitorReducer from "./Slices/ExhibitorSlice";
import sponsorListingReducer from "./Slices/SponsorListingSlice";
import exhibitorListingReducer from "./Slices/ExhibitorListingSlice";
import sponsorDetailReducer from "./Slices/SponsorDetailSlice";
import exhibitorDetailReducer from "./Slices/ExhibitorDetailSlice";
import documentsReducer from "./Slices/DocumentsSlice";
import cmsDetailReducer from "./Slices/CmsDetailSlice";
import pageBuilderPageReducer from "./Slices/PageBuilderPagesSlice";
import photoReducer from "./Slices/PhotoSlice";
import videoReducer from "./Slices/VideoSlice";
import programListingReducer from "./Slices/ProgramListingSlice";
import programReducer from "./Slices/ProgramSlice";
import formPackageReducer from "./Slices/FormPackageSlice";
import myProgramListingReducer from "./Slices/myAccount/MyProgramListingSlice";
export const store = configureStore({
  reducer: {
    event: eventReducer,
    global: globalReducer,
    speaker: speakerReducer,
    attendee: attendeeReducer,
    attendeeDetail: attendeeDetailReducer,
    speakerDetail: speakerDetailReducer,
    map: mapReducer,
    newsDetail: newsDetailReducer,
    news: newsReducer,
    profile: profileReducer,
    networkInterest: interestReducer,
    newsletter: newsletterReducer,
    subRegistration: subRegistrationReducer,
    mySubRegistration: mySubRegistrationReducer,
    surveyList: surveyListReducer,
    survey: surveyReducer,
    user: userReducer,
    sponsor: sponsorReducer,
    exhibitor: exhibitorReducer,
    sponsorListing: sponsorListingReducer,
    exhibitorListing: exhibitorListingReducer,
    sponsorDetail: sponsorDetailReducer,
    exhibitorDetail: exhibitorDetailReducer,
    documents: documentsReducer,
    cmsDetail: cmsDetailReducer,
    photo: photoReducer,
    video: videoReducer,
    programListing: programListingReducer,
    program: programReducer,
    myProgramListing: myProgramListingReducer,
    pageBuilderPage: pageBuilderPageReducer,
    formPackages: formPackageReducer,
  },
  devTools: true,
});
setupListeners(store.dispatch);
