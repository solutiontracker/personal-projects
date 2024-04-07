import React, { Component, FC } from 'react';
import moment from 'moment';
import { Scrollbars } from 'react-custom-scrollbars';
type appProps = {
  data: any,
  onListClick: any,
  onOptionClick: any,
  activeIndex: boolean
}
type appPropsList = {
  data: any,
  onClick: any,
  onOption: any,
  activeId: boolean
}

const ListItem:FC<appPropsList> = ({data,onClick,activeId,onOption}) => {
  return (
    <div className="ebs-scrollbar-wrapper">
    {data && data.map((element:any,k:number) =>
      <div onClick={() => onClick(element.id)} className={`ebs-list-item d-flex align-items-center ${Number(activeId) === element.id ? 'selected' : ''}`} key={k}>
        <div className="left-list-box">
          <div className="list-title">
            <div className="title-wrapp">{element.title}</div> {element.active && <img src={require('../img/ico-check-circle.svg')} alt="" />}
          </div>
          <div className="list-date">{moment(new Date(element.time)).format('DD MMMM YYYY, hh:mm:ss')}</div>
          <div className="list-bottom d-flex">
            <div className="data-created">
              <strong>Created:</strong> {moment(new Date(element.created)).format('DD MMMM YYYY')}
            </div>
            <div className="question-numbers"><strong>Number of Question:</strong> {element.questions.length > 0 ? element.questions.length : 0}</div>
          </div>
        </div>
        <div className="right-list-box d-flex align-items-center">
          <span className="btn-chart"><i className="material-icons">bar_chart</i></span>
          <span onClick={(e) => {e.stopPropagation(); e.preventDefault();onOption('active',element.id)}} className="ebs-btn-task">
            <span className={`ebs-custom-radio ${element.active ? 'active' : ''}`}></span>
          </span>
          <div onClick={(e) => {e.stopPropagation(); e.preventDefault();onOption('completed',element.id)}} className="ebs-btn-task">
            <i className="material-icons">{!element.completed ? 'remove_circle' : 'add_circle'}</i>
          </div>
        </div>
      </div> 
    )}
  </div>
  )
}


export class LeftPollPanel extends Component<appProps> {
  render():any {
    const { data } = this.props;
    return (
      <div className="ebs-left-section">
        <div className="ebs-top-header">
          <div className="event-title">
            {data.title}
          </div>
          <div className="event-id">
            Event ID: <strong>{data.eventid}</strong>
          </div>
        </div>
        <div className="ebs-listing-wrapp">
          <div className="ebs-listing-panel m-0 row d-flex align-items-center">
            <div className="col-4 p-0">
              <div className="panel-title">Session Polls</div>
            </div>
            <div className="col-8 d-flex justify-content-end p-0">
              <div className="panel-elements d-flex align-items-center">
                <div className="ebs-search-item">
                  <i className="material-icons">search</i>
                  <input type="text" placeholder="Search" />
                </div>
                <label className="ebs-search-select">
                <i className="material-icons">expand_more</i>
                  <select>
                    <option value="">Please Select</option>
                  </select>
                </label>
              </div>
            </div>
          </div>
          <div className="ebs-listing-section">
            <Scrollbars style={{ width: '100%', height: '100%' }}>
              {data.polls && data.polls.filter((x:any) => x.completed === false).length > 0 &&
                <ListItem
                activeId={this.props.activeIndex}
                onOption={this.props.onOptionClick}
                onClick={this.props.onListClick} data={data.polls && data.polls.filter((x:any) => x.completed === false)} />}
              {data.polls && data.polls.filter((x:any) => x.completed === true).length > 0 && 
              <ListItem
                onOption={this.props.onOptionClick}
               activeId={this.props.activeIndex} onClick={this.props.onListClick} data={data.polls.filter((x:any) => x.completed === true)} />}
            </Scrollbars>
          </div>
        </div>
      </div>
    )
  }
}

export default LeftPollPanel;
