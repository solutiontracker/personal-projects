import React, { useEffect, useState } from 'react';
import Slider from "react-slick";
import ExhibitorPopup from 'components/ui-components/ExhibitorPopup';
import HeadingElement from 'components/ui-components/HeadingElement';
import Image from 'next/image'

const Variation8 = ({ exhibitorsByCategories, labels, eventUrl, siteLabels, settings }) => {
	const [popup, setPopup] = useState(false);
	const [data, setData] = useState('');
	const [clientXonMouseDown, setClientXonMouseDown] = useState(null);
	const [clientYonMouseDown, setClientYonMouseDown] = useState(null);

	const handleClick = () => {
		setPopup(!popup);
		setData('');
	}
	var settingsslider = {
		dots: false,
		infinite: exhibitors?.length >= 5 ? true : false,
		arrows: false,
		speed: 5000,
		margin: 0,
		slidesToShow: 5,
		autoplay: true,
		autoplaySpeed: 0,
		slidesToScroll: 1,
		swipeToSlide: false,
		cssEase: 'linear',
		pauseOnHover: false,
		pauseOnFocus: false,
		responsive: [
			{
				breakpoint: 1024,
				settings: {
					slidesToShow: 3,
					slidesToScroll: 3,
					infinite: true,
					speed: 500,
					swipeToSlide: true,
					autoplaySpeed: 5000,
					dots: false,

				}
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 2,
					infinite: true,
					speed: 500,
					swipeToSlide: true,
					autoplaySpeed: 5000,
					initialSlide: 2
				}
			},

		]
	};
	const [exhibitors,] = useState(exhibitorsByCategories.reduce((ack, item) => {
		const newExhibitors = item.exhibitors.filter(exhibitor => !ack.some(existing => existing.id === exhibitor.id));
		return [...ack, ...newExhibitors];
	}, []));
	useEffect(()=>{
		console.log(exhibitors)
	},[])

	const handleOnMouseDown = (e) => {
		setClientXonMouseDown(e.clientX)
		setClientYonMouseDown(e.clientY)
		e.preventDefault() // stops weird link dragging effect
	}

	const handleOnClick = (e, exhibitor) => {
		e.stopPropagation()
		if (clientXonMouseDown !== e.clientX ||
			clientYonMouseDown !== e.clientY) {
			// prevent link click if the element was dragged
			e.preventDefault()
		} else {
			setData(exhibitor);
			setPopup(true)
		}
	}
	const bgStyle = (settings && settings.background_color !== "") ? { backgroundColor: settings.background_color } : { backgroundColor: '#f2f2f2' }

	return (
		<div style={bgStyle} className="module-section ebs-colored-logo-grid ebs-default-padding">
			{popup && <ExhibitorPopup data={data} eventUrl={eventUrl} onClick={handleClick} labels={siteLabels} />}
			<div className="container">
				<HeadingElement dark={false} label={siteLabels.EVENTSITE_EXHIBITORS} desc={siteLabels.EVENTSITE_EXHIBITORS_SUB} align={settings.text_align} />
			</div>
			<div className="container">
				<div className="edgtf-carousel-holder">
					<div
						className="edgtf-carousel edgtf-slick-slider-navigation-style"
					>
						<Slider {...settingsslider}>
							{exhibitors.map((exhibitor, i) => {
								return (
									<div className="edgtf-carousel-item-holder ebs-carousel-image-holder" key={i}>
										<span
											className="edgtf-carousel-first-image-holder ebs-carousel-image-box"
										>
											{
												exhibitor.logo && exhibitor.logo !== "" ? (
													<img
														onMouseDown={e => handleOnMouseDown(e)}
														onClick={e => handleOnClick(e, exhibitor)}
														src={

															process.env.NEXT_APP_EVENTCENTER_URL +
															"/assets/exhibitors/" +
															exhibitor.logo
														}
														alt="Client 11"
													/>
												) : (
													<Image objectFit='contain' layout="fill"
														src={require('public/img/exhibitors-default.png')}
														alt="x"
													/>
												)
											}
										</span>
									</div>
								);
							})}
						</Slider>
					</div>
				</div>
			</div>
		</div>
	)
}

export default Variation8
