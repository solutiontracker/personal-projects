import React from "react";
import Pagination from "react-bootstrap/Pagination";

const UiPagination = ({
  currentPage,
  onPageChange,
  fetchingData,
  total,
  perPage,
}) => {
  const totalPages = Math.ceil(total / perPage);
  if (totalPages <= 1) {
    return <React.Fragment></React.Fragment>;
  }
  return (
    <Pagination>
      <Pagination.Item
        disabled={fetchingData || currentPage === 1 ? true : false}
        onClick={() => {
          onPageChange(currentPage - 1);
        }}
      >
        Prev
      </Pagination.Item>
      <Pagination.Item
        disabled={fetchingData || currentPage === totalPages ? true : false}
        onClick={() => {
          onPageChange(currentPage + 1);
        }}
      >
        Next
      </Pagination.Item>
    </Pagination>
  );
};

export default UiPagination;
