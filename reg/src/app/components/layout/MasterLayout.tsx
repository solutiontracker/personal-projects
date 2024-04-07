import React, { FC, ReactNode, useEffect } from "react";
import ContextWrapper from '@/src/app/components/layout/ContextWrapper';
import EventProvider from "@/src//app/context/event/EventProvider";

interface Props {
    children: ReactNode;
}

// functional component
const MasterLayout: FC<Props> = ({ children }) => {
    return (
        <React.Fragment>
            <EventProvider>
                <ContextWrapper children={children} />
            </EventProvider>
        </React.Fragment>
    );
};

export default MasterLayout;