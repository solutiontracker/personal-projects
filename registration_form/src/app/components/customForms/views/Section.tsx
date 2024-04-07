import React, { useState, useContext, useRef } from 'react'
import FormMultipleChoice from './FormMultipleChoice';
import FormCheckboxes from './FormCheckboxes';
import FormDropDown from './FormDropDown';
import FormLinearScale from './FormLinearScale';
import FormShortAnswer from './FormShortAnswer';
import FormLongAnswer from './FormLongAnswer';
import FormRadioGrid from './FormRadioGrid';
import FormTickGrid from './FormTickGrid';
import FormTimebox from './FormTimebox';
import FormDatebox from './FormDatebox';
import FormTextBlock from './FormTextBlock';
import { validateShortAnswer } from '../helpers/validation';
import { EventContext } from "@/src//app/context/event/EventProvider";

const Section = (props: any) => {

    const { event } = useContext<any>(EventContext);

    const { section, sections, active, setActive, formData, setFormData, setStepType, attendee_id, order_id, event_id, regFormId, submitFormToServer, sectionHistory } = props;

    const [validated, setValidated] = useState<any>(true);

    const [nextSection, setNextSection] = useState<any>(section.next_section);

    const [submitForm, setSubmitForm] = useState<boolean>(false);

    const sectionBox = useRef<any>(null);

    const ValidateSection = async (e: any, type: any) => {
        console.log(type, nextSection);
        e.preventDefault();
        setStepType(type);
        if (type === 'back') {
            let newSectionHistory = [...sectionHistory];
            let removehistory: any = newSectionHistory.pop();
            setActive(removehistory?.previous);
            return;
        }
        let notValidatedFor: any = [];
        let formData2 = formData;

        await section.questions.forEach((question: any) => {
            if (question.required === 1) {

                if (!['checkboxes', 'drop_down', 'tick_box_grid', 'multiple_choice_grid', 'multiple_choice'].includes(question.type)) {
                    if (formData2[section.id][question.id].answer_value === "") {
                        formData2 = { ...formData2, [question.form_builder_section_id]: { ...formData2[section.id], [question.id]: { ...formData2[section.id][question.id], requiredError: true } } }
                        notValidatedFor.push(question.id);
                    }
                }

                if ((question.type === "checkboxes") || (question.type === "drop_down") || (question.type === "multiple_choice")) {
                    if (formData2[section.id][question.id].answer_id.length <= 0) {
                        formData2 = { ...formData2, [question.form_builder_section_id]: { ...formData2[section.id], [question.id]: { ...formData2[section.id][question.id], requiredError: true } } }
                        notValidatedFor.push(question.id);
                    }
                }

                if ((question.type === "tick_box_grid")) {
                    let answerdRows = section.questions.find((item: any) => (item.id === question.id)).grid_questions.filter((item: any) => (formData2[section.id][question.id].answer_id[item.id] !== undefined && formData2[section.id][question.id].answer_id[item.id].length > 0 ? true : false))
                    if (answerdRows.length !== section.questions.find((item: any) => (item.id === question.id)).grid_questions.length) {
                        formData2 = { ...formData2, [question.form_builder_section_id]: { ...formData2[section.id], [question.id]: { ...formData2[section.id][question.id], requiredError: true } } }
                        notValidatedFor.push(question.id);
                    }
                }

                if ((question.type === "multiple_choice_grid")) {
                    let answerdRows = section.questions.find((item: any) => (item.id === question.id)).grid_questions.filter((item: any) => (formData2[section.id][question.id].answer_id[item.id] !== undefined ? true : false))
                    if (answerdRows.length !== section.questions.find((item: any) => (item.id === question.id)).grid_questions.length) {
                        formData2 = { ...formData2, [question.form_builder_section_id]: { ...formData2[section.id], [question.id]: { ...formData2[section.id][question.id], requiredError: true } } }
                        notValidatedFor.push(question.id);
                    }
                }

                if (question.validation.type !== undefined) {
                    if (!validateShortAnswer(question.validation, (question.validation.type === "checkboxes" ? formData2[section.id][question.id].answer_id : formData2[section.id][question.id].answer_value))) {
                        formData2 = { ...formData2, [question.form_builder_section_id]: { ...formData2[section.id], [question.id]: { ...formData2[section.id][question.id], validationError: true } } }
                        setValidated(false);
                        notValidatedFor.push(question.id);
                    }
                }
                setTimeout(() => {
                    const scrollTo = document?.getElementsByClassName('wrapper-box')[0];
                    if (scrollTo !== undefined && scrollTo !== null) {
                        scrollTo.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
                    }
                }, 500);
            } else {
                setTimeout(() => {
                    const scrollTo = document?.getElementsByClassName('error-message')[0];
                    if (scrollTo !== undefined && scrollTo !== null) {
                        scrollTo.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
                    }
                }, 500);
            }
            if (formData2[section.id][question.id].validationError) {
                setValidated(false);
                notValidatedFor.push(question.id);
            }
        });

        setFormData(formData2);
        console.log(notValidatedFor.length <= 0 && validated === true);
        if (notValidatedFor.length <= 0 && validated === true) {
            if (type === 'next' && nextSection !== "SUBMIT") {
                let activate = (nextSection === "CONTINUE" || nextSection === "next" || nextSection === '') ? active + 1 : sections.findIndex((sect: any) => parseInt(sect.id) === parseInt(nextSection));

                setActive(activate);
            }
            if (type === 'submit' || nextSection === "SUBMIT") {
                if (nextSection === "CONTINUE" || nextSection === "next" || nextSection === "SUBMIT" || nextSection === "submit") {
                    submitFormToServer(event_id, regFormId, formData2);
                    setSubmitForm(true);
                } else {
                    let activate = sections.findIndex((sect: any) => parseInt(sect.id) === parseInt(nextSection));
                    setActive(activate);
                }
            }
        } else {
            sectionBox.current.querySelector('.error-message').parentElement?.scrollIntoView()
        }

    };


    return (
        !submitForm ? <React.Fragment>
            {sections && section &&
                <div className="ebs-form-wrapper">
                    <header className="header-section">
                        {section.title && (
                            <h3>{section.title}</h3>
                        )}
                        {section.description && (
                            <p style={{ paddingLeft: 0 }} className="ebs-description">{section.description}</p>
                        )}
                    </header>
                    <div className="wrapper-inner-content" ref={sectionBox}>
                        {section.questions.map((item: any, itemIndex: any) => {

                            if (item.type === "multiple_choice") {
                                return <FormMultipleChoice key={itemIndex} data={item} setFormData={setFormData} formData={formData} setValidated={setValidated} setNextSection={setNextSection} />
                            }
                            else if (item.type === "checkboxes") {
                                return <FormCheckboxes key={itemIndex} data={item} setFormData={setFormData} formData={formData} setValidated={setValidated} />
                            }
                            else if (item.type === "drop_down") {

                                return <FormDropDown key={itemIndex} data={item} setFormData={setFormData} formData={formData} setValidated={setValidated} setNextSection={setNextSection} />
                            }
                            else if (item.type === "linear_scale") {
                                return <FormLinearScale key={itemIndex} data={item} setFormData={setFormData} formData={formData} setValidated={setValidated} />
                            }
                            else if (item.type === "short_answer") {
                                return <FormShortAnswer key={itemIndex} data={item} setFormData={setFormData} formData={formData} setValidated={setValidated} />
                            }
                            else if (item.type === "paragraph") {
                                return <FormLongAnswer key={itemIndex} data={item} setFormData={setFormData} formData={formData} setValidated={setValidated} />
                            }
                            else if (item.type === "multiple_choice_grid") {
                                return <FormRadioGrid key={itemIndex} data={item} setFormData={setFormData} formData={formData} setValidated={setValidated} />
                            }
                            else if (item.type === "tick_box_grid") {
                                return <FormTickGrid key={itemIndex} data={item} setFormData={setFormData} formData={formData} setValidated={setValidated} />
                            }
                            else if (item.type === "time") {
                                return <FormTimebox key={itemIndex} data={item} setFormData={setFormData} formData={formData} setValidated={setValidated} />
                            }
                            else if (item.type === "date") {
                                return <FormDatebox key={itemIndex} data={item} setFormData={setFormData} formData={formData} setValidated={setValidated} />
                            }
                            else if (item.type === "text_block") {
                                return <FormTextBlock key={itemIndex} data={item} setFormData={setFormData} formData={formData} setValidated={setValidated} />
                            } else {
                                return null;
                            }

                        })}
                    </div>
                </div>
            }
            {sections && sections.length === 1 && (
                <div className="bottom-button text-center">
                    <button className="btn btn-save-next btn-loader" onClick={(e) => ValidateSection(e, 'submit')}>{event?.labels?.GENERAL_SUBMIT} <i className="material-icons">keyboard_arrow_right</i></button>
                </div>
            )}
            {sections && sections.length > 1 && active !== sections.length - 1 && (
                <div className="bottom-button text-center">
                    {active > 0 && (
                        <button
                            className="btn btn-save-next btn-loader"
                            onClick={(e) => ValidateSection(e, 'back')}
                        >
                            <i className="material-icons">keyboard_arrow_left</i>
                            {event?.labels?.FORM_BUILDER_BACK}
                        </button>
                    )}
                    <button
                        className="btn btn-save-next btn-loader"
                        onClick={(e) => ValidateSection(e, 'next')}
                    >
                        {event?.labels?.FORM_BUILDER_NEXT}
                        <i className="material-icons">keyboard_arrow_right</i>
                    </button>
                </div>
            )}
            {sections && sections.length > 1 && active === sections.length - 1 && (
                <div className="bottom-button text-center">
                    <button
                        className="btn btn-default"
                        onClick={(e) => ValidateSection(e, 'back')}
                    >
                        <i className="material-icons">keyboard_arrow_left</i>
                        {event?.labels?.FORM_BUILDER_BACK}
                    </button>
                    <button className="btn btn-save-next btn-loader" onClick={(e) => ValidateSection(e, 'submit')} > {event?.labels?.GENERAL_SUBMIT} <i className="material-icons">keyboard_arrow_right</i></button>
                </div>
            )}
        </React.Fragment> :
            <React.Fragment>
                Form Submitted successfully
            </React.Fragment>

    )
}

export default Section