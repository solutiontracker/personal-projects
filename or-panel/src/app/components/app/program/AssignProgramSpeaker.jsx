import React, { useState, useRef, useEffect } from 'react';
import Loader from '@/app/forms/Loader';
import { NavLink, useParams } from 'react-router-dom';
import DropDown from '@/app/forms/DropDown';
import { connect } from 'react-redux';
import { service } from 'services/service';
import InfiniteScroll from "react-infinite-scroll-component";
import { useTranslation } from 'react-i18next';
import { formatString } from 'helpers';
import { DebounceInput } from 'react-debounce-input'

function AssignProgramSpeaker(props) {
  
  const { t, i18n } = useTranslation();
  
  const { id } = useParams();

  const mounted = useRef(false);

  const initialLRender = useRef(false);

  const initialRRender = useRef(false);

  const [l_payload, setLPayload] = useState({
    query : '',
    sort_by: 'first_name',
    order_by : 'ASC',
    limit: '20'
  });

  const [program_data, setProgramData] = useState([]);

  const [unassign, setUnAssign] = useState([]);

  const [preLLoader, setPreLLoader] = useState(false);

  const [is_l_check_all, setIsLCheckAll] = useState(false);
  
  const [is_l_check, setIsLCheck] = useState([]);

  useEffect(() => {
    mounted.current = true;
    unassignApi();
    assignApi();
    return () => { 
      mounted.current = false;
      initialLRender.current = false;
      initialRRender.current = false;
    };
  }, []);

  const unassignApi = (activePage = 1, loader = false, data = []) => {
    setPreLLoader(loader ? true : false);
    service.post(`${process.env.REACT_APP_URL}/attendee/listing/${activePage}`, { id:id, page: activePage, query: l_payload.query, limit:l_payload.limit, sort_by: l_payload.sort_by, order_by: l_payload.order_by, type: 'unassigned-speakers','wizard_listing':true, agenda_id: id})
      .then(
        response => {
          if (response.success) {
            if (mounted.current) {
              if(activePage > 1) {
                var responseData = response.data;
                var allData = [...data, ...response.data.data ];
                responseData.data = allData;
                setUnAssign(responseData);
              } else {
                setUnAssign(response.data);
                setIsLCheck([]);
                setIsLCheckAll(false);
                initialLRender.current = true;
              }
              setPreLLoader(false);
            }
          }
        },
        error => { }
      );
  }

  useEffect(() => {
    if(initialLRender.current) {
      unassignApi();
    }
  }, [l_payload]);

  const handleLSelectAll = e => {
    setIsLCheckAll(!is_l_check_all);
    setIsLCheck(unassign !== undefined && unassign.data !== undefined && unassign.data.map(row => row.id));
    if (is_l_check_all) {
      setIsLCheck([]);
    }
  };
  
  const handleLClick = (id, checked) => {
    setIsLCheck([...is_l_check, id]);
    if (!checked) {
      setIsLCheck(is_l_check.filter(item => item !== id));
    }
  };

  const assignAttendees = (ids = null) => {
    setPreLLoader(true);
    setPreRLoader(true);
    service.put(`${process.env.REACT_APP_URL}/program/assign-speakers`, { id:id, is_l_check: ids === null ? is_l_check : ids, action: 'assign'})
      .then(
        response => {
          if (response.success) {
            if (mounted.current) {
              unassignApi();
              assignApi();
            }
          }
        },
        error => { }
      );
  }

  const [r_payload, setRPayload] = useState({
    query : '',
    sort_by: 'first_name',
    order_by : 'ASC',
    limit: '20'
  });

  const [assign, setAssign] = useState([]);

  const [is_r_check_all, setIsRCheckAll] = useState(false);
  
  const [is_r_check, setIsRCheck] = useState([]);

  const [preRLoader, setPreRLoader] = useState(false);
  const [lSelectedLabele, setLSelectedLabel] = useState('Name');
  const [lSelected, setLSelected] = useState('first_name');
  const [rSelectedLabele, setRSelectedLabel] = useState('Name');
  const [rSelected, setRSelected] = useState('first_name');

  const assignApi = (activePage = 1, loader = false, data = []) => {
    setPreRLoader(loader ? true : false);
    service.post(`${process.env.REACT_APP_URL}/program/assign-speakers`, { id:id, page: activePage, query: r_payload.query, limit:r_payload.limit, sort_by: r_payload.sort_by, order_by: r_payload.order_by})
      .then(
        response => {
          if (response.success) {
            if (mounted.current) {
              if(activePage > 1) {
                var allData = data.concat(response.data.program_attendees.data);
                response.data.program_attendees.data = allData;
                setAssign(response.data.program_attendees);
              } else {
                setAssign(response.data.program_attendees);
                setIsRCheck([]);
                setIsRCheckAll(false);
                setProgramData(response.data.program_data);
                initialRRender.current = true;
              }
              setPreRLoader(false);
            }
          }
        },
        error => { }
      );
  }

  const unassignAttendees = (ids = null) => {
    setPreLLoader(true);
    setPreRLoader(true);
    service.put(`${process.env.REACT_APP_URL}/program/assign-speakers`, { id:id, is_l_check: ids === null ? is_r_check : ids, action: 'unassign'})
      .then(
        response => {
          if (response.success) {
            if (mounted.current) {
              unassignApi();
              assignApi();
            }
          }
        },
        error => { }
      );
  }

  useEffect(() => {
    if(initialRRender.current) {
      assignApi();
    }
  }, [r_payload])
  

  const handleRSelectAll = e => {
    setIsRCheckAll(!is_r_check_all);
    setIsRCheck(unassign !== undefined && assign.data !== undefined && assign.data.map(row => row.id));
    if (is_r_check_all) {
      setIsRCheck([]);
    }
  };
  
  const handleRClick = (id, checked) => {
    setIsRCheck([...is_r_check, id]);
    if (!checked) {
      setIsRCheck(is_r_check.filter(item => item !== id));
    }
  };

  return (
    <React.Fragment>
      <div className="wrapper-content third-step">
        {(preLLoader || preRLoader) && <Loader />}
        {!preLLoader && !preRLoader && <React.Fragment>
          <div className="new-header clearfix">
            <h1 className="section-title ">{formatString(t('PROGRAM_ASSIGN_SPEAKER_PAGE_HEADING'), program_data.info !== undefined ? program_data.info.topic : '')}</h1>
          </div>
          <div className="ebs-assign-speakers-section h-100">
            <div className="row d-flex h-100">
              <div className="col-6">
                <div className="ebs-assign-speaker-column">
                  <div className="ebs-top-panel d-flex">
                    <button disabled={is_l_check.length === 0} className="btn" onClick={() => {
                      assignAttendees();
                    }}>{t('PROGRAM_ASSIGN_SPEAKER_ASSIGN')}</button>
                    <label className="label-select-alt">
                        <DropDown
                          listitems={[
                            { id: 'first_name', name: t('PROGRAM_ASSIGN_SPEAKER_NAME')},
                            { id: 'email', name: t('PROGRAM_ASSIGN_SPEAKER_EMAIL')},
                            { id: 'company_name', name: t('PROGRAM_ASSIGN_SPEAKER_COMPANY') },
                            { id: 'department', name: t('PROGRAM_ASSIGN_SPEAKER_DEPARTMENT') },
                            { id: 'title', name: t('PROGRAM_ASSIGN_SPEAKER_TITLE') }
                          ]}
                          selected={lSelected}
                          selectedlabel={lSelectedLabele}
                          onChange={(e) => {
                            setLSelectedLabel(e.label)
                            setLSelected(e.value)
                            setLPayload({...l_payload, sort_by: e.value})
                          }}
                        />
                      </label>
                      <div className="ebs-counter">{formatString(t('PROGRAM_ASSIGN_SPEAKER_ATTENDEES'), unassign !== undefined ? unassign.total : '')}</div>
                  </div>
                  <div className="ebs-assign-section">
                    <div className="ebs-assign-top">
                      <label className='ebs-custom-label'>
                        <input 
                          type="checkbox"
                          onClick={handleLSelectAll}
                          checked={is_l_check_all}
                        />
                        <span>{t('PROGRAM_ASSIGN_SPEAKER_SELECT_ALL')}</span>
                      </label>
                      <DebounceInput
                        className="search-field"
                        placeholder={t('PROGRAM_ASSIGN_SPEAKER_SEARCH')}
                        minLength={1}
                        value={l_payload.query}
                        debounceTimeout={1000}
                        onChange={event => {
                          setLPayload({...l_payload, query: event.target.value});
                        }}
                      />
                    </div>
                    <div className="ebs-assign-list" id="unAssignscrollableDiv" style={{ height: 600, overflow: "auto" }}>
                      <InfiniteScroll
                        dataLength={unassign !== undefined && unassign.data !== undefined ? unassign.data.length : 0}
                        next={() => {
                          unassignApi((unassign !== undefined && unassign.current_page ? (unassign.current_page + 1) : 1), false, unassign.data);
                        }}
                        hasMore={unassign !== undefined && unassign.next_page_url ? true : false}
                        loader={<div className='ebs-loadmore'><i className='material-icons'>loop</i></div>}
                        scrollableTarget="unAssignscrollableDiv"
                      >
                        {unassign !== undefined && unassign.data !== undefined && unassign.data.map((item,k) => 
                          <div key={k} className="ebs-assign-item">
                            <label className='ebs-custom-label'>
                              <input 
                                type="checkbox"
                                onChange={(e) => {
                                  handleLClick(item.id, e.target.checked);
                                }}
                                checked={is_l_check.includes(item.id)}
                              />
                              <span></span>
                            </label>
                            <div className="ebs-assign-detail">
                              {item.first_name && (
                                <h5 dangerouslySetInnerHTML={{ __html: item.first_name+' '+item.last_name }}></h5>
                              )}
                              {
                                (() => {
                                  if (item.attendee_detail !== undefined && item.attendee_detail.title && item.attendee_detail.company_name)
                                    return <p>{item.attendee_detail.title} at {item.attendee_detail.company_name}</p>
                                  else if (item.attendee_detail !== undefined && item.attendee_detail.title)
                                    return <p>{item.attendee_detail.title}</p>
                                  else if (item.attendee_detail !== undefined && item.attendee_detail.company_name)
                                    return <p>{item.attendee_detail.company_name}</p>
                                })()
                              }
                              <p>{item.email &&  item.email}</p>
                              <p>{item.attendee_detail !== undefined && item.attendee_detail.department}</p>
                            </div>
                            <button className="btn" onClick={(e) => {
                              assignAttendees([item.id])
                            }}>{t('PROGRAM_ASSIGN_SPEAKER_ASSIGN')}</button>
                          </div>
                        )}
                      </InfiniteScroll>
                    </div>
                  </div>
                </div>
              </div>
              <div className="col-6">
                <div className="ebs-assign-speaker-column">
                  <div className="ebs-top-panel d-flex">
                    <button disabled={is_r_check.length === 0} className="btn" onClick={() => {
                      unassignAttendees();
                    }}>{t('PROGRAM_ASSIGN_SPEAKER_UNASSIGN')}</button>
                    <label className="label-select-alt">
                        <DropDown
                          listitems={[
                            { id: 'first_name', name: t('PROGRAM_ASSIGN_SPEAKER_NAME')},
                            { id: 'email', name: t('PROGRAM_ASSIGN_SPEAKER_EMAIL')},
                            { id: 'company_name', name: t('PROGRAM_ASSIGN_SPEAKER_COMPANY') },
                            { id: 'department', name: t('PROGRAM_ASSIGN_SPEAKER_DEPARTMENT') },
                            { id: 'title', name: t('PROGRAM_ASSIGN_SPEAKER_TITLE') }
                          ]}
                          selectedlabel={rSelectedLabele}
                          selected={rSelected}
                          onChange={(e) => {
                            setRSelected(e.value)
                            setRSelectedLabel(e.label)
                            setRPayload({...r_payload, sort_by: e.value})
                          }}
                        />
                      </label>
                      <div className="ebs-counter">{formatString(t('PROGRAM_ASSIGN_SPEAKER_ATTENDEES'), assign !== undefined ? assign.total : '')}</div>
                  </div>
                  <div className="ebs-assign-section">
                    <div className="ebs-assign-top">
                      <label className='ebs-custom-label'>
                        <input 
                          type="checkbox"
                          onClick={handleRSelectAll}
                          checked={is_r_check_all}
                        />
                        <span>{t('PROGRAM_ASSIGN_SPEAKER_SELECT_ALL')}</span>
                      </label>
                      <DebounceInput
                        className="search-field"
                        value={r_payload.query}
                        placeholder={t('PROGRAM_ASSIGN_SPEAKER_SEARCH')}
                        minLength={1}
                        debounceTimeout={1000}
                        onChange={event => {
                          setRPayload({...r_payload, query: event.target.value});
                        }}
                      />
                    </div>
                    <div id="assignscrollableDiv" className="ebs-assign-list" style={{ height: 600, overflow: "auto" }}>
                      <InfiniteScroll
                        dataLength={assign !== undefined && assign.data !== undefined ? assign.data.length : 0}
                        next={() => {
                          assignApi((assign !== undefined && assign.current_page ? (assign.current_page + 1) : 1), false, assign.data);
                        }}
                        hasMore={assign !== undefined && assign.next_page_url ? true : false}
                        loader={<div className='ebs-loadmore'><i className='material-icons'>loop</i></div>}
                        scrollableTarget="assignscrollableDiv"
                      >
                        {assign!== undefined && assign.data !== undefined && assign.data.map((item, k) => 
                          <div key={k} className="ebs-assign-item">
                            <label className='ebs-custom-label'>
                              <input 
                                type="checkbox"
                                onChange={(e) => {
                                  handleRClick(item.attendee_id, e.target.checked);
                                }}
                                checked={is_r_check.includes(item.attendee_id)}
                              />
                              <span></span>
                            </label>
                            <div className="ebs-assign-detail">
                              {item.first_name && (
                                <h5 dangerouslySetInnerHTML={{ __html: item.first_name+' '+item.last_name }}></h5>
                              )}
                              {
                                (() => {
                                  if (item.attendee_detail !== undefined && item.attendee_detail.title && item.attendee_detail.company_name)
                                    return <p>{item.attendee_detail.title} at {item.attendee_detail.company_name}</p>
                                  else if (item.attendee_detail !== undefined && item.attendee_detail.title)
                                    return <p>{item.attendee_detail.title}</p>
                                  else if (item.attendee_detail !== undefined && item.attendee_detail.company_name)
                                    return <p>{item.attendee_detail.company_name}</p>
                                })()
                              }
                              <p>{item.email &&  item.email}</p>
                              <p>{item.attendee_detail !== undefined && item.attendee_detail.department}</p>
                            </div>
                            <button className="btn" onClick={(e) => {
                              unassignAttendees([item.attendee_id])
                            }}>{t('PROGRAM_ASSIGN_SPEAKER_UNASSIGN')}</button>
                          </div>
                        )}
                      </InfiniteScroll>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="bottom-component-panel clearfix">
              <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                <i className='material-icons'>remove_red_eye</i>
                {t('G_PREVIEW')}
              </NavLink>
              <NavLink className="btn btn-prev-step" to="/event/module/programs">
                <span className="material-icons">keyboard_backspace</span>
              </NavLink>
          </div>
        </React.Fragment>}
      </div>
    </React.Fragment>
  )
}

function mapStateToProps(state) {
  const { event, update } = state;
  return {
    event, update
  };
}

export default connect(mapStateToProps)(AssignProgramSpeaker);