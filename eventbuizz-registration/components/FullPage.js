import React from "react";
import Pagination  from "react-bootstrap/Pagination";

import ReactPageScroller from "components/scroller";

export default class FullPage extends React.Component {
  constructor(props) {
    super(props);
    this.state = { currentPage: null };
  }

  handlePageChange = (number) => {
    this.setState({ currentPage: number }); // set currentPage number, to reset it from the previous selected.
  };

  getPagesNumbers = () => {
    const pageNumbers = [];

    for (let i = 1; i <= 4; i++) {
      pageNumbers.push(
        <Pagination.Item key={i} eventKey={i - 1} onSelect={this.handlePageChange}>
          {i}
        </Pagination.Item>
      );
    }

    return [...pageNumbers];
  };

  render() {
    const pagesNumbers = 1;

    return (
      <React.Fragment>
        <ReactPageScroller
          pageOnChange={this.handlePageChange}
          customPageNumber={this.state.currentPage}
        >
          <div
            style={{ backgroundImage: `url(${require("public/img/h1-slide2.jpg")})` }}
            className="component parallax-backgroud"
          >
            <div>{/* <OurProgramv3 /> */}</div>
          </div>
          <div className="component">
            <div>{/* <Video /> */}</div>
          </div>
          <div
            style={{ backgroundImage: `url(${require("public/img/h2-slide3.jpg")})` }}
            className="component"
          >
            Lorem ipsum dolor sit.
          </div>
          <div
            style={{ backgroundImage: `url(${require("public/img/h1-slide1.jpg")})` }}
            className="component"
          >
            Lorem ipsum dolor sit.
          </div>
          <div
            style={{ backgroundImage: `url(${require("public/img/h1-slide2.jpg")})` }}
            className="component"
          >
            Lorem ipsum dolor sit.
          </div>
        </ReactPageScroller>
        <Pagination className="pagination-additional-class tp-bullet" bsSize="large">
          {pagesNumbers}
        </Pagination>
      </React.Fragment>
    );
  }
}
