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
  const { surveyListAnswered } = useSelector(surveyListSelector);

  return (
    surveyListAnswered ? <div className="edgtf-container ebs-my-profile-area pb-5">
      <div className="edgtf-container-inner container">
        <div className="ebs-header">
          <h2>Surveys</h2>
        </div>
        <div className="wrapper-inner-content network-category-sec">
          <div className="ebs-survey-heading d-flex">
            <h4>Answered Surveys</h4>
          </div>
          
            <div className="ebs-survey-list">
              <ul>
                {surveyListAnswered.map((survey) => (
                  survey.available === 'yes' ? <li key={survey.id}> {survey.info.name}</li> : null
                ))}
              </ul>
              {surveyListAnswered.length <=0 && <p>No Surveys Availble Yet</p>}
            </div>
          
        </div>
      </div>
    </div> : <PageLoader/>
  );
};

export default SurveyList;
