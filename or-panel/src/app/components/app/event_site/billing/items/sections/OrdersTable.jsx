import React from "react"
import {service} from "services/service";
import {Translation} from "react-i18next";
import {confirmAlert} from "react-confirm-alert";
import 'sass/billing.scss';
import OptionsMenu from "@/app/event_site/billing/items/sections/OptionsMenu";
import PaginationMenu from "@/app/event_site/billing/items/sections/PaginationMenu";
import moment from "moment";
import Loader from "../../../../forms/Loader";

const flexGrow = {
    flexGrow: 1,
    flexBasis: 0
}

const OrdersTable = (props) => {
    const orders = props.orders
    const url = process.env.REACT_APP_URL

    //handle select change
    function handleChange(e, order, value){
        e.preventDefault()

        if(value === "send_offer"){
            handleSendOffer(order)
        }

        if(value === "delete_order"){
            handleDelete(order)
        }
    }

    //handle sorting
    function handleSort(event){

        const dataOrder = event.target.attributes.getNamedItem('data-order').value
        const sortBy = event.target.attributes.getNamedItem('data-sort').value

        props.setPagination(prev => ({
            ...prev,
            order_by: dataOrder,
            sort_by: sortBy,
        }))

        //refetch data
        props.getWaitingListOrdersData()
    }

    function handleSuccessBox(){
        props.getWaitingListOrdersData()
    }

    //handle send offer
    function handleSuccess() {
        confirmAlert({
            customUI: ({ onClose }) => {
                return (
                    <div style={{minWidth: '315px'}} className='app-main-popup'>
                        <div className="app-body">
                            <div style={{width: 84, height: 84, border: '1px solid #23C22C', borderRadius: '50%', margin: '20px auto 5px'}} className="ebs-icon-area d-flex align-items-center justify-content-center">
                                <i style={{fontSize: '60px', color: '#23C22C'}} className="material-icons">check</i>
                            </div>
                        </div>
                        <div style={{textAlign: 'center',paddingBottom: '20px'}} className="app-footer">
                            <button className="btn m-0 btn-success" onClick={ () => {handleSuccessBox(); onClose()}}>
                                Done
                            </button>
                        </div>
                    </div>
                );
            }
        });
    }
    function handleSendOffer(selectedOrder){

        confirmAlert({
            customUI: ({ onClose }) => {
                return (
                    <Translation>
                        {
                            t =>
                                <div className='app-main-popup'>
                                    <React.Fragment>
                                        <div className="app-header">
                                            <h4>{t('G_SEND_OFFER')}</h4>
                                        </div>
                                        <div className="app-body">
                                            <p>{t('WAITING_LIST_ORDERS_SEND_OFFER_CONFIRMATION')}</p>
                                        </div>
                                        <div className="app-footer">
                                            <button className="btn btn-cancel" onClick={onClose}>{t('G_CANCEL')}</button>
                                            <button className="btn btn-success"
                                                    onClick={async () => {

                                                        props.setIsLoading(true)
                                                        onClose()
                                                        const orderId = selectedOrder.id
                                                        const path = "/billing/waiting-list-orders/send-offer/" + orderId

                                                        try {
                                                            const response = await service.get(url + path)
                                                            if (response) {
                                                                handleSuccess()
                                                                props.setIsLoading(false)
                                                            }
                                                        } catch (error) {
                                                            props.setIsLoading(false)
                                                            console.log(error)
                                                        }
                                                    }}
                                            >
                                                {t('G_YES')}
                                            </button>
                                        </div>
                                    </React.Fragment>
                                </div>
                        }
                    </Translation>
                );
            }
        })
    }

    //handle delete order
    function handleDelete(selectedOrder){

        confirmAlert({
            customUI: ({ onClose }) => {
                return (
                    <Translation>
                        {
                            t =>
                                <div className='app-main-popup'>
                                    <div className="app-header">
                                        <h4>{t('WAITING_LIST_ORDERS_DELETE_ORDER')}</h4>
                                    </div>
                                    <div className="app-body">
                                        <p>{t('WAITING_LIST_ORDERS_DELETE_ORDER_CONFIRMATION')}</p>
                                    </div>
                                    <div className="app-footer">
                                        <button className="btn btn-cancel" onClick={onClose}>{t('G_CANCEL')}</button>
                                        <button className="btn btn-success"
                                                onClick={async () => {

                                                    props.setIsLoading(true)
                                                    onClose()

                                                    const orderId = selectedOrder.id
                                                    const path = "/billing/waiting-list-orders/delete-order/" + orderId

                                                    try {
                                                        const response = await service.get(url + path)
                                                        if (response) {
                                                            props.setIsLoading(false)
                                                            handleSuccess()
                                                        }
                                                        console.log(response)
                                                    } catch (error) {
                                                        props.setIsLoading(false)
                                                        console.log(error)
                                                    }
                                                }}
                                        >
                                            {t('G_YES')}
                                        </button>
                                    </div>
                                </div>
                        }
                    </Translation>
                )
            }
        })
    }

    return(
        !props.isLoading ?
        <Translation>
            {(t) => (
                <React.Fragment>
                    <div className="new-header clearfix" style={{margin: 0}}>
                        <div className="row">
                            <div className="col-6">
                                <h1 className="section-title">Waiting list orders</h1>
                            </div>
                            <div className="col-6">

                            </div>
                        </div>
                        <div style={{ marginBottom: '10px' }} className="d-flex row align-items-center order-section-filter">
                            <div className="col-4">
                                <input value={props.searchQuery} name="query" type="text"
                                       placeholder={t('G_SEARCH')} onChange={(e) => props.setSearchQuery(e.target.value)}
                                />
                            </div>

                            <div className="col-8">
                                <div className="panel-right-table d-flex justify-content-end">
                                    {!props.isLargeScreen && (
                                        <button onClick={props.handleLargeScreen} className="btn btn-fullscreen">
                                            <img src={require('img/fullscreen.svg')} alt="" />
                                        </button>
                                    )}
                                    <PaginationMenu pagination={props.pagination} setPagination={props.setPagination} getWaitingListOrdersData={props.getWaitingListOrdersData} />
                                </div>
                            </div>

                        </div>
                    </div>
                    <div className="wrapper-billing-section voucher-select-items voucher-elements-main">
                        <div style={{ borderTop: '0px' }} className="row d-flex header-billing">
                            <div style={{ ...flexGrow, minWidth: '81px', width: '81px' }} className="grid-1">
                                <strong>{t("BILLING_ORDERS_ORDER_NUMBER")}</strong>
                                <i data-order={props.pagination.order_by === "ASC" ? "DESC" : "ASC"} data-sort="order_number" onClick={handleSort} className="material-icons">
                                    {(props.pagination.order_by === "ASC" && props.pagination.sort_by === "order_number" ? "keyboard_arrow_down" : (props.pagination.order_by === "DESC" && props.pagination.sort_by === "order_number" ? "keyboard_arrow_up" : "unfold_more"))}
                                </i>
                            </div>
                            <div style={{ ...flexGrow, minWidth: '85px', width: '85px' }} className="grid-3">
                                <strong>{t("BILLING_ORDERS_ORDER_DATE")}</strong>
                                <i data-order={props.pagination.order_by === "ASC" ? "DESC" : "ASC"} data-sort="order_date" onClick={handleSort} className="material-icons">
                                    {(props.pagination.order_by === "ASC" && props.pagination.sort_by === "order_date" ? "keyboard_arrow_down" : (props.pagination.order_by === "DESC" && props.pagination.sort_by === "order_date" ? "keyboard_arrow_up" : "unfold_more"))}
                                </i>
                            </div>
                            <div style={{ ...flexGrow, minWidth: '120px', width: '120px' }} className="grid-4">
                                <strong>{t("BILLING_ORDERS_ORDER_NAME")}</strong>
                                <i data-order={props.pagination.order_by === "ASC" ? "DESC" : "ASC"} data-sort="first_name" onClick={handleSort} className="material-icons">
                                    {(props.pagination.order_by === "ASC" && props.pagination.sort_by === "first_name" ? "keyboard_arrow_down" : (props.pagination.order_by === "DESC" && props.pagination.sort_by === "first_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                </i>
                            </div>
                            <div style={{ ...flexGrow, minWidth: '170px', width: '170px' }} className="grid-5">
                                <strong>{t("BILLING_ORDERS_ORDER_EMAIL")}</strong>
                                <i data-order={props.pagination.order_by === "ASC" ? "DESC" : "ASC"} data-sort="email" onClick={handleSort} className="material-icons">
                                    {(props.pagination.order_by === "ASC" && props.pagination.sort_by === "email" ? "keyboard_arrow_down" : (props.pagination.order_by === "DESC" && props.pagination.sort_by === "email" ? "keyboard_arrow_up" : "unfold_more"))}
                                </i>
                            </div>
                            <div style={{ ...flexGrow, minWidth: '110px', width: '110px' }} className="grid-6">
                                <strong>{t("BILLING_ORDERS_ORDER_COMPANY")}</strong>
                                <i data-order={props.pagination.order_by === "ASC" ? "DESC" : "ASC"} data-sort="company_name" onClick={handleSort} className="material-icons">
                                    {(props.pagination.order_by === "ASC" && props.pagination.sort_by === "company_name" ? "keyboard_arrow_down" : (props.pagination.order_by === "DESC" && props.pagination.sort_by === "company_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                </i>
                            </div>
                            <div style={{ ...flexGrow, minWidth: '90px', width: '90px' }} className="grid-7">
                                <strong>{t("WAITING_LIST_ORDER_ORDER_TICKET")}</strong>
                                <i data-order={props.pagination.order_by === "ASC" ? "DESC" : "ASC"} data-sort="order_tickets" onClick={handleSort} className="material-icons">
                                    {(props.pagination.order_by === "ASC" && props.pagination.sort_by === "order_tickets" ? "keyboard_arrow_down" : (props.pagination.order_by === "DESC" && props.pagination.sort_by === "order_tickets" ? "keyboard_arrow_up" : "unfold_more"))}
                                </i>
                            </div>
                            <div style={{ ...flexGrow, minWidth: '120px', width: '120px' }} className="grid-7">
                                <strong>{t("BILLING_ITEMS_PRICE_LABEL")}</strong>
                                <i data-order={props.pagination.order_by === "ASC" ? "DESC" : "ASC"} data-sort="grand_total" onClick={handleSort} className="material-icons">
                                    {(props.pagination.order_by === "ASC" && props.pagination.sort_by === "grand_total" ? "keyboard_arrow_down" : (props.pagination.order_by === "DESC" && props.pagination.sort_by === "grand_total" ? "keyboard_arrow_up" : "unfold_more"))}
                                </i>
                            </div>
                            <div className="grid-7">
                                <strong>{t("WAITING_LIST_ORDER_STATUS_LABEL")}</strong>
                                <i data-order={props.pagination.order_by === "ASC" ? "DESC" : "ASC"} data-sort="order_attendee_status" onClick={handleSort} className="material-icons">
                                    {(props.pagination.order_by === "ASC" && props.pagination.sort_by === "order_attendee_status" ? "keyboard_arrow_down" : (props.pagination.order_by === "DESC" && props.pagination.sort_by === "order_attendee_status" ? "keyboard_arrow_up" : "unfold_more"))}
                                </i>
                            </div>
                            <div className="grid-8" />
                        </div>
                        {orders && orders.map((order) =>
                            <React.Fragment key={order.id}>
                                <div className="row check-box-list">
                                    <div style={{ ...flexGrow, minWidth: '81px', width: '81px' }} className="grid-1">
                                        <p>{order.order_number}</p>
                                    </div>
                                    <div style={{ ...flexGrow, minWidth: '85px', width: '85px' }} className="grid-3">
                                        <p> {moment(new Date(order.order_date)).format('DD/MM/Y')}</p>
                                    </div>
                                    <div style={{ ...flexGrow, minWidth: '120px', width: '120px' }} className="grid-4">
                                        <p>{order.first_name + " " + order.last_name}</p>
                                    </div>
                                    <div style={{ ...flexGrow, minWidth: '170px', width: '170px' }} className="grid-5">
                                        <p title={order.email}>{order.email}</p>
                                    </div>
                                    <div style={{ ...flexGrow, minWidth: '110px', width: '110px' }} className="grid-6">
                                        <p>{order.company_name}</p>
                                    </div>
                                    <div style={{ ...flexGrow, minWidth: '90px', width: '90px' }} className="grid-7">
                                        <p>{order.order_tickets}</p>
                                    </div>
                                    <div style={{ ...flexGrow, minWidth: '120px', width: '120px' }} className="grid-7">
                                        <p>{order.grand_total}</p>
                                    </div>
                                    <div className="grid-7">
                                        <p>{order.order_attendee_status}</p>
                                    </div>
                                    <div className="grid-8">
                                        <OptionsMenu handleChange={handleChange} order={order} />
                                    </div>
                                </div>
                            </React.Fragment>)}
                    </div>
                </React.Fragment>
            )}
        </Translation> : <Loader />
    )
}

export default OrdersTable