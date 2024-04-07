import React, { Component } from 'react';
import CROSS from '../../assets/img/cross.svg';
import CHEVRON from '../../assets/img/chevron.svg';

export default class VoteView extends Component {
  state = {
    vote: ''
  }
  handleVote = (vote) => e => {
    e.preventDefault();
    this.setState({
      vote: vote
    });
    
  }
  handleSubmit = () => {
    if (this.state.vote === '') {
      alert('Please Select an Option')
    } else {
      this.props.vote(this.state.vote)
    }
  }
  render() {
    return (
      <div className="questionBox">
        <h2>OK21 - Kommunal</h2>
        <p className="readMore"><a href="$!">Read more about OK21</a></p>
        <p className="captionBox"><strong>Note that you have only one vote. When you have ticked or either yes or no, click on ‘Submit Vote’ and then ‘Confirm Vote’.</strong></p>
        <div className="voteBox">
          <h3>Select 1 option from the below</h3>
          <div onClick={this.handleVote(true)} className="questionLabel">
            <span className="title">For</span>
            <span className="check">
              {this.state.vote && <img src={CROSS} alt="" />}
            </span>
          </div>
          <div onClick={this.handleVote(false)} className="questionLabel">
            <span className="title">Against</span>
            <span className="check">{this.state.vote === false &&  <img src={CROSS} alt="" />}</span>
          </div>
          <div className="buttonPanel">
            <span className="btnCancel"  onClick={() => this.props.click(1)}>Cancel</span>
            <button onClick={this.handleSubmit.bind(this)} className="button">
              <span style={{fontSize: '15px', color: '#fff', marginRight: 10,fontWeight: '700'}}>Submit Vote</span>
              <img style={{width: '7px', height: '11px'}} alt="" src={CHEVRON} />
            </button>
          </div>
        </div>
      </div>
    )
  }
}
