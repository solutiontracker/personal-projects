"use client";
import '@/assets/css/app.scss'
import Image from 'next/image';
import Illustration from '@/assets/img/illustration.png';
import { useAppDispatch, useAppSelector } from '@/redux/hooks/hooks';
import { RootState } from '@/redux/store/store';
import { useRouter } from 'next/navigation';
import { logOutUser } from '@/redux/store/slices/AuthSlice';
import { useEffect, useState } from 'react';
import { userEvent, userEventOrderSend } from '@/redux/store/slices/EventSlice';
import { usePathname } from 'next/navigation';
import Loader from '@/components/forms/Loader';
import Link from 'next/link';
import axios from 'axios';
import { AGENT_ENDPOINT } from '@/constants/endpoints';
import { authHeader } from '@/helpers';
import { useTranslations } from 'next-intl';

export default function RootLayout({ children, params}: { children: React.ReactNode, params: { locale:string, event_id: string } }) {
    const t = useTranslations('manage-orders-layout');
    
    const dispatch = useAppDispatch();
    const pathname = usePathname();
    const {loading, event, event_orders, sending_order} = useAppSelector((state: RootState) => state.event);
    const [downloading, setDownloading] = useState(false);

    useEffect(() => {
      const promise = dispatch(userEvent({event_id:params.event_id}));  
    
      return () => {
        promise.abort();
      }
    }, []);

    const downloadPdf = async (data:any) => {
        setDownloading(true);
        try {
            const response = await axios.get(`${AGENT_ENDPOINT}/billing/send-order-pdf/${data.id}/invoice`,  {
              headers: authHeader('GET'),
              responseType: 'blob'
            });
            console.log(response);
            let url = window.URL.createObjectURL(response.data);
            console.log(url);
            let a = document.createElement("a");
            a.href = url;
            a.download = data.id+".pdf";
            a.click();
          } catch (err:any) {
            
          }
        setDownloading(false);
    }
    
  return (
    <>
    {(loading === false && event !== null) ?
        <>
            <div className="top-landing-page">
                    <div className="row d-flex">
                    <div className="col-8">
                        <div className="logo">
                        <div className="ebs-bottom-header-left">
                            
                            <h3>
                                <a href="#!">{event?.event_name}</a>
                            </h3>
                            <ul>
                            <li>
                                <i className="material-symbols-outlined">calendar_month</i>{event?.event_date}
                            </li>
                            <li>
                                <i className="material-symbols-outlined">place</i>{event?.event_location}
                            </li>
                            <li>
                                <strong>Event id : </strong>  {params.event_id}
                            </li>
                            </ul>
                        </div>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="right-top-header">
                        {(!pathname.includes('invoice') && !pathname.includes('edit') && !pathname.includes('create')) && (event?.payment_settings?.eventsite_billing === 1) ? 
                            <Link href={`/${params.locale}/manage/events/${params.event_id}/orders/create`}>
                                <button className="btn btn-default">
                                    <i className="material-symbols-outlined">add</i> {t('create_order')}
                                </button> 
                            </Link>
                        : null}
                        {pathname.includes('invoice') ? 
                            <>
                            <button className="btn btn-default d-flex" onClick={()=>{downloadPdf({id:pathname.split('/')[5]})}}>
                                 {downloading ? 
                                <div className="small-loader-wrapper" style={{marginRight:'8px'}}>
                                    <div className="small-loader"></div>
                                </div>
                                : <i className="material-symbols-outlined">sim_card_download</i>} 
                                {t('download_pdf')}
                            </button>
                            <button className="btn btn-default btn-send-order d-flex" onClick={()=>dispatch(userEventOrderSend({id:pathname.split('/')[5]}))}>
                                {sending_order ? 
                                <div className="small-loader-wrapper" style={{marginRight:'8px'}}>
                                    <div className="small-loader"></div>
                                </div>:
                                    <i className="material-symbols-outlined">send</i> 
                                }
                                {t('send_order')}
                            </button> 
                            </>
                        : null}
                        </div>
                    </div>
                    </div>
                </div>
            <div style={{ background: "#fff",  }} className="main-data-table">
                    {children}
            </div>
        </> :null}
      {loading === true && event === null ? <Loader className=''fixed='' /> : null}
      {loading === false && event === null ? 
        <div className='d-flex justify-content-center align-items-center' style={{fontSize:"32px", textAlign:"center", fontStyle:"italic",  minHeight:"350px"}}>
            {t('no_data_available')}
        </div> 
      : null }
    </>
  )
}
