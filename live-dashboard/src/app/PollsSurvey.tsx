import React, { Component } from 'react';
import RightPollsPanel from "./RightPollsPanel";
import LeftPollPanel from "./LeftPollPanel";
import data from '../data/data.jsx'
type Props = Record<string, never>;
type appState = {
data: any,
activeIndex: any,
}
class PollsSurvey extends Component<Props,appState> {
  state:appState = {
    data: data,
    activeIndex: null
  }
  handleClick = (id: boolean):any => {
    this.setState({
      activeIndex: id
    })
  };
  handleOptions = (type: string,k: number):any => {
    const _data:any = {...this.state.data};
    const _item:any = _data.polls.findIndex((x:any) => x.id === k); 
    const element:boolean =  _data.polls[_item][type];
    _data.polls[_item][type] = !element;
    this.setState({
      data : _data
    })
  };
 render():any {
   const { data, activeIndex } = this.state;
    return (
      <React.Fragment>
      {data &&<div className={`ebs-pollsservey-section ${activeIndex ? 'isActive' : ''}`}>
        <LeftPollPanel
          activeIndex={activeIndex}
          onListClick={this.handleClick.bind(this)}
          onOptionClick={this.handleOptions.bind(this)}
          data={data} />
         {activeIndex && <RightPollsPanel 
          data={data.polls[data.polls.findIndex((x:any) => x.id === activeIndex)]} />}
      </div>}
      </React.Fragment>
    )
  }
}
export default PollsSurvey;
