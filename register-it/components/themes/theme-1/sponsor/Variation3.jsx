import React, { useState } from 'react'
import SponsorPopup from 'components/ui-components/SponsorPopup';
import HeadingElement from 'components/ui-components/HeadingElement';
import Image from 'next/image'

const Variation3 = ({ sponsorsByCategories, labels, eventUrl, siteLabels, settings }) => {
    const [popup, setPopup] = useState(false);
    const [data, setData] = useState('');
    const handleClick = () => {
        setPopup(!popup);
        setData('');
    }

    const bgStyle = (settings && settings.background_color !== "") ? { backgroundColor: settings.background_color} : { backgroundColor: '#f2f2f2' }
    return (
        <div style={bgStyle} className="module-section ebs-default-padding">
            {popup && <SponsorPopup data={data} eventUrl={eventUrl} onClick={handleClick} labels={siteLabels} />}
            <div className="container">
                <HeadingElement dark={false} label={siteLabels.EVENTSITE_SPONSORS} desc={siteLabels.EVENTSITE_SPONSORS_SUB} align={settings.text_align} />
            </div>
            <div className="container">
                {sponsorsByCategories.map((sponsorsCategory, i) => (
                    <React.Fragment key={i}>
                        {sponsorsCategory.name ? <h4 style={{ textAlign: settings.text_align }}> {sponsorsCategory.name}</h4> : ""}
                        <div className={`row d-flex sponsorsv5-wrapper ${settings.text_align === 'left' ? 'justify-content-start' : 'justify-content-center'}`} >
                            {sponsorsCategory.sponsors.map((sponsor, j) => {
                                return (<div className="col-sm-4 col-6 col-md-3 col-lg-3" key={j}>
                                    <figure onClick={() => { setData(sponsor); setPopup(true) }} className="bghover">
                                        {sponsor.logo && sponsor.logo !== '' ? (
                                            <img
                                                src={process.env.NEXT_APP_EVENTCENTER_URL + "/assets/sponsors/" + sponsor.logo}
                                                className="vc_single_image-img attachment-full"
                                                alt="x"
                                            />
                                        ) : (
                                            <Image objectFit='contain' layout="fill"
                                                src={require('public/img/exhibitors-default.png')}
                                                className="vc_single_image-img attachment-full"
                                                alt="x"
                                            />
                                        )}
                                    </figure>
                                </div>)
                            })
                            }
                        </div>
                    </React.Fragment>
                ))}
            </div>
        </div>
    )
}

export default Variation3