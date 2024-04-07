import React, { useEffect } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { useSelector, useDispatch } from "react-redux";
import ActiveLink from "components/atoms/ActiveLink";
import {
  fetchSurveyListData,
  surveyListSelector,
} from "store/Slices/myAccount/surveyListSlice";
import PageLoader from "components/ui-components/PageLoader";
const SurveyList = () => {
  const { event } = useSelector(eventSelector);
  const dispatch = useDispatch();
  useEffect(() => {
    dispatch(fetchSurveyListData(event.id, event.url));
  }, []);
  const { surveyList } = useSelector(surveyListSelector);

  return (
    surveyList ? <div className="edgtf-container ebs-my-profile-area pb-5">
      <div className="edgtf-container-inner container">
        <div className="ebs-header">
          <h2>{event.labels.EVENTSITE_TAB_SURVEY}</h2>
        </div>
        <div className="wrapper-inner-content network-category-sec">
          <div className="ebs-survey-heading d-flex">
            <h4>{event.labels.EVENTSITE_TAB_SURVEY}</h4>
            <ActiveLink href={`/${event.url}/profile/surveys/answered`} className="btn-view-result" >{event.labels.EVENTSITE_SUBMITTED_SURVEY}</ActiveLink>
          </div>
          
            <div className="ebs-survey-list">
              <ul>
                {surveyList.map((survey) => (
                  survey.available === 'yes' ? <li key={survey.id}> <ActiveLink href={`/${event.url}/profile/surveys/${survey.id}`} >{survey.info.name}</ActiveLink> </li> : null
                ))}
              </ul>
              {surveyList.length <=0 && <p>No Surveys Availble Yet</p>}
            </div>
          
        </div>
      </div>
    </div> : <PageLoader/>
  );
};

export default SurveyList;
