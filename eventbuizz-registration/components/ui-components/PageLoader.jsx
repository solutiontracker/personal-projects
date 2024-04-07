import * as React from 'react';

const PageLoader = ({ className, fixed, title, description }) => {
    React.useEffect(() => {
       setTimeout(() => {
        window.scrollTo(0, 0);
       }, 50);
        
    }, [])
    return (
        <div id="loader-wrapper" className={`${className ? className : ''} ${fixed && 'popup-fixed'}`}>
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

export default PageLoader;

