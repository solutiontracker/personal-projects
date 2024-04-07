import * as React from 'react';
import ActiveLink from "components/atoms/ActiveLink";
import Image from 'next/image'

const SponsorPopup = ({ width, onClick, data, eventUrl, labels }) => {

    React.useEffect(() => {
        if (typeof window !== 'undefined') {
            document.getElementsByTagName('body')[0].classList.add('un-scroll');
            return () => {
                document.getElementsByTagName('body')[0].classList.remove('un-scroll');
            }
        }
    }, [])

    return (
        <div className="fixed ebs-popup-container">
            <div className="ebs-popup-wrapper" style={{ maxWidth: width ? width : '980px' }}>
                <span onClick={onClick} className="ebs-close-link"><i className="material-icons">close</i></span>
                <div className="ebs-popup-inner">
                    <div className="row d-flex">
                        <div className="col-sm-4">
                            <figure className="ebs-master-image">
                                {data.logo && data.logo !== '' ? (
                                    <img style={{ width: '90%' }}
                                        src={process.env.NEXT_APP_EVENTCENTER_URL + "/assets/sponsors/" + data.logo}
                                        className="vc_single_image-img attachment-full"
                                        alt="x"
                                    />
                                ) : (
                                    <Image objectFit='contain' layout="fill" 
                                        style={{ width: '90%' }}
                                        src={require('public/img/exhibitors-default.png')}
                                        className="vc_single_image-img attachment-full"
                                        alt="x"
                                    />
                                )}
                            </figure>
                        </div>
                        <div className="col-sm-8">
                            <div className="ebs-container-content">
                                {data.name && <h2>{data.name}</h2>}
                                {data.description && <p dangerouslySetInnerHTML={{ __html: data.description }}></p>}
                                <div className="ebs-social-icons">
                                    {data.website.replace(/^https?:\/\//, "") && <a href={data.website}><span style={{ fontSize: "30px", marginLeft: "-2px" }} data-icon="&#xe0e3;"></span></a>}
                                    {data.facebook.replace(/^https?:\/\//, "") && <a href={data.facebook}><i className="fa fa-facebook" /></a>}
                                    {data.twitter.replace(/^https?:\/\//, "") && <a href={data.twitter}><i className="fa fa-twitter" /></a>}
                                    {data.linkedin.replace(/^https?:\/\//, "") && <a href={data.linkedin}><i className="fa fa-linkedin" /></a>}
                                </div>
                                <p><ActiveLink href={data.url.replace(/^https?:\/\//, "") != "" ? data.url : `/${eventUrl}/sponsors/${data.id}`}>{labels.EVENTSITE_READMORE ? labels.EVENTSITE_READMORE : 'Read more'}</ActiveLink></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default SponsorPopup;

