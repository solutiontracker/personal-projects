import React from "react";
import Img from 'react-image';
import { connect } from 'react-redux';
import { Translation } from "react-i18next";

class Child extends React.Component {

  handleDrowon = e => {
    e.preventDefault();
    if (e.target.classList.contains('active')) {
      e.target.classList.remove('active');
    } else {
      var query = document.querySelectorAll('.btn_addmore');
      for (var i = 0; i < query.length; ++i) {
        query[i].classList.remove('active');
      }
      e.target.classList.add('active');
    }
  }

  render() {
    return (
      <Translation>
        {(t) => (
          <div className="practical-data-wrapper">
            {this.props.subItems.map((item, index) => (
              <div
                key={item.id}
                className="practical-data-list-wrapp"
              >
                <div className="practical-data-list">
                  <div className="form-item-wrapper row d-flex">
                    <div className="col-5">
                      <h4>{`${item.detail.item_name} (${item.item_number})`}</h4>
                      <p dangerouslySetInnerHTML={{ __html: item.detail.description }}></p>
                      {item.link_to !== "none" && (
                        <p><b>{t("BILLING_ITEMS_LINK_TO")} {item.link_to.replace("_", " ")}:</b>{item.detail.link_to_name}</p>
                      )}
                    </div>
                    <div className="col-4 text-right">
                      {this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 1 && (
                        <h4>{item.priceDisplay}</h4>
                      )}
                      <p>
                        {
                          (() => {
                            if (item.remaining_tickets === "Unlimited")
                              return t("BILLING_ITEMS_UNLIMITED");
                            else if (Number(item.remaining_tickets) === 0)
                              return t("BILLING_ITEMS_SOLD_OUT")
                            else
                              return item.remaining_tickets + " " + t('BILLING_ITEMS_LEFT');
                          })()
                        }
                      </p>
                    </div>
                    <div className="col-3">
                      {this.props.displayPanel && (
                        <div style={{ marginRight: '-4px' }} className="practical-edit-panel">
                          {!this.props.largeScreen && (
                            <span className="btn_delete" onClick={this.props.updateStatus(item.id, (item.status === 1 ? 0 : 1))}><i className="icons"><Img style={{ maxWidth: "18px" }} src={require(`img/ico-feathereye${item.status !== 1 ? '-alt' : ''}.svg`)} /></i></span>
                          )}
                          {!this.props.largeScreen && (
                            <span
                              onClick={this.props.handleEdit(
                                item.type,
                                this.props.form_container,
                                item,
                                item.edit
                              )}
                              className="btn_edit"
                            >
                              <i className="icons"><Img
                                src={require("img/ico-edit.svg")} /></i>
                            </span>
                          )}
                          {!this.props.largeScreen && (
                            <span
                              onClick={this.props.removeItem(
                                item.id,
                                item.delete
                              )}
                              className="btn_delete"
                            >
                              <i className="icons"><Img
                                src={require("img/ico-delete.svg")} /></i>
                            </span>
                          )}
                        </div>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </Translation>
    );
  }
}

function mapStateToProps(state) {
  const { event } = state;
  return {
    event
  };
}

export default connect(mapStateToProps)(Child);