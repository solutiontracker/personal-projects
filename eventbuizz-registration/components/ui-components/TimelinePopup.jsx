import * as React from 'react';
import Image from 'next/image'

const TimelinePopup = ({ width, onClick, data }) => {

	React.useEffect(() => {
		if (typeof window !== 'undefined') {
			document.getElementsByTagName('body')[0].classList.add('un-scroll');
			return () => {
				document.getElementsByTagName('body')[0].classList.remove('un-scroll');
			}
		}
	}, []);

	return (
		<div style={{ zIndex: 9999 }} className="fixed ebs-popup-container">
			<div className="ebs-popup-wrapper" style={{ maxWidth: width ? width : '980px' }}>
				<span onClick={onClick} className="ebs-close-link"><i className="material-icons">close</i></span>
				<div className="ebs-popup-inner ebs-popup-timeline">
					{data.program_workshop && <h4 className="workkshop-box">{data.program_workshop}</h4>}
					<div className="title">{data.topic}</div>
					{data.program_tracks && <div className="tracks">
						{data.program_tracks.map((track, k) =>
							<span style={{ backgroundColor: `${track.color ? track.color : '#000'}` }} key={k}>{track.name}</span>
						)}
					</div>}
					<div className="ebs-bottom-wrapp">
						<div className="location"><i className="material-icons">place</i> {data.location}</div>
						{data.start_time && data.end_time &&
							<div className="time"><i className="material-icons">access_time</i> {data.start_time} - {data.end_time}</div>
						}
						{data.video > 0 && <div className="video"><i className="material-icons">play_circle</i> {data.video}</div>}
					</div>
					{data.description && <div style={{ padding: '20px 0' }} dangerouslySetInnerHTML={{ __html: data.description }} />}
					{data.program_speakers.length > 0 && <div className="row d-flex ebs-program-speakers">
						{data.program_speakers.map((speakers, i) =>
							<div key={i} style={{ animationDelay: 50 * i + 'ms' }} className="col-md-3 col-sm-4 col-lg-2 col-6 ebs-speakers-box ebs-animation-layer">
								<span className="gallery-img-wrapper-square">
									{speakers.image && speakers.image !== "" ? (
										<img
											onLoad={(e) => e.target.style.opacity = 1}
											src={
												process.env.NEXT_APP_EVENTCENTER_URL +
												"/assets/attendees/" +
												speakers.image
											} alt="" />
									) : (
										<Image objectFit='contain' layout="fill"
											onLoad={(e) => e.target.style.opacity = 1}
											src={
												require("public/img/user-placeholder.jpg")
											} alt="" />
									)}

								</span>
								<h4>{speakers.first_name} {speakers.last_name}</h4>
								{speakers.info &&
									(speakers.info.company_name || speakers.info.title) && (
										<div className="edge-info-row">
											<p className="info">
												{speakers.info.title &&
													`${speakers.info.title},`}{" "}
												{speakers.info.company_name &&
													speakers.info.company_name}
											</p>
										</div>
									)}
							</div>)}
					</div>}
				</div>
			</div>
		</div>
	);
}

export default TimelinePopup;

