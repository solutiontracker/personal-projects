import React, { Suspense, useMemo, useState } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { useSelector } from "react-redux";
import axios from "axios";
const in_array = require("in_array");

const loadModule = (theme, variation) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/newsletter_subscription/${variation}`)
  );
  return Component;
};

const NewsLetterSubscription = () => {
  const [errors, setErrors] = useState({});
  const [alert, setAlert] = useState('');
  const [loading, setLoading] = useState(false);
  const { event } = useSelector(eventSelector);
  let moduleVariation = event.moduleVariations.filter(function (module, i) {
    return in_array(module.alias, ["newsletter_subscription"]);
  });

  const Component = useMemo(
    () => loadModule(event.theme.slug, moduleVariation[0]["variation_slug"]),
    [event]
  );

  const handleSubmit = async (data) =>{
    setLoading(true);
    setAlert('');
    setErrors({});
    try {
      const response = await axios.post(`${process.env.NEXT_APP_URL}/event/${event.url}/subscribeToMailingList/${event.news_settings.subscriber_id}`, data)
      if(response.data.status){
        setAlert(response.data.message);
        setLoading(false);
      }else{
        setErrors(response.data.errors);
        setLoading(false);
      }
    } catch (error) {
      setErrors(errors);
      setLoading(false);
    }
  }

  return (
    <Suspense fallback={''}>
      {event.news_settings && event.news_settings.subscriber_id !== null && <Component event={event} moduleVariation={moduleVariation[0]} settings={event.newsletter_subcription_form_settings} alert={alert} errors={errors} loading={loading} handleSubmit={(data)=>{ handleSubmit(data); }} labels={event.labels} />}
    </Suspense>
  );
};

export default NewsLetterSubscription;

