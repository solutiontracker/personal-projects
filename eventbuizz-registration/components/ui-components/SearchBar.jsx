import React from 'react'

const SearchBar = ({searchLabel, setText, loading}) => {
  return (
    <div className={`container pb-5`}>
    <div className="ebs-form-control-search">
      <input
        className="form-control"
        placeholder={searchLabel}
        type="text"
        onChange={(e) => {setText(e.currentTarget.value)}}
      />
        {!loading ? <em className="fa fa-search"></em> : <em className="fa fa-pulse fa-spinner"></em>}
    </div>
  </div>
  )
}

export default SearchBar