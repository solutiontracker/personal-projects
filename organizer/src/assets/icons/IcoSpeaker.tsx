import * as React from 'react';
import Svg, { SvgProps, G, Rect, Path, Line } from 'react-native-svg';
const IcoSpeaker = (props: SvgProps) => (
  <Svg
    xmlns="http://www.w3.org/2000/svg"
    width={14.419}
    height={21.006}
    viewBox="0 0 14.419 21.006"
    {...props}
  >
    <G id="Group_1858" data-name="Group 1858" transform="translate(0.5)">
      <G
        id="Rectangle_44"
        data-name="Rectangle 44"
        transform="translate(1.465)"
        fill="none"
        stroke={props.color || '#bbb'}
        strokeWidth={1}
      >
        <Rect width={10.045} height={15.486} rx={5.022} stroke="none" />
        <Rect
          x={0.5}
          y={0.5}
          width={9.045}
          height={14.486}
          rx={4.522}
          fill="none"
        />
      </G>
      <Path
        id="Path_12"
        data-name="Path 12"
        d="M858.715,580c0,7.969,13.311,7.969,13.311,0"
        transform="translate(-858.715 -568.87)"
        fill="none"
        stroke={props.color || '#bbb'}
        strokeLinecap="round"
        strokeMiterlimit={10}
        strokeWidth={1}
      />
      <Line
        id="Line_12"
        data-name="Line 12"
        y2={3.269}
        transform="translate(6.71 17.237)"
        fill="none"
        stroke={props.color || '#bbb'}
        strokeLinecap="round"
        strokeWidth={1}
      />
      <Line
        id="Line_14"
        data-name="Line 14"
        x1={0.688}
        transform="translate(12.731 11.044)"
        fill="none"
        stroke={props.color || '#bbb'}
        strokeLinecap="round"
        strokeWidth={1}
      />
      <Line
        id="Line_15"
        data-name="Line 15"
        x1={0.688}
        transform="translate(0 11.044)"
        fill="none"
        stroke={props.color || '#bbb'}
        strokeLinecap="round"
        strokeWidth={1}
      />
      <Line
        id="Line_13"
        data-name="Line 13"
        x2={6.882}
        transform="translate(3.269 20.506)"
        fill="none"
        stroke={props.color || '#bbb'}
        strokeLinecap="round"
        strokeWidth={1}
      />
    </G>
  </Svg>
);
export default IcoSpeaker;
