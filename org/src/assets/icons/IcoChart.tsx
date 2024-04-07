import * as React from 'react';
import Svg, { SvgProps, G, Rect } from 'react-native-svg';
const IcoChart = (props: SvgProps) => (
  <Svg
    xmlns="http://www.w3.org/2000/svg"
    width={21}
    height={20}
    viewBox="0 0 21 20"
    {...props}
  >
    <G id="Group_1844" data-name="Group 1844" transform="translate(-51 -246)">
      <G
        id="Rectangle_53"
        data-name="Rectangle 53"
        transform="translate(51 256)"
        fill="none"
        stroke={props.color || '#bbb'}
        strokeWidth={1}
      >
        <Rect width={6} height={10} rx={2} stroke="none" />
        <Rect x={0.5} y={0.5} width={5} height={9} rx={1.5} fill="none" />
      </G>
      <G
        id="Rectangle_54"
        data-name="Rectangle 54"
        transform="translate(59 246)"
        fill="none"
        stroke={props.color || '#bbb'}
        strokeWidth={1}
      >
        <Rect width={5} height={20} rx={2} stroke="none" />
        <Rect x={0.5} y={0.5} width={4} height={19} rx={1.5} fill="none" />
      </G>
      <G
        id="Rectangle_55"
        data-name="Rectangle 55"
        transform="translate(67 252)"
        fill="none"
        stroke={props.color || '#bbb'}
        strokeWidth={1}
      >
        <Rect width={5} height={14} rx={2} stroke="none" />
        <Rect x={0.5} y={0.5} width={4} height={13} rx={1.5} fill="none" />
      </G>
    </G>
  </Svg>
);
export default IcoChart;
