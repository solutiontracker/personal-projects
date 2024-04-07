import React, { useEffect } from 'react'
import {
  fetchSurveyData,
  surveySelector
} from "store/Slices/myAccount/surveySlice";
import { eventSelector } from "store/Slices/EventSlice";
import { useSelector, useDispatch } from "react-redux";
import SurveyForm from './SurveyForm';
import PageLoader from 'components/ui-components/PageLoader';
import { useRouter } from 'next/router';
import SurveyFormAnswered from './SurveyFormAnswered';

const SurveyDetail = ({ match }) => {

  const router = useRouter();

  const { id } = router.query;

  const { event } = useSelector(eventSelector);

  const dispatch = useDispatch();

  useEffect(() => {
    dispatch(fetchSurveyData(event.id, event.url, id));
  }, []);

  const { surveyDetail, surveyResult, updating, survey } = useSelector(surveySelector);

  return (
    surveyDetail ? <div className="edgtf-container ebs-my-profile-area pb-5">
      <div className="edgtf-container-inner container">
        <div className="ebs-header">
          <h2>{survey ? survey?.info?.name : 'Surveys'}</h2>
        </div>
        <div className="wrapper-inner-content network-category-sec">
          <SurveyForm surveyDetail={surveyDetail} event={event} surveyResults={surveyResult} survey_id={id} updating={updating} />
        </div>
      </div>
    </div> : <PageLoader />
  )

}

export default SurveyDetail