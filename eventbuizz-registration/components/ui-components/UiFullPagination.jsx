import React from "react";
import Pagination from "react-bootstrap/Pagination";

const UiFullPagination = ({
  total,
  perPage,
  currentPage,
  onPageChange,
  fetchingData,
}) => {
  const [pageArray, setPageArray] = React.useState([]);

  const totalPages = Math.ceil(total / perPage);

  React.useEffect(() => {
    var pageArr = [];
    if (totalPages > 1) {
      if (totalPages <= 5) {
        var i = 1;
        while (i <= totalPages) {
          pageArr.push(i);
          i++;
        }
      } else if (currentPage < 5) {
        pageArr = [1, 2, 3, 4, 5];
      } else {
        pageArr = [
          currentPage - 2,
          currentPage - 1,
          currentPage,
          currentPage + 1,
          currentPage + 2,
        ];
      }
    }
    setPageArray(pageArr);
  }, [currentPage]);
  if (totalPages <= 1) {
    return <React.Fragment></React.Fragment>;
  }
  return (
    <Pagination>
      <Pagination.Item
        disabled={fetchingData || currentPage === 1 ? true : false}
        onClick={() => {
          onPageChange(1);
        }}
      >
        First
      </Pagination.Item>
      <Pagination.Prev
        disabled={fetchingData || currentPage === 1 ? true : false}
        onClick={() => {
          onPageChange(currentPage - 1);
        }}
      />
      {pageArray.map((link, index) => {
        return (
          <Pagination.Item
            key={index}
            disabled={fetchingData}
            active={link === currentPage ? true : false}
            onClick={() => {
              onPageChange(link);
            }}
          >
            {link}
          </Pagination.Item>
        );
      })}
      <Pagination.Next
        disabled={fetchingData || currentPage === totalPages ? true : false}
        onClick={() => {
          onPageChange(currentPage + 1);
        }}
      />
      <Pagination.Item
        disabled={fetchingData || currentPage === totalPages ? true : false}
        onClick={() => {
          onPageChange(totalPages);
        }}
      >
        Last
      </Pagination.Item>
    </Pagination>
  );
};

export default UiFullPagination;
