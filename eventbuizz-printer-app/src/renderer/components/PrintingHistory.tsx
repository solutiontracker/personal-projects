import { useOutletContext } from 'react-router-dom';

const PrintingHistory = () => {
  const { printHistory } = useOutletContext<any>();
  return (
    <>
      <div className="preview-section">
        <div className="preview-section-header">
          <h4>Printing history</h4>
          <div className="search-header">
            <input type="text" placeholder="Search" />
          </div>
        </div>
        <div className="printing-history">
          <div className="table-row header-table">
            <div className="table-box">
              <span>Attendee name</span>
            </div>
            <div className="table-box">
              <span>Badge name</span>
            </div>
            <div className="table-box">
              <span>Printing date</span>
            </div>
            <div className="last table-box" />
          </div>
          {printHistory.map((badge: any) => (
            <div key={badge.date} className="table-row">
              <div className="table-box">
                <span>{badge.attendee}</span>
              </div>
              <div className="table-box">
                <span>{badge.badgeName}</span>
              </div>
              <div className="table-box">
                <span>{badge.date}</span>
              </div>
              <div className="last table-box">
                <img src={require('../img/printer.svg')} alt="" />
              </div>
            </div>
          ))}
        </div>
      </div>
    </>
  );
};

export default PrintingHistory;
