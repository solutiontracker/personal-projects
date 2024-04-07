"use client";

import { useAppDispatch } from "@/redux/hooks/hooks";
import { clearErrors } from "@/redux/store/slices/AuthSlice";
import { useEffect } from "react";

const Loading = () => {
  const dispatch = useAppDispatch();  
  useEffect(() => {
    dispatch(clearErrors())
    
  }, [])
  
  return (
    <div className="widget-loader-wrapper">
        <div className="widget-loader"></div>
    </div>
  )
}

export default Loading