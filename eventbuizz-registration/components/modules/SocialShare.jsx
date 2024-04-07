import React, { Suspense, useMemo } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { useSelector } from "react-redux";

const in_array = require("in_array");

const loadModule = (theme, variation) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/social-media-share/${variation}`)
  );
  return Component;
};

const SocialShare = () => {
  const { event } = useSelector(eventSelector);
  let moduleVariation = event.moduleVariations.filter(function (module, i) {
    return in_array(module.alias, ["social_media_share"]);
  });

  const Component = useMemo(
    () => loadModule(event.theme.slug, moduleVariation[0]["variation_slug"]),
    [event]
  );

  return (
    <Suspense fallback={''}>
      {event.socialMediaShare && <Component event={event} settings={moduleVariation[0]} socialMediaShare={event.socialMediaShare} labels={event.labels} />}
    </Suspense>
  );
};

export default SocialShare;
