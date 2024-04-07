import React, { Component } from 'react';
import VoteView from './VoteView';
import Verification from './Verification';
import LOGO from '../../assets/img/logo.svg';
import NEM from '../../assets/img/nem.svg';
import CROSS from '../../assets/img/cross.svg';
import SUCCESS from '../../assets/img/success.svg';


export default class ViewPoint extends Component {
  state = {
    step: 1,
    screen: 4,
    vote: ''
  }
   handlePress = (item) => {
    this.setState({step: item})
  }
  handleVote = (vote) => {
    this.setState({
      vote: vote,
      screen: 2
    });
  }
  render() {
    
    return (
      <React.Fragment>
        {this.state.screen === 1 && <div className="viewpoint">
          <div className="leftContainer">
            <img className="viewpointLogo" alt="" src={LOGO}/>
            <h2 className="heading">ELECTION OK21</h2>
            <button className="button">
              <span style={{fontSize: '15px', color: '#fff', marginRight: 6,fontWeight: '400'}}>Vote with</span>
              <img style={{width: '56px', height: '12px'}} alt="" src={NEM} />
            </button>
              <p>Should you experience any problem voting, please contact out hotline via email?</p>
              <p>Should you experience any problem voting, please contact out hotline via email: <a href="mailto:abc@abc.com">abc@abc.com</a> or via telephone: 4697-3676. Our hotline is open on working days between 8.30 am to 4.00 pm. </p>
          </div>
          <div className="rightContainer">
            {this.state.step === 1 && 
              <Verification click={this.handlePress.bind(this)} />
            }
            {this.state.step === 2 && 
              <VoteView vote={this.handleVote.bind(this)} click={this.handlePress.bind(this)} />
            }
          </div>
        </div>}
        {this.state.screen === 2 && <div className="eb-confirmation-box">
            <header className="eb-header">
              <h3>Confirm Vote</h3>
              <span onClick={() => this.setState({screen: 1})} className="btnCancel"><img style={{width: '20px', height: '20px'}} alt="" src={CROSS} /></span>
            </header>
            <div className="eb-databoxy">
              <div className="eb-question">OK21 - Kommunal</div>
              <div className="eb-voteBox">
                <span className="eb-check"><img src={CROSS} alt="" /></span>
                {this.state.vote ? 'For' : 'Against' }
              </div>
            </div>
            <div className="buttonPanel">
          <span className="btnCancel"  onClick={() => this.setState({screen: 1})}>Cancel</span>
            <button onClick={() => this.setState({screen: 3})} className="button">
              <span style={{fontSize: '15px', color: '#fff', marginRight: 10,fontWeight: '700'}}>Confirm Vote</span>
            </button>
          </div>
          </div>}
          {this.state.screen === 3 && <div className="eb-thankyou">
            <img  alt="" src={SUCCESS} />
            <h3>Thank you for the Vote</h3>
            <p>You will be Redirected to <a href="https://www.hk.dk/omhk/sektor/kommunal/ok21">https://www.hk.dk/omhk/sektor/kommunal/ok21</a> in 5 seconds</p>
            </div>}
            {this.state.screen === 4 && <div className="eb-confirmation-box">
              <header className="eb-header">
                <h3>Your session will expire soon</h3>
                <span onClick={() => this.setState({screen: 1})} className="btnCancel"><img style={{width: '20px', height: '20px'}} alt="" src={CROSS} /></span>
              </header>
              <div className="eb-databoxy">
                <div className="eb-expiretime">Your session will expires in <span id="timeExpire">0:59</span></div>
              </div>
              <div className="buttonPanel">
            <span className="btnCancel"  onClick={() => this.setState({screen: 1})}>Cancel</span>
              <button onClick={() => this.setState({screen: 3})} className="button">
                <span style={{fontSize: '15px', color: '#fff', marginRight: 10,fontWeight: '700'}}>Give me more time</span>
              </button>
            </div>
          </div>}
      </React.Fragment>
    )
  }
}
