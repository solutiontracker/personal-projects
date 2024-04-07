"use client"
import { useTranslations } from 'next-intl';
import React, { useState } from 'react'
import DateTime from '@/components/DateTimePicker';
import moment from 'moment';


const ConfirmPopup = ({handleClose, processing, currentPaymentStatus}: any) => {
  const [changeType, setChangeType] = useState('pending');
  const [date, setDate] = useState(moment().format('YYYY-MM-DD HH:mm'));

  const handleContinue = () => {
    if(!currentPaymentStatus ){
      handleClose('continue', { date, paymentStatus:!currentPaymentStatus})
    }
    if(currentPaymentStatus){
      handleClose('continue', { date, paymentStatus:changeType === 'pending' ? 0 : 1})
    }
  }
  return (
    <div className="ebs-modal-wrapper">
      <div className="modal" role="dialog">
        <div className="modal-dialog" role="document">
          <div className="modal-content">
            <div className="modal-body">
              {!currentPaymentStatus ? (
                <>
                  <h3>Are you sure? you want to change status to received.</h3>
                  <DateTime
                    showtime={'HH:mm'}
                    showdate={'YYYY-MM-DD'}
                    label="Order Completion date time"
                    value={date}
                    onChange={(anser:any)=>setDate(anser.format('YYYY-MM-DD HH:mm'))}
                  />
                </>
              ) : (
                <>
                  <h3>Are you sure? you want to change status to pending.</h3>
                  <div className=''>
                    <label className='d-flex'> <input defaultChecked  type="radio" checked={changeType === 'pending' ? true : false} onChange={()=>{setChangeType('pending')}} name="changeType" className='mx-1'/>
                        CHANGE STATUS TO PENDING
                    </label>
                    <label className='d-flex'> <input type="radio" name="changeType" checked={changeType === 'date' ? true : false} onChange={()=>{setChangeType('date')}} className='mx-1' />
                        CHANGE ONLY DATE
                    </label>
                  </div>
                 {changeType === 'date' &&  <DateTime
                    showtime={'HH:mm'}
                    showdate={'YYYY-MM-DD'}
                    label="Order Completion date time"
                    value={date}
                    onChange={(anser:any)=>setDate(anser.format('YYYY-MM-DD HH:mm'))}
                  />}
                </>
              )}
              
            </div>
            <div className="modal-footer">
              <button onClick={() => handleClose('close')} type="button" className="btn btn-secondary">Cancel</button>
              <button onClick={() => handleContinue()} type="button" className="btn btn-primary">{processing ? 
                <div className="small-loader-wrapper">
                    <div className="small-loader"></div>
                </div>
               : "Continue"}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default ConfirmPopup;