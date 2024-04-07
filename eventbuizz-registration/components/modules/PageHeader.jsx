import React from 'react'
import { eventSelector } from "store/Slices/EventSlice";

import SimpleHeader from 'components/themes/theme-1/PageHeader/SimpleHeader'
import WithBackground from 'components/themes/theme-1/PageHeader/WithBackground'
import WithSolidColor from 'components/themes/theme-1/PageHeader/WithSolidColor'
import HeadingElement from 'components/ui-components/HeadingElement';
import { useSelector, useDispatch } from "react-redux";
const in_array = require("in_array");
const PageHeader = ({children, label, desc, breadCrumbs, showBreadcrumb}) => {
  const { event } = useSelector(eventSelector);
  let moduleVariation = event.moduleVariations.filter(function (module, i) {
    return in_array(module.alias, ["page_header"]);
  });
  if(moduleVariation[0]['variation_slug'] === 'Variation1'){
    return <WithBackground moduleVariation={moduleVariation[0]} >
        <HeadingElement breakheading dark={true} label={label} page_header={true} align={"left"} desc={desc} />
        {breadCrumbs && showBreadcrumb === 1 && breadCrumbs('background')}
    </WithBackground>
  }
  
  if(moduleVariation[0]['variation_slug'] === 'Variation2'){
    return <WithSolidColor>
      <HeadingElement breakheading dark={false} label={label} page_header={true}  align={"left"} desc={desc} />
      {breadCrumbs && showBreadcrumb === 1 && breadCrumbs('color')}

    </WithSolidColor>
  }

  return <SimpleHeader>
    <HeadingElement dark={false} label={label} page_header={true} align={"center"} desc={desc} />
     {breadCrumbs && showBreadcrumb === 1 && breadCrumbs(moduleVariation)}
    </SimpleHeader>
}

export default PageHeader