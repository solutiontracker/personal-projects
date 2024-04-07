import React, { useState } from 'react'
import moment from 'moment'
import ActiveLink from "components/atoms/ActiveLink";
import Image from 'next/image'

const ProgramItem = ({ program, eventUrl, labels, agendaSettings }) => {
    const [showText, setShowText] = useState(program.description.length > 450 ? false : true);
    return (
        <div className="ebs-program-child">
            <div className="row d-flex">
                <div className="col-lg-2">
                    {parseInt(agendaSettings.agenda_display_time) === 1 && parseInt(program.hide_time) === 0 && <div className='ebs-program-date'>{moment(new Date(`${program.date} ${program.start_time}`)).format('HH:mm')} - {moment(new Date(`${program.date} ${program.end_time}`)).format('HH:mm')}</div>}
                </div>
                <div className="col-lg-10">
                    <div className="ebs-program-content">
                        {program.topic && <h3>{program.topic}</h3>}
                        {program.location && <div className="ebs-program-location">
                            <i className="fa fa-map-marker" /> {program.location}
                        </div>}
                        {program.program_tracks.length > 0 && <div className="ebs-tracks-program">
                            {program.program_tracks.map((track, i) => (
                                <span key={i} style={{ backgroundColor: `${track.color ? track.color : '#000'}` }}>{track.name}</span>
                            ))}
                        </div>}
                        {program.description && <div className="ebs-description">
                            <div className={`ebs-contain ${!showText ? 'truncate' : ''}`} dangerouslySetInnerHTML={{ __html: program.description }} />
                            {program.description.length > 450 && <span className='ebs-more' onClick={() => { setShowText(!showText) }}>{showText ? labels.EVENTSITE_READLESS : labels.EVENTSITE_READMORE}</span>}
                        </div>}

                        {program.program_speakers.length > 0 && <div className="row d-flex ebs-program-speakers">
                            {program.program_speakers?.map((speakers, o) =>
                                <div style={{ animationDelay: 50 * o + 'ms' }} key={o} className="col-md-3 col-sm-4 col-lg-2 col-6 ebs-speakers-box ebs-animation-layer">
                                    <ActiveLink href={`/${eventUrl}/speakers/${speakers.id}`}>
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
                                                    style={{ maxWidth: '90%' }}
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
                                    </ActiveLink>
                                </div>
                            )}
                        </div>}
                    </div>
                </div>
            </div>
        </div>
    )
}

export default ProgramItem