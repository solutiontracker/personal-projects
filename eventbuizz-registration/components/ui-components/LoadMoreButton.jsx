import React from 'react'

const LoadMoreButton = ({loadingLabel,loading, page, onPageChange}) => {
  return (
    <div className="container pb-5 p-0 pt-5 text-center">
        <button
        className="edgtf-btn edgtf-btn-medium edgtf-btn-outline edgtf-btn-custom-hover-bg edgtf-btn-custom-border-hover edgtf-btn-custom-hover-color"
        onClick={() => onPageChange(page +1)}
        disabled={(loading ) ? true : false}
        >
        {loadingLabel ? loadingLabel : 'Load More'}
        {loading && <em style={{verticalAlign: 'bottom',marginLeft: 4,fontSize: 24}} className="fa fa-pulse fa-spinner"></em>}
        </button>
    </div>
  )
}

export default LoadMoreButton