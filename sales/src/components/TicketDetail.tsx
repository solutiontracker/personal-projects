"use client"
import moment from 'moment';
import React, { useEffect, useState } from 'react'
import Countdown from './CountDownSmall';
import { useTranslations } from 'next-intl';

const TicketDetail = ({handleClose, form_stats }: any) => {
  const t = useTranslations('manage-orders-page');
  const _container = React.useRef<any>();
  const [searchQuery, setSearchQuery] = useState('');
  const [filteredFormStats, setFilteredFormStats] = useState(form_stats);

  useEffect(() => {
    if(searchQuery !== ''){
        setFilteredFormStats(form_stats.filter((item:any)=>(item.attendee_type.attendee_type.toLowerCase().includes(searchQuery.toLowerCase()))))
    }else{
      setFilteredFormStats(form_stats);
    }
  
    
  }, [searchQuery])
  
  return (
    <div ref={_container} style={{overflow: 'hidden'}} className="ebs-modal-wrapper">
      <div className="modal" role="dialog">
        <div className="modal-dialog ebs-modal-tickets" role="document">
          <div className="modal-content">
            <div className="modal-body">
              <div className="d-flex align-items-center">
                <h3 className='d-flex align-items-center' style={{marginRight: 'auto'}}> <span className='material-symbols-outlined pr-2' onClick={() => handleClose('close')}>arrow_back</span> {t('tickets_detail')}</h3>
                <input style={{marginRight: 0}} type="text" className="ebs-search-area" placeholder={t('search')} value={searchQuery} onChange={(e)=> setSearchQuery(e.target.value)} />
              </div>
              <div className="ebs-grid-ticket-wrapper">
                <div className="d-flex ebs-grid-ticket-row ebs-grid-ticket-header">
                  <div className="ebs-box-1"><strong>{t('tickets_detail_table.form')} </strong></div>
                  <div className="ebs-box-2"><strong>{t('tickets_detail_table.waiting')}</strong></div>
                  <div className="ebs-box-2"><strong>{t('tickets_detail_table.tickets_sold')}</strong></div>
                  <div className="ebs-box-2"><strong>{t('tickets_detail_table.tickets_left')}</strong></div>
                  <div className="ebs-box-2"><strong>{t('tickets_detail_table.total_tickets')}</strong></div>
                  <div className="ebs-box-2 text-center"><strong>{t('tickets_detail_table.time_left')}</strong></div>
                </div>
                <div style={{maxHeight: _container?.current?.offsetHeight - 300}} className="ebs-grid-ticket-scroll">
                  {filteredFormStats.length > 0 && filteredFormStats.map((item:any, i:any) => <div key={i} className="d-flex ebs-grid-ticket-row">
                    <div className="ebs-box-1"><p>{item.attendee_type.attendee_type}</p></div>
                    <div className="ebs-box-2"><p>{item.waiting_attendees_count}</p></div>
                    <div className="ebs-box-2"><p>{item.tickets_sold}</p></div>
                    <div className="ebs-box-2"><p>{item.tickets_left}</p></div>
                    <div className="ebs-box-2"><p>{item.total_tickets}</p></div>
                    <div className="ebs-box-2 text-center"><p>
                        {item.eventsite_settings.registration_end_date !== "0000-00-00 00:00:00" ? <Countdown date={`${moment(item.eventsite_settings.registration_end_date).format('YYYY-MM-DD')} ${item.eventsite_settings.registration_end_time}` } /> : '00:00:00:00'}
                      </p></div>
                  </div>)}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default TicketDetail;