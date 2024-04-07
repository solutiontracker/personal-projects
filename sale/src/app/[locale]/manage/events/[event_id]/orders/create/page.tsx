'use client';
import { useAppDispatch, useAppSelector } from '@/redux/hooks/hooks';
import { userEvent } from '@/redux/store/slices/EventSlice';
import { RootState } from '@/redux/store/store';
import Image from 'next/image'
import { useRouter } from 'next/navigation';
import { useEffect, useState } from 'react';

export default function page({ params }: { params: { locale:string, event_id: string } }) {
  const {loading, event, event_orders} = useAppSelector((state: RootState) => state.event);
  const {user} = useAppSelector((state: RootState) => state.authUser);
  const router = useRouter();
  const dispatch = useAppDispatch();
  const [expandIframe, setexpandIframe] = useState<any>(false);
  const [iframeHeight, setIframeHeight] = useState(window.innerHeight - 280);

  useEffect(() => {
    const listener = (event:any) =>{
        if(event.data.order_id !== undefined) {
          dispatch(userEvent({event_id:params.event_id}));

            router.push(`/${params.locale}/manage/events/${params.event_id}/orders`);
        } 
        if(event.data.contentHeight !== undefined){
          setIframeHeight(event.data.contentHeight  + 135);
      }
    }
    window.addEventListener("message", listener);
    return () => {
      window.removeEventListener('message', listener);
    }
  }, []);
  useEffect(() => {
    if (expandIframe) {
      document.body.style.overflowY = 'hidden'
    } else {
      document.body.style.overflowY = 'auto'
    }
    return () => {
      document.body.style.overflowY = 'auto'
    }
  }, [expandIframe])
  const handleClickexpand = () => {
    setexpandIframe(!expandIframe);
  }
  return (
    <div>
      {window !== undefined && <div className={expandIframe && 'ebs-expanded-iframe'} id="ebs-master-wrapper-iframe">
        <button onClick={handleClickexpand} className='btn p-1 btn-primary rounded-circle ebs-button-expand'><span className="material-icons">{!expandIframe ? 'fullscreen' : 'close_fullscreen'}</span></button> 
           <iframe width="100%" style={{minHeight: '100vh'}} height={expandIframe ? '' : iframeHeight}  src={event.eventsite_settings.evensite_additional_attendee === 1 ? `${process.env.regSiteHost}/${event.event_url}/sale/?sale_id=${user.id}` :  `${process.env.regSiteHost}/${event.event_url}/sale/manage-attendee?sale_id=${user.id}`  } />
      </div>}
    </div>
  )
}
