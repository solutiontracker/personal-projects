import {ReactSVG} from "react-svg";
import React from "react";
import {Translation} from "react-i18next";

const OptionsMenu = (props) => {

    function handleDropdown(e){
        e.preventDefault()
        if (e.target.classList.contains('active')) {
            e.target.classList.remove('active')
        } else {
            let query = document.querySelectorAll('.btn_addmore')
            for (let i = 0; i < query.length; ++i) {
                query[i].classList.remove('active')
            }
            e.target.classList.add('active')
        }
    }

    return (
        <Translation>
            {
                t => (
                    <div className="grid-8">
                        <div className="parctical-button-panel button-panel-list">
                            <div className="dropdown">
                                <span onClick={(e) => handleDropdown(e)} className="btn btn_dots">
                                        <ReactSVG style={{pointerEvents: 'none'}} wrapper="span" className='icons' alt="" src={require("img/ico-dots-gray.svg")} />
                                </span>
                                <div className="dropdown-menu">
                                    <button className="dropdown-item" onClick={(e) => props.handleChange(e, props.order, "send_offer")}>
                                        {t('G_SEND_OFFER')}
                                    </button>
                                    <button className="dropdown-item" onClick={(e) => props.handleChange(e, props.order, "delete_order")}>
                                        {t('G_DELETE')}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                )
            }
        </Translation>

    )
}

export default OptionsMenu