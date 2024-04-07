import React, { Component } from 'react';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import { connect } from 'react-redux';
import { service } from 'services/service';
import Loader from '@app/modules/Loader';
import moment from 'moment';
import { Link } from 'react-router-dom';
import { confirmAlert } from 'react-confirm-alert';
import 'react-confirm-alert/src/react-confirm-alert.css';

const in_array = require("in_array");
class Index extends Component {
	_isMounted = false;

	constructor(props) {
		super(props);
		this.state = {
			tasks: {
				'active': { id: 'active' },
			},
			columns: {
				'checkout-column': {
					id: 'checkout-column',
					title: 'check-out',
					label: this.props.event.labels.DESKTOP_APP_LABEL_DRAG_QR_CODE_TO_CHECKOUT,
					taskIds: []
				},
				'default-column': {
					id: 'default-column',
					title: 'default',
					label: 'label',
					taskIds: []
				},
				'checkin-column': {
					id: 'checkin-column',
					title: 'check-in',
					label: this.props.event.labels.DESKTOP_APP_LABEL_DRAG_QR_CODE_TO_CHECKIN,
					taskIds: []
				}
			},

			// Facilitate reordering of the columns
			columnOrder: ['checkout-column', 'default-column', 'checkin-column'],
			defaultCheckIn: 'default-column',
			preLoader: true,
			enableEvent: true,
			enableCheckinWithoutLocatiom: true,
			eventStatusMsg: "",
			status: "",
			history: [],
		};
	}

	componentDidMount() {
		this.loadData();
	}

	loadData() {
		this._isMounted = true;
		this.setState({ preLoader: true });
		service.get(`${process.env.REACT_APP_URL}/${this.props.event.url}/check-in-out`)
			.then(
				response => {
					if (response.success) {
						if (this._isMounted) {
							this.setState({
								status: response.data.status,
								history: response.data.history,
								enableEvent: response.data.enableEvent,
								enableCheckinWithoutLocatiom: response.data.enableCheckinWithoutLocatiom,
								eventStatusMsg: response.data.eventStatusMsg,
								preLoader: false,
								defaultCheckIn: ((response.data.status === "check-in" ? 'checkout-column' : (in_array(response.data.status, ['check-out', 'attended']) ? "checkin-column" : "default-column"))),
							}, () => {
								let columns = this.state.columns;
								if (this.state.defaultCheckIn === "checkout-column") {
									columns['checkout-column']['taskIds'] = ['active'];
								} else if (this.state.defaultCheckIn === "default-column") {
									columns['default-column']['taskIds'] = ['active'];
								} else if (this.state.defaultCheckIn === "checkin-column") {
									columns['checkin-column']['taskIds'] = ['active'];
								}
								this.setState(columns);
							});
						}
					}
				},
				error => { }
			);
	}

	updateCheckin() {
		this._isMounted = true;
		this.setState({ preLoader: true });
		if (this.state.enableEvent) {
			service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/check-in-out/save`, this.state)
				.then(
					response => {
						if (response.success) {
							if (this._isMounted) {
								this.setState({
									history: response.data.history,
									preLoader: false
								}, () => {
									confirmAlert({
										customUI: ({ onClose }) => {
											return (
												<div className='app-popup-wrapper'>
													<div className="app-popup-container">
														<div style={{ backgroundColor: this.props.event.settings.primary_color }} className="app-popup-header">
															{this.props.event.labels.DESKTOP_APP_LABEL_SUCCESS || 'Success'}
														</div>
														<div className="app-popup-pane">
															<div className="gdpr-popup-sec">
																<p>{response.data.message}</p>
															</div>
														</div>
														<div className="app-popup-footer">
															<button
																style={{ backgroundColor: this.props.event.settings.primary_color }}
																className="btn btn-success"
																onClick={() => {
																	onClose();
																	this.props.history.push(`/event/${this.props.event.url}/lobby`);
																}}
															>
																{this.props.event.labels.GENERAL_OK || 'OK'}
															</button>
														</div>
													</div>
												</div>
											);
										},
										onClickOutside: () => {
											this.props.history.push(`/event/${this.props.event.url}/lobby`);
										},
									});
								});
							}
						}
					},
					error => { }
				);
		} else {
			this.setState({ preLoader: false });
			confirmAlert({
				customUI: ({ onClose }) => {
					return (
						<div className='app-popup-wrapper'>
							<div className="app-popup-container">
								<div style={{ backgroundColor: this.props.event.settings.primary_color }} className="app-popup-header">
									{this.props.event.labels.DESKTOP_APP_LABEL_SUCCESS || 'Error'}
								</div>
								<div className="app-popup-pane">
									<div className="gdpr-popup-sec">
										<p>{this.state.eventStatusMsg}</p>
									</div>
								</div>
								<div className="app-popup-footer">
									<button style={{ backgroundColor: this.props.event.settings.primary_color }} className="btn btn-success" onClick={onClose}>{this.props.event.labels.GENERAL_OK || 'OK'}</button>
								</div>
							</div>
						</div>
					);
				}
			});
		}
	}

	componentWillUnmount() {
		this._isMounted = false;
	}

	onDragEnd = result => {
		if (this.state.enableCheckinWithoutLocatiom) {
			const { destination, source, draggableId } = result
			if (!destination) {
				return
			}
			if (destination.droppableId === 'default-column' && source.droppableId !== 'default-column') {
				return
			}
			if (
				destination.droppableId === source.droppableId &&
				destination.index === source.index
			) {
				return
			}

			const start = this.state.columns[source.droppableId]
			const finish = this.state.columns[destination.droppableId]

			if (start === finish) {
				const newTaskIds = Array.from(start.taskIds)
				newTaskIds.splice(source.index, 1)
				newTaskIds.splice(destination.index, 0, draggableId)

				const newColumn = {
					...start,
					taskIds: newTaskIds
				}

				const newState = {
					...this.state,
					columns: {
						...this.state.columns,
						[newColumn.id]: newColumn
					}
				}
				this.setState(newState)
				return
			}

			// Moving from one list to another
			const startTaskIds = Array.from(start.taskIds)
			startTaskIds.splice(source.index, 1);
			const newStart = {
				...start,
				taskIds: startTaskIds
			}

			const finishTaskIds = Array.from(finish.taskIds)
			finishTaskIds.splice(destination.index, 0, draggableId)
			const newFinish = {
				...finish,
				taskIds: finishTaskIds
			}

			const newState = {
				...this.state,
				columns: {
					...this.state.columns,
					[newStart.id]: newStart,
					[newFinish.id]: newFinish
				},
				defaultCheckIn: newFinish.id,
				status: (finish.id === "checkin-column" ? "check-out" : "check-in"),
			}

			this.setState(newState, () => {
				this.updateCheckin();
			});
		}
	}

	render() {
		return (
			<React.Fragment>
				{this.state.preLoader && <Loader fixed="true" />}
				<div id="app-checkinwrapp" className="h-100 w-100">
					<Link to={`/event/${this.props.event.url}/lobby`} className="app-btn-back"><span className="material-icons">west</span> {this.props.event.labels.DESKTOP_APP_BACK}</Link>
					<div className="checkincard">
						<DragDropContext onDragEnd={this.onDragEnd}>
							<div className="row d-flex">
								{this.state.columnOrder.map(columnId => {
									const column = this.state.columns[columnId]
									const tasks = column.taskIds.map(
										taskId => this.state.tasks[taskId]
									)
									return (
										<div key={column.id} className="col-4">
											<div className={column.id !== 'default-column' ? 'app-checkin-card' : 'app-empty-area'}>
												{column.id === 'checkout-column' ? (
													<React.Fragment>
														<h2 className={this.state.defaultCheckIn === column.id ? 'active' : ''}><span>{this.props.event.labels.DESKTOP_APP_LABEL_CHECK_OUT}</span></h2>
														<div className="app-client-info">
															<div className="app-name">{this.props.event.labels.DESKTOP_APP_LABEL_NAME}</div>
															{this.props.auth && this.props.auth.data && (
																<div className="app-title">{this.props.auth.data.user.first_name + ' ' + this.props.auth.data.user.last_name}</div>
															)}
														</div>
													</React.Fragment>
												) : column.id === 'checkin-column' ? (
													<React.Fragment>
														<h2 className={this.state.defaultCheckIn === column.id ? 'active' : ''}><span>{this.props.event.labels.DESKTOP_APP_LABEL_CHECK_IN}</span></h2>
														<div className="app-client-info">
															<div className="app-name">{this.props.event.labels.DESKTOP_APP_LABEL_NAME}</div>
															{this.props.auth && this.props.auth.data && (
																<div className="app-title">{this.props.auth.data.user.first_name + ' ' + this.props.auth.data.user.last_name}</div>
															)}
														</div>
													</React.Fragment>
												) : ''}

												<Droppable droppableId={column.id}>
													{(provided, snapshot) => (
														<div
															className='app-dragqrcodearea'
															ref={provided.innerRef}
															{...provided.droppableProps}
															style={{ borderColor: column.id === 'checkout-column' ? '#C32C2C' : '' }}
														>
															{tasks.map((task, index) => (
																<Draggable draggableId={task.id} key={task.id} task={task} index={index}>
																	{(provided, snapshot) => (
																		<div
																			className="app-dragbar"
																			{...provided.draggableProps}
																			{...provided.dragHandleProps}
																			ref={provided.innerRef}
																		>
																			<img src={require('images/qrcode.png')} alt="" />
																		</div>
																	)}
																</Draggable>
															))}
															{column.id !== 'default-column' && this.state.defaultCheckIn !== column.id && <p style={{ position: 'absolute', margin: 0 }}>{column.label}</p>}
															{provided.placeholder}
														</div>
													)}
												</Droppable>
												{column.id === 'checkout-column' ? (
													<div style={{ background: '#C32C2C' }} className="app-checkinhistory">
														<h3>{this.props.event.labels.DESKTOP_APP_LABEL_CHECK_OUT_HISTORY} </h3>
														<ul>
															{this.state.history.map((row, key) => {
																return (
																	(row.checkout && row.checkout !== "0000-00-00 00:00:00" && (
																		<li key={key}><span className="text-left">{moment(row.checkout).format('DD-MM-YYYY')}</span><span className="text-right">{moment(row.checkout).format('HH:mm')}</span></li>
																	))
																);
															})}
														</ul>
													</div>
												) : column.id === 'checkin-column' ? (
													<div style={{ background: '#459F30' }} className="app-checkinhistory">
														<h3>{this.props.event.labels.DESKTOP_APP_LABEL_CHECK_IN_HISTORY} </h3>
														<ul>
															{this.state.history.map((row, key) => {
																return (
																	(row.checkin && row.checkin !== "0000-00-00 00:00:00" && (
																		<li key={key}><span className="text-left">{moment(row.checkin).format('DD-MM-YYYY')}</span><span className="text-right">{moment(row.checkin).format('HH:mm')}</span></li>
																	))
																);
															})}
														</ul>
													</div>
												) : ''}

											</div>
										</div>
									)
								})}
							</div>
						</DragDropContext>
					</div>
				</div>
			</React.Fragment>
		)
	}
}

function mapStateToProps(state) {
	const { event, auth } = state;
	return {
		event, auth
	};
}

export default connect(mapStateToProps)(Index);