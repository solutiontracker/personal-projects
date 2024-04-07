import React, { ReactElement, FC } from "react";
import Event from '@/src/app/components/event/interface/Event';

const AutoRegistrationSuccess: FC<any> = (): ReactElement => {
    return (
        <React.Fragment>
            <div className="registration-success">
                <div className="header-area">
                    <img src={require('@/src/img/ico-success.svg')} alt="" />
                    <h3>Registration successful</h3>
                    <p>Thank you for your registration!</p>
                </div>
            </div>
        </React.Fragment>
    );
};

export default AutoRegistrationSuccess;