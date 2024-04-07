import React, { useState } from 'react'
import moment from 'moment-timezone';
import Image from 'next/image'
import PageHeader from 'components/modules/PageHeader';
import { GATrackEventDocumentDownloadEvent } from '../../helpers/helper';
const getDirectoryName = (item) => {
    if (item['name'] !== undefined) return item.name
    else if (item['Program'] !== undefined) return item.Program
    else if (item['Speakers'] !== undefined) return item.Speakers
    else if (item['Sponsors'] !== undefined) return item.Sponsors
    else if (item['Exhibitors'] !== undefined) return item.Exhibitors
    else if (item['Other'] !== undefined) return item.Other
}

function DocumentsListing({ documents, documentPage, labels, page, eventTimezone }) {
    const [currentDirectory, setCurrentDirectory] = useState(documents);
    const [currentFolder, setCurrentFolder] = useState({});
    const [breadCrumbs, setBreadCrumbs] = useState([{ pid: 0, cid: 0, pname: labels.GENERAL_DOCUMENT }]);
    const onDirectoryClick = (id) => {
        let currentFolder = currentDirectory.find((item) => (item.id === id))
        setCurrentFolder(currentFolder);
        setCurrentDirectory(currentFolder.children_files);
        let newObj = { cid: currentFolder.id, pname: getDirectoryName(currentFolder) }
        setBreadCrumbs([...breadCrumbs, newObj]);
    }
    const onBreadCrumbClick = (crumb, index) => {
        if (crumb.cid === 0) {
            setCurrentFolder({});
            setCurrentDirectory(documents);
            setBreadCrumbs([breadCrumbs[0]]);
        } else {
            if (currentFolder.id !== crumb.pid) {
                let toFolder = null;
                documents.forEach((document, i) => {
                    if (document.id === crumb.cid) {
                        toFolder = document;
                    } else {
                        if (toFolder === null) {
                            toFolder = traverse(document.children, crumb.cid);
                        }
                    }
                });
                setCurrentFolder(toFolder);
                setCurrentDirectory(toFolder.children_files);
                setBreadCrumbs(breadCrumbs.slice(0, (index + 1)));
            }
        }
    }

    const traverse = (childern, id) => {
        let arr = null;
        childern.every((document, i) => {
            if (document.id === id) {
                arr = document;
            } else {
                arr = traverse(document.children, id);
            }
            if (arr !== null) {
                return false;
            }
            return true;
        });
        return arr;
    }

    const checkFile = (directory) => {
        
        let files = false;
        if (directory.files !== undefined && directory.files.length > 0) {
            return true;
        }        
        if (directory.children_files.length > 0) {
            directory.children_files.map((directory, i) => {
                if (directory.files !== undefined && directory.files.length > 0) {
                    files = true;
                }
                else if ((directory.files !== undefined && directory.files.length > 0) || directory.children_files.length > 0) {
                    files = checkFile(directory);
                }
            });
        }
        console.log(files)
        return files;
    }

    let filesCount = 0;

    return (
        <>
            {documentPage && <PageHeader label={labels.GENERAL_DOCUMENT !== undefined ? labels.GENERAL_DOCUMENT : 'My Documents'} showBreadcrumb={1} breadCrumbs={(type) => {
                return (<nav aria-label="breadcrumb" className={`ebs-breadcrumbs ${type !== "background" ? "ebs-dark" : ""}`}>
                    <ul className="breadcrumb">
                        {
                            breadCrumbs.map((crumb, i) => (
                                <li className="breadcrumb-item" key={i} ><span style={{ cursor: 'pointer' }} onClick={() => { onBreadCrumbClick(crumb, i); }}>{crumb.pname}</span></li>
                            ))
                        }
                    </ul>
                </nav>)
            }} />}
            <div style={{ paddingTop: 30 }} className="ebs-document-module">
                {!documentPage && <nav aria-label="breadcrumb" className={`ebs-breadcrumbs ebs-dark`}>
                    <ul className="breadcrumb">
                        {
                            breadCrumbs.map((crumb, i) => (
                                <li className="breadcrumb-item" key={i}><span style={{ cursor: 'pointer' }} onClick={() => { onBreadCrumbClick(crumb, i); }}>{crumb.pname}</span></li>
                            ))
                        }
                    </ul>
                </nav>}
                <div style={{ padding: 0 }} className="container">
                    <div className="ebs-document-header">
                        <div className="row d-flex align-items-center">
                            <div className="col-6 col-sm-8 col-lg-9">
                                <h6>{labels.GENERAL_DOCUMENT_NAME !== undefined ? labels.GENERAL_DOCUMENT_NAME : 'Name'} <i className="material-icons">arrow_downward</i></h6>
                            </div>
                            <div className="col-6 col-sm-4 col-lg-3">
                                <h6>{labels.GENERAL_DOCUMENT_MODIFIED !== undefined ? labels.GENERAL_DOCUMENT_MODIFIED : 'Modified'}</h6>
                            </div>
                        </div>
                    </div>
                    {currentDirectory && currentDirectory.length > 0 &&
                        currentDirectory.map((item, i) => {
                            if ((item['directory_id'] === undefined) && checkFile(item)) {
                                filesCount++;
                                return (<div key={i} className="ebs-document-content">
                                    <div className="row d-flex align-items-center"
                                        onClick={() => { onDirectoryClick(item.id) }}
                                    >
                                        <div className="col-6 col-sm-8 col-lg-9">
                                            <div className="ebs-title" ><i className="material-icons">folder</i>{getDirectoryName(item)}</div>
                                        </div>

                                        <div className="col-6 col-sm-4 col-lg-3">
                                            <div className="ebs-date"><span>{moment(item.start_date ? `${item.start_date} ${item.start_time}` : item.updated_at).tz(eventTimezone).format('D-MM-YYYY HH:mm')}
                                            </span></div>
                                        </div>
                                    </div>
                                </div>)
                            }

                            if (item['directory_id'] !== undefined) {
                                filesCount++;
                                return (<div key={i} className="ebs-document-content">
                                    <a href={item.s3 === 1 ? item.s3_url : `${process.env.NEXT_APP_EVENTCENTER_URL}/assets/directory/${item.path}`} download onClick={() => GATrackEventDocumentDownloadEvent('DownloadedDocuments', page, '::' + item['id'] + "::" + item['directory_id'])} target="_blank" rel="noreferrer">
                                        <div className="row d-flex align-items-center">
                                            <div className="col-6 col-sm-8 col-lg-9">
                                                <div className="ebs-title" >
                                                    <FileImageByType type={item.path.split('.')[1]} path={item.path} />
                                                    {item.name}
                                                </div>

                                            </div>

                                            <div className="col-6 col-sm-4 col-lg-3">
                                                <div className="ebs-date"><span>{item.start_date ? moment(`${item.start_date} ${item.start_time}`).format('D-MM-YYYY HH:mm') : moment(item.updated_at).tz(eventTimezone).format('D-MM-YYYY HH:mm')}
                                                    {(moment().diff(moment(item.start_date ? item.start_date : item.created_at)) > 0) &&
                                                        <i className="material-icons">file_download</i>
                                                    }
                                                </span></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>)
                            }
                        })
                    }
                    {(!currentDirectory || currentDirectory.length <= 0) || (filesCount <= 0) &&
                        <div className="ebs-document-content">
                            <div className="row d-flex align-items-center">
                                <div className="ebs-title">{labels.GENERAL_NO_RECORD ? labels.GENERAL_NO_RECORD : "No Folders or Files found in current Directory"}</div>
                            </div>
                        </div>
                    }
                </div>
            </div>
        </>
    )
}

export default DocumentsListing;


const FileImageByType = ({ type, path }) => {
    if (type == "doc" || type == "docx") {
        return <img style={{ marginRight: "5px" }} alt="" src={`${process.env.NEXT_APP_EVENTCENTER_URL}/_eventsite_assets/images/word.png`} width="30" className="img-responsive" />;
    } else if (type == "xls" || type == "xlsx" || type == "csv") {
        return <img style={{ marginRight: "5px" }} alt="" src={`${process.env.NEXT_APP_EVENTCENTER_URL}/_eventsite_assets/images/excel.png`} width="30" className="img-responsive" />;
    } else if (type == "ppt" || type == "pptx") {
        return <img style={{ marginRight: "5px" }} alt="" src={`${process.env.NEXT_APP_EVENTCENTER_URL}/_eventsite_assets/images/pptx.png`} width="30" className="img-responsive" />;
    } else if (type == "pdf") {
        return <img style={{ marginRight: "5px" }} alt="" src={`${process.env.NEXT_APP_EVENTCENTER_URL}/_eventsite_assets/images/pdf.png`} width="30" className="img-responsive" />;
    } else if (type == "mp3" || type == "avi" || type == "mp4") {
        return <img style={{ marginRight: "5px" }} alt="" src={`${process.env.NEXT_APP_EVENTCENTER_URL}/_eventsite_assets/images/music.png`} width="30" className="img-responsive" />;
    } else if (type == "jpg" || type == "jpeg" || type == "png" || type == "gif") {
        return <img style={{ marginRight: "5px" }} alt="" src={`${process.env.NEXT_APP_EVENTCENTER_URL}/assets/directory/${path}`} width="30" className="img-responsive" />;
    } else {
        return <img style={{ marginRight: "5px" }} alt="" src={`${process.env.NEXT_APP_EVENTCENTER_URL}/_eventsite_assets/images/allFiles.png`} width="30" className="img-responsive" />;
    }
}






