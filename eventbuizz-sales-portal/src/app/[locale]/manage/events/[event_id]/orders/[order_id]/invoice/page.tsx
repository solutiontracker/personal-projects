'use client'
import React, { useEffect } from 'react';
import Image from 'next/image'
import Dropdown from '@/components/DropDown';
import { useAppDispatch, useAppSelector } from '@/redux/hooks/hooks';
import { RootState } from '@/redux/store/store';
import { userEventOrderInvoice } from '@/redux/store/slices/OrderSlice';
import Loader from '@/components/forms/Loader';

export default function Invoice({ params }: { params: { event_id: string, order_id:string } }) {
  const dispatch = useAppDispatch();
  
  const {loading, order, invoice} = useAppSelector((state: RootState) => state.order);

  useEffect(() => {
    const promise = dispatch(userEventOrderInvoice({event_id:params.event_id, order_id:params.order_id}));  

      return () => {
        promise.abort();
      }
  }, [])
  
  
  return (
    <>
    
      {invoice ?  <div className='ebs-invoice-wrapper' dangerouslySetInnerHTML={{__html:invoice}}>

      </div> : <Loader className='' fixed=''/>}
    </>
  );
}
