import React, { Component } from 'react';
import { NavLink } from 'react-router-dom';
import Img from "react-image";
import { ReactSVG } from 'react-svg';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import { RegistrationService } from "services/registration/registration-service";
import Loader from '@/app/forms/Loader';
import { Translation } from "react-i18next";
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import FormWidget from '@/app/registration-form/forms/FormWidget';
import ConfirmationModal from "@/app/forms/ConfirmationModal";

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

export default class AttendeeType extends Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            status: 0,
            items: [],

            preLoader: false,
            isLoader: false,
            message: false,
            success: true,

            displayElement: false,

            change: false
        };
    }

    componentDidMount() {
        this._isMounted = true;
        this.listing();
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.status !== this.state.status || prevState.items !== this.state.items) {
            RegistrationService.update(this.state, 'attendee_type_head')
                .then(
                    response => {
                        if (response.success) {
                            this.setState({
                                success: true
                            });
                        }
                    },
                    error => { }
                );
        }
    }

    listing = (alias = 'attendee_type_head', type = "save") => {
        this.setState({ preLoader: true });
        RegistrationService.listing(alias).then(
            response => {
                if (response.success) {
                    if (this._isMounted) {
                        if (response.data) {
                            this.setState({
                                items: response.attendee_types,
                                status: (response.data[0] ? response.data[0]['status'] : 0),
                                preLoader: false,
                                displayElement: (type === "save-new" ? true : false)
                            });
                        } else {
                            this.setState({
                                preLoader: false,
                                displayElement: false
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
        droppable: 'items',
    };

    getList = id => this.state[this.id2List[id]];

    onDragStart = result => {
        this.setState({
            editIndex: false,
            activeState: false
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
            this.setState({ items: items });
        } else {
            const result = move(
                this.getList(source.droppableId),
                this.getList(destination.droppableId),
                source,
                destination
            );
            this.setState({
                items: result.droppable
            });
        }
    };

    handleSelect = (index, state) => e => {
        e.preventDefault();
        const items = [...this.state[state]];
        items[index].status = items[index].status === 1 ? 0 : 1;
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
        items[index].attendee_type = e.target.value;
        this.setState({ items: items, change: true })
    }

    saveData = e => {
        e.preventDefault();
        const type = e.target.getAttribute('data-type');
        this.setState({ isLoader: type });
        RegistrationService.update(this.state, 'attendee_type_head')
            .then(
                response => {
                    if (response.success) {
                        this.setState({
                            'message': response.message,
                            'success': true,
                            isLoader: false,
                            change: false,
                            errors: {}
                        });
                        if (type === "save-next") this.props.history.push('/event/registration/company-detail-form');
                    } else {
                        this.setState({
                            'message': response.message,
                            'success': false,
                            'isLoader': false,
                            'errors': response.errors
                        });
                    }
                },
                error => { }
            );
    }

    updateFlag = input => e => {
        this.setState({
            [input]: this.state[input] === 1 ? 0 : 1
        });
    };

    handleCancel = () => {
        this.setState({
            displayElement: false,
            change: true
        });
    }

    handleAddElement = () => {
        this.setState({
            displayElement: true,
            change: true
        });
    }

    render() {
        return (
            <Translation>
                {
                    t =>
                        <div className="wrapper-content main-landing-page third-step">
                            <ConfirmationModal update={this.state.change} />
                            {this.state.preLoader &&
                                <Loader />
                            }
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
                                    <div style={{ height: '100%' }}>
                                      <header style={{marginBottom: 0}} className="new-header clearfix">
                                        <div className="row d-flex">
                                          <div className="col-6">
                                            <h1 className="section-title">
                                              {t('M_ATTENDEE_TYPES')}
                                              <label className="custom-checkbox-toggle"><input
                                                onChange={this.updateFlag('status')}
                                                type="checkbox" defaultChecked={this.state.status} /><span></span>
                                              </label>
                                            </h1>
                                            <p>{t('RF_CUSTOMIZE_ATTENDEE_TYPE_SORTING_INFO')}</p>
                                          </div>
                                          <div className="col-6">
                                          <div className="new-right-header new-panel-buttons float-right">
                                            {!this.state.displayElement && (
                                              <button onClick={this.handleAddElement} className="btn_addNew">
                                                <ReactSVG wrapper="span" className="icons" src={require('img/ico-plus-lg.svg')} />
                                              </button>
                                            )}
                                          </div>
                                          </div>
                                        </div>
                                      </header>
                                        {this.state.displayElement ? (
                                          <FormWidget listing={this.listing} datacancel={this.handleCancel} />
                                        ) : ''}
                                        <DragDropContext onDragEnd={this.onDragEnd}
                                            onDragStart={this.onDragStart.bind(this)}>
                                            {this.state.items.length > 0 && (
                                                <div className="row d-flex">
                                                    <div className="col-6">
                                                        <Droppable droppableId="droppable">
                                                            {(provided, snapshot) => (
                                                                <div
                                                                    className={`${!this.state.status && 'disabled-box'} draggable-left customize-page new-style`}
                                                                    ref={provided.innerRef}
                                                                    style={getListStyle(snapshot.isDraggingOver)}>
                                                                    {this.state.items.length && this.state.items.map((item, index) => (
                                                                        <Draggable
                                                                            key={`item-${item.id}`}
                                                                            draggableId={`item-${item.id}`}
                                                                            index={index}>
                                                                            {(provided, snapshot) => (
                                                                                <div
                                                                                    onClick={this.handleSelect(index, 'items')}
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
                                                                                            onChange={this.handleInput(index, 'items')}
                                                                                            value={item.attendee_type}
                                                                                            disabled={this.state.editIndex === index && this.state.activeState === 'items' ? false : true} />
                                                                                    </div>
                                                                                    <span onClick={this.handleEdit(index, 'items')}
                                                                                        className="list-edit material-icons">{this.state.editIndex === index && this.state.activeState === 'items' ? 'check' : 'edit'}</span>
                                                                                </div>
                                                                            )}
                                                                        </Draggable>
                                                                    ))}
                                                                    {provided.placeholder}
                                                                </div>
                                                            )}
                                                        </Droppable>
                                                    </div>
                                                </div>
                                            )}
                                        </DragDropContext>
                                    </div>
                                    <div className="bottom-component-panel clearfix">
                                        <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                                            <i className='material-icons'>remove_red_eye</i>
                                            {t('G_PREVIEW')}
                                        </NavLink>
                                        <NavLink className="btn btn-prev-step" to={`/event/registration/basic-detail-form`}><span className="material-icons">
                                            keyboard_backspace</span></NavLink>
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
