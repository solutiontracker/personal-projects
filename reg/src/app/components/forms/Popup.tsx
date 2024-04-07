import * as React from 'react';

interface Props {
    onClick?: any,
    title?: any,
    width?: any,
    children?: any
}

const Popup: React.FC<Props> = (props) => {
    return (
        <div id="loader-wrapper" className="fixed ebs-popup-container">
            <div className="ebs-popup-wrapper" style={{ maxWidth: props?.width ? props?.width : '550px' }}>
                <span onClick={props.onClick} className="ebs-close link"><i className="material-icons">close</i></span>
                {props.title && <header className="ebs-header">
                    <h3 className='link'>{props.title}</h3>
                </header>}
                {props.children}
            </div>
        </div>
    );
}

export default Popup;

