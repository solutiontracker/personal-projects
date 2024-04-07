import React from 'react'
import SliderBanner from './components/SliderBanner';

const Variation1 = ({ event, banner, countdown, regisrationUrl }) => {

	const WrapperLayout = (props) => {
		if (props.slides && Number(props.slides.video_type) === 1) {
			return (
				<div style={{ backgroundImage: `url(${process.env.NEXT_APP_EVENTCENTER_URL + props.slides.image})`, backgroundPosition: '50% 0' }} className="background parallax-backgroud ebs-no-opacity">
					{props.slides.url ? <a href={props.slides.url} target="_blank" rel="noreferrer">
						{props.children}
					</a >: props.children}
				</div>
			);
		} else {
			return (
				<div style={{ backgroundPosition: '50% 0' }} className="background parallax-backgroud ebs-no-opacity"
					>
					{props.slides.url ? <a href={props.slides.url} target="_blank" rel="noreferrer">
						{props.children}
					</a >: props.children}
				</div>
			);
		}

	}

	return (
		<div className="main-slider-wrapper ebs-transparent-box ebs-sortable-banner">
			{banner && <SliderBanner
				fullscreen
			>
				{banner.map((slides, i) =>
					<div key={i} className="slide-wrapper">
						<WrapperLayout
							slides={slides}
						>
							{Number(slides.video_type) === 2 &&
								<div className="video-fullscreen">
									<video autoPlay playsInline muted loop src={`${process.env.NEXT_APP_EVENTCENTER_URL}/${slides.image}`} type="video/mp4"></video>
								</div>}
							<div className="caption-wrapp">
								<div className="col-12 align-items-center d-flex inner-caption-wrapp">
								</div>
							</div>
						</WrapperLayout>
					</div>
				)}
			</SliderBanner>}
		</div>
	)
}

export default Variation1

