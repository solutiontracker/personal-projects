"use client"; // this is a client component
import Dropdown from '@/components/DropDown';
import { useState, useEffect, useMemo } from "react";
import Image from 'next/image';
import AlertMessage from '@/components/forms/alerts/AlertMessage';
import Loader from '@/components/forms/Loader';
import { useRouter } from 'next/navigation';
import Pagination from "@/components/pagination";
import { useAppDispatch, useAppSelector } from "@/redux/hooks/hooks";
import { RootState, store } from "@/redux/store/store";
import { setCurrentPage, userEvents } from '@/redux/store/slices/EventsSlice';
import Link from 'next/link';

import {getSelectedLabel} from '@/helpers'; 
import { useTranslations } from 'next-intl';



export default function Dashboard({params:{locale}}:{params:{locale:string}}) {
    const t = useTranslations('manage-events-page');
    
    const dispatch = useAppDispatch();
    const router = useRouter();
    const {user} = useAppSelector((state: RootState) => state.authUser);
    const {events, loading, totalPages, currentPage} = useAppSelector((state: RootState) => state.events);

    let eventsRequestDataStored = typeof window !== "undefined" && localStorage.getItem("eventsRequestData");
    const eventsRequestDataStore = eventsRequestDataStored && eventsRequestDataStored !== undefined ? JSON.parse(eventsRequestDataStored) : null;

    const [eventsRequestData, setEventsRequestData] = useState<any>(eventsRequestDataStore !== null ? eventsRequestDataStore : {search_text: '', event_action: 'active_future', sort_by: '', order_by: '', page:1, limit:10});
    const [toggoleLimited, settoggoleLimited] = useState(false)
    const [limit, setLimit] = useState(eventsRequestDataStore !== null ? eventsRequestDataStore.limit : 10);

    useEffect(() => {
        const promise = dispatch(userEvents(eventsRequestData));
        return () =>{
            promise.abort();
        }
    }, []);

    const eventFilters = useMemo(() => [
        {id: 'active_future', name: t('event_filters.active_future')},
        {id: 'active', name: t('event_filters.active')},
        {id: 'future', name: t('event_filters.future')},
        {id: 'expired', name: t('event_filters.expired')},
        {id: 'name', name: t('event_filters.name')}
    ], [locale])

    const sortFilters = useMemo(() => [
        {id: 'name', name: t('sort_filters.name')},
        {id: 'organizer_name', name: t('sort_filters.organizer_name')},
        {id: 'start_date', name: t('sort_filters.start_date')},
        {id: 'end_date', name: t('sort_filters.end_date')}
    ], [locale])

    const storeEventRequestData = (eventsRequestDataStored:any) => {
        if(window !== undefined){
            localStorage.setItem('eventsRequestData', JSON.stringify(eventsRequestDataStored));
        }
    }

    const handleSearchTextFilter = (e:any) => {
        const {value} = e.target;
        const eventsRequestDataUpdate = eventsRequestData;
        eventsRequestDataUpdate.search_text = value;
        eventsRequestDataUpdate['page'] = 1;
        // Update the requestData state with the modified array
        storeEventRequestData(eventsRequestDataUpdate);
        setEventsRequestData(eventsRequestDataUpdate);
        dispatch(userEvents(eventsRequestDataUpdate));
    }

    const handleFilterByFilter = (e:any) => {
        const eventsRequestDataUpdate = eventsRequestData;
        eventsRequestDataUpdate['event_action'] = e.value;
        eventsRequestDataUpdate['page'] = 1;
        storeEventRequestData(eventsRequestDataUpdate);
        setEventsRequestData(eventsRequestDataUpdate);
        dispatch(userEvents(eventsRequestDataUpdate));
    }

    const handleSortByFilter = (e:any) => {
        const eventsRequestDataUpdate = eventsRequestData;
        eventsRequestDataUpdate['sort_by'] = e.value;
        eventsRequestDataUpdate['page'] = 1;
        // Update the requestData state with the modified array
        storeEventRequestData(eventsRequestDataUpdate);
        setEventsRequestData(eventsRequestDataUpdate);
        dispatch(userEvents(eventsRequestDataUpdate));
    }

    const handlePageChange = (page: number) => {
        // Perform API call or update data based on the new page
        const eventsRequestDataUpdate = eventsRequestData;
        eventsRequestDataUpdate['page'] = page;
        storeEventRequestData(eventsRequestDataUpdate);
        setEventsRequestData(eventsRequestDataUpdate);
        dispatch(setCurrentPage(page));
        dispatch(userEvents(eventsRequestDataUpdate));
    };

    const handleToggle = (e:any) => {
        e.stopPropagation();
        e.preventDefault();
        settoggoleLimited(!toggoleLimited);
      }

      const handleLimitChange = (e:any, value:any) => {
        setLimit(value); 
        handleToggle(e);
        const eventsRequestDataUpdate = eventsRequestData;
        eventsRequestDataUpdate['limit'] = value;
        eventsRequestDataUpdate['page'] = 1;
        storeEventRequestData(eventsRequestDataUpdate);
        setEventsRequestData(eventsRequestDataUpdate);
        dispatch(userEvents(eventsRequestDataUpdate));
      }
    

    return (
        <>
            <div className="top-landing-page">
                <div className="row d-flex">
                    <div className="col-4">
                        <div className="logo">
                            <a href="">
                                <Image
                                    src={require('@/assets/img/logo.svg')} alt="" width="150" height="32"
                                    className='logos'
                                />
                            </a>
                        </div>
                    </div>
                    <div className="col-8">
                        <div className="right-top-header">
                            <input className="search-field" name="search_text" type="text" placeholder={t('search')} value={eventsRequestData.search_text} onKeyUp={(e) => { e.key === 'Enter' ? handleSearchTextFilter(e): null}} onChange={(e)=>{setEventsRequestData((prev:any)=> ({...prev, search_text:e.target.value}))}} />
                            <label className="label-select-alt">
                                <Dropdown
                                    selected={eventsRequestData.event_action} 
                                    onChange={handleFilterByFilter}
                                    selectedlabel={getSelectedLabel(eventFilters,eventsRequestData.event_action)}
                                    label={t('event_filter_label')}
                                    listitems={eventFilters}
                                />
                            </label>
                            <label className="label-select-alt">
                                <Dropdown
                                    selected={eventsRequestData.sort_by}
                                    onChange={handleSortByFilter}
                                    selectedlabel={getSelectedLabel(sortFilters,eventsRequestData.sort_by)}
                                    label={t('sort_filter_label')}
                                    listitems={sortFilters}
                                />
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div className="main-data-table" style={{minHeight:'calc(100vh - 272px)'}}>
                 <div className="ebs-data-table">
                    <div className="d-flex align-items-center ebs-table-header">
                        <div className="ebs-table-box ebs-box-3"><strong>{t('event_table.event_logo')}</strong></div>
                        <div className="ebs-table-box ebs-box-3"><strong>{t('event_table.event_name')} </strong></div>
                        <div className="ebs-table-box ebs-box-3"><strong>{t('event_table.event_date')}</strong></div>
                        <div className="ebs-table-box ebs-box-3"><strong>{t('event_table.created_by')}</strong></div>
                        <div className="ebs-table-box ebs-box-3"><strong>{t('event_table.organized_by')}</strong></div>
                        {/* <div className="ebs-table-box ebs-box-4"><strong>{t('event_table.tickets_left')}</strong></div> */}
                        <div className="ebs-table-box ebs-box-3"><strong>{t('event_table.sold_tickets')}</strong></div>
                        {/* <div className="ebs-table-box ebs-box-5"><strong>{t('event_table.total_tickets')}</strong></div> */}
                        <div style={{paddingRight: 0}} className="ebs-table-box ebs-box-3"><strong>{t('event_table.my_sold_tickets')}</strong></div>
                        <div style={{textAlign: 'right'}} className="ebs-table-box ebs-box-3 text-right"><strong
                            style={{justifyContent: 'flex-end'}}>{t('event_table.my_revenue')}</strong></div>
                    </div>
                    <>
                        {
                            events?.length > 0 ? (
                                events?.map((item: any, key: any) =>
                                <Link key={key} style={{textDecoration:"none"}} href={`/${locale}/manage/events/${item.id}/orders`}>
                                    <div 
                                            className="d-flex align-items-center ebs-table-content"
                                            >
                                        <div className="ebs-table-box ebs-box-3">
                                            <Image
                                                src={item.header_logo ? (`${process.env.serverImageHost + '/' + item.header_logo}`) : require('@/assets/img/logo-placeholder.svg')}
                                                alt="" width={100} height={34}/>
                                        </div>
                                        <div className="ebs-table-box ebs-box-3"><p>{item.name}</p>
                                        </div>
                                        <div className="ebs-table-box ebs-box-3">
                                            <p>{item.start_date}</p>
                                            <p>{item.end_date}</p>
																					</div>
                                        <div className="ebs-table-box ebs-box-3">
                                            <p>{item.organizer_name}</p></div>
                                        <div className="ebs-table-box ebs-box-3">
                                            <p>{item.organizer_name}</p></div>
                                        {/* <div className="ebs-table-box ebs-box-3">
                                            <p>{item.event_stats.tickets_left}</p></div> */}
                                        <div className="ebs-table-box ebs-box-3">
                                            <p>{item.event_stats.tickets_sold}</p></div>
                                        {/* <div className="ebs-table-box ebs-box-5">
                                            <p>{item.event_stats.total_tickets}</p></div> */}
                                        <div style={{paddingRight: 0}}
                                                className="ebs-table-box ebs-box-3">
                                            <p>{item.sale_agent_stats.tickets_sold}</p></div>
                                        <div style={{textAlign: 'right'}}
                                                className="ebs-table-box ebs-box-3 text-right">
                                            <p>{item.sale_agent_stats.revenue} {" "} {(item.currency) ? item.currency : 'DKK'}</p>
                                        </div>
                                    </div>
                                </Link>
                                )
                            ) : (
                                !loading ? (
                                        <div style={{minHeight: '450px', backgroundColor: '#fff', borderRadius: '8px'}} className='d-flex align-items-center justify-content-center h-100 w-100'>
                                            <div className="text-center">
                                                <Image
                                                        src={require('@/assets/img/no_record_found.svg')} alt="" width="100" height="100"
                                                />
                                                <p className='pt-3 m-0'>{t('no_data_available')}</p>
                                            </div>
                                        </div>
                                ) : <Loader className='' fixed='' />
                            )
                        }
                    </>
                </div>
                {/* Render the pagination component */}
                {events?.length > 0 && <div className='d-flex justify-content-end align-items-center pt-3'>
                    <Pagination
                        currentPage={currentPage}
                        totalPages={totalPages}
                        onPageChange={handlePageChange}
                    />
                    <div onClick={(e) => e.stopPropagation()} className="ebs-dropdown-area">
                      <button onClick={handleToggle} className={`ebs-btn-dropdown btn-select ${toggoleLimited ? "ebs-active" : ''}`}>
                        {limit} <i className="material-symbols-outlined">expand_more</i>
                      </button>
                      <div className={`ebs-dropdown-menu`}>
                        <button className="dropdown-item" onClick={(e)=> { handleLimitChange(e, 10);  }}>10</button>
                        <button className="dropdown-item" onClick={(e)=> { handleLimitChange(e, 20);  }}>20</button>
                        <button className="dropdown-item" onClick={(e)=> { handleLimitChange(e, 100); }}>100</button>
                        <button className="dropdown-item" onClick={(e)=> { handleLimitChange(e, 500);  }}>500</button>
                      </div>
                    </div>
                </div>}
            </div>    
        </>
    );
}