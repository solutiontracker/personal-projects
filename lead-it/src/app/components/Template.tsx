import React from 'react'
import Header from './Header';
import Sidemenu from './Sidemenu';
type Props = {
  children:
  | JSX.Element
  | JSX.Element[]
  | string
  | string[];
};
const Template = ({ children }: Props) => {
  return (
	<>
    <Header />
    <main className="ebs-main-section ebs-main-section-template" role="main">
      <div className="container-fluid">
        <div className="row d-flex">
          <div className="ebs-left-content">
            <Sidemenu />
          </div>
          <div className="ebs-right-content">
            <div style={{background: 'none'}} className="wrapper-box">{children}</div>
          </div>
        </div>
      
      </div>
    </main>
  </>
  )
}

export default Template;