import React from 'react'

const SimpleHeader = ({children}) => {
  return (
    <div className="container" style={{paddingTop: "40px",}}>
       { children }
    </div>
  )
}

export default SimpleHeader