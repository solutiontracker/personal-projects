import React, {useState} from "react";

const BoxLayoutForTicketInfo = ({count, text}) => {
    return (
        <div className="ebs-counters-box">
            <div className="ebs-box-wrapp">
                <h4 className="text-center">
                    {count || 0}
                </h4>
                <p className="text-center">
                    {text}
                </p>
            </div>
        </div>
    )
}
const WaitingListCounters = ({remainingTickets, pendingTickets, sentOffers, confirmedAttendees, notInterested, setOrders, state}) => {

    const [selectedCounterBox, setSelectedCounterBox] = useState({
        pendingCounter: false,
        sentCounter: false
    })
    //show filtered data based on the selected div
    const showFilteredInvites = (filter) => {
        //if already selected set both to false
        if (selectedCounterBox[filter] === true) {
            setSelectedCounterBox({
                pendingCounter: false,
                sentCounter: false
            })

            //reset orders to original state
            setOrders(state.waitingListOrders.data);
        } else {
            //select clicked box and set state
            setSelectedCounterBox({
                pendingCounter: filter === 'pendingCounter',
                sentCounter: filter === 'sentCounter'
            })
            if (state && state.waitingListOrders) {
                //filter data based on the selected div
                const filteredOrders = state.waitingListOrders.data.filter(
                    // replace('Counter', '') will replace Counter in pendingCounter
                    // and sentCounter and the remaining text will be sent or pending
                    order => order.order_attendee_status.toLowerCase() === filter.replace('Counter', '')
                );
                setOrders(filteredOrders)
            }
        }
    }
    return(
        <div className="ebs-wishlist-box">
            <div className="row d-flex">
                <div className="col">
                    <BoxLayoutForTicketInfo  count={remainingTickets} text="Remaining Tickets" />
                </div>
                <div className={`col ${selectedCounterBox.pendingCounter && "selected-box bg-primary"}`}
                     onClick={() => showFilteredInvites('pendingCounter')}>
                    <BoxLayoutForTicketInfo count={pendingTickets} text="Pending" />
                </div>

                <div className={`col ${selectedCounterBox.sentCounter && "selected-box bg-primary"}`}
                     onClick={() => showFilteredInvites('sentCounter')}>
                    <BoxLayoutForTicketInfo  count={sentOffers} text="Sent" />
                </div>
                <div className="col">
                    <BoxLayoutForTicketInfo  count={confirmedAttendees} text="Attending" />
                </div>
                <div className="col">
                    <BoxLayoutForTicketInfo count={notInterested} text="Not Interested" />
                </div>
            </div>
        </div>
    )
}

export default WaitingListCounters