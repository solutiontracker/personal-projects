import React,{useState} from "react";
export default function OrderAttendees(props) {
    const[display,setDisplay]=useState(false);
    const flexGrow = {
        flexGrow: 1,
        flexBasis: 0
    }
    const pStyles = {
        fontSize: '12px',
        whiteSpace: 'nowrap',
        overflow: 'hidden',
        textOverflow: 'ellipsis'
    }
    const showHideAttendee=()=>{
        setDisplay(!display)
    }
    console.log(props)
    return( 
        <div style={{marginBottom: 12}}>
           <div className="row check-box-list">
           <div style={{ ...flexGrow, minWidth: '81px', maxWidth: '81px' }} className="grid-2">&nbsp;</div>
            <div style={{ ...flexGrow, minWidth: '85px', maxWidth: '85px' }} className="grid-3">&nbsp;</div>
                <div style={{ ...flexGrow, minWidth: '120px' }} className="grid-4">
                <span onClick={showHideAttendee} style={{fontSize: 12,cursor: 'pointer'}}>
                <i style={{fontSize: 16, verticalAlign: 'text-bottom'}} className="material-icons">{!display ? 'keyboard_arrow_right' : 'expand_less'}</i>
                        {props.order_attendees.length-1} more attendees
            </span>
                </div>
        
           </div>
            {display && <React.Fragment>
                {props.order_attendees.map((order_attendee, inde) => (
                    <React.Fragment key={inde}>
                        {order_attendee.attendee_id != props.attendee_id && <div className="row check-box-list">
                            <div style={{ ...flexGrow, minWidth: '81px', maxWidth: '81px' }} className="grid-2">&nbsp;</div>
                            <div style={{ ...flexGrow, minWidth: '85px', maxWidth: '85px' }} className="grid-3">&nbsp;</div>
                            <div style={{ ...flexGrow, minWidth: '120px', maxWidth: '120px' }} className="grid-4">
                                <p style={pStyles}>{order_attendee.attendee_detail.first_name + ' ' + order_attendee.attendee_detail.last_name}</p>
                            </div>
                            <div style={{ ...flexGrow, minWidth: '170px', width: '170px' }} className="grid-5">
                                <p style={pStyles}>{order_attendee.attendee_detail.email}</p>
                            </div>
                        </div>}
                    </React.Fragment>
                    ))}
            </React.Fragment>}
    </div>)
  };