import React from "react"

const PaginationMenu = (props) => {

    const flexGrow = {
        flexGrow: 1,
        flexBasis: 0
    }


    //handle limit drop down
    const handleDropdown = (e) => {
        e.preventDefault()

        if (e.target.classList.contains('active')) {
            e.target.classList.remove('active')
        } else {
            let query = document.querySelectorAll('.btn_addmore')
            for (let i = 0; i < query.length; ++i) {
                query[i].classList.remove('active')
            }
            e.target.classList.add('active')
        }
    }

    const handleLimit = (e, limit) => {

        const itemClasses = document.querySelector("#pagination-dropdown-btn").classList

        //close dropdown
        if (itemClasses.contains("active")) {
            itemClasses.remove('active')
        }

        props.setPagination(prevState => ({
            ...prevState,
            limit: limit,
        }))

        //call the api function to fetch the data
        props.getWaitingListOrdersData()
    }

    return (
        <div className="panel-right-table d-flex justify-content-end">
            <div className="parctical-button-panel">
                <div className="dropdown">
                    <button
                        onClick={(e) => handleDropdown(e)}
                        className="btn"
                        id="pagination-dropdown-btn"
                        style={{ ...flexGrow, minWidth: '54px' }}>
                        {props.pagination.limit}
                        <i className="material-icons">
                            keyboard_arrow_down
                        </i>
                    </button>
                    <div className="dropdown-menu">
                        {props.pagination.limit !== 10 && (
                            <button className="dropdown-item" onClick={(e) => handleLimit(e, 10)}>
                                10
                            </button>
                        )}
                        {props.pagination.limit !== 20 && (
                            <button className="dropdown-item" onClick={(e) => handleLimit(e, 20)}>
                                20
                            </button>
                        )}
                        {props.pagination.limit !== 50 && (
                            <button className="dropdown-item" onClick={(e) => handleLimit(e, 50)}>
                                50
                            </button>
                        )}
                        {props.pagination.limit !== 500 && (
                            <button className="dropdown-item" onClick={(e) => handleLimit(e, 500)}>
                                500
                            </button>
                        )}
                        {props.pagination.limit !== 1000 && (
                            <button className="dropdown-item" onClick={(e) => handleLimit(e, 1000)}>
                                1000
                            </button>
                        )}
                    </div>
                </div>
            </div>
        </div>
    )
}

export default PaginationMenu