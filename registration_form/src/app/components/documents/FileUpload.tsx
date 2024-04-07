import React, { useState, useCallback, useContext } from 'react';
import {useDropzone} from 'react-dropzone';
import { classNames } from 'react-select/src/utils';
import { ReactSVG } from 'react-svg';
import DropDown from '../forms/DropDown';
import { service } from '@/src/app/services/service';
import toast, { Toaster } from 'react-hot-toast';


type Props = {
  label?: any,
  display?: any,
  type?: any,
  fields?: any,
  checked?: any
  event:any,
  order_id:any,
  attendee_id:any,
  types:any,
  orderAttendeeDocs:any,
  requiredTypes:any,
  gotoNextSection:any
}
const options = [
	{
		id: 1,
		name: 'Passport'
	},
	{
		id: 2,
		name: 'CNIC'
	},
	{
		id: 3,
		name: 'Licence'
	},
	{
		id: 4,
		name: 'Visa'
	},
]

const FileUpload = ({ label, display, type, fields, checked, event, order_id, attendee_id, types, orderAttendeeDocs, requiredTypes, gotoNextSection }: Props) => {
	
	const [items, setItems] = useState<any>(orderAttendeeDocs);

	const [percentages, setPercentage] = useState<any>();

	const [answeredTypes, setAnsweredTypes] = useState<any>([]);

	const [errorsItems, setErrorItems] = useState<any>();

	const [errorsType, setErrorType] = useState<any>();

	const [selectedTypes, setSelectedTypes] = useState<any>([]);

	const onDrop = (acceptedFiles:any) => {
		console.log(acceptedFiles[0])
		let newItems = items;
		newItems.unshift({
			name:acceptedFiles[0].name,
			size:acceptedFiles[0].size,
			progress:0,
			type:acceptedFiles[0].type,
			documentType:"",
			types:selectedTypes
		});
		setItems([...newItems]);
		const formData = new FormData();
		formData.append('file', acceptedFiles[0])
		formData.append('types', JSON.stringify(selectedTypes.map((type:any)=> ({...type, label:''}))))
		setSelectedTypes([]);
		const xhr = new XMLHttpRequest();
		xhr.open('POST', `${process.env.REACT_APP_API_URL}/registration/event/${event.url}/documents/upload/${order_id}/${attendee_id}`, true);
		xhr.upload.onprogress = (event:any) => {
			const percentage = +((event.loaded / event.total) * 100).toFixed(2);
			setPercentage(percentage);
		};
		xhr.send(formData)
		xhr.onreadystatechange = () => {
			if (xhr.readyState !== 4) return;
			if (xhr.status !== 200) {
				console.log('error');
			}
			const res = JSON.parse(xhr.response)
			let newItems = items;
			newItems[0] = res.data;
			newItems[0].progress = 1;
			setItems([...newItems]);

		};
	  }
	
	  const deleteDocument = useCallback((item, index)=>{
		
		setItems((prevState:any)=> prevState.filter((it:any,k:any)=>(k !== index)) );
		service.destroy(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/documents/delete/${item.id}`)
        .then(
          response => {
            
          },
          error => { }
        );
	  },[])

	  const attachItems = (types:any, itemIndex:number, item:any) => {
		let newItems = items;
		setErrorItems(false);
		console.log(types, 'types');
		newItems[itemIndex];
		newItems[itemIndex].types = types;
		setItems([...newItems]);
		service.simplePost(`${process.env.REACT_APP_API_URL}/registration/event/${event.url}/documents/attach-types/${item.id}`, {types:types.map((type:any)=> ({...type, label:''}))})
        .then(
          response => {
            
          },
          error => { }
        );
	  }

	  const save = async () => {
		setErrorItems(false);
		setErrorType(false);
		let answers:any = [];
		let itemNotAssignedTypes = false;
		await items.forEach(async (item:any)=>{
			if(item.types === undefined || item.types.length < 1)
			{
				itemNotAssignedTypes = true;
			}
			else{
				let filter = await item.types.filter((i:any)=> requiredTypes.find((type:any)=>(type.id === i.value)))
				if(filter.length > 0){
					await filter.forEach((f:any)=>{
							if(!answers.find((i:any)=>(i.value === f.value))){
								answers.push(f);
							}
					})
				}
				console.log(item.types.length);
			}
		});
		
		console.log(answers);
		console.log(requiredTypes);
		console.log(itemNotAssignedTypes);
		if(answers.length !== requiredTypes.length){
			setErrorItems(true);
		}
		else if(itemNotAssignedTypes)
		{
			setErrorType(true);
		}
		else{
			gotoNextSection();
		}
	}

	const {
    getRootProps,
    getInputProps,
	open,
    isFocused,
    isDragAccept,
    isDragReject,
	acceptedFiles
	  } = useDropzone({    
		maxFiles:10,
		noClick: true,
		noKeyboard: true,
		accept: {
			'image/*': ['.png','.svg'],
			'application/pdf': ['.pdf'],
			'application/zip': ['.zip'],
			// 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': ['.docx','doc'],
			'text/plain': [],
			'text/html': ['.html', '.htm'],
		},
		onDrop,
	  });
		const focusedStyle = {
			borderColor: '#2196f3'
		};
		
		const acceptStyle = {
			borderColor: '#00e676'
		};
		
		const rejectStyle = {
			borderColor: '#ff1744'
		};
			const style = React.useMemo(() => ({
				...(isFocused ? focusedStyle : {}),
				...(isDragAccept ? acceptStyle : {}),
				...(isDragReject ? rejectStyle : {})
			}), [
				isFocused,
				isDragAccept,
				isDragReject
			]);
			 
	  return (
		<>
				<div className="row">
					<div className={`${items.length > 0 ? 'col-md-5' : 'col-md-12'}`}>
						<div id="ebs-upload-box" {...getRootProps({className: 'dropzone'})}>
							<div className={`ebs-upload-box-inner ${items.length > 0 && 'ebs-smaller-box'}`}>
							<input {...getInputProps()} />
							
							<div  style={{minWidth:'350px'}} className='d-flex align-items-center'>
								<DropDown
									  label={event?.labels?.REGISTRATION_FORM_DOCUMENT_TYPE}
										listitems={types.map((type:any)=>(
											{...type,
												name: type.is_required === 1 ? (
												<React.Fragment>
												  {type.name}   <span style={{color:'red'}}>*</span> 
												</React.Fragment>
											  ) : type.name }
										))}
										isMulti={true}
									  placeholder={event?.labels?.REGISTRATION_FORM_DOCUMENT_TYPE}
										selected={selectedTypes}
										onChange={(types:any)=> {setSelectedTypes(types); setErrorItems(false);} }
								/>
								<div >
									<button type='button' onClick={open} style={{marginLeft:'20px', marginBottom:'10px'}} className='btn btn-save-next btn-loader d-flex justify-content-around align-items-center'>
										
										<img src={require('@/src/img/upload-file-icon.svg')} alt="file upload icon" className='mr-2'  />	
										  {event?.labels?.REQUIRED_DOCUMENTS_BUTTON_UPLOAD_FILES}
									</button>
								</div>
							</div>
							<h3>{event?.labels?.REQUIRED_DOCUMENTS_BUTTON_UPLOAD_FILES !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_BUTTON_UPLOAD_FILES : 'Upload file'}</h3>
							<p>{event?.labels?.REQUIRED_DOCUMENTS_BUTTON_UPLOAD_FILES_SUBTITLE !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_BUTTON_UPLOAD_FILES_SUBTITLE : 'Drag file here or select a file for upload'}
							{/* <strong>Drag</strong> file here or <strong>select</strong> a file for Upload */}
							</p>
							<div className="tagline">{event?.labels?.REQUIRED_DOCUMENTS_BUTTON_UPLOAD_FILES_ALLOWED_FILES !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_BUTTON_UPLOAD_FILES_ALLOWED_FILES : 'Supported file types: JPG, PNG, DOCX, PDF'}</div>
						</div>
					</div>
				</div>
				{items.length > 0 && <div className="col-md-7">
					<ul className='ebs-fileupload-list'>
						{items && items.map((item:any,k:any) =>
							<li style={{zIndex: (items.length - k)}} className='d-flex align-items-center' key={k}>
								<div className="ebs-file-info d-flex">
									<div className="ebs-ico-ext">
										<ReactSVG wrapper="span" className="ebs-icon" src={require('@/src/img/ico-file.svg')} />
										<span className='ebs-ext'>{item.name.split('.').pop()}</span>
									</div>
									<div className="ebs-file-content">
										<h5 title={item.name}>
											<span className='ebs-file-name'>{item.name.split(".").slice(0, -1).join(".")}</span>
											<span className="ebs-file-ext">.{item.name.split('.').pop()}</span>
										</h5>
										<p>{Math.ceil(item.size/1024) < 1024 ? Math.ceil(item.size/1024)+'KB' : Math.ceil(item.size/1024000)+'MB'}</p>
										{item.progress === 0 && <div className="ebs-loader"><span style={{width: `${percentages}%`}}></span></div>}
									</div>
								</div>
								<div className="ebs-file-panel">
									<div className="ebs-responsive-select">
										<DropDown
											label={event?.labels?.REGISTRATION_FORM_DOCUMENT_TYPE}
											listitems={types.map((type:any)=>(
												{...type,
													name: type.is_required === 1 ? (
													<React.Fragment>
													  {type.name}   <span style={{color:'red'}}>*</span> 
													</React.Fragment>
												  ) : type.name }
											))}
											isMulti={true}
											placeholder={event?.labels?.REGISTRATION_FORM_DOCUMENT_TYPE}
											selected={item.types !== undefined ? item.types.map((type:any)=>{
												console.log(typeof type.label)
												return {...type,
													label: (requiredTypes.find((rt:any)=>(type.value == rt.id)) !== undefined && typeof type.label === 'string') ? (
													<React.Fragment>
													  {type.label}   <span style={{color:'red'}}>*</span> 
													</React.Fragment>
												  ) : type.label }
											}) : []}
											onChange={(types:any)=> attachItems(types, k, item) }
										/>
									</div>
									<button className='btn' onClick={()=> deleteDocument(item, k)}><i className="material-icons">delete_outline</i></button>
								</div>
							</li> 
						)}
					</ul>
				</div>}
				
			</div>
			{errorsItems && <p  className="error-message">
				{event?.labels?.REQUIRED_DOCUMENTS_MANDATORY_TYPE_ERROR !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_MANDATORY_TYPE_ERROR : 'Documents for these types are required'}
				{requiredTypes.map((item:any, i:any)=>(
					<span key={item.id} className='ml-2'>{item.name}{i !== (requiredTypes.length - 1) ? "," : ""}</span>
				))}
			</p>}
			{errorsType && 
				<p  className="error-message">
					{event?.labels?.REQUIRED_DOCUMENTS_ATTACH_TYPE_TO_EVERY_DOCUMENT !== undefined ? event?.labels?.REQUIRED_DOCUMENTS_ATTACH_TYPE_TO_EVERY_DOCUMENT : 'Please attach a type to every document'}
					</p>
			}
			<div className="bottom-button text-center">
							<button
								onClick={()=>{
									if(selectedTypes.length === 0){
										save()
									}else{
										toast.error(event?.labels?.REGISTRATION_FORM_PLEASE_CLEAR_SELECT_DOCUMENT_TYPE, {
											position: "bottom-center"
										  });
									}
								}}
								type="submit"
								disabled={false}
								className="btn btn-save-next btn-loader">
						
								{/* <>
								Loading...
								<i className="material-icons ebs-spinner">autorenew</i>
								</> */}
							
								<>
								{event?.labels?.REGISTRATION_FORM_SAVE_AND_NEXT}
								<i className="material-icons">keyboard_arrow_right</i>
								</>
							
							</button>
			</div>
			<Toaster/>
		</>
  )
}
export default FileUpload;
