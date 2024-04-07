import React, { Suspense, useEffect, useMemo, useRef } from "react";
import { eventSelector } from "store/Slices/EventSlice";
import { pageBuilderPageSelector, fetchPage, clearState } from "store/Slices/PageBuilderPagesSlice";
import PageLoader from "components/ui-components/PageLoader";
import { useSelector, useDispatch } from "react-redux";
import { useRouter } from 'next/router';
import PageHeader from "../PageHeader";
import Head from 'next/head'

const in_array = require("in_array");

const loadModule = (theme) => {
  const Component = React.lazy(() =>
    import(`components/themes/${theme}/PageBuilderPage/Variation1`)
  );
  return Component;
};

const CmsPage = (props) => {

  const router = useRouter();

  const { id } = router.query;

  const { event } = useSelector(eventSelector);

  const dispatch = useDispatch();

  const eventUrl = event.url;

  const Component = useMemo(
    () => loadModule(event.theme.slug),
    [event]
  );

  useEffect(() => {
    dispatch(fetchPage(eventUrl,  id));
    return () => {
      dispatch(clearState());
    }
  }, [props.moduleName, id]);

  const { page, labels, loading, error } = useSelector(pageBuilderPageSelector);

  return (
    <Suspense fallback={<PageLoader />}>
      {page && <PageHeader label={page.name.toUpperCase()}/>}
      {page &&
        <Component data={page}  />
      }
    </Suspense>
  );
};

export default CmsPage;
