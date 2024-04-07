import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import Img from 'react-image';
import { Translation } from "react-i18next";

export default class PageCrashed extends Component {
    render() {
        return (
            <Translation>
                {
                    t =>
                        <div className="page-crashed">
                            <div className="inner-page-content">
                                <Img src={require("images/crashed.svg")} />
                                <h1>Crash Heading</h1>
                                <p>Crash sub Heading</p>
                                <Link to='/' className="btn">Home</Link>
                            </div>
                        </div>
                }
            </Translation>
        )
    }
}
