import * as React from 'react';
import { NavLink } from 'react-router-dom';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Img from "react-image";
import { Translation } from "react-i18next";
import { connect } from 'react-redux';
import { GeneralAction } from 'actions/general-action';
import { EventAction } from 'actions/event/event-action';
import ConfirmationModal from "@/app/forms/ConfirmationModal";

const in_array = require("in_array");

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/module/sub-registration", "ddirectory": "/event/module/documents" };

// a little function to help us with reordering the result
const reorder = (list, startIndex, endIndex) => {
    const result = Array.from(list);
    const [removed] = result.splice(startIndex, 1);
    result.splice(endIndex, 0, removed);
    return result;
};

const getItemStyle = (isDragging, draggableStyle) => ({
    // some basic styles to make the items look a bit nicer
    userSelect: 'none',
    // change background colour if dragging
    // styles we need to apply on draggables
    ...draggableStyle,
    ...isDragging
});

const move = (source, destination, droppableSource, droppableDestination) => {
    const sourceClone = Array.from(source);
    const destClone = Array.from(destination);
    const [removed] = sourceClone.splice(droppableSource.index, 1);

    destClone.splice(droppableDestination.index, 0, removed);

    const result = {};
    result[droppableSource.droppableId] = sourceClone;
    result[droppableDestination.droppableId] = destClone;

    return result;
};

const getListStyle = () => ({
    width: '100%'
});

class EventSiteModuleOrder extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            menus: [],
            event_id: this.props.event.id,
            editIndex: false,
            activeState: false,

            //loading & message
            preLoader: false,
            message: "",

            change: false, 

            next: "/event/manage/surveys",
            prev: (Number(this.props.event.is_app) === 1 ? "/event/module/event-module-order" : (Number(this.props.event.is_registration) === 1 ? "/event/registration/tos" : "/event_site/billing-module/manage-orders")),
        };
    }

    componentDidMount() {
        this._isMounted = true;
        this.menus();

        //set next previous
        if (this.props.event.modules !== undefined && this.props.event.modules.length > 0) {
            let modules = this.props.event.modules.filter(function (module, i) {
                return ((!in_array(module.alias, ["polls"]) && Number(module.status) === 1) || (in_array(module.alias, ["subregistration"])));
            });

            this.setState({
                next: (modules[0] !== undefined && module_routes[modules[0]['alias']] !== undefined ? module_routes[modules[0]['alias']] : "/event/manage/surveys"),
            });

        }
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    menus = () => {
        this.setState({ preLoader: true });
        service.post(`${process.env.REACT_APP_URL}/eventsite-settings/eventSiteTopMenus`, this.state)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            if (response.data) {
                                this.setState({
                                    menus: response.data.menus,
                                    preLoader: false
                                });
                            } else {
                                this.setState({
                                    preLoader: false
                                });
                            }
                        }
                    }
                },
                error => { }
            );
    }

    /**
     * A semi-generic way to handle multiple lists. Matches
     * the IDs of the droppable container to the names of the
     * source arrays stored in the state.
     */
    id2List = {
        droppable: 'menus',
    };

    getList = id => this.state[this.id2List[id]];

    onDragStart = result => {
        this.setState({
            editIndex: false,
            activeState: false,
            change: true
        })
    }

    onDragEnd = result => {
        const { source, destination } = result;
        // dropped outside the list
        if (!destination) {
            return;
        }
        if (source.droppableId === destination.droppableId) {
            const items = reorder(
                this.getList(source.droppableId),
                source.index,
                destination.index
            );
            this.setState({
                menus: items,
                change: true
            });
        } else {
            const result = move(
                this.getList(source.droppableId),
                this.getList(destination.droppableId),
                source,
                destination
            );
            this.setState({
                menus: result.droppable,
                change: true
            });
        }
    };

    handleSelect = (index, state) => e => {
        e.preventDefault();
        const items = [...this.state[state]];
        items[index].status = items[index].status === 1 ? 0 : 1
        this.setState({
            items: items,
            change: true
        });
    }

    handleEdit = (index, state) => e => {
        e.stopPropagation();
        if (this.state.editIndex === index) {
            this.setState({
                editIndex: false,
                activeState: false,
                change: true
            })
        } else {
            this.setState({
                editIndex: index,
                activeState: state,
                change: true
            })
        }
    }

    handleInput = (index, state) => e => {
        e.preventDefault();
        const items = [...this.state[state]];
        items[index].value = e.target.value;
        this.setState({
            items: items,
            change: true
        });
    }

    saveData = (e) => {
        e.preventDefault();
        const type = e.target.getAttribute('data-type');
        this.save(type);
    };

    save(type) {
        this.setState({ isLoader: type });
        service.put(`${process.env.REACT_APP_URL}/eventsite-settings/eventSiteTopMenus`, this.state)
            .then(
                response => {
                    if (this._isMounted) {
                        if (response.success) {
                            this.setState({
                                message: response.message,
                                success: true,
                                isLoader: false,
                                change: false,
                                errors: {}
                            });

                            this.props.event.module_permissions = response.data.module_permissions;
                            this.props.event.modules = response.data.modules;
                            this.props.dispatch(EventAction.eventInfo(this.props.event));

                            if (type === "save-next") {
                                this.props.history.push(this.state.next);
                            }

                            this.props.dispatch(GeneralAction.redirect(!this.props.redirect));
                        } else {
                            this.setState({
                                message: response.message,
                                success: false,
                                isLoader: false,
                                errors: response.errors
                            });
                        }
                    }
                },
                error => { }
            );
    }

    back = e => {
        e.preventDefault();
        this.props.history.push(this.state.prev);
    }

    render() {

        return (
            <Translation>
                {
                    t =>
                        <div className="wrapper-content third-step main-landing-page">
                            <ConfirmationModal update={this.state.change} />
                            {this.state.preLoader && <Loader />}
                            {!this.state.preLoader && (
                                <React.Fragment>
                                    {this.state.message &&
                                        <AlertMessage
                                            className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                                            title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                                            content={this.state.message}
                                            icon={this.state.success ? "check" : "info"}
                                        />
                                    }
                                    <h1 className="section-title">{t('ED_CUSTOMIZE_WEBSITE_MENUS')}</h1>
                                    <div className="row d-flex">
                                        <div className="col-6">
                                            <div data-count="3" className="customize-menu counter-wrapp">
                                                <p>{t('ED_CUSTOMIZE_WEBSITE_MENUS_DETAIL')}</p>
                                                {/* <h4>{t('ED_PRIMARY_MENUS')} <em className="app-tooltip"><i className="material-icons">info</i>
                                                    <div className="app-tooltipwrapper">{t('ED_CUSTOMIZE_MENUS_INFO')}</div>
                                                </em>
                                                </h4> */}
                                            </div>
                                            <div className="">
                                                <div className="row">
                                                    <DragDropContext onDragEnd={this.onDragEnd}
                                                        onDragStart={this.onDragStart.bind(this)}>
                                                        {this.state.menus.length > 0 && (
                                                            <div className="col-12">
                                                                <Droppable droppableId="droppable">
                                                                    {(provided, snapshot) => (
                                                                        <div
                                                                            className="draggable-left customize-page new-style"
                                                                            ref={provided.innerRef}
                                                                            style={getListStyle(snapshot.isDraggingOver)}>
                                                                            {this.state.menus.length && this.state.menus.map((item, index) => (
                                                                                <React.Fragment key={index}>
                                                                                    {!in_array(item.alias, ["photos", "videos", "sponsors", "exhibitors", "gallery"]) && (
                                                                                        <Draggable
                                                                                            key={`item-${item.id}`}
                                                                                            draggableId={`item-${item.id}`}
                                                                                            index={index}>
                                                                                            {(provided, snapshot) => (
                                                                                                <div
                                                                                                    onClick={this.handleSelect(index, 'menus')}
                                                                                                    className={item.status === 1 ? "input-list-item active" : "input-list-item"}
                                                                                                    data-index={index}
                                                                                                    ref={provided.innerRef}
                                                                                                    {...provided.draggableProps}
                                                                                                    style={getItemStyle(
                                                                                                        snapshot.isDragging,
                                                                                                        provided.draggableProps.style
                                                                                                    )}>
                                                                                                    {item.status === 1 && <span className="ischecked"><Img src={require("img/icon-close.svg")} /></span>}
                                                                                                    <span
                                                                                                        {...provided.dragHandleProps}
                                                                                                        className="list-drag material-icons">more_vert more_vert</span>
                                                                                                    <div className="inner-wrapper">
                                                                                                        <input type="text" onClick={(e) => e.stopPropagation()}
                                                                                                            onChange={this.handleInput(index, 'menus')}
                                                                                                            value={item.value}
                                                                                                            disabled={this.state.editIndex === index && this.state.activeState === 'menus' ? false : true} />
                                                                                                    </div>
                                                                                                    <span onClick={this.handleEdit(index, 'menus')}
                                                                                                        className="list-edit material-icons">{this.state.editIndex === index && this.state.activeState === 'menus' ? 'check' : 'edit'}</span>
                                                                                                </div>
                                                                                            )}
                                                                                        </Draggable>
                                                                                    )}
                                                                                </React.Fragment>
                                                                            ))}

                                                                            {provided.placeholder}
                                                                        </div>
                                                                    )}
                                                                </Droppable>
                                                            </div>
                                                        )}
                                                    </DragDropContext>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="bottom-component-panel clearfix">
                                        <button id="btn-prev-step" className="btn btn-prev-step" onClick={this.back}><span className="material-icons">
                                            keyboard_backspace</span></button>
                                        <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                                            <i className='material-icons'>remove_red_eye</i>
                                            {t('G_PREVIEW')}
                                        </NavLink>
                                        <button data-type="save" disabled={this.state.isLoader ? true : false} className="btn btn btn-save" onClick={this.saveData}>{this.state.isLoader === "save" ?
                                            <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE')}
                                        </button>
                                        <button data-type="save-next" disabled={this.state.isLoader ? true : false} className="btn btn-save-next" onClick={this.saveData}>{this.state.isLoader === "save-next" ?
                                            <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE_NEXT')}
                                        </button>
                                    </div>
                                </React.Fragment>
                            )}
                        </div>
                }
            </Translation>
        );
    }
}

function mapStateToProps(state) {
    const { event, redirect } = state;
    return {
        event, redirect
    };
}

export default connect(mapStateToProps)(EventSiteModuleOrder);