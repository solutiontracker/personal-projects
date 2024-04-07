import React, { useEffect, useState } from "react";
import OrdersTable from "@/app/event_site/billing/items/sections/OrdersTable";
import WaitingListCounters from "@/app/event_site/billing/items/sections/WaitingListCounters";
import useDebounce from "@/app/event_site/billing/items/sections/hooks/useDebounce";
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import { Translation } from "react-i18next";
import { connect } from "react-redux";
import { NavLink } from "react-router-dom";
import in_array from "in_array";

const WaitingListOrders = (props) =>
{

	const url = process.env.REACT_APP_URL
	const path = "/billing/waiting-list-orders"
	const [orders, setOrders] = useState([])
	const [state, setState] = useState({})
	const initialState = {
		remainingTickets: 0,
		pendingTickets: 0,
		sentOffers: 0,
		confirmedAttendees: 0,
		notInterested: 0
	}
	const module_routes = {
		"attendees": "/event/module/attendees",
		"agendas": "/event/module/programs",
		"speakers": "/event/module/speakers",
		"infobooth": "/event/module/practical-information",
		"additional_info": "/event/module/additional-information",
		"general_info": "/event/module/general-information",
		"maps": "/event/module/map",
		"subregistration": "/event/module/sub-registration",
		"ddirectory": "/event/module/documents"
	}
	const [eventTicketsCounters, setEventTicketsCounters] = useState(initialState)
	const [isLargeScreen, setIsLargeScreen] = useState(false)
	const [searchQuery, setSearchQuery] = useState("")
	const [debouncedSearchQuery] = useDebounce(searchQuery, 500)
	const [isLoading, setIsLoading] = useState(false)
	const [displayPanel, setDisplayPanel] = useState(true)
	const [nextPreviousState, setNextPreviousState] = useState({
		next: "",
		prev: ""
	})
	const [pagination, setPagination] = useState({
		limit: 10,
		total: '',
		from: 0,
		to: 0,
		activePage: 1,
		order_by: 'ASC',
		sort_by: 'order_date'
	})

	useEffect(() =>
	{
		//function calls
		getWaitingListOrdersData().catch(error => console.log(error))
		handleNextPrevButtons()

	}, [debouncedSearchQuery])

	//make api call to fetch the data for the
	//waiting list orders/attendees
	async function getWaitingListOrdersData()
	{
		const currentPage = pagination.activePage ? pagination.activePage : 1
		setIsLoading(true)
		try
		{
			const response = await service.post(url + path + "/" + currentPage, {
				query: debouncedSearchQuery,
				...pagination
			})

			if (response)
			{
				console.log(response)
				setEventTicketsCounters({
					remainingTickets: response.remaining_tickets,
					pendingTickets: response.pending_tickets,
					sentOffers: response.offer_letters,
					confirmedAttendees: response.confirmed_attendees,
					notInterested: response.not_interested
				})

				setOrders(response.waitingListOrders.data)
				setState(response)
				setIsLoading(false)

				setPagination(prevState => ({
					...prevState,
					activePage: response.waitingListOrders.current_page,
					total: response.waitingListOrders.total,
					from: response.waitingListOrders.from,
					to: response.waitingListOrders.to,
				}))

			}
		} catch (error)
		{
			setIsLoading(false)
			console.log(error)
		}
	}

	//handle next and previous buttons
	function handleNextPrevButtons()
	{

		//set next previous
		if (props.event.modules !== undefined && props.event.modules.length > 0 && Number(props.event.is_registration) === 0 && Number(props.event.is_app) === 0)
		{
			let modules = props.event.modules.filter(function (module, i)
			{
				return ((!in_array(module.alias, ["polls"]) && Number(module.status) === 1) || (in_array(module.alias, ["subregistration"])))
			})

			setNextPreviousState({
				next: (modules[0] !== undefined && module_routes[modules[0]['alias']] !== undefined ? module_routes[modules[0]['alias']] : "/event/manage/surveys"),
			})
		}

		setNextPreviousState({
			prev: "/event_site/billing-module/manage-orders",
			next: (Number(props.event.is_registration) === 1 ? "/event/registration/basic-detail-form" : (Number(props.event.is_app) === 1 ? "/event/module/event-module-order" : "/event/module/eventsite-module-order"))
		})
	}

	//toggle large screen
	function handleLargeScreen()
	{
		setIsLargeScreen(prevState => !prevState)
	}

	//render content
	function showContent(t)
	{
		return (
				<React.Fragment>
					<div style={{ height: "100%" }}>
							<WaitingListCounters remainingTickets={eventTicketsCounters.remainingTickets}
								pendingTickets={eventTicketsCounters.pendingTickets}
								sentOffers={eventTicketsCounters.sentOffers}
								confirmedAttendees={eventTicketsCounters.confirmedAttendees}
								notInterested={eventTicketsCounters.notInterested}
								orders={orders} setOrders={setOrders} state={state} />
							{!isLoading ?
									<OrdersTable orders={orders}
										isLargeScreen={isLargeScreen}
										handleLargeScreen={handleLargeScreen}
										setSearchQuery={setSearchQuery}
										searchQuery={searchQuery} getWaitingListOrdersData={getWaitingListOrdersData}
										pagination={pagination} setPagination={setPagination} isLoading={isLoading} setIsLoading={setIsLoading} />
									:
									<Loader />
							}
					</div>
					{showBottomPanel(t)}
				</React.Fragment>
		)
	}

	function showBottomPanel(t)
	{
		return (
			<React.Fragment>
				{
					displayPanel && (
						<div className="bottom-component-panel clearfix">
							{!isLargeScreen ? (
								<React.Fragment>
									<NavLink
										target="_blank"
										className="btn btn-preview float-left"
										to={`/event/preview`}
									>
										<i className="material-icons">remove_red_eye</i>
										{t("G_PREVIEW")}
									</NavLink>
									{nextPreviousState.prev !== undefined && (
										<NavLink className="btn btn-prev-step" to={nextPreviousState.prev}>
											<span className="material-icons">keyboard_backspace</span>
										</NavLink>
									)}
									{nextPreviousState.next !== undefined && (
										<NavLink className="btn btn-next-step" to={nextPreviousState.next}>
											{t("G_NEXT")}
										</NavLink>
									)}
								</React.Fragment>
							) : (
								<button className="btn btn-save-next" onClick={handleLargeScreen}>{t('G_CLOSE')}
								</button>
							)}
						</div>
					)
				}
			</React.Fragment>
		)
	}

	return (
		<React.Fragment>
			<div className="wrapper-content third-step">
				<Translation>
					{t => (
						<React.Fragment>
							{isLargeScreen === true ?
								<div className="wrapper-import-file-wrapper">
									<div className="wrapper-import-file inline-popup-records">
										<div style={{ height: '100%', display: 'flex', flexDirection: 'column' }}>
											<div className="top-popuparea">
												{showContent(t)}
											</div>
										</div>
									</div>
								</div>
								:
								showContent(t)
							}
						</React.Fragment>
						)
					}
				</Translation>
			</div>
		</React.Fragment>
	)
}

//state is redux state
function mapStateToProps(state)
{
	const { event, update } = state
	return { event, update }
}

export default connect(mapStateToProps)(WaitingListOrders)