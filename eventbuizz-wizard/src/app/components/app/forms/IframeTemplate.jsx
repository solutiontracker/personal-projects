import * as React from 'react';

const IframeTemplate = ({ url,onClick }) => {
  return (
    <div className="wrapper-import-file-wrapper iframe-email-full-width">
      <div className="wrapper-import-file">
        <span onClick={onClick} className="btn-close">
          <i className="material-icons">close</i>
        </span>
          <iframe title="Email Builder" src={url} frameBorder="0" width="100%" height="100%"></iframe>
        </div>
      </div>
  );
}

export default IframeTemplate;

