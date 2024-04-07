import * as React from 'react';
import { Translation } from "react-i18next";
import Img from "react-image";
import { OverlayTrigger, Tooltip } from "react-bootstrap";
import { NavLink } from 'react-router-dom';

const DashboardTopBar = ({ className }) => {
    return (
        <Translation>
            {t => (
                <nav className="dashboard-nav" style={{display:'none'}}>
                    <ul className="d-inline">
                        <li className="d-inline">
                            <NavLink to="/">
                                <i className="icons">
                                    <Img src={require("img/ico-home.svg")} />
                                </i>
                                {t("EL_HOME")}
                            </NavLink>
                        </li>
                        <OverlayTrigger
                            overlay={<Tooltip>{t("R_COMING_SOON")}</Tooltip>}
                        >
                            <li className="d-inline">
                                <a href="#!">
                                    <i className="icons">
                                        <Img src={require("img/ico-chart-gray.svg")} />
                                    </i>
                                    {t("EL_DASHBOARD")}
                                </a>
                            </li>
                        </OverlayTrigger>
                        <OverlayTrigger
                            overlay={<Tooltip>{t("R_COMING_SOON")}</Tooltip>}
                        >
                            <li className="d-inline">
                                <a href="#!">
                                    <i className="icons">
                                        <Img src={require("img/ico-setting.svg")} />
                                    </i>
                                    {t("EL_SETTINGS")}
                                </a>
                            </li>
                        </OverlayTrigger>
                    </ul>
                </nav>
            )}
        </Translation>
    );
}

export default DashboardTopBar;

