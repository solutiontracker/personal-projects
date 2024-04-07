import * as React from 'react';
import Svg, { SvgProps, Path } from 'react-native-svg';
const IcomenuPlus = (props: SvgProps) => (
  <Svg
    xmlns="http://www.w3.org/2000/svg"
    width={24}
    height={17.685}
    viewBox="0 0 24 17.685"
    {...props}
  >
    <Path
      id="Path_1658"
      data-name="Path 1658"
      d="M0,12.632H8.842V10.1H0ZM0,7.579H13.895V5.053H0ZM16.421,5.053v5.053H11.368v2.526h5.053v5.053h2.526V12.632H24V10.106H18.947V5.053ZM0,2.527H13.895V0H0Z"
      transform="translate(0 0.001)"
      fill={props.color}
    />
  </Svg>
);
export default IcomenuPlus;
