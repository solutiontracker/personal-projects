import * as React from 'react';
import Videopopup from "components/Videopopup";
import { PortalWithState } from "react-portal";
import Image from 'next/image'

class Variation1 extends React.Component {
    render() {
        const video_data = this.props.videos[0];
        return (
            <React.Fragment>
                {video_data !== undefined && (
                    <div style={{ padding: '100px 0' }} className="edgtf-parallax-section-holder ebs-bg-holder">
                        <div className="container">
                            <div className="row d-flex align-items-center">
                                <div className="col-md-6">
                                    <div className="edgtf-video-button">
                                        <PortalWithState closeOnOutsideClick closeOnEsc>
                                            {({ openPortal, closePortal, isOpen, portal }) => (
                                                <React.Fragment>
                                                    <span className="edgtf-video-button-play" onClick={openPortal} >
                                                        <span className="edgtf-video-button-image">
                                                            <img itemProp="image" src={video_data && video_data.thumnail && video_data.thumnail !== '' ? process.env.NEXT_APP_EVENTCENTER_URL + '/assets/videos/' + video_data.thumnail : "https://xpo.qodeinteractive.com/wp-content/uploads/2016/12/h1-image1.jpg"} alt="" />
                                                        </span>
                                                        <span className="edgtf-video-button-wrapper">
                                                            <span className="edgtf-video-button-wrapper-inner">
                                                                <i className="fa fa-play-circle" aria-hidden="true"></i>
                                                            </span>
                                                        </span>
                                                    </span>
                                                    {portal(
                                                        <Videopopup
                                                            url={video_data && video_data.type && Number(video_data.type) !== 5 ? video_data.URL : process.env.NEXT_APP_EVENTCENTER_URL + '/assets/videos/' + video_data.video_path}
                                                            onClose={closePortal} />
                                                    )}
                                                </React.Fragment>
                                            )}
                                        </PortalWithState>
                                    </div>
                                </div>
                                <div className="col-md-6">
                                    <div style={{ color: '#ffffff', padding: '0 3% 19px 8%' }} className="edgtf-elements-holder-item-inner">
                                        <div className="edgtf-title-section-holder">
                                            <h2 style={{ color: '#ffffff' }} className="edgtf-title-with-dots edgtf-appeared">{video_data && video_data.info ? Object.keys(video_data.info)[0] : 'Event highlights'} </h2>
                                            {/* <span className="edge-title-separator edge-enable-separator"></span>
                                            <h6 style={{ color: '#ffffff' }} className="edgtf-section-subtitle">Lorem ipsum dolor sit amet, ut vidisse commune scriptorem. Ad his suavita tevi disse </h6> */}
                                        </div>
                                        {/* <p>Alienum phaedrum torquatos nec eu, vis detraxit periculis ex, nihil expetendis in mei. Mei an pericula euripidis, hinc partem ei est. Eos ei nisl graecis, vix aperiri consequat an. Eius lorem tincidunt vix at, vel pertinax sensibus id, error epicurei mea et. Mea facilisis urbanitas. moderatius id. Vis ei rationibus definiebas, eu qui purto zril laoreet. Ex error omnium interpretaris pro, alia illum ea vim.</p> */}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </React.Fragment>
        );
    }
}

export default Variation1;
