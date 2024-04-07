import React, { Suspense, useMemo } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { useSelector } from "react-redux";
const in_array = require("in_array");

const loadModule = (theme, variation) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/footer/${variation}`)
  );
  return Component;
};

const Footer = () => {
  const { event } = useSelector(eventSelector);
  let moduleVariation = event.moduleVariations.filter(function (module, i) {
    return in_array(module.alias, ["footer"]);
  });

  const Component = useMemo(
    () => loadModule(event.theme.slug, moduleVariation[0]["variation_slug"]),
    [event]
  );

  return (
    <Suspense fallback={<div>Loading...</div>}>
      {event.eventsiteSettings.eventsite_footer === 1 ? <Component event={event} siteLabels={event.labels} /> : null}
    </Suspense>
  );
};

export default Footer;
