import React, { useState, useRef } from "react";
import Input from "components/forms/Input";
import DateTime from "components/forms/DateTime";
import Select from "react-select";
import SimpleReactValidator from "simple-react-validator";
import {
    updateSurveyData,
  } from "store/Slices/myAccount/surveySlice";
import { useDispatch } from "react-redux";
import { useRouter } from 'next/router';
import moment from "moment";
const SurveyForm = ({ surveyDetail, event, surveyResults, survey_id }) => {
  const dispatch = useDispatch();
  const router = useRouter();
  const [surveyResult, setSurveyResult] = useState({});
  const [submittingForm, setSubmittingForm] = useState(false);
  const [surveyId, setSurveyId] = useState(survey_id);
  const [questions, setQuestions] = useState(
    surveyDetail
  );
  const [optionals, setOptionals] = useState(
    surveyDetail
      .filter((item) => item.required_question !== "1")
      .map((item) => item.id)
  );
  const [questionsType, setQuestionsType] = useState(
    surveyDetail.reduce(
      (ack, item) => Object.assign(ack, { [item.id]: item.question_type }),
      {}
    )
  );
  const [, forceUpdate] = useState(0);

  const simpleValidator = useRef(new SimpleReactValidator({
    element: (message) => <p className="error-message">{message}</p>,
    messages: {
      required: event.labels.REGISTRATION_FORM_FIELD_REQUIRED
    },
    autoForceUpdate: { forceUpdate: () => forceUpdate(1) }
  }))
  const updateResult = (
    feild,
    type,
    answerId = 0,
    questionId,
    agendaId = 0,
    matrixId = 0
  ) => {
    if (type === "multiple") {
      if (Object.keys(surveyResult).length > 0) {
        let newObj = surveyResult;
        newObj[feild]=
        surveyResult[feild]
          ? (surveyResult[feild].indexOf(answerId) !== -1
            ? surveyResult[feild].filter((item) => (item !== answerId))
            : [...surveyResult[feild], answerId])
          : [answerId];          
        if (agendaId !== 0) {
            newObj[`answer_agenda_${answerId}`] = agendaId;
        }
        setSurveyResult({ ...newObj });
      } else {
        let newObj = {
          [feild]: [answerId],
        };
        if (agendaId !== 0) {
            newObj[`answer_agenda_${answerId}`] = agendaId;
        }
        setSurveyResult({ ...newObj });
      }
    }
    else if (type === "dropdown") {
      Object.keys(surveyResult).length > 0
        ? setSurveyResult({
            ...surveyResult,
            [feild]: [`${answerId.value}`],
          })
        : setSurveyResult({
            [feild]: [`${answerId.value}`],
          });
    }
    else if (type === "single") {
      if (Object.keys(surveyResult).length > 0) {
        let newObj = {
          [feild]: [answerId],
        };
        if (agendaId !== 0) {
          if (surveyResult[`answer_agenda_${answerId}`] === undefined) {
            newObj[`answer_agenda_${answerId}`] = agendaId;
          }
        }
        setSurveyResult({ ...surveyResult, ...newObj });
      } else {
        let newObj = {
          [feild]: [answerId],
        };
        if (agendaId !== 0) {
          if (surveyResult[`answer_agenda_${answerId}`] === undefined) {
            newObj[`answer_agenda_${answerId}`] = agendaId;
          }
        }
        setSurveyResult({ ...newObj });
      }
    }
   else if (type === "matrix") {
      if (Object.keys(surveyResult).length > 0) {
        setSurveyResult({
          ...surveyResult,
          [feild]:
            surveyResult[feild] !== undefined
              ? surveyResult[feild].indexOf(answerId) !== -1  
                ? surveyResult[feild]
                : [...surveyResult[feild], answerId]
              : [answerId],
          [`matrix${questionId}_${answerId}`]: [
            `${answerId}_${matrixId}`,
          ],
        });
      } else {
        setSurveyResult({
          [feild]: [answerId],
          [`answer_matrix${questionId}_${answerId}`]: [
            `${answerId}-${matrixId}`,
          ],
        });
      }
    } else {
      Object.keys(surveyResult).length > 0
        ? setSurveyResult({ ...surveyResult, [feild]: [answerId] })
        : setSurveyResult({ [feild]: [answerId] });
    }
  };



  const handleSave =(e) =>{
    const formValid = simpleValidator.current.allValid()
    if (!formValid) {
      simpleValidator.current.showMessages()
    }else{ 
        let submittedQuestion = surveyDetail.map((item) => {
          let questionsObject = {
            id: item.id,
            type: item.question_type,
            required: item.required_question,
            is_anonymous: item.is_anonymous,
            comment: surveyResult[`comments${item.id}`] !== undefined ? surveyResult[`comments${item.id}`][0] : '',
          }
          if(item.question_type === 'single' || item.question_type === 'multiple' || item.question_type === 'dropdown' || item.question_type === 'matrix'){
            questionsObject['original_answers']= item.answer.map((answer)=>({id:answer.id, correct:answer.correct}));
            if(item.question_type === 'single'){
              questionsObject['answers'] = [{id:surveyResult[`answer${item.id}`] !== undefined ? surveyResult[`answer${item.id}`][0] : ''}]
            }
            else if(item.question_type === 'dropdown'){
              questionsObject['answers'] = [{id:surveyResult[`answer_${item.question_type}${item.id}`] !== undefined ? surveyResult[`answer_${item.question_type}${item.id}`][0] : ''}]
            }
            else if(item.question_type === 'multiple'){
              questionsObject['answers'] = surveyResult[`answer${item.id}`] !== undefined ? surveyResult[`answer${item.id}`].map((i)=>({id:i})) : [];
            }
            else if(item.question_type === 'matrix'){
              questionsObject['answers'] = surveyResult[`answer${item.id}`] !== undefined ? surveyResult[`answer${item.id}`].map((i)=>({id:surveyResult[`matrix${item.id}_${i}`][0]})) : [];
            }
          }else{
            if(item.question_type === 'world_cloud'){
              console.log('hello')
              questionsObject['answers'] = Array.apply(null, Array(item.entries_per_participant)).reduce((ack, t, index)=>{
                if(surveyResult[`answer_${item.question_type}${item.id}_${index}`] !== undefined){
                  ack.push({value:surveyResult[`answer_${item.question_type}${item.id}_${index}`][0]});
                  
                } 
                return ack; 
              },[]); 
            }
            else{
              questionsObject['answers'] = [{value:surveyResult[`answer_${item.question_type}${item.id}`] !== undefined ? surveyResult[`answer_${item.question_type}${item.id}`][0] : ''}]
            }
          }
      
          return questionsObject;
      
        });

        console.log(submittedQuestion);

        setSubmittingForm(true);
        let attendee_id = JSON.parse(localStorage.getItem(`event${event.id}User`)).user.id;
        dispatch(updateSurveyData(event.id, event.url ,surveyId, {
          survey_id: surveyId,
          event_id: event.id,
          attendee_id: attendee_id,
          base_url: process.env.NEXT_APP_EVENTCENTER_URL,
          organizer_id: event.owner_id,
          create_date: moment().toDate().toDateString(),
          env: process.env.NEXT_APP_APP_ENVIRONMENT,
          submitted_questions:submittedQuestion
        }, ()=>{
            router.push(`/${event.url}/profile/surveys`);
        }))
    }  
  }

  return (
    <React.Fragment>
      <div
        className={`manage-sub-registrations  wrapper-box other-information-sec`}
      >
        <React.Fragment>
          <div className="wrapper-inner-content">
            <div className="other-information-inner">
              {questions.map((question) => (
                <React.Fragment key={question.id}>
                  {question.question_type === "multiple" &&
                      <React.Fragment>
                        <div className="radio-check-field">
                          <h5>{question.value}
                          {question.required_question == 1 ? <span style={{color: 'red', marginLeft:'5px'}}>*</span> : null}
                          
                          </h5>
                          {question.answer.map((answer) => (
                            <label
                              key={answer.id}
                              onClick={() => {
                                updateResult(
                                  `answer${question.id}`,
                                  "multiple",
                                  answer.id,
                                  question.id,
                                );
                              }}
                              className={
                                surveyResult[`answer${question.id}`] !==
                                  undefined &&
                                surveyResult[`answer${question.id}`].indexOf(
                                  answer.id
                                ) !== -1
                                  ? "checked"
                                  : ""
                              }
                            >
                              <span>{answer.answer}</span>
                            </label>
                          ))}
                          {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, surveyResult[`answer${question.id}`] !== undefined ? true : null, 'required')}
                          {Number(question.enable_comments) === 1 && (
                            <div className="generic-form">
                              <p>Your comment:</p>
                              <textarea
                                placeholder="Your comment"
                                cols={30}
                                rows={5}
                                disabled={surveyResult[`answer${question.id}`] !== undefined ? false : true}
                                onChange={(e) => {
                                  updateResult(
                                    `comments${question.id}`,
                                    "comment",
                                    e.target.value
                                  );
                                }}
                              ></textarea>
                            </div>
                          )}
                        </div>
                      </React.Fragment>
                    }

                  {question.question_type === "number" && (
                    <React.Fragment>
                      <div className="generic-form">
                        <h5>{question.value}
                          {question.required_question == 1 ? <span style={{color: 'red', marginLeft:'5px'}}>*</span> : null}
                        </h5>
                        <Input
                          type="number"
                          label={"Answer"}
                          value={
                            surveyResult[`answer_number${question.id}`] ?
                            surveyResult[`answer_number${question.id}`][0]: ''
                          }
                          onChange={(e) => {
                            updateResult(
                              `answer_number${question.id}`,
                              "number",
                              e.target.value,
                              question.id
                            );
                          }}
                        />
                        {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, surveyResult[`answer_number${question.id}`] !== undefined ? true : null, 'required')}
                        {Number(question.enable_comments) === 1 && (
                          <div className="generic-form">
                            <p>Your comment:</p>
                            <textarea
                              placeholder="Your comment"
                              cols={30}
                              rows={5}
                              disabled={surveyResult[`answer_number${question.id}`] !== undefined ? false : true}
                              onChange={(e) => {
                                updateResult(
                                  `comments${question.id}`,
                                  "comment",
                                  e.target.value
                                );
                              }}
                            ></textarea>
                          </div>
                        )}
                      </div>
                    </React.Fragment>
                  )}

                  {question.question_type === "open" && (
                    <React.Fragment>
                      <div className="generic-form">
                        <h5>{question.value}
                        {question.required_question == 1 ? <span style={{color: 'red', marginLeft:'5px'}}>*</span> : null}
                        
                        </h5>
                        <textarea
                          placeholder="Answer"
                          value={
                            surveyResult[`answer_open${question.id}`] &&
                            surveyResult[`answer_open${question.id}`][0]
                          }
                          onChange={(e) => {
                            updateResult(
                              `answer_open${question.id}`,
                              "open",
                              e.target.value,
                              question.id
                            );
                          }}
                          cols={30}
                          rows={10}
                        ></textarea>
                        {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, surveyResult[`answer_open${question.id}`] !== undefined ? true : null, 'required')}
                        {Number(question.enable_comments) === 1 && (
                          <div className="generic-form">
                            <p>Your comment:</p>
                            <textarea
                              placeholder="Your comment"
                              cols={30}
                              rows={5}
                              disabled={surveyResult[`answer_open${question.id}`] !== undefined ? false : true}
                              onChange={(e) => {
                                updateResult(
                                  `comments${question.id}`,
                                  "comment",
                                  e.target.value
                                );
                              }}
                            ></textarea>
                          </div>
                        )}
                      </div>
                    </React.Fragment>
                  )}

                  {question.question_type === "dropdown" && (
                      <React.Fragment>
                        <div className="generic-form">
                          <h5>{question.value}
                          {question.required_question == 1 ? <span style={{color: 'red', marginLeft:'5px'}}>*</span> : null}
                          
                          </h5>
                          <div
                            className="custom-label-select"
                            style={{ width: "46%" }}
                          >
                            <Select
                              placeholder="Select value from dropdown"
                              components={{ IndicatorSeparator: null }}
                              options={question.answer.map((answer, i) => ({
                                label: answer.answer,
                                value: answer.id,
                                key: i,
                              }))}
                              
                              value={surveyResult[`answer_dropdown${question.id}`] !== undefined && { label:  question.answer.find((answer) => ( answer.id == surveyResult[`answer_dropdown${question.id}`][0] )).answer , value: surveyResult[`answer_dropdown${question.id}`][0] }}
                              onChange={(item) => {
                                  updateResult(
                                      `answer_dropdown${question.id}`,
                                      "dropdown",
                                      item,
                                      question.id
                                      );
                              }}
                            />
                          </div>
                          {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, surveyResult[`answer_dropdown${question.id}`] !== undefined ? true : null, 'required')}
                          {Number(question.enable_comments) === 1 && (
                            <div className="generic-form">
                              <p>Your comment:</p>
                              <textarea
                                placeholder="Your comment"
                                cols={30}
                                rows={5}
                                disabled={surveyResult[`answer_dropdown${question.id}`] !== undefined ? false : true}
                                onChange={(e) => {
                                  updateResult(
                                    `comments${question.id}`,
                                    "comment",
                                    e.target.value
                                  );
                                }}
                              ></textarea>
                            </div>
                          )}
                        </div>
                      </React.Fragment>
                    )}

                  {question.question_type === "date" && (
                    <React.Fragment>
                      <div className="generic-form" style={{ width: "46%" }}>
                        <h5>{question.value}
                        {question.required_question == 1 ? <span style={{color: 'red', marginLeft:'5px'}}>*</span> : null}
                        
                        </h5>
                        <DateTime
                          onChange={(item) => {
                            updateResult(
                              `answer_date${question.id}`,
                              "date",
                              item._isAMomentObject !== undefined && item._isAMomentObject === true ? item.format("YYYY-MM-DD") : item,
                              question.id
                            );
                            
                          }}
                          value={
                            surveyResult[`answer_date${question.id}`] &&
                            surveyResult[`answer_date${question.id}`][0]
                          }
                          label={`Select date`}
                          showdate={"YYYY-MM-DD"}
                        />
                        {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, surveyResult[`answer_date${question.id}`] !== undefined ? true : null, 'required')}
                        {Number(question.enable_comments) === 1 && (
                          <div className="generic-form">
                            <p>Your comment:</p>
                            <textarea
                              placeholder="Your comment"
                              cols={30}
                              rows={5}
                              disabled={surveyResult[`answer_date${question.id}`] !== undefined ? false : true}
                              onChange={(e) => {
                                updateResult(
                                  `comments${question.id}`,
                                  "comment",
                                  e.target.value
                                );
                              }}
                            ></textarea>
                          </div>
                        )}
                      </div>
                    </React.Fragment>
                  )}

                  {question.question_type === "date_time" && (
                    <React.Fragment>
                      <div className="generic-form" style={{ width: "46%" }}>
                        <h5>{question.value}
                          {question.required_question == 1 ? <span style={{color: 'red', marginLeft:'5px'}}>*</span> : null}
                        </h5>
                        <DateTime
                          onChange={(item) => {
                            console.log(item)
                            updateResult(
                              `answer_date_time${question.id}`,
                              "date_time",
                              item._isAMomentObject !== undefined && item._isAMomentObject === true ? item.format("YYYY-MM-DD HH:mm:ss") : item,
                              question.id
                            );
                          }}
                          value={
                            surveyResult[`answer_date_time${question.id}`] ?
                            surveyResult[`answer_date_time${question.id}`][0]: ''
                          }
                          label={`Select date time`}
                          showdate={"YYYY-MM-DD"}
                          showtime={"HH:mm:ss"}
                        />
                          {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, surveyResult[`answer_date_time${question.id}`] !== undefined ? true : null, 'required')}
                        {Number(question.enable_comments) === 1 && (
                          <div className="generic-form">
                            <p>Your comment:</p>
                            <textarea
                              placeholder="Your comment"
                              cols={30}
                              rows={5}
                              disabled={surveyResult[`answer_date_time${question.id}`] !== undefined ? false : true}
                              onChange={(e) => {
                                updateResult(
                                  `comments${question.id}`,
                                  "comment",
                                  e.target.value
                                );
                              }}
                            ></textarea>
                          </div>
                        )}
                      </div>
                    </React.Fragment>
                  )}

                  {question.question_type === "single" && (
                      <React.Fragment>
                        <div className="radio-check-field style-radio">
                          <h5>{question.value}
                          {question.required_question == 1 ? <span style={{color: 'red', marginLeft:'5px'}}>*</span> : null}                       
                          </h5>
                          {question.answer.map((answer) => (
                            <label
                              key={answer.id}
                              onClick={() => {
                                updateResult(
                                  `answer${question.id}`,
                                  "single",
                                  answer.id,
                                  question.id,
                                );
                              }}
                              className={
                                surveyResult[`answer${question.id}`] !==
                                  undefined &&
                                surveyResult[`answer${question.id}`].indexOf(
                                  answer.id
                                ) !== -1
                                  ? "checked"
                                  : ""
                              }
                            >
                              <span>{answer.answer}</span>
                            </label>
                          ))}
                          {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, surveyResult[`answer${question.id}`] !== undefined ? true : null, 'required')}
                          {Number(question.enable_comments) === 1 && (
                            <div className="generic-form">
                              <p>Your comment:</p>
                              <textarea
                                placeholder="Your comment"
                                cols={30}
                                rows={5}
                                disabled={surveyResult[`answer${question.id}`] !== undefined ? false : true}
                                onChange={(e) => {
                                  updateResult(
                                    `comments${question.id}`,
                                    "comment",
                                    e.target.value
                                  );
                                }}
                              ></textarea>
                            </div>
                          )}
                        </div>
                      </React.Fragment>
                    )}

                  {question.question_type === "matrix" && (
                      <React.Fragment>
                        <div className={`matrix-question-wrapper`}>
                          <h5>{question.value}
                            {question.required_question == 1 ? <span style={{color: 'red', marginLeft:'5px'}}>*</span> : null}
                          </h5>
                          <div className="matrix-wrapper">
                          <div className="matrix-table">
                            <div className="martix-row matrix-header">
                              <div className="matrix-box matrix-heading"></div>
                              {question.matrix.map((matrix) => (
                                <div key={matrix.id} className="matrix-box">
                                  {matrix.name}
                                </div>
                              ))}
                            </div>
                            {question.answer.map((answer) => (
                              <React.Fragment key={answer.id}>
                                <div className="martix-row">
                                  <div className="matrix-box matrix-heading">
                                    {answer.answer}
                                  </div>
                                  {question.matrix.map((matrix) => (
                                    <React.Fragment key={matrix.id}>
                                      <div className="matrix-box">
                                        <label className="label-radio">
                                          <input
                                            checked={
                                              surveyResult[
                                                `matrix${question.id}_${answer.id}`
                                              ] !== undefined &&
                                              surveyResult[
                                                `matrix${question.id}_${answer.id}`
                                              ][0].indexOf(matrix.id) !== -1
                                                ? true
                                                : false
                                            }
                                            type="radio"
                                            onChange={() => {
                                              updateResult(
                                                `answer${question.id}`,
                                                "matrix",
                                                answer.id,
                                                question.id,
                                                answer.link_to,
                                                matrix.id
                                              );
                                            }}
                                          />
                                          <span></span>
                                        </label>
                                      </div>
                                    </React.Fragment>
                                  ))}
                                </div>
                              </React.Fragment>
                            ))}
                          </div>
                          </div>
                          {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, surveyResult[`answer${question.id}`] !== undefined && surveyResult[`answer${question.id}`].length === question.answer.length ? true : null, 'required')}
                          {Number(question.enable_comments) === 1 && (
                            <div className="generic-form">
                              <p>Your comment:</p>
                              <textarea
                                placeholder="Your comment"
                                cols={30}
                                rows={5}
                                disabled={surveyResult[`answer${question.id}`] !== undefined ? false : true}
                                onChange={(e) => {
                                  updateResult(
                                    `comments${question.id}`,
                                    "comment",
                                    e.target.value
                                  );
                                }}
                              ></textarea>
                            </div>
                          )}
                        </div>
                      </React.Fragment>
                    )}
                
                {question.question_type === "world_cloud" && (
                    <React.Fragment>
                      <div className="generic-form">
                        <h5>{question.value}
                        {question.required_question == 1 ? <span style={{color: 'red', marginLeft:'5px'}}>*</span> : null}
                        
                        </h5>
                        {Array.apply(null, Array(question.entries_per_participant))
                          .map((i, index)=>(
                            <React.Fragment key={index}>
                              <textarea
                                placeholder="Answer"
                                value={
                                  surveyResult[`answer_world_cloud${question.id}_${index}`] &&
                                  surveyResult[`answer_world_cloud${question.id}_${index}`][0]
                                }
                                onChange={(e) => {
                                  updateResult(
                                    `answer_world_cloud${question.id}_${index}`,
                                    "world_cloud",
                                    e.target.value,
                                    question.id
                                  );
                                }}
                                cols={30}
                                rows={10}
                              ></textarea>
                              <br/>
                            </React.Fragment>
                          ))
                          
                          }
                        {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, surveyResult[`answer_open${question.id}`] !== undefined ? true : null, 'required')}
                        {Number(question.enable_comments) === 1 && (
                          <div className="generic-form">
                            <p>Your comment:</p>
                            <textarea
                              placeholder="Your comment"
                              cols={30}
                              rows={5}
                              // disabled={surveyResult[`answer_world_cloud${question.id}`] !== undefined ? false : true}
                              onChange={(e) => {
                                updateResult(
                                  `comments${question.id}`,
                                  "comment",
                                  e.target.value
                                );
                              }}
                            ></textarea>
                          </div>
                        )}
                      </div>
                    </React.Fragment>
                  )}


                  <div className="ebs-seperator" />
                </React.Fragment>
              ))}
            </div>
          </div>
        </React.Fragment>
      </div>
      <div className="bottom-button">
        <button className="btn btn-save-next btn-loader" disabled={submittingForm} onClick={(e)=>{handleSave(e)}}> {submittingForm ? "Saving..." : "Save"} </button>
      </div>
    </React.Fragment>
  );
};

export default SurveyForm;
