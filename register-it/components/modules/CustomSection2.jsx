import React, { Suspense, useMemo } from "react";
import { useSelector } from "react-redux";
import { eventSelector } from 'store/Slices/EventSlice'

const loadModule = (theme, variation) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/custom-sections/CustomSection`)
  );
  return Component;
}

const CustomSection2 = ({ pageId }) => {
  const { event } = useSelector(eventSelector)
  const eventUrl = event.url;
  const Component = useMemo(() =>  loadModule(event.theme.slug, "CustomSection"), [event])
 
  return (
    <Suspense fallback={''}>
      {event ? <Component data={event.customSection2} /> : null}
    </Suspense>
  );
};


export default CustomSection2;



