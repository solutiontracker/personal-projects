import React, { useState, useEffect } from "react";
import {
  fetchKeywordsData,
  interestSelector,
  updateKeywordData,
} from "store/Slices/myAccount/networkInterestSlice";
import { eventSelector } from "store/Slices/EventSlice";
import { useSelector, useDispatch } from "react-redux";
import PageLoader from "components/ui-components/PageLoader";
const ManageKeywords = () => {
  const { event } = useSelector(eventSelector);
  const dispatch = useDispatch();
  useEffect(() => {
    dispatch(fetchKeywordsData(event.id, event.url));
  }, []);
  const { keywords, updating } = useSelector(interestSelector);
  return (
    keywords ?<div className="edgtf-container ebs-my-profile-area pb-5">
      <div className="edgtf-container-inner container">
        <div className="ebs-header">
          <h2>My Keywords</h2>
        </div>
        <div className="wrapper-inner-content network-category-sec">
            {keywords.length > 0 ? <ManageKeywordsList keywords={keywords} event={event} updating={updating} /> : 
              <div>
              {event.labels.GENERAL_NO_RECORD ? event.labels.GENERAL_NO_RECORD : " You have no answers yet..."}
             </div>
            }
        </div>
      </div>
    </div> : <PageLoader/>
  );
};

export default ManageKeywords;

const ManageKeywordsList = ({ keywords, event, updating }) => {
  const [interestkeywords, setInterestKeywords] = useState(keywords);
  const [mykeywords, setMyKeywords] = useState(keywords.reduce((ack, item)=>{
    const childern = item.children.reduce((ack2, item2)=>{
      if(item2.keywords.length > 0){
          return [item2.id, ...ack2]
      }else{
        return ack2
      }
    },[]);
    if(item.keywords.length > 0 ){
      return [item.id, ...childern, ...ack];
    }else{
      return [...ack, ...childern];
    }
  },[]));
  const [filteredkeywords, setFilteredKeywords] = useState([]);
  const [searchTerm, setSearchTerm] = useState("");
  const [filters, setFilters] = useState([]);
  const dispatch = useDispatch();

  const setFilter= (kid)=>{
    setSearchTerm("");
    if(kid !== 0){
      if(filters.indexOf(kid) === -1) {
        setFilters([...filters, kid])
      }else{
        setFilters([...filters.filter((item)=>( item !== kid))])
      }
    }else{
      setFilters([]);
    }
  }
  const setSearch = (e)=>{
    const {value} = e.target;
    setSearchTerm(value);
    setFilters([ ...interestkeywords.filter((kword)=> (kword.name.toLowerCase().indexOf(value.toLowerCase()) !== -1)).map((kword)=>(kword.id)) ])
  }
  useEffect(() => {
    if(filters.length > 0)
    {
      setFilteredKeywords([...interestkeywords.filter((kword)=> (filters.indexOf(kword.id) !== -1) )])
    }
    else{
      setFilteredKeywords([])
    }
  }, [filters])
  
  const addMyKeyword = (kid) =>{
    if(mykeywords.indexOf(kid) === -1) {
      setMyKeywords([...mykeywords, kid])
    }else{
      setMyKeywords([...mykeywords.filter((item)=>( item !== kid))])
    }
  }
  const handleSave = (e) =>{
    dispatch(updateKeywordData(event.id, event.url, mykeywords));
  }
  return (
    <React.Fragment>
      <div className="ebs-keywords-filter">
        <div className="network-cateogry-list ebs-cateogry-filter">
          <ul>
            <li>
              <label>
                <input type="checkbox" onChange={()=>{setFilter(0)}} />
                <span>All</span>
              </label>
            </li>
            {interestkeywords.map((kword)=>(<li key={kword.id}>
              <label>
                <input type="checkbox" checked={filters.indexOf(kword.id) !== -1 ? true : false} onChange={()=>{setFilter(kword.id)}} />
                <span>{kword.name}</span>
              </label>
            </li>))}
          </ul>
        </div>
      </div>
      <div className="ebs-keyword-search">
        <label>
          <input placeholder="Search" type="text" value={searchTerm} onChange={(e)=>{ setSearch(e) }} />
          <i className="material-icons">search</i>
        </label>
      </div>
        <div className="ebs-keyword-wrapper">
          {filteredkeywords.length > 0 ? filteredkeywords.map((item) => (
          <div className="network-cateogry-list" key={item.id}>
            <h5>{item.name}</h5>
            <ul>
              {item.children.map((child) => (
                <li key={child.id}>
                  <label>
                    <input type="checkbox" checked={mykeywords.indexOf(child.id) !== -1 ? true : false} />
                    <span>{child.name}</span>
                  </label>
                </li>
              ))}
            </ul>
          </div>
          )):
          interestkeywords.map((item) => (
            <div className="network-cateogry-list" key={item.id}>
              <h5>{item.name}</h5>
              <ul>
                {item.children.map((child) => (
                  <li key={child.id}>
                    <label>
                      <input type="checkbox" checked={mykeywords.indexOf(child.id) !== -1 ? true : false} onChange={()=>{addMyKeyword(child.id)}} />
                      <span>{child.name}</span>
                    </label>
                  </li>
                ))}
              </ul>
            </div>
            ))}
            <div className="bottom-button">
              <button className="btn btn-save-next btn-loader" disabled={updating ? true : false} onClick={(e)=>{handleSave(e)}}>{updating ?  "Saving..." : 'Save'}</button>
            </div>
        </div>
    </React.Fragment>
  );
};
