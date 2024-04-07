import React, { Component, useState, useEffect } from 'react';
import { NavLink } from 'react-router-dom';
import Img from 'react-image';
import Loader from '@/app/forms/Loader';
import moment from 'moment';
import Input from '@/app/forms/Input';
import { service } from 'services/service';
import { Translation, withTranslation } from "react-i18next";
import DropDown from "@/app/forms/DropDown";
import DateTime from '@/app/forms/DateTime';
import Timepicker from '@/app/forms/Timepicker';
import { confirmAlert } from 'react-confirm-alert'; // Import
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { CopyToClipboard } from 'react-copy-to-clipboard';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { connect } from 'react-redux';
import { OverlayTrigger, Tooltip } from "react-bootstrap";

const in_array = require("in_array");

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/module/sub-registration", "ddirectory": "/event/module/documents" };

class MoveCopyContent extends Component {
    state = {
        data: this.props.data.filter(function (directory, i) {
            return (!in_array(directory.alias, ["exhibitors", "sponsors"]));
        }),
        fileID: this.props.fileID,
        levelId: 0,
        alias: '',
        breadCrumb: [0],
    }

    dropDownIcons = (id) => {
        let item = false;
        this.state.data.forEach(element => {
            if (element.parent_id === id && element.type === 'folder') {
                item = true
            }
        });
        if (item) {
            return true;
        }
    }

    handleFolder = (value, alias) => e => {
        e.preventDefault();
        this.setState({
            breadCrumb: [...this.state.breadCrumb, value],
            levelId: value,
            alias: alias
        })
    }

    handlePrev = (value) => e => {
        e.preventDefault();
        var array = this.state.breadCrumb;
        var index = array.indexOf(value);
        array = this.state.breadCrumb.splice(0, index + 1)
        this.setState({
            breadCrumb: array,
            levelId: value
        })
    }

    render() {
        return (
            <Translation>
                {
                    t =>
                        <div id="react-confirm-alert">
                            <div className="react-confirm-alert-overlay">
                                <div className="react-confirm-alert">
                                    <div className="app-main-popup">
                                        <div className="app-header">
                                            <h4>{this.props.action === 'move' ? t('DOCUMENT_MOVE_TO') : t('DOCUMENT_COPY_TO')}</h4>
                                        </div>
                                        <div className="app-body">
                                            <nav style={{ marginBottom: 10 }} className="doc-breadcrumbs" aria-label="breadcrumb">
                                                <ol style={{ margin: 0 }} className="breadcrumb">
                                                    <li onClick={this.handlePrev(0)} className="breadcrumb-item">{t('DOCUMENT_ROOT')}</li>
                                                    {this.state.data.map((items, k) =>
                                                    ((this.state.breadCrumb.indexOf(items.id) !== -1) && items.type === 'folder' &&
                                                        <li onClick={this.handlePrev(items.id)} key={k} className="breadcrumb-item">{items.name}</li>
                                                    ))}
                                                </ol>
                                            </nav>
                                            <div className="doc-folderTree active">
                                                {this.state.data.map((item, k) =>
                                                    (item.parent_id === this.state.levelId && item.type === 'folder' && <div onClick={this.handleFolder(item.id, item.alias)} className={this.dropDownIcons(item.id) ? 'folder-items has-items' : 'folder-items'} key={k}>{item.name} {this.dropDownIcons(item.id) ? <span className="icon"><i className="material-icons">chevron_right</i></span> : ''}</div>)
                                                )}
                                            </div>
                                        </div>
                                        <div className="app-footer">
                                            <button onClick={() => this.props.onClose()} className="btn btn-cancel">Cancel</button>
                                            <button onClick={() => this.props.onCreate(this.state.fileID, this.state.levelId)} className={`${this.state.levelId === 0 || in_array(this.state.alias, ['agendas', 'speakers', 'exhibitors', 'sponsors']) ? 'disabled pointer-none' : ''} btn btn-success`}> {this.props.action === 'move' ? t('DOCUMENT_MOVE') : t('DOCUMENT_COPY')}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                }
            </Translation>
        )
    }
}

const CreateFolder = ({ onClose, onCreate, errors, module ,groups,isLoader}) => {
    const [value, setValue] = useState('');
    const [groupvalueLable, setGroupValueLable] = useState('');
    const [groupvalue, setGroupValue] = useState([]);
    const [grouplabel, setGroupLabel] = useState('');
    const getSelectedLabel = (item, id) => {
        if (item && item.length > 0 && id) {
            let obj = item.find(o => o.id.toString() === id.toString());
            return (obj ? obj.name : '');
        }
    }
    const getGroupSelectedLabel = (item, id) => {
        if (item && item.length > 0 && id) {
            let obj = item.find(o => o.id.toString() === id.toString());
            return (obj ? obj.name : '');
        }
    }
    const setGroupValues = (groups) => {
        const value=[];
        groups.length&&groups.map((item, index) => {
            value.push(item.id);
          });
          setGroupValue(value)
    }
    return (
        <Translation>
            {
                t =>
                    <div id="react-confirm-alert">
                        <div className="react-confirm-alert-overlay">
                            <div className="react-confirm-alert">
                                <div className="app-main-popup">
                                    <div className="app-header">
                                        <h4>{t('DOCUMENT_NEW_FOLDER')}</h4>
                                    </div>
                                    <div className="app-body">
                                        <Input
                                            type='text'
                                            label={t('DOCUMENT_NEW_FOLDER_NAME')}
                                            name='Folder'
                                            value={value}
                                            onChange={e => setValue(e.target.value)}
                                            required={true}
                                        />
                                        {errors !== undefined && errors.name && (
                                            <p className="error-message">{errors.name}</p>
                                        )}
                                         <DropDown
                                            label="Groups"
                                            listitems={groups}
                                            selected={groupvalueLable}
                                            isMulti={true}
                                            isGroup={true}
                                            selectedlabel={getGroupSelectedLabel(groups, groupvalueLable)}
                                            onChange={e => {setGroupValues(e); setGroupValueLable(e.value);setGroupLabel(e.label) 
                                            }}
                                            required={true}
                                        />
                                    </div>
                                    <div className="app-footer">
                                        <button onClick={onClose} className="btn btn-cancel">{t('G_CANCEL')}</button>
                                        <button
                                            onClick={() => onCreate(value,groupvalue)}
                                            className={`${!value ? 'disabled pointer-none' : ''} btn btn-success`} disabled={isLoader === false?false:true}>{isLoader === 'create-folder' ?
                                            <span className="spinner-border spinner-border-sm"></span> : null}{t('G_CREATE')}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            }
        </Translation>
    );
}

const AssignModule = ({ onClose, onCreate, modulesData, errors, module, props,groups,isLoader }) => {
    const [value, setValue] = useState('');
    const [label, setLabel] = useState('');
    const [grouplabel, setGroupLabel] = useState('');
    const [groupvalueLable, setGroupValueLable] = useState('');
    const [groupvalue, setGroupValue] = useState([]);

    const getSelectedLabel = (item, id) => {
        if (item && item.length > 0 && id) {
            let obj = item.find(o => o.id.toString() === id.toString());
            return (obj ? obj.name : '');
        }
    }
    const getGroupSelectedLabel = (item, id) => {
        if (item && item.length > 0 && id) {
            let obj = item.find(o => o.id.toString() === id.toString());
            return (obj ? obj.name : '');
        }
    }
    const setGroupValues = (groups) => {
        const value=[];
        groups.length&&groups.map((item, index) => {
            value.push(item.id);
          });
          setGroupValue(value)
    }

    const labels = {
        "agendas": props.t('DOCUMENT_SELECT_PROGRAM'),
        "speakers": props.t('DOCUMENT_SELECT_SPEAKER'),
        "sponsors": props.t('DOCUMENT_SELECT_SPONSOR'),
        "exhibitors": props.t('DOCUMENT_SELECT_EXHIBITOR'), 
    };

    return (
        <Translation>
            {
                t =>
                    <div id="react-confirm-alert">
                        <div className="react-confirm-alert-overlay">
                            <div className="react-confirm-alert">
                                <div className="app-main-popup">
                                    <div className="app-header">
                                        <h4>{t('DOCUMENT_NEW_FOLDER')}</h4>
                                    </div>
                                    <div className="app-body">
                                        <DropDown
                                            label={labels[module]}
                                            listitems={modulesData}
                                            selected={value}
                                            selectedlabel={getSelectedLabel(modulesData, value)}
                                            onChange={e => { setValue(e.value); setLabel(e.label) }}
                                            required={true}
                                        />
                                        {errors !== undefined && errors.agenda_id && module === "agendas" && (
                                            <p className="error-message">{errors.agenda_id}</p>
                                        )}
                                        {errors !== undefined && errors.speaker_id && module === "speakers" && (
                                            <p className="error-message">{errors.speaker_id}</p>
                                        )}
                                        {errors !== undefined && errors.sponsor_id && module === "sponsors" && (
                                            <p className="error-message">{errors.sponsor_id}</p>
                                        )}
                                        {errors !== undefined && errors.exhibitor_id && module === "exhibitors" && (
                                            <p className="error-message">{errors.exhibitor_id}</p>
                                        )}

                                        <DropDown
                                            label="Groups"
                                            listitems={groups}
                                            selected={groupvalueLable}
                                            isMulti={true}
                                            isGroup={true}
                                            selectedlabel={getGroupSelectedLabel(groups, groupvalueLable)}
                                            onChange={e => {setGroupValues(e); setGroupValueLable(e.value);setGroupLabel(e.label) 
                                            }}
                                            required={true}
                                        />
                                    </div>
                                    <div className="app-footer">
                                        <button onClick={onClose} className="btn btn-cancel">{t('G_CANCEL')}</button>
                                        <button onClick={() =>{
                                            onCreate(value,groupvalue, label);
                                            
                                            }} className={`${!value ? 'disabled pointer-none' : ''} btn btn-success`} disabled={isLoader === false?false:true}>{isLoader === 'save-document' ?
                                            <span className="spinner-border spinner-border-sm"></span> : null}{t('G_CREATE')}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            }
        </Translation>
    );
}

const RenameElement = ({ onClose, onCreate, errors, module, renameElementId, renameElementName }) => {
    const [value, setValue] = useState(renameElementName);
    return (
        <Translation>
            {
                t =>
                    <div id="react-confirm-alert">
                        <div className="react-confirm-alert-overlay">
                            <div className="react-confirm-alert">
                                <div className="app-main-popup">
                                    <div className="app-header">
                                        <h4>{t('DOCUMENT_RENAME')}</h4>
                                    </div>
                                    <div className="app-body">
                                        <Input
                                            type='text'
                                            label={t('DOCUMENT_RENAME')}
                                            name='name'
                                            value={value}
                                            onChange={e => setValue(e.target.value)}
                                            required={true}
                                        />
                                        {errors !== undefined && errors.name && (
                                            <p className="error-message">{errors.name}</p>
                                        )}
                                    </div>
                                    <div className="app-footer">
                                        <button onClick={onClose} className="btn btn-cancel">{t('G_CANCEL')}</button>
                                        <button
                                            onClick={() => onCreate(value, renameElementId)}
                                            className={`${!value ? 'disabled pointer-none' : ''} btn btn-success`}>{t('DOCUMENT_UPDATE')}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            }
        </Translation>
    );
}
const EditElement = ({ onClose, onCreate, modulesData, errors, module, props,groups,renameElementId,renameElementName,selectedGroups,isLoader }) => {
    const [renameElementIdVal, setrenameElementId] = useState(renameElementId);
    const [value, setValue] = useState(renameElementName);
    const [grouplabel, setGroupLabel] = useState('');
    const [groupvalueLable, setGroupValueLable] = useState('');
    const [groupvalue, setGroupValue] = useState([]);
    const getGroupSelectedLabel = (item, id) => {
        console.log(item)
        if (item && item.length  > 0 && id && id!=undefined) {
            let obj = item.find(o =>o.id!=undefined&& o.id.toString() === id.toString());
            return (obj ? obj.name : '');
        }
    }
    useEffect(() => {
        setValuesForGroups()
      },[]);
   const setValuesForGroups=()=>{
    var check=[]
    var group_val=[]
        selectedGroups.map((item,index)=>{
            groups.map((item2,index2)=>{
                item2.options.map((item3,index3)=>{
                    if(item3.id==item.id){
                        check.push(item3);
                        group_val.push(item.id);
                    }
                }) 
            })
        })
    setGroupValueLable(check)
    setGroupValue(group_val)
    console.log(groupvalue)
   }
    
    const setGroupValues = (groups) => {
        const value=[];
        groups!=null&&groups.length&&groups.map((item, index) => {
            value.push(item.id);
          });
          setGroupValue(value)
    }
    return (
        <Translation>
            {
                t =>
                    <div id="react-confirm-alert">
                        <div className="react-confirm-alert-overlay">
                            <div className="react-confirm-alert">
                                <div className="app-main-popup">
                                    <div className="app-header">
                                        <h4>{t('DOCUMENT_EDIT_FOLDER')}</h4>
                                    </div>
                                    <div className="app-body">
                                    
                                        <Input
                                            type='text'
                                            label={t('DOCUMENT_RENAME')}
                                            name='name'
                                            value={value}
                                            onChange={e => setValue(e.target.value)}
                                            required={true}
                                        />
                                        {errors !== undefined && errors.name && (
                                            <p className="error-message">{errors.name}</p>
                                        )}
                                        <DropDown
                                            label="Groups"
                                            listitems={groups}
                                            selected={groupvalueLable}
                                            isMulti={true}
                                            isGroup={true}
                                            selectedlabel={getGroupSelectedLabel(groups, groupvalueLable)}
                                            onChange={e => {
                                                setGroupValues(e); setGroupValueLable(e);
                                            }}
                                            required={true}
                                        />
                                    </div>
                                    <div className="app-footer">
                                        <button onClick={onClose} className="btn btn-cancel">{t('G_CANCEL')}</button>
                                        <button onClick={() =>{
                                            onCreate(renameElementId,groupvalue,value);
                                            }} className={`${!value ? 'disabled pointer-none' : ''} btn btn-success`} disabled={isLoader === false?false:true}>{isLoader === 'update' ?
                                            <span className="spinner-border spinner-border-sm"></span> : null}{t('G_UPDATE')}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            }
        </Translation>
    );
}
const ScheduleElement = ({ onClose, onCreate, errors, module, start_date, start_time, scheduleElementId }) => {
    const [date, setDate] = useState(start_date);
    const [time, setTime] = useState(start_time ? start_time : '00:00');
    const handleTimeChange = (input, value, validate) => {
        if (value !== '') {
            setTime(value);
        }
    }
    return (
        <Translation>
            {
                t =>
                    <div id="react-confirm-alert">
                        <div className="react-confirm-alert-overlay">
                            <div className="react-confirm-alert">
                                <div className="app-main-popup">
                                    <div className="app-header">
                                        <h4>{t('DOCUMENT_SCHEDULE')}</h4>
                                    </div>
                                    <div className="app-body">
                                        <DateTime fromDate={new Date()} value={date} onChange={e => setDate((e !== undefined && e !== 'Invalid date' && e !== 'cleardate' ? moment(new Date(e)).format('YYYY-MM-DD') : ""))} label="Start date" required={true} />
                                        {errors !== undefined && errors && errors.start_date && <p className="error-message">{errors.start_date}</p>}
                                        <Timepicker
                                            label="Start time"
                                            value={time}
                                            onChange={handleTimeChange}
                                            stateName='start_time'
                                            validateName='start_time_validate'
                                            required={true}
                                        />
                                        {errors !== undefined && errors && errors.start_time && <p className="error-message">{errors.start_time}</p>}
                                    </div>
                                    <div className="app-footer">
                                        <button onClick={onClose} className="btn btn-cancel">{t('G_CANCEL')}</button>
                                        <button
                                            onClick={() => onCreate(date, time, scheduleElementId)}
                                            className={`${!date ? 'disabled pointer-none' : ''} btn btn-success`}>{t('DOCUMENT_UPDATE')}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            }
        </Translation>
    );
}
class Document extends Component {
    _isMounted = false;
    constructor(props) {
        super(props);
        this.state = {
            id: (this.props.match.params.id ? this.props.match.params.id : (this.props.event.defaultDirectory ? this.props.event.defaultDirectory.id : "")),
            module: (this.props.match.params.module ? this.props.match.params.module : (this.props.event.defaultDirectory ? this.props.event.defaultDirectory.alias : "")),
            data: [],
            modulesData: [],
            groupsData: [],
            preLoader: true,
            isLoader: false,
            popupFolder: false,
            popupModule: false,
            renamePopup: false,
            schedulePopup: false,
            start_date: "",
            start_time: "",
            scheduleElementId: "",
            renameElementId: "",
            renameElementName: "",
            moveCopy: false,
            copied: false,
            action: '',
            fileID: 0,
            levelId: "",
            breadCrumb: [0],
            dragBar: false,
            uploading: false,
            uploaded: false,
            errors: {},

            checkedItems: new Map()
        }

        this.removePopup = this.removePopup.bind(this);
        this.handleFolder = this.handleFolder.bind(this);
        this.dragEnter = this.dragEnter.bind(this);
        this.dragLeave = this.dragLeave.bind(this);
        this.handleDrop = this.handleDrop.bind(this);
    }

    componentDidMount() {
        this._isMounted = true;
        this.setState({
            levelId: Number(this.state.id)
        }, () => {
            this.listing();
            this.modulesData();
            this.groupsData();
        });

        //set next previous
        if (this.props.event.modules !== undefined && this.props.event.modules.length > 0) {
            let modules = this.props.event.modules.filter(function (module, i) {
                return ((!in_array(module.alias, ["polls"]) && Number(module.status) === 1) || (in_array(module.alias, ["subregistration"])));
            });

            let index = modules.findIndex(function (module, i) {
                return module.alias === "ddirectory";
            });

            this.setState({
                next: (modules[index + 1] !== undefined && module_routes[modules[index + 1]['alias']] !== undefined ? module_routes[modules[index + 1]['alias']] : "/event/manage/surveys"),
                prev: (modules[index - 1] !== undefined && module_routes[modules[index - 1]['alias']] !== undefined ? module_routes[modules[index - 1]['alias']] : (Number(this.props.event.is_registration) === 1 ? "/event/module/eventsite-module-order" : (Number(this.props.event.is_app) === 1 ? "/event/module/event-module-order" : "/event_site/billing-module/manage-orders")))
            });

        }

        document.body.addEventListener('click', this.removePopup);
        window.addEventListener('dragover', this.dragOver, false)
        window.addEventListener('dragenter', this.dragEnter, false)
        window.addEventListener('dragleave', this.dragLeave, false)
        window.addEventListener('drop', this.handleDrop, false)
    }

    static getDerivedStateFromProps(props, state) {
        if (props.match.params.module && props.match.params.id && state.module !== props.match.params.module) {
            return {
                module: props.match.params.module,
                levelId: Number(props.match.params.id),
                id: props.match.params.id
            };
        }
        // Return null to indicate no change to state.
        return null;
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.module !== this.state.module) {
            this.listing();
            this.modulesData();
            this.groupsData();
        }
    }

    modulesData = () => {
        this.setState({ preLoader: true });
        service.get(`${process.env.REACT_APP_URL}/directory/document/load-module-data/${this.state.module}`)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                modulesData: response.data,
                                preLoader: false
                            });
                        }
                    }
                },
                error => { }
            );
    }
    groupsData = () => {
        this.setState({ preLoader: true });
        service.get(`${process.env.REACT_APP_URL}/directory/document/load-group-data`)
            .then(
            response => {
                if (response.success) {
                    if (this._isMounted) {
                        this.setState({
                            groupsData: response.data,
                            preLoader: false
                        });
                    }
                }
            },
            error => { }
        );
    }

    listing = () => {
        this.setState({ preLoader: true });
        service.post(`${process.env.REACT_APP_URL}/directory/document/listing/${this.state.module}/${this.state.id}`, this.state)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                data: response.data,
                                preLoader: false,
                                checkedItems: new Map()
                            });
                        }
                    }
                },
                error => { }
            );
    }

    componentWillUnmount() {
        document.body.removeEventListener('click', this.removePopup);
        window.removeEventListener('dragover', this.dragEnter);
        window.removeEventListener('dragenter', this.dragOver);
        window.removeEventListener('dragleave', this.dragLeave);
        window.removeEventListener('drop', this.handleDrop);
        this._isMounted = false;
    }

    dragOver = e => {
        e.preventDefault()
        e.stopPropagation()
        this.setState({
            dragBar: true,
            uploaded: false,
        })
    }
    
    dragEnter = e => {
        e.preventDefault()
        e.stopPropagation()
    }

    dragLeave = e => {
        e.preventDefault()
        e.stopPropagation()
        setTimeout(() => {
            if (e.target.className === 'doc-dropzone-check') {
                this.setState({
                    dragBar: false
                });
                document.getElementsByTagName('html')[0].style.pointerEvents = 'auto'
            }
        }, 500);

    }

    handleDrop = e => {
        e.preventDefault()
        e.stopPropagation()
        let dt = e.dataTransfer;
        let files = dt.files;
        this.setState({
            message: "",
            uploading: true,
            uploaded: false,
            dragBar: false,
        }, () => {
            this.handleUploadFiles(files);
        });
    }

    handleMoveCopy = (id, action) => e => {
        e.preventDefault();
        this.setState({
            moveCopy: true,
            fileID: id,
            action: action
        })
    }

    handleUploadFiles = (files) => {
        service.post(`${process.env.REACT_APP_URL}/directory/upload/document/${this.state.module}`, {
            files: [...files],
            parent_id: this.state.levelId,
            directory_id: this.state.levelId,
        }).then(
            response => {
                if (this._isMounted) {
                    if (response.success) {
                        this.setState({
                            uploading: false,
                            uploaded: true
                        }, () => {
                            this.listing();
                        });
                    } else {
                        this.setState({
                            message: (response.errors.files ? response.errors.files : ''),
                            success: false,
                            preLoader: false,
                            errors: response.errors,
                            uploading: false,
                            dragBar: false
                        });
                    }
                }
            },
            error => { }
        );
    }

    removePopup = e => {
        if (e.target.className !== 'btn active') {
            const items = document.querySelectorAll(".parctical-button-panel .btn");
            for (let i = 0; i < items.length; i++) {
                const element = items[i];
                element.classList.remove("active");
            }
        }
    }

    checkExtention = (name) => {
        const img = ['png', 'jpg', 'ico', 'jpeg', 'gif', 'svg', 'bmp']
        const type = name.split('.').pop();
        if (img.includes(type)) {
            return 'Image';
        } else {
            return "Document"
        }
    }

    handleDropdown = e => {
        e.stopPropagation();
        const items = document.querySelectorAll(".parctical-button-panel .btn");
        for (let i = 0; i < items.length; i++) {
            const element = items[i];
            if (element.classList === e.target.classList) {
                e.target.classList.toggle("active");
            } else {
                element.classList.remove("active");
            }
        }
    };

    handlePrev = (value) => e => {
        e.preventDefault();
        var array = this.state.breadCrumb;
        var index = array.indexOf(value);
        array = this.state.breadCrumb.splice(0, index + 1);
        this.setState({
            breadCrumb: array,
            levelId: value,
            checkedItems: new Map()
        })
    }

    handleFolder = (value) => e => {
        e.preventDefault();
        this.setState({
            breadCrumb: [...this.state.breadCrumb, value],
            levelId: value,
            checkedItems: new Map()
        })
    }

    handleCreateFolder = (value,groups) => {
        this.setState({
            isLoader:'create-folder',
        });
        service.post(`${process.env.REACT_APP_URL}/directory/add/document/${this.state.module}`, {
            name: value,
            groups: groups,
            parent_id: this.state.levelId,
        }).then(
            response => {
                if (this._isMounted) {
                    if (response.success) {
                        this.setState({
                            popupFolder: false,
                            isLoader: false,
                        }, () => {
                            this.listing();
                        });
                    } else {
                        this.setState({
                            message: response.message,
                            success: false,
                            preLoader: false,
                            isLoader: false,
                            errors: response.errors,
                        });
                    }
                }
            },
            error => { }
        );
    }

    assignModule = (value, groups,label) => {
        this.setState({
            isLoader:'save-document',
        });
        service.post(`${process.env.REACT_APP_URL}/directory/add/document/${this.state.module}`, {
            name: label,
            agenda_id: (this.state.module === "agendas" ? value : ""),
            speaker_id: (this.state.module === "speakers" ? value : ""),
            sponsor_id: (this.state.module === "sponsors" ? value : ""),
            exhibitor_id: (this.state.module === "exhibitors" ? value : ""),
            parent_id: this.state.id,
            groups: groups,
        }).then(
            response => {
                if (this._isMounted) {
                    if (response.success) {
                        this.setState({
                            popupModule: false,
                            isLoader: false,
                        }, () => {
                            this.listing();
                        });
                    } else {
                        this.setState({
                            message: response.message,
                            success: false,
                            preLoader: false,
                            isLoader: false,
                            errors: response.errors,
                        });
                    }
                }
            },
            error => { }
        );
    }
    handleEditElement = (id, groups,name) => {
        this.setState({
            isLoader:'update',
        });
        service.post(`${process.env.REACT_APP_URL}/directory/update/document/${this.state.module}`, {
            name: name,
            id: id,
            groups: groups,
        }).then(
            response => {
                if (this._isMounted) {
                    if (response.success) {
                        this.setState({
                            editPopupModule: false,
                            isLoader: false,
                        }, () => {
                            this.listing();
                        });
                    } else {
                        this.setState({
                            message: response.message,
                            success: false,
                            isLoader: false,
                            preLoader: false,
                            errors: response.errors,
                        });
                    }
                }
            },
            error => { }
        );
    }

    handleClose = () => {
        this.setState({
            fileID: '',
            action: '',
            moveCopy: false
        })
    }

    handleFile = e => {
        e.preventDefault();
        let files = e.target.files;
        this.setState({
            uploading: true,
            uploaded: false,
            message: "",
        }, () => {
            this.handleUploadFiles(files)
        });
    }

    handleDeleteElement = id => {
        if (id === "selected" && this.state.checkedItems.size > 0) {
            let ids = [];
            this.state.checkedItems.forEach((value, key, map) => {
                if (value === true) {
                    ids.push(key);
                }
            });
            this.deleteRecords(ids);
        } else if (id !== "selected") {
            this.deleteRecords([id]);
        }
    }

    deleteRecords(ids) {
        confirmAlert({
            customUI: ({ onClose }) => {
                return (
                    <Translation>
                        {
                            t =>
                                <div className='app-main-popup'>
                                    <div className="app-header">
                                        <h4>{t('G_DELETE')}</h4>
                                    </div>
                                    <div className="app-body">
                                        <p>{t('EE_ON_DELETE_ALERT_MSG')}</p>
                                    </div>
                                    <div className="app-footer">
                                        <button className="btn btn-cancel" onClick={onClose}>{t('G_CANCEL')}</button>
                                        <button className="btn btn-success"
                                            onClick={() => {
                                                onClose();
                                                service
                                                    .destroy(
                                                        `${process.env.REACT_APP_URL}/directory/destroy/document/${this.state.module}`,
                                                        { ids: ids }
                                                    )
                                                    .then(
                                                        response => {
                                                            if (response.success) {
                                                                this.listing();
                                                            } else {
                                                                this.setState({
                                                                    preLoader: false,
                                                                    message: response.message,
                                                                    success: false
                                                                });
                                                            }
                                                        },
                                                        error => {
                                                        }
                                                    );
                                            }}
                                        >
                                            {t('G_DELETE')}
                                        </button>
                                    </div>
                                </div>
                        }
                    </Translation>
                );
            }
        });
    }

    handleRenameElement = (name, id) => {
        let parts = id.split('-');
        service.post((parts[0] === "file" ? `${process.env.REACT_APP_URL}/directory/rename/document/file/${this.state.module}` : `${process.env.REACT_APP_URL}/directory/update/document/${this.state.module}`), {
            id: parts[1],
            name: name
        }).then(
            response => {
                if (this._isMounted) {
                    if (response.success) {
                        this.setState({
                            renamePopup: false,
                        }, () => {
                            this.listing();
                        });
                    } else {
                        this.setState({
                            message: response.message,
                            success: false,
                            preLoader: false,
                            errors: response.errors,
                        });
                    }
                }
            },
            error => { }
        );
    }

    handleScheduleElement = (start_date, start_time, id) => {
        service.post(`${process.env.REACT_APP_URL}/directory/schedule/document/${this.state.module}`, {
            id: id,
            start_date: start_date,
            start_time: start_time,
        }).then(
            response => {
                if (this._isMounted) {
                    if (response.success) {
                        this.setState({
                            schedulePopup: false,
                        }, () => {
                            this.listing();
                        });
                    } else {
                        this.setState({
                            message: response.message,
                            success: false,
                            preLoader: false,
                            errors: response.errors,
                        });
                    }
                }
            },
            error => { }
        );
    }

    handleCopyMoveElement = (from_id, to_id) => {
        service.post(`${process.env.REACT_APP_URL}/directory/${this.state.action}/document/file/${this.state.module}`, {
            to_id: to_id,
            from_id: from_id,
        }).then(
            response => {
                if (this._isMounted) {
                    if (response.success) {
                        this.setState({
                            moveCopy: false,
                        }, () => {
                            this.listing();
                        });
                    } else {
                        this.setState({
                            message: response.message,
                            success: false,
                            preLoader: false,
                            errors: response.errors,
                        });
                    }
                }
            },
            error => { }
        );
    }

    handleSelectAll = e => {
        const check = e.target.checked;
        const checkitems = document.querySelectorAll(".check-box-list input");
        for (let i = 0; i < checkitems.length; i++) {
            const element = checkitems[i];
            this.setState(prevState => ({
                checkedItems: prevState.checkedItems.set(element.name, check)
            }));
        }
    };

    handleCheckbox = e => {
        const checkitems = document.querySelectorAll(".check-box-list input");
        const selectall = document.getElementById("selectall");
        for (let i = 0; i < checkitems.length; i++) {
            const element = checkitems[i].checked;
            if (element === false) {
                selectall.checked = false;
                break;
            } else {
                selectall.checked = true;
            }
        }
        const item = e.target.name;
        const isChecked = e.target.checked;
        this.setState(prevState => ({
            checkedItems: prevState.checkedItems.set(item, isChecked)
        }));
    };

    render() {
        const levelId = this.state.levelId;

        const selected_rows_length = new Map(
            [...this.state.checkedItems]
                .filter(([k, v]) => v === true)
        ).size;
            console.log(this.props);
        const total_rows = (this.state.data !== undefined && this.state.data.length > 0 ? this.state.data.filter(function (row, i) {
            return Number(row.parent_id) === Number(levelId);
        }).length : 0);

        let module = this.props.event.modules.filter(function (module, i) {
            return in_array(module.alias, ["ddirectory"]);
        });
        let self = this;
        let doucumentModule = this.state.module !== "other" ? this.props.event.modules.filter(function (module, i) {
            return in_array(module.alias, [self.state.module]);
        }) : [{ value: 'Other'}];

        return (
            <Translation>
                {
                    t =>
                        <div className="wrapper-content third-step">
                            {this.state.message &&
                                <AlertMessage
                                    className="alert-warning"
                                    title={t('EE_OCCURRED')}
                                    content={this.state.message}
                                    icon="info"
                                />
                            }
                            {this.state.preLoader && <Loader />}
                            {!this.state.preLoader &&
                                <React.Fragment>
                                    {this.state.uploading && <div className="d-flex align-items-center uploadMessage">
                                        <span className="icon"><i className="material-icons animation">cached</i></span>
                                        <p>{t('DOCUMENT_UPLOADING_FILE')}</p>
                                    </div>}
                                    {this.state.copied && <div className="d-flex align-items-center uploadMessage success">
                                        <span className="icon"><i className="material-icons">content_copy</i></span>
                                        <p>{t('DOCUMENT_COPIED')}</p>
                                        <span onClick={() => this.setState({ copied: false })} className="close"><i className="material-icons">highlight_off</i></span>
                                    </div>}
                                    {this.state.uploaded && <div className="d-flex align-items-center uploadMessage success">
                                        <span className="icon"><i className="material-icons">check</i></span>
                                        <p>{t('DOCUMENT_FILE_UPLOADED_SUCCESS')}</p>
                                        <span onClick={() => this.setState({ uploaded: false })} className="close"><i className="material-icons">highlight_off</i></span>
                                    </div>}
                                    {this.state.dragBar && <div className="doc-dropzone-check" draggable="true" onDragStart={(event) => event.dataTransfer.setData('text/plain', null)} id="doc-dropzone">
                                        <div style={{ pointerEvents: 'none' }} className="iconbox">
                                            <img src={require('img/upload.svg')} alt="" />
                                            <p>{t('DOCUMENT_FILE_DROP_LABEL')}</p>
                                        </div>
                                    </div>}
                                    <header style={{ margin: 0 }} className="new-header clearfix">
                                        <div className="row">
                                            <div className="col-12">
                                                <h1 className="section-title float-left">{(module[0]['value'] !== undefined ? module[0]['value'] : t('DOCUMENT_MAIN_HEADING'))} : { doucumentModule[0]['value'] !== undefined ? doucumentModule[0]['value'] : t(`DOCUMENT_SUB_MODULE_HEADING_${this.state.module.toUpperCase()}`) } </h1>
                                                <div className="new-right-header new-panel-buttons float-right">
                                                    {((in_array(this.state.module, ["agendas", "speakers", "sponsors", "exhibitors"]) && this.state.levelId !== Number(this.state.id)) || this.state.module === "other") && (
                                                        <React.Fragment>
                                                            <OverlayTrigger overlay={<Tooltip>{t('DOCUMENT_MAX_SIZE_GB')}</Tooltip>}>
                                                                <button
                                                                    className="btn_addNew">
                                                                    <label className="d-flex h-100 align-items-center justify-content-center" style={{ margin: 0, cursor: 'pointer' }}><Img style={{ pointerEvents: "none" }} width="20px" src={require('img/ico-addpage-lg.svg')} />
                                                                        <input style={{ position: 'absolute', opacity: 0, visibility: 'hidden' }} type="file" multiple onChange={this.handleFile.bind(this)} accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf" />
                                                                    </label>
                                                                </button>
                                                            </OverlayTrigger>
                                                            <button
                                                                onClick={() => this.setState({ popupFolder: true })}
                                                                className="btn_addNew">
                                                                <Img style={{ pointerEvents: "none" }} width="20px" src={require('img/ico-addfolder-lg.svg')} />
                                                            </button>
                                                        </React.Fragment>
                                                    )}
                                                    {!in_array(this.state.module, ["other"]) && this.state.levelId === Number(this.state.id) && (
                                                        <button
                                                            onClick={() => this.setState({ popupModule: true })}
                                                            className="btn_addNew">
                                                            <Img style={{ pointerEvents: "none" }} width="20px" src={require('img/ico-plus-lg.svg')} />
                                                        </button>
                                                    )}
                                                </div>
                                            </div>

                                            <div className="col-6">
                                                <p>{t(`DOCUMENT_SUB_HEADING_${this.state.module.toUpperCase()}`)} </p>
                                            </div>
                                        </div>
                                    </header>
                                    {this.state.levelId !== 0 && <nav className="doc-breadcrumbs" aria-label="breadcrumb">
                                        <ol style={{ margin: 0 }} className="breadcrumb">
                                            <li onClick={this.handlePrev(Number(this.state.id))} className="breadcrumb-item">Root</li>
                                            {this.state.data.map((items, k) =>
                                            ((this.state.breadCrumb.indexOf(items.id) !== -1) && items.type === 'folder' &&
                                                <li onClick={this.handlePrev(items.id)} key={k} className="breadcrumb-item">{items.name}</li>
                                            ))}
                                        </ol>
                                    </nav>}
                                    <div style={{ height: '100%' }}>
                                        {this.state.data && total_rows > 0 &&
                                            <div className="custom-grid-documents attendee-records-template">
                                                <header className="header-records row d-flex">
                                                    <div className="col-1 d-flex">
                                                        <div className="header-invitations">
                                                            <label>
                                                                <input
                                                                    id="selectall"
                                                                    checked={(selected_rows_length === total_rows ? true : false)}
                                                                    onChange={this.handleSelectAll.bind(this)}
                                                                    type="checkbox"
                                                                    name="selectall"
                                                                />
                                                                <span style={{ height: '21px', paddingLeft: '21px', marginLeft: 0 }}></span>
                                                            </label>
                                                            <div style={{ marginLeft: 0 }} className="parctical-button-panel">
                                                                <div className="dropdown">
                                                                    <button onClick={this.handleDropdown.bind(this)} className="btn">
                                                                        <i className="material-icons">keyboard_arrow_down</i>
                                                                    </button>
                                                                    <div className="dropdown-menu leftAlign">
                                                                        {selected_rows_length > 0 && (
                                                                            <button
                                                                                className="dropdown-item"
                                                                                onClick={() =>
                                                                                    this.handleDeleteElement("selected")
                                                                                }
                                                                            >
                                                                                {t("G_DELETE_SELECTED")}
                                                                            </button>
                                                                        )}
                                                                        <button
                                                                            className="dropdown-item"
                                                                            onClick={() =>
                                                                                this.handleDeleteElement("all")
                                                                            }
                                                                        >
                                                                            {t("G_DELETE_ALL")}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="col-4">
                                                        <strong>{t('DOCUMENT_NAME')}</strong>
                                                    </div>
                                                    <div className="col-3">
                                                        <strong>{t('DOCUMENT_TYPE')}</strong>
                                                    </div>
                                                    <div className="col-3">
                                                        <strong>{t('DOCUMENT_MODIFIED')}</strong>
                                                    </div>
                                                    <div className="col-3 col-extra">
                                                        <strong></strong>
                                                    </div>
                                                    <div className="col-2 text-right">
                                                        <strong>{t('DOCUMENT_ACTIONS')}</strong>
                                                    </div>
                                                </header>
                                                {this.state.data.map((items, k) =>
                                                (items.parent_id === this.state.levelId && <div key={k} className="row d-flex align-items-center">
                                                    <div className="col-1 check-box-list">
                                                        <label className="checkbox-label">
                                                            <input
                                                                type="checkbox"
                                                                name={items.type + "-" + items.id.toString()}
                                                                checked={(this.state.checkedItems.get(
                                                                    items.type + "-" + items.id.toString()
                                                                ))}
                                                                onChange={this.handleCheckbox}
                                                            />
                                                            <em></em>
                                                        </label>
                                                    </div>
                                                    <div className="col-4">
                                                        {items.type === 'folder' && <p className="file-ext" onClick={this.handleFolder(items.id)} data-type="folder">{items.name}</p>}
                                                        {items.type !== 'folder' && <p className="file-ext" data-type={items.path.split('.').pop()}>{items.name}</p>}
                                                    </div>
                                                    <div className="col-3">
                                                        <p>{items.type === "file" ? this.checkExtention(items.name) : 'Folder'}</p>
                                                    </div>
                                                    <div className="col-3">
                                                        <p>{moment(items.updated_date).format('D MMMM, Y')}</p>
                                                        <p style={{ color: '#888' }}>{items.updated_time}</p>
                                                    </div>
                                                    <div className="col-3 col-extra">
                                                        {items.type === 'file' && <div className="d-flex justify-content-center file-btn-panel">
                                                            <a href={items.url} className="btn"><span className="material-icons">get_app</span></a>
                                                            <CopyToClipboard text={items.url}
                                                                onCopy={() => this.setState({ copied: true })}>
                                                                <button className="btn"><span className="material-icons">content_copy</span> Copy URL</button>
                                                            </CopyToClipboard>
                                                        </div>}
                                                    </div>
                                                    <div className="col-2">
                                                        <div className="parctical-button-panel document-edit-panel">
                                                            <div className="dropdown">
                                                                <button onClick={this.handleDropdown.bind(this)} className="btn">
                                                                    <i className="icons"><Img src={require("img/ico-dots.svg")} /></i>
                                                                </button>
                                                                <div className="dropdown-menu">
                                                                    <button className="dropdown-item" onClick={() => this.handleDeleteElement(items.type + "-" + items.id)}>{t('G_DELETE')}</button>
                                                                    <button className="dropdown-item" onClick={() => this.setState({ renamePopup: true, renameElementId: items.type + "-" + items.id, renameElementName: items.name })}>{t('DOCUMENT_RENAME')}</button>
                                                                    <button className="dropdown-item" onClick={() => this.setState({ editPopupModule: true, renameElementId: items.id, renameElementName: items.name,selectedGroups:items.groups })}>{t('DOCUMENT_EDIT')}</button>
                                                                    {items.type !== 'folder' && <React.Fragment>
                                                                        <button onClick={this.handleMoveCopy(items.id, 'move')} className="dropdown-item">{t('DOCUMENT_MOVE_TO')}</button>
                                                                        <button onClick={this.handleMoveCopy(items.id, 'copy')} className="dropdown-item">{t('DOCUMENT_COPY_TO')}</button>
                                                                        <button className="dropdown-item" onClick={() => this.setState({ schedulePopup: true, start_date: items.start_date, start_time: items.start_time, scheduleElementId: items.id })}>{t('DOCUMENT_SCHEDULE')}</button>
                                                                    </React.Fragment>}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>)
                                                )}
                                            </div>
                                        }
                                        {this.state.popupFolder && <CreateFolder onCreate={this.handleCreateFolder} isLoader={this.state.isLoader} onClose={() => this.setState({ popupFolder: false,isLoader:false })} errors={this.state.errors} module={this.state.module}  groups={this.state.groupsData}/>}
                                        {this.state.popupModule && <AssignModule props={this.props} onCreate={this.assignModule} isLoader={this.state.isLoader} groups={this.state.groupsData} onClose={() => this.setState({ popupModule: false,isLoader:false })} modulesData={this.state.modulesData} errors={this.state.errors} module={this.state.module} />}
                                        {this.state.editPopupModule && <EditElement props={this.props} onCreate={this.handleEditElement} isLoader={this.state.isLoader} groups={this.state.groupsData} onClose={() => this.setState({ editPopupModule: false,isLoader:false })} errors={this.state.errors} module={this.state.module} renameElementId={this.state.renameElementId} renameElementName={this.state.renameElementName} selectedGroups={this.state.selectedGroups} />}
                                        {this.state.moveCopy && <MoveCopyContent onCreate={this.handleCopyMoveElement} fileID={this.state.fileID} onClose={this.handleClose.bind(this)} action={this.state.action} data={this.state.data} id={this.state.id} module={this.state.module} />}
                                        {this.state.renamePopup && <RenameElement onCreate={this.handleRenameElement} onClose={() => this.setState({ renamePopup: false })} errors={this.state.errors} module={this.state.module} renameElementId={this.state.renameElementId} renameElementName={this.state.renameElementName} />}
                                        {this.state.schedulePopup && <ScheduleElement onCreate={this.handleScheduleElement} onClose={() => this.setState({ schedulePopup: false })} errors={this.state.errors} module={this.state.module} start_date={this.state.start_date} start_time={this.state.start_time} scheduleElementId={this.state.scheduleElementId} />}
                                    </div>
                                    <div className="bottom-component-panel clearfix">
                                        <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                                            <i className='material-icons'>remove_red_eye</i>
                                            {t('G_PREVIEW')}
                                        </NavLink>
                                        {this.state.prev !== undefined && (
                                            <NavLink className="btn btn-prev-step" to={this.state.prev}><span className="material-icons">
                                                keyboard_backspace</span></NavLink>
                                        )}
                                        {this.state.next !== undefined && (
                                            <NavLink className="btn btn-next-step" to={this.state.next}>{t('G_NEXT')}</NavLink>
                                        )}
                                    </div>
                                </React.Fragment>
                            }
                        </div>
                }
            </Translation>
        )
    }
}

function mapStateToProps(state) {
    const { event } = state;
    return {
        event
    };
}

export default connect(mapStateToProps)(withTranslation()(Document));