import React, { useState, useRef } from "react";
import Input from "components/forms/Input";
import TextArea from "components/forms/TextArea";
import DateTime from "components/forms/DateTime";
import Select from "react-select";
import SimpleReactValidator from "simple-react-validator";
import {
  updateSubRegistrationData,
} from "store/Slices/myAccount/subRegistrationSlice";
import { useSelector, useDispatch } from "react-redux";
import moment from 'moment';
const SubRegForm = ({ subRegistration, event, afterLogin,  updating, alert, error }) => {
  const dispatch = useDispatch();
  const [programs, setPrograms] = useState(subRegistration.all_programs);
  const [settings, setSettings] = useState(subRegistration.settings);
  const [subRegResult, setSubRegResult] = useState(afterLogin ? {} : subRegistration.questions.question
    .reduce(
      (ack, item) => {
      if(item.question_type === "multiple"){
        let newObj ={ [`answer${item.id}`]: item.result.map(item=>(item.answer_id)), [`comments${item.id}`]:item.result[0].comments }
        let agendas = item.answer.filter((filterItem)=>(filterItem.link_to > 0)).reduce((ack, ritem) => {
          if(item.result.map(item=>(item.answer_id)).indexOf(ritem) !== -1){
           return Object.assign(ack, { [`answer_agenda_${ritem.id}`] : ritem.link_to })
          }
          return ack;          
          },
        {})
        if(Object.keys(agendas).length > 0){
          newObj ={...newObj,...agendas};
        }
        return Object.assign(ack, {...newObj} );
      }
      else if(item.question_type === "single"){
        let newObj ={ [`answer${item.id}`]: [item.result[0].answer_id] , [`comments${item.id}`]:item.result[0].comments }
        if(item.answer.find((answer)=>(item.result[0].answer_id === answer.id)).link_to > 0){
          newObj ={...newObj,[`answer_agenda_${item.answer_id}`] : item.answer[item.result[0].answer_id].link_to};
        }
        return Object.assign(ack, {...newObj} );
      }
      else if(item.question_type === "dropdown"){
        let newObj ={ [`answer_dropdown${item.id}`]: [`${item.result[0].answer_id}-${item.answer.find((answer)=>(item.result[0].answer_id === answer.id)).link_to}`], [`comments${item.id}`]:item.result[0].comments }
        return Object.assign(ack, {...newObj} );
      }
      else if(item.question_type === "matrix"){
        let newObj ={ [`answer${item.id}`]: item.result.map((anwser)=>(anwser.answer_id)) }
        let matrix = item.result.reduce((ack, ritem) => {
           return Object.assign(ack, { [`answer_matrix${item.id}_${ritem.answer_id}`] : [`${ritem.answer_id}-${ritem.answer}`], [`comments${item.id}`]:item.result[0].comments })},
          
        {})
        return Object.assign(ack, {...newObj, ...matrix} );
      }
      else{
        if(item.result !== undefined && item.result.length > 0){
          return Object.assign(ack, { [`answer_${item.question_type}${item.id}`]: [item.result[0].answer], [`comments${item.id}`]:item.result[0].comments} );
        }else{
          return ack;
        }
      }
    },{}));
  const [subRegId] = useState(subRegistration.questions.id);
  const [questions] = useState(
    subRegistration.questions.question
  );
  const [optionals] = useState(
    subRegistration.questions.question
      .filter((item) => item.required_question !== "1")
      .map((item) => item.id)
  );
  const [questionsType] = useState(
    subRegistration.questions.question.reduce(
      (ack, item) => Object.assign(ack, { [item.id]: item.question_type }),
      {}
    )
  );
  const [, forceUpdate] = useState(0);
  const [validationErros, setValidationErrors] = useState({});

  const simpleValidator = useRef(new SimpleReactValidator({
    element: (message) => <p className="error-message">{message}</p>,
    messages: {
      required: event.labels.REGISTRATION_FORM_FIELD_REQUIRED
    },
    autoForceUpdate: { forceUpdate: () => forceUpdate(1) }
  }))
  const updateResult = async (
    feild,
    type,
    answerId = 0,
    questionId,
    agendaId = 0,
    matrixId = 0,
  ) => {
    console.log();
    setValidationErrors({})
    if (type === "multiple") {
    
        if(settings.favorite_session_registration_same_time != 1 && agendaId !== 0 && subRegResult[feild] !== undefined && subRegResult[feild].length > 0){
          let selectedProgram = programs.find((item)=>(item.id == agendaId))
          let start_time1 = selectedProgram.start_time;
          let end_time1 = selectedProgram.end_time;
          let exit = false;
          await subRegResult[feild].forEach(answer => {
            let pId = subRegResult['answer_agenda_'+answer];
            let thisPrograms = programs.find((item)=>(item.id == pId));
            let start_time2 = thisPrograms.start_time;
            let end_time2 = thisPrograms.end_time;
            start_time1 = moment(start_time1,'HH:mm');
            end_time1 = moment(end_time1, 'HH:mm');
            start_time2 = moment(start_time2, 'HH:mm');
            end_time2 = moment(end_time2, 'HH:mm');
            console.log(start_time1);
            console.log(start_time2);
            console.log(pId != agendaId && (moment(thisPrograms.start_date, 'DD-MM-YYYY').isSame(moment(selectedProgram.start_date, 'DD-MM-YYYY'))) == true)
            console.log((start_time1 >= start_time2 && start_time1 < end_time2) || (start_time2 >= start_time1 && start_time2 < end_time1))
            if(pId != agendaId && (moment(thisPrograms.date, 'DD-MM-YYYY').isSame(moment(selectedProgram.date, 'DD-MM-YYYY'))) == true ){
                if ((start_time1 >= start_time2 && start_time1 < end_time2) || (start_time2 >= start_time1 && start_time2 < end_time1)) {
                        window.alert('Do not allow double booking of program sessions that start at the same time. (session registration) You cannot select several program sessions that start simultaneously.');
                        exit = true;

                }
            }
          });
          if(exit){
            return;
          }
        }

      if (Object.keys(subRegResult).length > 0) {
        let question = questions.find((question)=>(question.id === questionId));
        let newObj = subRegResult;
        newObj[feild]=
        subRegResult[feild]
          ? (subRegResult[feild].indexOf(answerId) !== -1
            ? subRegResult[feild].filter((item) => (item !== answerId))
            : (question !== null && question.max_options > 0 && subRegResult[feild].length == question.max_options) ? [...subRegResult[feild]] : [...subRegResult[feild], answerId])
          : [answerId];
          
        if (agendaId !== 0) {
            newObj[`answer_agenda_${answerId}`] = agendaId;
        }
        setSubRegResult({ ...newObj });
      } else {
        let newObj = {
          [feild]: [answerId],
        };
        if (agendaId !== 0) {
            newObj[`answer_agenda_${answerId}`] = agendaId;
        }
        setSubRegResult({ ...newObj });
      }
    }
    else if (type === "dropdown") {
      Object.keys(subRegResult).length > 0
        ? setSubRegResult({
            ...subRegResult,
            [feild]: [`${answerId.value}-${answerId.linkTo}`],
          })
        : setSubRegResult({
            [feild]: [`${answerId.value}-${answerId.linkTo}`],
          });
    }
    else if (type === "single") {
      if (Object.keys(subRegResult).length > 0) {
        let newObj = {
          [feild]: [answerId],
        };
        if (agendaId !== 0) {
          if (subRegResult[`answer_agenda_${answerId}`] === undefined) {
            newObj[`answer_agenda_${answerId}`] = agendaId;
          }
        }
        setSubRegResult({ ...subRegResult, ...newObj });
      } else {
        let newObj = {
          [feild]: [answerId],
        };
        if (agendaId !== 0) {
          if (subRegResult[`answer_agenda_${answerId}`] === undefined) {
            newObj[`answer_agenda_${answerId}`] = agendaId;
          }
        }
        setSubRegResult({ ...newObj });
      }
    }
   else if (type === "matrix") {
      if (Object.keys(subRegResult).length > 0) {
        setSubRegResult({
          ...subRegResult,
          [feild]:
            subRegResult[feild] !== undefined
              ? subRegResult[feild].indexOf(answerId) !== -1  
                ? subRegResult[feild]
                : [...subRegResult[feild], answerId]
              : [answerId],
          [`answer_matrix${questionId}_${answerId}`]: [
            `${answerId}-${matrixId}`,
          ],
        });
      } else {
        setSubRegResult({
          [feild]: [answerId],
          [`answer_matrix${questionId}_${answerId}`]: [
            `${answerId}-${matrixId}`,
          ],
        });
      }
    }
    else if (type === "comment") {
      Object.keys(subRegResult).length > 0
      ? setSubRegResult({ ...subRegResult, [feild]: answerId })
      : setSubRegResult({ [feild]: answerId });
    } else {
      Object.keys(subRegResult).length > 0
        ? setSubRegResult({ ...subRegResult, [feild]: [answerId] })
        : setSubRegResult({ [feild]: [answerId] });
    }
  };



  const handleSave = async (e) =>{
    const formValid = simpleValidator.current.allValid()
    if (!formValid) {
      simpleValidator.current.showMessages()
    }else{

     let validator = await validateFromDatabeforesubmit(questions, subRegResult);
      console.log(validator);
      if(!validator.valid){
        setValidationErrors(validator.errors);
        return
      }

      if(afterLogin){
        dispatch(updateSubRegistrationData(event.id, event.url, {
            first_time:"yes",
            sub_reg_id: subRegId,
            optionals,
            questionsType,
            questions:questions.reduce((ack, item) => { return ack.concat(item.id)},[]),
            ...subRegResult,
          }))
      }
      else{
        dispatch(updateSubRegistrationData(event.id, event.url, {
          first_time:"no",
          sub_reg_id: subRegId,
          optionals,
          questionsType,
          questions:questions.reduce((ack, item) => { return ack.concat(item.id)},[]),
          ...subRegResult,
        }))
      }

    }  
  }

  const validateFromDatabeforesubmit = (question, formdata) => {
    return new Promise(function(resolve, reject) {
      let errors = {}; 

      question.forEach(async (element) => {
            if(element.question_type === "multiple"){
              if(formdata[`answer${element.id}`] !== undefined){
                if(element.min_options > 0){
                  if(formdata[`answer${element.id}`].length < element.min_options){
                      errors[element.id] = `Select at least ${element.min_options} options`
                  }
                }
              }
            }
      });

      let valid = Object.keys(errors).length > 0 ? false : true;
      resolve({valid:valid, errors:errors});
    });
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
                    (afterLogin ? question.display_question === "yes" : true) && (
                      <React.Fragment>
                        <div className="radio-check-field">
                          <h5>{question.info[0].value}</h5>
                          {question.answer.map((answer) => (
                            <label
                              key={answer.id}
                              onClick={() => {
                                if((answer.tickets !== undefined &&  (answer.tickets <= 0))){
                                    return;
                                }
                                updateResult(
                                  `answer${question.id}`,
                                  "multiple",
                                  answer.id,
                                  question.id,
                                  answer.link_to,
                                );
                              }}
                              className={
                                subRegResult[`answer${question.id}`] !==
                                  undefined &&
                                subRegResult[`answer${question.id}`].indexOf(
                                  answer.id
                                ) !== -1
                                  ? "checked"
                                  : (answer.tickets !== undefined &&  (answer.tickets <= 0)) ? 'check-disabled' : ""
                              }
                            >
                              <span>{answer.info[0].value}</span>
                            </label>
                          ))}
                          {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, subRegResult[`answer${question.id}`] !== undefined ? true : null, 'required')}
                          {validationErros[question.id] !== undefined &&  <p className="error-message">{validationErros[question.id]}</p>}
                          {Number(question.enable_comments) === 1 && (
                            <div className="generic-form">
                              <p>{event.labels.GENERAL_YOUR_COMMENT}</p>
                              <textarea
                                placeholder={event.labels.GENERAL_YOUR_COMMENT}
                                cols={30}
                                rows={5}
                                disabled={subRegResult[`answer${question.id}`] !== undefined ? false : true}
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

                  {question.question_type === "number" && (
                    <React.Fragment>
                      <div className="generic-form">
                        <h5>{question.info[0].value}</h5>
                        <Input
                          type="number"
                          label={"Answer"}
                          value={
                            subRegResult[`answer_number${question.id}`] ?
                            subRegResult[`answer_number${question.id}`][0]: ''
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
                        {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, subRegResult[`answer_number${question.id}`] !== undefined ? true : null, 'required')}
                        {Number(question.enable_comments) === 1 && (
                          <div className="generic-form">
                            <p>{event.labels.GENERAL_YOUR_COMMENT}</p>
                            <textarea
                              placeholder={event.labels.GENERAL_YOUR_COMMENT}
                              cols={30}
                              rows={5}
                              disabled={subRegResult[`answer_number${question.id}`] !== undefined ? false : true}
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
                        <h5>{question.info[0].value}</h5>
                        <textarea
                          placeholder="Answer"
                          value={
                            subRegResult[`answer_open${question.id}`] &&
                            subRegResult[`answer_open${question.id}`][0]
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
                        {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, subRegResult[`answer_open${question.id}`] !== undefined ? true : null, 'required')}
                        {Number(question.enable_comments) === 1 && (
                          <div className="generic-form">
                            <p>{event.labels.GENERAL_YOUR_COMMENT}</p>
                            <textarea
                              placeholder={event.labels.GENERAL_YOUR_COMMENT}
                              cols={30}
                              rows={5}
                              disabled={subRegResult[`answer_open${question.id}`] !== undefined ? false : true}
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

                  {question.question_type === "dropdown" &&
                    (afterLogin ? question.display_question === "yes" : true) && (
                      <React.Fragment>
                        <div className="generic-form">
                          <h5>{question.info[0].value}</h5>
                          <div
                            className="custom-label-select"
                            style={{ width: "46%" }}
                          >
                            <Select
                              placeholder="Select value from dropdown"
                              components={{ IndicatorSeparator: null }}
                              options={question.answer.map((answer, i) => ({
                                label: answer.info[0].value,
                                value: answer.id,
                                linkTo: answer.link_to,
                                key: i,
                                isDisabled: (answer.tickets !== undefined && answer.tickets <= 0) ? true : false
                              }))}
                              
                              value={subRegResult[`answer_dropdown${question.id}`] !== undefined && { label:  question.answer.find((answer) => ( answer.id == subRegResult[`answer_dropdown${question.id}`][0].split('-')[0] )).info[0].value , value: subRegResult[`answer_dropdown${question.id}`][0].split('-')[0] }}
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
                          {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, subRegResult[`answer_dropdown${question.id}`] !== undefined ? true : null, 'required')}
                          {Number(question.enable_comments) === 1 && (
                            <div className="generic-form">
                              <p>{event.labels.GENERAL_YOUR_COMMENT}</p>
                              <textarea
                                placeholder={event.labels.GENERAL_YOUR_COMMENT}
                                cols={30}
                                rows={5}
                                disabled={subRegResult[`answer_dropdown${question.id}`] !== undefined ? false : true}
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
                        <h5>{question.info[0].value}</h5>
                        <DateTime
                          onChange={(item) => {
                            updateResult(
                              `answer_date${question.id}`,
                              "date",
                              item.format("YYYY-MM-DD"),
                              question.id
                            );
                            
                          }}
                          value={
                            subRegResult[`answer_date${question.id}`] &&
                            subRegResult[`answer_date${question.id}`][0]
                          }
                          label={`Select date`}
                          showdate={"YYYY-MM-DD"}
                        />
                        {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, subRegResult[`answer_date${question.id}`] !== undefined ? true : null, 'required')}
                        {Number(question.enable_comments) === 1 && (
                          <div className="generic-form">
                            <p>{event.labels.GENERAL_YOUR_COMMENT}</p>
                            <textarea
                              placeholder={event.labels.GENERAL_YOUR_COMMENT}
                              cols={30}
                              rows={5}
                              disabled={subRegResult[`answer_date${question.id}`] !== undefined ? false : true}
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
                        <h5>{question.info[0].value}</h5>
                        <DateTime
                          onChange={(item) => {
                            updateResult(
                              `answer_date_time${question.id}`,
                              "date_time",
                              item.format("YYYY-MM-DD HH:mm:ss"),
                              question.id
                            );
                          }}
                          value={
                            subRegResult[`answer_date_time${question.id}`] ?
                            subRegResult[`answer_date_time${question.id}`][0]: ''
                          }
                          label={`Select date time`}
                          showdate={"YYYY-MM-DD"}
                          showtime={"HH:mm:ss"}
                        />
                          {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, subRegResult[`answer_date_time${question.id}`] !== undefined ? true : null, 'required')}
                        {Number(question.enable_comments) === 1 && (
                          <div className="generic-form">
                            <p>{event.labels.GENERAL_YOUR_COMMENT}</p>
                            <textarea
                              placeholder={event.labels.GENERAL_YOUR_COMMENT}
                              cols={30}
                              rows={5}
                              disabled={subRegResult[`answer_date_time${question.id}`] !== undefined ? false : true}
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

                  {question.question_type === "single" &&
                    (afterLogin ? question.display_question === "yes" : true) && (
                      <React.Fragment>
                        <div className="radio-check-field style-radio">
                          <h5>{question.info[0].value}</h5>
                          {question.answer.map((answer) => (
                            <label
                              key={answer.id}
                              onClick={() => {
                                if((answer.tickets !== undefined && (answer.tickets <= 0))){
                                  return;
                                }
                                updateResult(
                                  `answer${question.id}`,
                                  "single",
                                  answer.id,
                                  question.id,
                                  answer.link_to
                                );
                              }}
                              className={
                                subRegResult[`answer${question.id}`] !==
                                  undefined &&
                                subRegResult[`answer${question.id}`].indexOf(
                                  answer.id
                                ) !== -1
                                  ? "checked"
                                  : (answer.tickets !== undefined &&  (answer.tickets <= 0)) ? 'check-disabled' : ""
                              }
                            >
                              <span>{answer.info[0].value}</span>
                            </label>
                          ))}
                          {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, subRegResult[`answer${question.id}`] !== undefined ? true : null, 'required')}
                          {Number(question.enable_comments) === 1 && (
                            <div className="generic-form">
                              <p>{event.labels.GENERAL_YOUR_COMMENT}</p>
                              <textarea
                                placeholder={event.labels.GENERAL_YOUR_COMMENT}
                                cols={30}
                                rows={5}
                                disabled={subRegResult[`answer${question.id}`] !== undefined ? false : true}
                              ></textarea>
                            </div>
                          )}
                        </div>
                      </React.Fragment>
                    )}

                  {question.question_type === "matrix" &&
                    (afterLogin ? question.display_question === "yes" : true) && (
                      <React.Fragment>
                        <div className={`matrix-question-wrapper`}>
                          <h5>{question.info[0].value}</h5>
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
                                      {answer.info[0].value}
                                    </div>
                                    {question.matrix.map((matrix) => (
                                      <React.Fragment key={matrix.id}>
                                        <div className="matrix-box">
                                          <label className="label-radio">
                                            <input
                                              checked={
                                                subRegResult[
                                                  `answer_matrix${question.id}_${answer.id}`
                                                ] !== undefined &&
                                                subRegResult[
                                                  `answer_matrix${question.id}_${answer.id}`
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
                          {Number(question.required_question) === 1 && simpleValidator.current.message(`${question.question_type}-${question.id}`, subRegResult[`answer${question.id}`] !== undefined && subRegResult[`answer${question.id}`].length === question.answer.length ? true : null, 'required')}
                          {Number(question.enable_comments) === 1 && (
                            <div className="generic-form">
                              <p>{event.labels.GENERAL_YOUR_COMMENT}</p>
                              <textarea
                                placeholder={event.labels.GENERAL_YOUR_COMMENT}
                                cols={30}
                                rows={5}
                                disabled={subRegResult[`answer${question.id}`] !== undefined ? false : true}
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
      <p style={{color:"green", textAlign:"center"}}>{alert !== null  &&  alert}</p>
      <p  className='error-message' style={{textAlign:"center"}}>{error !== null  &&  error}</p>
      <div className="bottom-button">
        <button className="btn btn-save-next btn-loader" disabled={updating ? true : false} onClick={(e)=>{handleSave(e)}}> {event.labels.GENERAL_SEND} </button>
      </div>
    </React.Fragment>
  );
};

export default SubRegForm;
