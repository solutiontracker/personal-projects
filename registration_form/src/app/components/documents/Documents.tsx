import React, { ReactElement, FC, useEffect, useState, useRef, useContext, useMemo } from "react";

import Loader from '@/src/app/components/forms/Loader';
import { ReactSVG } from "react-svg";
import FileUpload from "./FileUpload";
import in_array from "in_array";
import { useHistory } from 'react-router-dom';
import { EventContext } from "@/src//app/context/event/EventProvider";
import { service } from '@/src/app/services/service';

interface Props {
	section: any;
	order_id?: number;
	attendee_id?: number;
	provider?: any;
	ids?: any;
	registration_form_id?: any;
	goToSection: any;
	event: Event;
	orderAttendee?: any;
	formSettings?: any;
	loadFormSeting?: any;
}

const Documents: FC<Props> = (props): ReactElement => {

	const [show, setShow] = useState(true);

	const [docs, setDocs] = useState([]);

	const [orderAttendeeDocs, setOrderAttendeeDocs] = useState<any>(null);

	const [requiredTypes, setRequiredTypes] = useState<any>([]);

	const [types, setTypes] = useState<any>([]);

	const history = useHistory();

	const { event, formBuilderForms, updateOrder } = useContext<any>(EventContext);

	const { section } = props;
	
	const mounted = useRef(false);

	const [action, setAction] = useState(false);

	const [errors, setErrors] = useState<any>({});

	const [loading, setLoading] = useState(section === "manage-documents" ? true : false);

	useEffect(() => {
		mounted.current = true;

		return () => {
			mounted.current = false;
		};
	}, []);

	useEffect(() => {
		if (section === "manage-documents") {
			service.get(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/documents-data/${props.order_id}/${props.attendee_id}`)
				.then(
					response => {
						console.log(response?.data?.types)
						console.log(response.success && mounted.current)
						if (response.success && mounted.current) {
							if (response?.data?.types.length > 0) {
								setDocs(response?.data?.docs);
								setTypes(response?.data?.types);
								setRequiredTypes(response?.data?.types.filter((type: any) => (type.is_required === 1)))
								setOrderAttendeeDocs(response?.data?.order_attendee_docs);
								updateOrder(response?.data?.order);
								setLoading(false);
							} else {
								if (Number(props?.formSettings?.show_hotels) === 1) {
									props.goToSection('manage-hotel-booking', props.order_id, props.attendee_id);
								} else if (formBuilderForms.length > 0) {
									props.goToSection('custom-forms', props.order_id, props.attendee_id, formBuilderForms[0].id);
								} else {
									completedAttendeeIteration(props.attendee_id);
								}
								setLoading(false);
							}
						} else {

							history.push(`/${event.url}/${props?.provider}`);
						}
					},
					error => { }
				);
		}
	}, [section]);


	const completedAttendeeIteration = (attendee_id: any) => {
		service.post(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/registration/completed-attendee-iteration`, { attendee_id: attendee_id, order_id: props.order_id, provider: props?.provider })
			.then(
				response => {
					if (mounted.current) {
						if (response.success) {
							history.push(`/${event.url}/${props?.provider}/order-summary/${props.order_id}`);
						} else {
							setErrors(response.errors);
						}
						setAction(false);
						setLoading(false);
					}
				},
				error => {
					setAction(false);
					setLoading(false);
				}
			);
	}

	const gotoNextSection = () => {
		if (Number(props?.formSettings?.show_hotels) === 1) {
			props.goToSection('manage-hotel-booking', props.order_id, props.attendee_id);
		} else if (formBuilderForms.length > 0) {
			props.goToSection('custom-forms', props.order_id, props.attendee_id, formBuilderForms[0].id);
		} else {
			completedAttendeeIteration(props.attendee_id);
		}
	}

	return (
		<div className={`${!in_array(props.section, ["manage-documents"]) && 'tab-collapse'} wrapper-box`}>
			{loading && <Loader className='fixed' />}
			<React.Fragment>
				{loading && <Loader className='fixed' />}
				<header className="header-section">
					<h3 onClick={(e: any) => {
						if (props?.orderAttendee?.status === 'complete') {
							history.push(`/${event.url}/${props?.provider}/manage-documents/${props.order_id}/${props.attendee_id}`);
						} else {
							setShow(!show)
						}
					}}>
						{event?.labels?.REQUIRED_DOCUMENTS !== undefined ? event?.labels?.REQUIRED_DOCUMENTS : 'Required Document(s)'}  <i className="material-icons"> {props.section === "manage-documents" && show ? 'keyboard_arrow_up' : 'keyboard_arrow_down'}</i>
					</h3>
					<div className="icon-tick">
						{(location.pathname.toString().includes("/manage-documents") || props?.orderAttendee?.status === 'complete') ? (
							<img src={require('@/src/img/tick-green.svg')} alt="" />
						) : (
							<img src={require('@/src/img/tick-grey.svg')} alt="" />
						)}
					</div>
				</header>
				<div className="wrapper-box">
					{props.section === "manage-documents" && <div className="wrapper-inner-content">
						<div className="ebs-documents-wrapper">
							<p>{event?.labels?.REQUIRED_DOCUMENTS_DESCRIPTIONS !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_DESCRIPTIONS : 'Required Documents description'} </p>
							<div className="ebs-document-section">
								<h4 className="ebs-title-section">{event?.labels?.REQUIRED_DOCUMENTS_FILES !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_FILES : 'Download file(s)'}</h4>
								<p>{event?.labels?.REQUIRED_DOCUMENTS_FILES_DESCRIPTIONS !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_FILES_DESCRIPTIONS : 'Download the following files to get a better idea'}</p>
								<div className="ebs-document-list">

									{docs.length > 0 && docs.map((item: any) => (
										<div key={item.id} className="d-flex row ebs-document-list-box align-items-center">
											<div className="col-9">
												<div className="d-flex align-items-center">
													<ReactSVG wrapper="span" className="ebs-icon" src={require('@/src/img/ico-pdf.svg')} />
													<div className="ebs-doc-info">
														<h5>{item.file_title}</h5>
														<p>{event?.labels?.REQUIRED_DOCUMENTS_FILES_SUBTITLE !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_FILES_SUBTITLE : 'Download and fill the form'}</p>
													</div>
												</div>
											</div>
											<div className="col-3 d-flex justify-content-end">
												<a href={item.s3 === 1 ? item.s3_url : `${process.env.REACT_APP_EVENTCENTER_URL}/assets/documents/${item.file_name}`} download target="_blank" rel="noreferrer">
													<ReactSVG wrapper="span" className="ebs-icon" src={require('@/src/img/ico-download.svg')} />
												</a>
											</div>
										</div>
									))}
								</div>
							</div>
							<div className="ebs-document-upload">
								<h4 className="ebs-title-section">{event?.labels?.REQUIRED_DOCUMENTS_UPLOAD_FILES !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_UPLOAD_FILES : 'Upload file(s)'}</h4>
								<p>{event?.labels?.REQUIRED_DOCUMENTS_UPLOAD_FILES_DESCRIPTION !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_UPLOAD_FILES_DESCRIPTION : 'Upload file here'}</p>
								<div className="ebs-document-upload-section">
									{orderAttendeeDocs !== null && <FileUpload
										event={event}
										attendee_id={props.attendee_id}
										order_id={props.order_id}
										types={types}
										orderAttendeeDocs={orderAttendeeDocs}
										requiredTypes={requiredTypes}
										gotoNextSection={gotoNextSection}
									/>}
								</div>
							</div>
						</div>
					</div>}
				</div>
			</React.Fragment>
		</div>
	)
}


export default Documents;