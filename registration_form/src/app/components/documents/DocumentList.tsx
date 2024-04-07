import React, {useState} from 'react';
import {useDropzone} from 'react-dropzone';
import { ReactSVG } from 'react-svg';
import DropDown from '../forms/DropDown';

type Props = {
  label?: any,
  display?: any,
  type?: any,
  fields?: any,
  checked?: any
}
const options = [
	{
		id: 1,
		name: 'Passport Passport Passport Passport'
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

const DocumentList = ({ label, display, type, fields, checked }: Props) => {

	  return (
			<div className="ebs-document-wrapper">
				<h4 className="ebs-title-section">Document file(s)</h4>
				<div className="ebs-table-list d-none d-lg-block">
						<div className="row align-items-center">
							<div className="col-lg-6">
								<div className="row">
									<div className="col-sm-2"></div>
									<div className="col-sm-5"><strong>NAME</strong></div>
									<div className="col-sm-5"><strong>TYPE</strong></div>
								</div>
							</div>
							<div className="col-lg-6">
								<div className="row">
									<div className="col-md-5"><strong>DATE MODIFIED</strong></div>
									<div className="col-md-4"><strong>KIND</strong></div>
									<div className="col-md-3"><strong>ACTIONS</strong></div>
								</div>
							</div>
						</div>
					</div>
					<div className="ebs-table-list">
						<div className="row align-items-center">
							<div className="col-lg-6">
								<div className="row align-items-center">
									<div className="col-2">
										<div className="radio-check-field">
											<label style={{margin: 0, height: '14px'}} className='label-radio'>
												<input type="checkbox"  />
												<span></span>
											</label>
										</div>
									</div>
									<div className="col-5">
										<div className="ebs-file-name d-flex align-items-center">
											<div className="ebs-ico-ext">
												<ReactSVG wrapper="span" className="ebs-icon" src={require('@/src/img/ico-file.svg')} />
												<span className='ebs-ext'>jpg</span>
											</div>
											<span className="ebs-title">IMG-34356.jpg</span>
										</div>
									</div>
									<div className="col-5">
											<div className="ebs-responsive-select">
												<DropDown
													label={'Document type1111'}
													listitems={options}
													isMulti={true}
													placeholder={'Document type 1111'}
												/>
											</div>
									</div>
								</div>
							</div>
							<div className="col-lg-6">
								<div className="row align-items-center">
									<div className="col-5">
										<div className="">02 April,2021  11:08 </div>
									</div>
									<div className="col-4">
										<div className="">Image</div>
									</div>
									<div className="col-3">
										<div className="ebs-panel-btn">
											<button><ReactSVG wrapper="span" className="ebs-icon" src={require('@/src/img/ico-download.svg')} /></button>
											<button><i className="material-icons">delete_outline</i></button>
										</div>
									</div>
								</div>
							</div>
						</div>
				</div>
			</div>
  )
}
export default DocumentList;
