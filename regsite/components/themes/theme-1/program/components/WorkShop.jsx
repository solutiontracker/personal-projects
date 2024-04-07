import React, {useState} from 'react'
import moment from 'moment'
import ProgramItem from "components/themes/theme-1/program/components/ProgramItem";

const WorkShop = ({item, eventUrl, showWorkshop, labels, agendaSettings}) => {
  const [open, setOpen] = useState(showWorkshop);
  return (
    <div className="ebs-program-parent ebs-program-workshop">
    <div onClick={()=>{setOpen(!open)}} className="ebs-workshop-header">
      {item.program_workshop} 
      {parseInt(item.hide_time) === 0  && <>
      {" "}({moment(item?.program_workshop_start_time, "HH:mm:ss").format("HH:mm")} - {moment(item?.program_workshop_end_time, "HH:mm:ss").format("HH:mm")})
      </>}
      <i className="material-icons">{!open ? 'expand_less' : 'expand_more'}</i></div>
          {!open ? item.workshop_programs.map((program,i) =>
                <ProgramItem key={i} eventUrl={eventUrl} program={program} labels={labels} agendaSettings={agendaSettings}/>           
          ) : ''}
    </div>
  )
}

export default WorkShop