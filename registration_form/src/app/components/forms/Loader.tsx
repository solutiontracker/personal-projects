import * as React from 'react';

type props = {
    className?: any;
    fixed?: any;
    title?: any;
    description?: any;
}

const Loader = ({ className, fixed, title, description }: props) => {
    React.useEffect(() => {
    if (window.location.pathname.includes('/embed')) {
    setTimeout(() => {
        const scrollTo = document?.getElementsByClassName('wrapper-box')[0];
        if (scrollTo !== undefined && scrollTo !== null) {
          scrollTo.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
        }
      }, 500);
    }
    }, [])
    return (
        <div id="loader-wrapper" className={`${className && className} ${fixed && 'popup-fixed'}`}>
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

export default Loader;

