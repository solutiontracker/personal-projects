import * as React from "react";
import { NavLink } from 'react-router-dom';
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";
import Img from 'react-image';
import FormGroup from "@/app/event_site/billing/items/FormGroup";
import FormItem from "@/app/event_site/billing/items/FormItem";
import Child from "@/app/event_site/billing/items/Child";
import Loader from '@/app/forms/Loader';
import { confirmAlert } from 'react-confirm-alert'; // Import
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { Translation } from "react-i18next";
import { connect } from 'react-redux';
import { service } from 'services/service';
import 'sass/billing.scss';

const in_array = require("in_array");

function setToValue(obj, path, sourceIndex, destIndex) {
    path = path.split('.');
    for (let i = 0; i < path.length; i++) {
        obj = obj[path[i]];
        if (i === path.length - 2) {
            const reorderedSubItems = reorder(
                obj.subItems,
                sourceIndex,
                destIndex
            );
            obj.subItems = reorderedSubItems;
            return obj.subItems;
        }
    }
}

// a little function to help us with reordering the result
const reorder = (list, startIndex, endIndex) => {
    const result = Array.from(list);
    const [removed] = result.splice(startIndex, 1);
    result.splice(endIndex, 0, removed);
    return result;
};

const getItemStyle = (isDragging, draggableStyle) => ({
    background: isDragging ? "white" : "white",
    margin: '0px 0',
    // styles we need to apply on draggables
    ...draggableStyle
});

const getListStyle = isDraggingOver => ({
    background: isDraggingOver ? "white" : "white",
    margin: '0px 0',
    width: '100%'
});

class Listing extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            currency: '',
            query: '',
            sort_by: 'sort_order',
            order_by: 'ASC',
            items: [],
            groups: [],
            permissions: [],
            billing_item_type: 0,
            displayPanel: true,
            type: "",
            parent_id: '',
            childEditMode: false,
            editData: undefined,
            stateToggle: false,

            //errors & loading
            preLoader: true,

            typing: false,
            typingTimeout: 0,

            message: false,
            success: true,

            checkedItems: new Map(),

            prev: (this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 1 ? '/event_site/billing-module/fik-setting' : '/event/settings/branding'),
            next: (this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 1 ? '/event_site/billing-module/voucher' : '/event_site/billing-module/manage-orders')
        };

        this.onDragEnd = this.onDragEnd.bind(this);

        this.onSorting = this.onSorting.bind(this);
    }

    componentDidMount() {
        this._isMounted = true;
        this.listing();
        document.body.addEventListener('click', this.removePopup.bind(this));
    }

    componentDidUpdate(prevProps, prevState) {
        const { order_by, sort_by } = this.state;
        if (order_by !== prevState.order_by || sort_by !== prevState.sort_by) {
            this.listing();
        }
    }

    componentWillUnmount() {
        this._isMounted = false;
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

    onDragEnd(result) {
        if (!result.destination) {
            return;
        }
        const sourceIndex = result.source.index;
        const destIndex = result.destination.index;
        if (result.type === "droppableItem") {
            const items = reorder(this.state.items, sourceIndex, destIndex);
            this.setState({ items: items });
            this.updateOrder(items);
        } else {
            let type = result.type;
            let newItems = [...this.state.items];
            const items = setToValue(newItems, type, sourceIndex, destIndex);
            this.setState({ items: newItems });
            this.updateOrder(items);
        }
    }

    updateOrder = (items) => {
        service.put(`${process.env.REACT_APP_URL}/eventsite/billing/items/update-order`, { items: items })
            .then(
                response => {
                    if (response.success) {
                        this.listing(true);
                    }
                },
                error => { }
            );
    }

    listing = (loader = false) => {
        this.setState({ preLoader: (!loader ? true : false) });
        service.post(`${process.env.REACT_APP_URL}/eventsite/billing/items/listing`, this.state)
            .then(
                response => {
                    if (response.success) {
                        if (response.data) {
                            if (this._isMounted) {
                                this.setState({
                                    items: response.data,
                                    groups: response.groups,
                                    permissions: response.permissions,
                                    billing_item_type: response.payment_setting.billing_item_type,
                                    currency: response.currency,
                                    displayPanel: true,
                                    childEditMode: false,
                                    editData: undefined,
                                    type: "",
                                    preLoader: false,
                                    checkedItems: new Map()
                                }, () => {
                                });
                            }
                        }
                    }
                },
                error => { }
            );
    }

    cancel = value => {
        this.setState({
            type: "",
            displayPanel: true,
            parent_id: '',
            childEditMode: false,
            editData: undefined
        })
    }

    handleshowInner = (input, index, parent_id) => e => {
        e.preventDefault();
        this.setState({
            [input]: true,
            displayPanel: false,
            stateToggle: false,
            parent_id: parent_id
        });
    }

    handleEdit = (input, index, item, edit) => e => {
        e.preventDefault();
        if (edit === "show") {
            this.setState({
                type: input,
                displayPanel: false,
                stateToggle: false,
                childEditMode: true,
                editData: item
            });
        }
    }

    handleAdd = (type) => e => {
        e.preventDefault();
        this.setState({
            type: type,
            displayPanel: false,
            stateToggle: false
        })
    }

    handleDropdown = e => {
        e.preventDefault();
        if (e.target.classList.contains('active')) {
            e.target.classList.remove('active');
        } else {
            var query = document.querySelectorAll('.btn_addmore');
            for (var i = 0; i < query.length; ++i) {
                query[i].classList.remove('active');
            }
            e.target.classList.add('active');
        }
    }

    handleToggle = e => {
        e.preventDefault();
        this.setState({
            stateToggle: !this.state.stateToggle
        })
    }

    onFieldChange(event) {
        const self = this;
        if (self.state.typingTimeout) {
            clearTimeout(self.state.typingTimeout);
        }
        self.setState({
            query: event.target.value,
            typing: false,
            typingTimeout: setTimeout(function () {
                self.listing();
            }, 1000)
        });
    }

    updateStatus = (id, status) => e => {
        service.put(`${process.env.REACT_APP_URL}/eventsite/billing/items/update-status/${id}`, { id: id, status: status })
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.listing();
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

    removeItem = (id, action = '') => e => {
        if (action !== "hide") {
            if (id === "selected" && this.state.checkedItems.size > 0) {
                let ids = [];
                this.state.checkedItems.forEach((value, key, map) => {
                    if (value === true) {
                        ids.push(key);
                    }
                });
                this.deleteRecords(id, action, ids);
            } else if (id !== "selected") {
                this.deleteRecords(id, action);
            }
        }
    }

    deleteRecords(id, action, ids = []) {
        confirmAlert({
            customUI: ({ onClose }) => {
                return (
                    <Translation>
                        {
                            t =>
                                <div className='app-main-popup'>
                                    <div className="app-header">
                                        <h4>{(action === "archive" ? t('G_ARCHIVE') : t('G_DELETE'))}</h4>
                                    </div>
                                    <div className="app-body">
                                        <p>{(action === "archive" ? t('EE_ON_ARCHIVE_ALERT_MSG') : t('EE_ON_DELETE_ALERT_MSG'))}</p>
                                    </div>
                                    <div className="app-footer">
                                        <button className="btn btn-cancel" onClick={onClose}>{t('G_CANCEL')}</button>
                                        <button className="btn btn-success"
                                            onClick={() => {
                                                onClose();
                                                service
                                                    .destroy(
                                                        `${process.env.REACT_APP_URL}/eventsite/billing/items/delete/${id}`,
                                                        { ids: ids, action: action }
                                                    )
                                                    .then(
                                                        response => {
                                                            if (response.success) {
                                                                this.listing();
                                                            } else {
                                                                this.setState({
                                                                    preLoader: false,
                                                                    'message': response.message,
                                                                    'success': false
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

    onSorting(event) {
        this.setState({
            order_by: event.target.attributes.getNamedItem('data-order').value,
            sort_by: event.target.attributes.getNamedItem('data-sort').value,
        });
    }

    render() {

        const selected_rows_length = new Map(
            [...this.state.checkedItems]
                .filter(([k, v]) => v === true)
        ).size;

        return (
            <Translation>
                {(t) => (
                    <React.Fragment>
                        {this.state.preLoader && <Loader />}
                        {!this.state.preLoader && (
                            <React.Fragment>
                                <div style={{ height: "100%" }}>
                                    {this.state.displayPanel && (
                                        <div style={{ margin: "0" }} className="new-header clearfix">
                                            {!this.props.largeScreen && (
                                                <div className="row">
                                                    <div className="col-6">
                                                        <h1 className="section-title">{t("BILLING_ITEMS_MAIN_HEADING")}</h1>
                                                        <p>
                                                            {t("BILLING_ITEMS_SUB_HEADING")}
                                                        </p>
                                                    </div>
                                                    {Number(this.state.permissions["add"]) === 1 && (
                                                        <div className="col-6">
                                                            <div className="right-panel-billingitem float-right">
                                                                <button
                                                                    className={`${
                                                                        this.state.stateToggle && "active"
                                                                        } btn_addNew_main`}
                                                                    onClick={this.handleToggle.bind(this)}
                                                                >
                                                                    <span className="icons">
                                                                        <Img src={require("img/ico-plus-lg.svg")} />
                                                                    </span>
                                                                </button>
                                                                <div className="drop_down_panel">
                                                                    <button
                                                                        className="btn_addNew"
                                                                        onClick={this.handleAdd("item")}
                                                                    >
                                                                        {t("BILLING_ITEMS_ADD_ITEMS_LABEL")}
                                                                    </button>
                                                                    <button
                                                                        className="btn_addNew"
                                                                        onClick={this.handleAdd("group")}
                                                                    >
                                                                        {t("BILLING_ITEMS_ADD_GROUP_LABEL")}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    )}
                                                </div>
                                            )}
                                            <div style={{ marginBottom: '10px' }} className="d-flex row align-items-center">
                                                <div className="col-6">
                                                    <input value={this.state.query} name="query" type="text"
                                                        placeholder={t('G_SEARCH')} onChange={this.onFieldChange.bind(this)}
                                                    />
                                                </div>

                                                <div className="col-6">
                                                    <div className="panel-right-table d-flex justify-content-end">
                                                        {!this.props.largeScreen && (
                                                            <button onClick={this.props.handleLargeScreen} className="btn btn-fullscreen">
                                                                <img src={require('img/fullscreen.svg')} alt="" />
                                                            </button>
                                                        )}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    )}
                                    <div>
                                        {this.state.type === "group" && (
                                            <div className="parctical-info-widgets">
                                                <FormGroup
                                                    datamode={
                                                        this.state.childEditMode
                                                            ? t("BILLING_ITEMS_EDIT_GROUP_INNER_LABEL")
                                                            : t("BILLING_ITEMS_ADD_GROUP_INNER_LABEL")
                                                    }
                                                    onCancel={this.cancel}
                                                    save={this.save}
                                                    editData={this.state.editData}
                                                    parent_id={this.state.parent_id}
                                                    listing={this.listing}
                                                    type={this.state.type}
                                                />
                                            </div>
                                        )}
                                        {in_array(this.state.type, ["item", "admin_fee", "event_fee"]) && (
                                            <div className="parctical-info-widgets">
                                                <FormItem
                                                    datamode={
                                                        this.state.childEditMode
                                                            ? t("BILLING_ITEMS_EDIT_ITEM_INNER_LABEL")
                                                            : t("BILLING_ITEMS_ADD_ITEM_INNER_LABEL")
                                                    }
                                                    onCancel={this.cancel}
                                                    save={this.save}
                                                    editData={this.state.editData}
                                                    parent_id={this.state.parent_id}
                                                    groups={this.state.groups}
                                                    listing={this.listing}
                                                    billing_item_type={this.state.billing_item_type}
                                                    type={this.state.type}
                                                />
                                            </div>
                                        )}
                                    </div>
                                    {this.state.items && this.state.displayPanel && (
                                        <div className="wrapper-billing-section">
                                            <div
                                                style={{ paddingBottom: "5px" }}
                                                className="row d-flex header-billing"
                                            >
                                                <div style={{ whiteSpace: 'nowrap' }} className="col-1">
                                                    <label className="checkbox-items">
                                                        <input
                                                            id="selectall"
                                                            checked={(selected_rows_length === this.state.items.length ? true : false)}
                                                            onChange={this.handleSelectAll.bind(this)}
                                                            type="checkbox"
                                                            name="selectall"
                                                        />
                                                        <span></span>
                                                    </label>
                                                    <div style={{ marginLeft: 0 }} className="parctical-button-panel">
                                                        <div className="dropdown">
                                                            <button
                                                                onClick={this.handleDropdown.bind(this)}
                                                                className="btn"
                                                            >
                                                                <i className="material-icons">
                                                                    keyboard_arrow_down
                                                        </i>
                                                            </button>
                                                            <div className="dropdown-menu leftAlign">
                                                                {selected_rows_length > 0 && (
                                                                    <button
                                                                        className="dropdown-item"
                                                                        onClick={this.removeItem("selected")
                                                                        }
                                                                    >
                                                                        {t("G_DELETE_SELECTED")}
                                                                    </button>
                                                                )}
                                                                <button
                                                                    className="dropdown-item"
                                                                    onClick={this.removeItem("all")
                                                                    }
                                                                >
                                                                    {t("G_DELETE_ALL")}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style={{ paddingLeft: "25px" }} className="col-6">
                                                    <strong>{t("BILLING_ITEMS_TABLE_ITEM_LABEL")}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="item_name" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.order_by === "ASC" && this.state.sort_by === "item_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "item_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div className="col-3 text-right">
                                                    {this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 1 ? (
                                                        <React.Fragment>
                                                            <strong>{`${t("BILLING_ITEMS_TABLE_PRICE_LABEL")} (${this.state.currency})`}</strong>
                                                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="price" onClick={this.onSorting} className="material-icons">
                                                                {(this.state.order_by === "ASC" && this.state.sort_by === "price" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "price" ? "keyboard_arrow_up" : "unfold_more"))}
                                                            </i>
                                                        </React.Fragment>
                                                    ) : (
                                                            <strong>{t("BILLING_ITEMS_TABLE_TICKETS_LABEL")}</strong>
                                                        )}
                                                </div>
                                                <div className="col-2"></div>
                                            </div>
                                            <DragDropContext onDragEnd={this.onDragEnd}>
                                                <Droppable droppableId="droppable" type="droppableItem">
                                                    {(provided, snapshot) => (
                                                        <div
                                                            ref={provided.innerRef}
                                                            style={getListStyle(snapshot.isDraggingOver)}
                                                            className="practical-data-wrapper"
                                                        >
                                                            {this.state.items.map((item, index) => (
                                                                <Draggable
                                                                    key={item.id}
                                                                    draggableId={item.id.toString()}
                                                                    index={index}
                                                                >
                                                                    {(provided, snapshot) => (
                                                                        <div
                                                                            ref={provided.innerRef}
                                                                            className={`practical-data-list-wrapp ${this.state.checkedItems.get(item.id.toString()) ? 'check' : ''}`}
                                                                            {...provided.draggableProps}
                                                                            style={getItemStyle(
                                                                                snapshot.isDragging,
                                                                                provided.draggableProps.style
                                                                            )}
                                                                        >
                                                                            <div
                                                                                className={`${
                                                                                    item.type !== "group" &&
                                                                                    "form-item-style"
                                                                                    } wrapper-list-outer row d-flex`}
                                                                            >
                                                                                <div className="col-1 check-box-list">
                                                                                    <label className="checkbox-items">
                                                                                        <input
                                                                                            type="checkbox"
                                                                                            name={item.id.toString()}
                                                                                            disabled={in_array(item.delete, ["delete", "archive"]) ? false : true}
                                                                                            checked={(this.state.checkedItems.get(
                                                                                                item.id.toString()
                                                                                            ))}
                                                                                            onChange={this.handleCheckbox}
                                                                                        />
                                                                                        <span></span>
                                                                                    </label>
                                                                                </div>
                                                                                <div className="col-11">
                                                                                    <div
                                                                                        className={` practical-data-list`}
                                                                                    >
                                                                                        <span
                                                                                            {...provided.dragHandleProps}
                                                                                            className={`${
                                                                                                !this.state.displayPanel &&
                                                                                                "disable"
                                                                                                } handle-drag`}
                                                                                        >
                                                                                            <i className="material-icons">
                                                                                                more_vert more_vert
                                                                                            </i>
                                                                                        </span>
                                                                                        {item.type !== "group" ? (
                                                                                            <React.Fragment>
                                                                                                <div className="form-item-wrapper row d-flex">
                                                                                                    <div className="col-5">
                                                                                                        <h4>{`${item.detail.item_name} (${item.item_number})`}</h4>
                                                                                                        <p dangerouslySetInnerHTML={{ __html: item.detail.description }}></p>
                                                                                                        {item.link_to !== "none" && (
                                                                                                            <p><b>Link to {item.link_to.replace("_", " ")}:</b>{item.detail.link_to_name}</p>
                                                                                                        )}
                                                                                                    </div>
                                                                                                    <div className="col-4 text-right">
                                                                                                        {this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 1 && (
                                                                                                            <h4>{item.priceDisplay}</h4>
                                                                                                        )}
                                                                                                        <p>
                                                                                                            {
                                                                                                                (() => {
                                                                                                                    if (item.remaining_tickets === "Unlimited")
                                                                                                                        return t("BILLING_ITEMS_UNLIMITED");
                                                                                                                    else if (Number(item.remaining_tickets) === 0)
                                                                                                                        return t("BILLING_ITEMS_SOLD_OUT")
                                                                                                                    else
                                                                                                                        return item.remaining_tickets + " " + t('BILLING_ITEMS_LEFT');
                                                                                                                })()
                                                                                                            }
                                                                                                        </p>
                                                                                                    </div>
                                                                                                    <div className="col-3">
                                                                                                        {this.state.displayPanel && (
                                                                                                            <div className="practical-edit-panel">
                                                                                                                {!this.props.largeScreen && (
                                                                                                                    <span className="btn_delete" onClick={this.updateStatus(item.id, (item.status === 1 ? 0 : 1))}><i className="icons"><Img style={{ maxWidth: "18px" }} src={require(`img/ico-feathereye${item.status !== 1 ? '-alt' : ''}.svg`)} /></i></span>
                                                                                                                )}
                                                                                                                {!this.props.largeScreen && (
                                                                                                                    <span
                                                                                                                        onClick={this.handleEdit(
                                                                                                                            item.type,
                                                                                                                            item.id,
                                                                                                                            item,
                                                                                                                            item.edit
                                                                                                                        )}
                                                                                                                        className="btn_edit"
                                                                                                                    >
                                                                                                                        <i className="icons">
                                                                                                                            <Img
                                                                                                                                src={require("img/ico-edit.svg")}
                                                                                                                            />
                                                                                                                        </i>
                                                                                                                    </span>
                                                                                                                )}
                                                                                                                {!this.props.largeScreen && (
                                                                                                                    <span
                                                                                                                        onClick={this.removeItem(
                                                                                                                            item.id,
                                                                                                                            item.delete
                                                                                                                        )}
                                                                                                                        className="btn_delete"
                                                                                                                    >
                                                                                                                        <i className="icons">
                                                                                                                            <Img
                                                                                                                                src={require("img/ico-delete.svg")}
                                                                                                                            />
                                                                                                                        </i>
                                                                                                                    </span>
                                                                                                                )}
                                                                                                            </div>
                                                                                                        )}
                                                                                                    </div>
                                                                                                </div>
                                                                                            </React.Fragment>
                                                                                        ) : (
                                                                                                <React.Fragment>
                                                                                                    {`${item.detail.group_name} ${item.group_type === "single" ? "(Choose one)" : "(Choose any)"}`}
                                                                                                    {this.state.displayPanel && (
                                                                                                        <div className="practical-edit-panel">
                                                                                                            {!this.props.largeScreen && (
                                                                                                                <span className="btn_delete" onClick={this.updateStatus(item.id, (item.status === 1 ? 0 : 1))}><i className="icons"><Img style={{ maxWidth: "18px" }} src={require(`img/ico-feathereye${item.status !== 1 ? '-alt' : ''}.svg`)} /></i></span>
                                                                                                            )}
                                                                                                            {!this.props.largeScreen && (
                                                                                                                <span
                                                                                                                    onClick={this.handleEdit(
                                                                                                                        item.type,
                                                                                                                        item.id,
                                                                                                                        item,
                                                                                                                        item.edit
                                                                                                                    )}
                                                                                                                    className="btn_edit"
                                                                                                                >
                                                                                                                    <i className="icons">
                                                                                                                        <Img
                                                                                                                            src={require("img/ico-edit.svg")}
                                                                                                                        />
                                                                                                                    </i>
                                                                                                                </span>
                                                                                                            )}
                                                                                                            {!this.props.largeScreen && (
                                                                                                                <span
                                                                                                                    onClick={this.removeItem(
                                                                                                                        item.id,
                                                                                                                        item.delete
                                                                                                                    )}
                                                                                                                    className="btn_delete"
                                                                                                                >
                                                                                                                    <i className="icons">
                                                                                                                        <Img
                                                                                                                            src={require("img/ico-delete.svg")}
                                                                                                                        />
                                                                                                                    </i>
                                                                                                                </span>
                                                                                                            )}
                                                                                                        </div>
                                                                                                    )}
                                                                                                </React.Fragment>
                                                                                            )}
                                                                                        {item.group_data &&
                                                                                            item.group_data.length > 0 && (
                                                                                                <div className="inner-droppable">
                                                                                                    <Child
                                                                                                        subItems={item.group_data}
                                                                                                        type={`item-${item.id}`}
                                                                                                        onClick={
                                                                                                            this.handleshowInner
                                                                                                        }
                                                                                                        removeItem={this.removeItem}
                                                                                                        handleEdit={this.handleEdit}
                                                                                                        updateStatus={this.updateStatus}
                                                                                                        form_container={item.id}
                                                                                                        displayPanel={this.state.displayPanel}
                                                                                                        data={`${index}.subItems`}
                                                                                                        largeScreen={this.props.largeScreen}
                                                                                                    />
                                                                                                </div>
                                                                                            )}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            {provided.placeholder}
                                                                        </div>
                                                                    )}
                                                                </Draggable>
                                                            ))}
                                                            {provided.placeholder}
                                                        </div>
                                                    )}
                                                </Droppable>
                                            </DragDropContext>
                                        </div>
                                    )}
                                </div>
                                {this.state.displayPanel && (
                                    <div className="bottom-component-panel clearfix">
                                        {!this.props.largeScreen ? (
                                            <React.Fragment>
                                                <NavLink
                                                    target="_blank"
                                                    className="btn btn-preview float-left"
                                                    to={`/event/preview`}
                                                >
                                                    <i className="material-icons">remove_red_eye</i>
                                                    {t("G_PREVIEW")}
                                                </NavLink>
                                                {this.state.prev !== undefined && (
                                                    <NavLink className="btn btn-prev-step" to={this.state.prev}>
                                                        <span className="material-icons">keyboard_backspace</span>
                                                    </NavLink>
                                                )}
                                                {this.state.next !== undefined && (
                                                    <NavLink className="btn btn-next-step" to={this.state.next}>
                                                        {t("G_NEXT")}
                                                    </NavLink>
                                                )}
                                            </React.Fragment>
                                        ) : (
                                                <button className="btn btn-save-next" onClick={this.props.handleLargeScreen}>{t('G_CLOSE')}
                                                </button>
                                            )}
                                    </div>
                                )}
                            </React.Fragment>
                        )}
                    </React.Fragment>
                )}
            </Translation>
        );
    }
}

function mapStateToProps(state) {
    const { event } = state;
    return {
        event
    };
}

export default connect(mapStateToProps)(Listing);