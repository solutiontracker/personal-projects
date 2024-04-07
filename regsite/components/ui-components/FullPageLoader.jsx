import * as React from 'react';

const FullPageLoader = ({ className, fixed, title, description }) => {
    return (
        <div id="full-loader-wrapper" className={`${className ? className : ''} ${fixed && 'popup-fixed'}`}>
            {className ? (
                <div className="wrapper_laoder">
                    {title && <h2>{title}</h2>}
                    {description && <p>{description} </p>}
                    <div id="loader"></div>
                </div>
            ) : (
                <div id="loader"></div>
            )}
        </div>
    );
}

export default FullPageLoader;

