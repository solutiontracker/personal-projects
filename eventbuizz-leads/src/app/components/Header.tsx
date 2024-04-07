import React from "react";
import Image from 'next/image';

const Header = () => {
  return (
    <header className="header">
      <div className="container-fluid">
        <div className="row d-flex align-items-center">
					<div className="left-header">
						<div className="d-flex align-items-center">
							<Image src={require('@/app/assets/img/logo-sm.svg')} alt="" width="35" height="35"  />
							<span style={{paddingLeft: '10px', fontWeight: '600', fontSize: '22px'}} className="ebs-title">Leads</span>
						</div>
					</div>
          <div className="right-header">
						<div className="row">
							<div className="col-6"></div>
								<div className="col-6 d-flex justify-content-end ebs-header-panel">
									<a href="" className="btn btn-settings">
										<span className="material-symbols-outlined">settings</span>
									</a>
									<a href="" className="btn btn-notification">
										<span className="ebs-mark"></span>
										<span className="material-symbols-outlined">notifications</span>
									</a>
									<ul className="main-navigation">
										<li>
											<Image src={require('@/app/assets/img/eng.svg')} alt="" width="18" height="10"  /> English <i className="material-icons">expand_more</i>
											<ul>
												<li>
													<a href=""> English</a>
												</li>
												<li>
													<a href=""> Danish</a>
												</li>
											</ul>
										</li>
									</ul>
									<a href="" className="btn btn-icon">
										EA
									</a>
								</div>
						</div>
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header;
