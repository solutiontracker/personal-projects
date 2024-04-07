import React, { Component } from 'react';
import { NavLink } from 'react-router-dom';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import { RegistrationService } from "services/registration/registration-service";
import Loader from '@/app/forms/Loader';
import { Translation } from "react-i18next";
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Img from "react-image";
import ConfirmationModal from "@/app/forms/ConfirmationModal";

// fake data generator
const getItems = (count, val, offset = 0) =>
    Array.from({ length: count.length }, (v, k) => k).map(k => ({
        id: `item-${k + offset}-${val}`,
        name: count[k].name,
        required: count[k].required,
        alias: count[k].alias,
        non_editable: count[k].non_editable,
        sort_order: count[k].sort_order,
        backend_id: count[k].id
    }));

// a little function to help us with reordering the result
const reorder = (list, startIndex, endIndex) => {
    const result = Array.from(list);
    const [removed] = result.splice(startIndex, 1);
    result.splice(endIndex, 0, removed);
    return result;
};

/**
 * Moves an item from one list to another list.
 */
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


const getItemStyle = (isDragging, draggableStyle) => ({
    // some basic styles to make the items look a bit nicer
    userSelect: 'none',
    // change background colour if dragging
    // styles we need to apply on draggables
    ...draggableStyle,
    ...isDragging
});

const getListStyle = () => ({
    width: '100%'
});

export default class CompanyDetails extends Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            items: [],
            selected: [],

            preLoader: false,
            isLoader: false,
            message: false,
            success: true,

            change: false,
            editIndex: false
        };
    }

    componentDidMount() {
        this._isMounted = true;
        this.listing();
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    listing = (alias = 'company_detail') => {
        this.setState({ preLoader: true });
        RegistrationService.listing(alias).then(
            response => {
                if (response.success) {
                    if (this._isMounted) {
                        let visibleFields = [];
                        let nonVisibleFields = [];
                        response.data.forEach(function (object, index) {
                            if (object.status) {
                                visibleFields.push({ id: object.id, name: object.name, status: true, required: object.mandatory, alias: object.field_alias, non_editable: object.non_editable, sort_order: object.sort_order });
                            } else {
                                nonVisibleFields.push({ id: object.id, name: object.name, status: false, required: object.mandatory, alias: object.field_alias, non_editable: object.non_editable, sort_order: object.sort_order });
                            }
                        });
                        this.setState({
                            items: getItems(visibleFields, 1),
                            selected: getItems(nonVisibleFields, visibleFields.length + 1),
                            preLoader: false
                        });
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
        droppable2: 'selected'
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

            let state = { items: items, change: true };

            if (source.droppableId === 'droppable2') {
                state = { selected: items, change: true };
            }

            this.setState(state);
        } else {
            const result = move(
                this.getList(source.droppableId),
                this.getList(destination.droppableId),
                source,
                destination
            );
            this.setState({
                items: result.droppable,
                selected: result.droppable2,
                change: true
            });
        }
        setTimeout(() => this.sortList(this.state.items, this.state.selected), 500);


    };

    continue = e => {
        e.preventDefault();
        this.props.nextStep();
    }

    back = e => {
        e.preventDefault();
        this.props.prevStep();
    }

    saveData = e => {
        e.preventDefault();
        const type = e.target.getAttribute('data-type');
        this.setState({ isLoader: type });
        const request_data = {};
        request_data.data = this.state.items;
        RegistrationService.update(request_data, 'company_detail').then(
            response => {
                if (response.success) {
                    this.setState({
                        isLoader: false,
                        message: response.message,
                        success: true,
                        change: false
                    });
                    if (type === "save-next") this.props.history.push('/event/registration/sub-registration');
                }
            },
            error => { }
        );
    }

    updateField = (index) => {
        const request_data = {};
        const item = this.state.items.find(function (item, key) {
          return key === Number(index);
        });
        if(item !== undefined) {
          request_data.data = this.state.items;
          request_data.alias = item.alias;
          RegistrationService.update(request_data, 'company_detail').then(
                response => {
                    if (response.success) {
                        this.setState({
                            isLoader: false,
                            message: response.message,
                            success: true,
                            change: false
                        });
                    }
                }, 
                error => { }
            ); 
        }
    }

    removeItem = e => {
        const index = e.target.parentNode.getAttribute('data-index');
        var result = this.state.items;
        var selected = this.state.selected;
        selected.push(result[index])
        result.splice(index, 1);
        this.setState({
            items: result,
            selected: selected,
            change: true
        });
        this.sortList(this.state.items, this.state.selected)
    }

    requiredItem = e => {
        const index = e.target.parentNode.getAttribute('data-index');
        const required = e.target.getAttribute('data-required');
        var result = this.state.items;
        if (required === '1') {
            result[index].required = 0;
        } else {
            result[index].required = 1;
        }
        this.setState({
            items: result,
            change: true
        }, () => {
            this.updateField(index);
        });
        this.sortList(this.state.items, this.state.selected)
    }

    searchList = e => {
        const items = document.querySelectorAll('.draggable-right .input-list-item .inner-wrapper')
        items.forEach(function (obj, index) {
            var text = obj.innerHTML.toLowerCase();
            let cond = text.includes(e.target.value.toLowerCase());
            if (cond) {
                obj.parentNode.style.display = "inline-block";
            } else {
                obj.parentNode.style.display = "none";
            }
        });
    }

    sortList = (dropable, dropable2) => {
        this.setState({
            basicInformation: dropable,
            otherInformation: dropable2,
            change: true
        })
    }

    handleInput = (index, state) => e => {
        e.preventDefault();
        const items = [...this.state[state]];
        items[index].name = e.target.value;
        this.setState({ items: items, change: true })
    }

    handleEdit = (index, state) => e => {
        e.stopPropagation();
        if (this.state.editIndex === index) {
            this.setState({
                editIndex: false,
                activeState: false,
                change: true
            }, () => {
              this.updateField(index);
            });
        } else {
            this.setState({
                editIndex: index,
                activeState: state,
                change: true
            })
        }
    }

    handleDateChange = input => e => {
        if (e !== undefined) {
            var month = e.getMonth() + 1;
            var day = e.getDate();
            var year = e.getFullYear();
            var daydigit = (day.toString().length === 2) ? day : '0' + day;
            var date = month + '/' + daydigit + '/' + year;
            this.setState({
                [input]: date,
                change: true
            });
        } else {
            this.setState({
                [input]: '',
                change: true
            });
        }
    }

    handleChange = input => e => {
        if (e.hex) {
            this.setState({
                [input]: e.hex,
                change: true
            });
        } else {

            if (e.target.type === 'checkbox') {
                this.setState({
                    [input]: e.target.checked,
                    change: true
                });
            } else if (e.target.type === 'file') {
                if (e.target.getAttribute('multiple') !== null) {
                    if (this.state[input].length === 0) {
                        this.setState({
                            [input]: e.target.files,
                            change: true
                        });
                    } else {
                        let fileName = e.target.files;
                        this.setState(prevstate => ({
                            [input]: [...prevstate[input], ...fileName],
                            change: true
                        }))
                    }

                } else {
                    this.setState({
                        [input]: e.target.files,
                        change: true
                    });
                }
            } else if (e.target.getAttribute('data-value') === 'remove-file') {
                if (this.state[input].length > 1) {
                    var array = [...this.state[input]];
                    var index = 1;
                    if (index !== -1) {
                        array.splice(index, 1);
                        this.setState({
                            [input]: array,
                            change: true
                        });
                    }
                } else {
                    this.setState({
                        [input]: [],
                        change: true
                    });
                }
            } else {
                this.setState({
                    [input]: e.target.value,
                    change: true
                });
            }
        }
    };

    render() {

        return (
            <Translation>
                {
                    t =>
                        <div className="wrapper-content third-step">
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
                                    <header style={{ marginBottom: 0 }} className="new-header clearfix">
                                        <div className="row">
                                            <div className="col-6">
                                                <h1 className="section-title">{t('RF_COMPANY_FIELDS')}</h1>
                                                <p>{t('RF_SUB_HEADING')}</p>
                                            </div>
                                        </div>
                                    </header>
                                    <DragDropContext onDragEnd={this.onDragEnd} onDragStart={this.onDragStart.bind(this)}>
                                        <div className="row d-flex">
                                            <div style={{ paddingRight: 0 }} className="col-6">
                                                {/* <h4 className="component-heading">{t('RF_COMPANY_DETAIL')}</h4> */}
                                                <Droppable droppableId="droppable">
                                                    {(provided, snapshot) => (
                                                        <div
                                                            className="draggable-left draggable-component"
                                                            ref={provided.innerRef}
                                                            style={getListStyle(snapshot.isDraggingOver)}>
                                                            {this.state.items.map((item, index) => (
                                                                <Draggable
                                                                    key={item.id}
                                                                    draggableId={item.id}
                                                                    index={index}>
                                                                    {(provided, snapshot) => (
                                                                        <div
                                                                            className="input-list-item"
                                                                            data-index={index}
                                                                            ref={provided.innerRef}
                                                                            {...provided.draggableProps}
                                                                            {...provided.dragHandleProps}
                                                                            style={getItemStyle(
                                                                                snapshot.isDragging,
                                                                                provided.draggableProps.style
                                                                            )}>
                                                                            <span
                                                                                className="list-drag material-icons">more_vert more_vert</span>
                                                                            <div className="inner-wrapper">
                                                                                <input type="text" onClick={(e) => e.stopPropagation()}
                                                                                    onChange={this.handleInput(index, 'items')}
                                                                                    value={item.name}
                                                                                    disabled={this.state.editIndex === index && this.state.activeState === 'items' ? false : true} />
                                                                            </div>
                                                                            <span style={{right: 62}}
                                                                             data-index={index}
                                                                                data-required={item.required}
                                                                                onClick={this.requiredItem.bind(this)}
                                                                                className={item.required ? 'btn-list-required active' : 'btn-list-required'}>
                                                                                {t('G_REQUIRED')} <em></em>
                                                                            </span>
                                                                            <span style={{fontSize: '16px',color: '#0072BC',right: 38}} onClick={this.handleEdit(index, 'items')}
                                                                            className="btn-remove-list material-icons">{this.state.editIndex === index && this.state.activeState === 'items' ? 'check' : 'edit'}</span>
                                                                            <span data-index={index}
                                                                                onClick={this.removeItem.bind(this)}
                                                                                className="btn-remove-list">
                                                                                <i data-index={index} className="icons"><Img src={require('img/ico-bin-alt.svg')} alt="" /></i>
                                                                            </span>
                                                                        </div>
                                                                    )}
                                                                </Draggable>
                                                            ))}
                                                            {provided.placeholder}
                                                        </div>
                                                    )}
                                                </Droppable>
                                            </div>
                                            <div className="col-6">
                                                <div className="selection-item-wrapp">
                                                    <h4 className="component-heading">{t('RF_SELECT_FIELDS')}</h4>
                                                    <div className="search-fields">
                                                        <span className="material-icons">search</span>
                                                        <input onChange={this.searchList.bind(this)} type="text"
                                                            placeholder={t('G_SEARCH')} />
                                                    </div>
                                                    <Droppable droppableId="droppable2">
                                                        {(provided, snapshot) => (
                                                            <div
                                                                className="draggable-right draggable-component"
                                                                ref={provided.innerRef}
                                                                style={getListStyle(snapshot.isDraggingOver)}>
                                                                {this.state.selected.map((item, index) => (
                                                                    <Draggable
                                                                        key={item.id}
                                                                        draggableId={item.id}
                                                                        index={index}>
                                                                        {(provided, snapshot) => (
                                                                            <div
                                                                                className="input-list-item"
                                                                                ref={provided.innerRef}
                                                                                {...provided.draggableProps}
                                                                                {...provided.dragHandleProps}
                                                                                style={getItemStyle(
                                                                                    snapshot.isDragging,
                                                                                    provided.draggableProps.style
                                                                                )}>
                                                                                <span
                                                                                    className="list-drag material-icons">more_vert more_vert</span>
                                                                                <div className="inner-wrapper">
                                                                                    {item.name}
                                                                                </div>
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
                                        </div>
                                    </DragDropContext>

                                    <div className="bottom-component-panel clearfix">
                                        <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                                            <i className='material-icons'>remove_red_eye</i>
                                            {t("G_PREVIEW")}
                                        </NavLink>
                                        <NavLink className="btn btn-prev-step" to={`/event/registration/attendee-type-form`}><span className="material-icons">
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
