'use client'
import React, { useState, useEffect } from 'react';
import {useAppSelector} from "@/redux/hooks/hooks";
import {RootState} from "@/redux/store/store";


interface PaginationProps {
    currentPage: number;
    totalPages: number;
    onPageChange: (page: number) => void;
}

const Pagination: React.FC<PaginationProps> = ({ currentPage, totalPages, onPageChange }) => {
    // const pages = useAppSelector((state: RootState) => state.paginate);

    const [pages, setPages] = useState<number[]>([]);

    useEffect(() => {
        const generatePages = () => {
            const pageArray: number[] = [];
            for (let i = 1; i <= totalPages; i++) {
                pageArray.push(i);
            }
            setPages(pageArray);
        };
        generatePages();
    }, [currentPage, totalPages]);

    return (
        <nav>
            <ul className="ebs-pagination list-inline">
                <li className={`list-inline-item page-item ${currentPage === 1 ? 'disabled' : ''}`}>
                    <button
											className='ebs-prev-next'
                        disabled={currentPage === 1}
                        onClick={() => onPageChange(currentPage - 1)}
                    >
                       <span className="material-symbols-outlined">chevron_left</span>
                    </button>
                </li>
                {pages.map((page) => (
                    <li
                        key={page}
                        className={`list-inline-item page-item ${currentPage === page ? 'active' : ''}`}
                    >
                        <button  onClick={() => onPageChange(page)}>
                            {page}
                        </button>
                    </li>
                ))}
                <li className={`list-inline-item page-item ${currentPage === totalPages ? 'disabled' : ''}`}>
                    <button
											className='ebs-prev-next'
                        disabled={currentPage === totalPages}
                        onClick={() => onPageChange(currentPage + 1)}
                    >
                        <span className="material-symbols-outlined">chevron_right</span>
                    </button>
                </li>
            </ul>
        </nav>
    );
};

export default Pagination;