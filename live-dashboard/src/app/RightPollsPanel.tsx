import React, { Component, FC } from 'react';
import { Scrollbars } from 'react-custom-scrollbars';
import moment from 'moment'

type appProps = {
  data: any
}
type appProp = {
  data: any,
  type: string
}

const ListRadio: FC<appProp> = ({data, type}) => {
  console.log(data);
  return (
    <div className="ebs-radio-check">
      {data.map((items: any, k: number) =>
        <div className="ebs-listing d-flex align-items-center" key={k}>
          <i className="material-icons">{type === 'radio' ? 'radio_button_unchecked' : 'check_box_outline_blank'}</i>
          <div className="ebs-question-title">{items}</div>
        </div>
      )}
    </div>
  )
}

export class RightPollsPanel extends Component<appProps> {
  render():any {
    const { data } = this.props;
    return (  
      <div className="ebs-right-section">
        <div className="ebs-top-header">
          <div className="row m-0">
            <div className="col-7">
              <div className="ebs-title">{data.title}</div>
              <div className="ebs-date">{moment(new Date(data.time)).format('DD MMMM YYYY, hh:mm:ss')}</div>
            </div>
            <div className="col-5 text-right">
              <div className="ebs-total-question">
                <strong>Numbers of Questins:</strong> {data.questions.length}
              </div>
            </div>
          </div>
        </div>
        {data.questions && <div className="ebs-questions-wrapper">
          <Scrollbars style={{ width: '100%', height: '100%' }}>
            <div className="ebs-questions-section">
              {data.questions.map((list:any,k:number) => 
                <div key={k} className="ebs-question-detail">
                  <div className="ebs-question-header d-flex">
                    <div className="ebs-title-area">
                      <div className="ebs-title">
                        {list.title} {list.required && <span className="ebs-required">*</span>}
                      </div>
                  </div>
                  <div className="ebs-control-panel d-flex align-items-center justify-content-end">
                    <span className="ebs-btn-chart">
                      <i className="material-icons">bar_chart</i>
                    </span>
                    <span className="ebs-btn-chart ebs-bordered">
                      <i className="material-icons">bar_chart</i>
                    </span>
                    <span className={`ebs-custom-radio ${list.active ? 'active' : ''}`}></span>
                  </div>
                </div>
                <div className="ebs-question-form">
                  {list.type === 'radio' && <ListRadio type={list.type} data={list.choices} />}
                  {list.type === 'checkboxes' && <ListRadio type={list.type} data={list.choices} />}
                  {list.type === 'dropdown' && 
                  <div style={{marginBottom: 10}} className="ebs-search-select ebs-form-select">
                    <i className="material-icons">expand_more</i>
                    <select>
                      <option>Please Select</option>
                      {list.choices.map((select: any,key: number) =>
                      <option key={key} value={select}>{select}</option>
                      )}
                    </select>
                  </div>
                  }
                </div>
              </div>
              )}
            </div>
          </Scrollbars>

        </div>}
      </div>
    )
  }
}

export default RightPollsPanel;
