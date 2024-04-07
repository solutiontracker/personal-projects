import * as React from 'react';

const AlertMessage = ({ className ,icon,title, content}) => {
  function createMarkup() {
    return { __html: content };
  }
  return (
    <div className={`${className} custom-messages`}>
      <span className="ico-close"><i className="material-icons">{icon ? icon : 'close'}</i></span>
      {title && <h5>{title}</h5>}
      {content && <div className="content-alert" dangerouslySetInnerHTML={createMarkup()} />}
    </div>
  );
}

export default AlertMessage;

