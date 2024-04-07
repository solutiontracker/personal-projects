import React, { Component } from "react";
import DateTime from "@/app/forms/DateTime";

export default class FilterBy extends Component {
  render() {
    return (
      <div className="wrapper-popup">
        <div className="wrapper-sidebar">
          <header>
            <h3>Filter by:</h3>
          </header>
          <div className="bottom-content">
            <div className="package-wrapper">
              <h4>
                Date <i className="material-icons">keyboard_arrow_down</i>
              </h4>
              <div className="row">
                <div className="col-6">
                  <h6>From</h6>
                  <DateTime
                    fromDate={new Date()}
                    label="From"
                    required={true}
                  />
                </div>
                <div className="col-6">
                  <h6>To</h6>
                  <DateTime fromDate={new Date()} label="To" required={true} />
                </div>
              </div>
            </div>
            <div className="package-wrapper">
              <h4>
                Email <i className="material-icons">keyboard_arrow_down</i>
              </h4>
              <div className="sort-list">
                <span><i className="material-icons">check_box_outline_blank</i>Not registered</span>
                <span><i className="material-icons">check_box</i>Not attending </span>
              </div>
            </div>
            <div className="package-wrapper">
              <h4>
                Status signup <i className="material-icons">keyboard_arrow_down</i>
              </h4>
              <div className="sort-list">
                <span><i className="material-icons">check_box_outline_blank</i>Registered</span>
                <span><i className="material-icons">check_box_outline_blank</i>NO RESPONSE</span>
                <span><i className="material-icons">check_box_outline_blank</i>CANCELLED</span>
                <span><i className="material-icons">check_box</i>Not attending </span>
              </div>
            </div>
          </div>
          <div className="bottom-component-panel clearfix">
            <span className="btn_close float-left">
              <i className="material-icons">clear</i> Reset all
            </span>
            <button data-type="save" className="btn btn btn-save">
              Cancel
            </button>
            <button data-type="save-next" className="btn btn-save-next">
             Apply
            </button>
          </div>
        </div>
      </div>
    );
  }
}
