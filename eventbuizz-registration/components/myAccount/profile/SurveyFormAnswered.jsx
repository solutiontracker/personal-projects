import React, { useState, useRef } from "react";
import Input from "components/forms/Input";
import DateTime from "components/forms/DateTime";
import Select from "react-select";
import { useDispatch } from "react-redux";
const SurveyFormAnswered = ({ surveyDetail, event, surveyResults, survey_id }) => {
  const dispatch = useDispatch();
  const [surveyResult, setSurveyResult] = useState(surveyResults);
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
                          <h5>{question.value}</h5>
                          {question.answer.map((answer) => {
                            return (<label
                              key={answer.id}
                              className={
                                surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.filter((item)=>(item.question_id === question.id)).find((item)=>(item.answer_id === answer.id)) 
                                  ? "checked"
                                  : ""
                              }
                            >
                              <span>{answer.answer}</span>
                            </label>)
                            })}
                          {Number(question.enable_comments) === 1 && (
                            <div className="generic-form">
                              <p>Your comment:</p>
                              <textarea
                                placeholder="Your comment"
                                cols={30}
                                rows={5}
                                defaultValue={surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.find((item)=>(item.question_id === question.id)).comment}
                                disabled={true} 
                              ></textarea>
                            </div>
                          )}
                        </div>
                      </React.Fragment>
                    }

                  {question.question_type === "number" && (
                    <React.Fragment>
                      <div className="generic-form">
                        <h5>{question.value}</h5>
                        <Input
                          type="number"
                          readOnly
                          placeholder={"Answer"}
                          value={ surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.find((item)=>(item.question_id === question.id)).answer}
                        />
                        {Number(question.enable_comments) === 1 && (
                          <div className="generic-form">
                            <p>Your comment:</p>
                            <textarea
                              placeholder="Your comment"
                              cols={30}
                              rows={5}
                              disabled={true}
                              defaultValue={ surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.find((item)=>(item.question_id === question.id)).comment}                            
                            ></textarea>
                          </div>
                        )}
                      </div>
                    </React.Fragment>
                  )}

                  {question.question_type === "open" && (
                    <React.Fragment>
                      <div className="generic-form">
                        <h5>{question.value}</h5>
                        <textarea
                          placeholder="Answer"
                          defaultValue={surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.find((item)=>(item.question_id === question.id)).answer}
                          cols={30}
                          rows={10}
                        ></textarea>
                        {Number(question.enable_comments) === 1 && (
                          <div className="generic-form">
                            <p>Your comment:</p>
                            <textarea
                              placeholder="Your comment"
                              cols={30}
                              rows={5}
                              disabled={ true}
                              defaultValue={surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.find((item)=>(item.question_id === question.id)).comment}
                            ></textarea>
                          </div>
                        )}
                      </div>
                    </React.Fragment>
                  )}

                  {question.question_type === "dropdown" && (
                      <React.Fragment>
                        <div className="generic-form">
                          <h5>{question.value}</h5>
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
                              value={surveyResult.find((item)=>(item.question_id === question.id)) && 
                                { label:question.answer.find((item)=>(item.id === surveyResult.find((item)=>(item.question_id === question.id)).answer_id)).answer ,
                                     
                                value:question.answer.find((item)=>(item.id === surveyResult.find((item)=>(item.question_id === question.id)).answer_id)).id}  }
                            />
                          </div>
                          {Number(question.enable_comments) === 1 && (
                            <div className="generic-form">
                              <p>Your comment:</p>
                              <textarea
                                placeholder="Your comment"
                                cols={30}
                                rows={5}
                                disabled={true}
                                defaultValue={surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.find((item)=>(item.question_id === question.id)).comment}
                                
                              ></textarea>
                            </div>
                          )}
                        </div>
                      </React.Fragment>
                    )}

                  {question.question_type === "date" && (
                    <React.Fragment>
                      <div className="generic-form" style={{ width: "46%" }}>
                        <h5>{question.value}</h5>
                        <DateTime
                          disabled={true}
                          value={surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.find((item)=>(item.question_id === question.id)).answer}
                          label={`Select date`}
                          showdate={"YYYY-MM-DD"}
                        />
                        {Number(question.enable_comments) === 1 && (
                          <div className="generic-form">
                            <p>Your comment:</p>
                            <textarea
                              placeholder="Your comment"
                              cols={30}
                              rows={5}
                              disabled={true}
                              defaultValue={surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.find((item)=>(item.question_id === question.id)).comment}
                            ></textarea>
                          </div>
                        )}
                      </div>
                    </React.Fragment>
                  )}

                  {question.question_type === "date_time" && (
                    <React.Fragment>
                      <div className="generic-form" style={{ width: "46%" }}>
                        <h5>{question.value}</h5>
                        <DateTime
                          disabled
                          value={surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.find((item)=>(item.question_id === question.id)).answer}
                          label={`Select date time`}
                          showdate={"YYYY-MM-DD"}
                          showtime={"HH:mm:ss"}
                        />
                        {Number(question.enable_comments) === 1 && (
                          <div className="generic-form">
                            <p>Your comment:</p>
                            <textarea
                              placeholder="Your comment"
                              cols={30}
                              rows={5}
                              disabled={true}
                              defaultValue={surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.find((item)=>(item.question_id === question.id)).comment}
                            ></textarea>
                          </div>
                        )}
                      </div>
                    </React.Fragment>
                  )}

                  {question.question_type === "single" && (
                      <React.Fragment>
                        <div className="radio-check-field style-radio">
                          <h5>{question.value}</h5>
                          {question.answer.map((answer) => (
                            <label
                              key={answer.id}
                              className={
                                surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.find((item)=>(item.question_id === question.id)).answer_id === answer.id 
                                  ? "checked"
                                  : ""
                              }
                            >
                              <span>{answer.answer}</span>
                            </label>
                          ))}
                          {Number(question.enable_comments) === 1 && (
                            <div className="generic-form">
                              <p>Your comment:</p>
                              <textarea
                                placeholder="Your comment"
                                cols={30}
                                rows={5}
                                disabled={true}
                                defaultValue={surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.find((item)=>(item.question_id === question.id)).comment}
                              ></textarea>
                            </div>
                          )}
                        </div>
                      </React.Fragment>
                    )}

                  {question.question_type === "matrix" && (
                      <React.Fragment>
                        <div className={`matrix-question-wrapper`}>
                          <h5>{question.value}</h5>
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
                                          {console.log(surveyResult.find((item)=>((item.question_id === question.id) )) )}
                                          {console.log(matrix.id )}
                                          <input
                                          readOnly
                                          checked={
                                            surveyResult.find((item)=>((item.question_id === question.id) && (item.answer == matrix.id) && (item.answer_id === answer.id))) 
                                              ? true
                                              : false
                                          }
                                            type="radio"
                                            disabled
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
                          {Number(question.enable_comments) === 1 && (
                            <div className="generic-form">
                              <p>Your comment:</p>
                              <textarea
                                placeholder="Your comment"
                                cols={30}
                                rows={5}
                                disabled={true}
                                defaultValue={surveyResult.find((item)=>(item.question_id === question.id)) && surveyResult.find((item)=>(item.question_id === question.id)).comment}
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
        <button className="btn btn-save-next btn-loader" disabled > Save </button>
      </div>
    </React.Fragment>
  );
};

export default SurveyFormAnswered;
