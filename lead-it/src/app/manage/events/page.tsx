import Image from 'next/image'
import Dropdown from '@/app/components/DropDown';

export default function Dashboard() {
  return (
   <>
    <header className="header">
      <div className="container">
        <div className="row bottom-header-elements">
          <div className="col-8">
          </div>
          <div className="col-4 d-flex justify-content-end">
            <ul className="main-navigation">
              <li>Irfan Danish <i className="material-icons">expand_more</i>
              <ul>
                <li><a href="">My account</a></li>
                <li><a href=""> Change password</a></li>
                <li><a href="">Logout</a></li>
              </ul>
              </li>
              <li>English <i className="material-icons">expand_more</i>
              <ul>
                <li><a href="">English</a></li>
                <li><a href=""> Danish</a></li>
              </ul>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </header>
    <main className="main-section" role="main">
      <div className="container">
        <div className="wrapper-box">
          <div className="container-box main-landing-page">
            <div className="top-landing-page">
              <div className="row d-flex">
                <div className="col-4">
                  <div className="logo">
                    <a href="">
                      <Image src={require('@/app/assets/img/logo.svg')} alt="" width="200" height="29" className='logos' />
                    </a>
                  </div>
                </div>
                <div className="col-8">
                  <div className="right-top-header">
                    <input className="search-field" name="query" type="text" placeholder="Search" value="" />
                    <label className="label-select-alt">
                      <Dropdown 
                        label="Filter by"
                        listitems={[
                          { id: 'active_future', name: "Active and future events" },
                          { id: 'active', name: "Active events" },
                          { id: 'future', name: "Future events" },
                          { id: 'expired', name: "Expired events" },
                          { id: 'name', name: "All events" }
                        ]}
                      />
                    </label>
                    <label className="label-select-alt">
                      <Dropdown 
                        label="Filter by"
                        listitems={[
                          { id: 'active_future', name: "Active and future events" },
                          { id: 'active', name: "Active events" },
                          { id: 'future', name: "Future events" },
                          { id: 'expired', name: "Expired events" },
                          { id: 'name', name: "All events" }
                        ]}
                      />
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div className="main-data-table">
              <div className="ebs-data-table">
                <div className="d-flex align-items-center ebs-table-header">
                  <div className="ebs-table-box ebs-box-1"><strong>Event Logo</strong></div>
                  <div className="ebs-table-box ebs-box-2"><strong>Event Name <i className="material-icons">unfold_more</i></strong></div>
                  <div className="ebs-table-box ebs-box-3"><strong>Event Date</strong></div>
                  <div className="ebs-table-box ebs-box-4"><strong>Created by</strong></div>
                  <div className="ebs-table-box ebs-box-4"><strong>Organized by</strong></div>
                  <div className="ebs-table-box ebs-box-4"><strong>Tickets Left</strong></div>
                  <div className="ebs-table-box ebs-box-5"><strong>Sold Tickets</strong></div>
                  <div className="ebs-table-box ebs-box-5"><strong>Total Tickets</strong></div>
                  <div style={{paddingRight: 0}} className="ebs-table-box ebs-box-5"><strong>My Sold Tickets</strong></div>
                  <div style={{textAlign: 'right'}}  className="ebs-table-box ebs-box-4 text-right"><strong style={{justifyContent: 'flex-end'}}>My Revenue</strong></div>
                </div>
                {[...Array(10)].map(item => 
                <div key={item} className="d-flex align-items-center ebs-table-content">
                  <div className="ebs-table-box ebs-box-1">
                    <Image src={require('@/app/assets/img/logo-placeholder.png')} alt="" width={100} height={34} />
                  </div>
                  <div className="ebs-table-box ebs-box-2"><p>Parent event leadevent 2.</p></div>
                  <div className="ebs-table-box ebs-box-3"><p>30/09/23 - 02/10/23</p></div>
                  <div className="ebs-table-box ebs-box-4"><p>Eventorg</p></div>
                  <div className="ebs-table-box ebs-box-4"><p>Mr Creig</p></div>
                  <div className="ebs-table-box ebs-box-4"><p>3</p></div>
                  <div className="ebs-table-box ebs-box-5"><p>5</p></div>
                  <div className="ebs-table-box ebs-box-5"><p>20</p></div>
                  <div style={{paddingRight: 0}} className="ebs-table-box ebs-box-5"><p>25</p></div>
                  <div style={{textAlign: 'right'}}  className="ebs-table-box ebs-box-4 text-right"><p>43128DKK</p></div>
                </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
   </>
  )
}
