"use client";
import React from 'react';

import { store } from "@/redux/store/store";
import { Provider } from "react-redux";

// export function Providers({ children }: { children: React.ReactNode } ) {
export function Providers({ children }: { children: React.ReactNode } ) {
    return <Provider store={store}>{children}</Provider>;
}