/* eslint-disable import/prefer-default-export */
/* eslint-disable */
// @ts-ignore

export const queueRangeOptions:string[] = [
  '1',
  '2',
  '3',
  '4',
  '5',
  '6',
  '7',
  '8',
  '9',
  '10',
  '11',
  '12',
  '13',
  '14',
  '15',
  '16',
  '17',
  '18',
  '19',
  '20',
  '21',
  '22',
  '23',
  '24',
  '25',
  '26',
  '27',
  '28',
  '29',
  '30',
  '40',
  '50',
  '60',
  '70',
  '80',
  '90',
  '100',
  '150',
  '200',
  '250',
  '300',
  '350',
  '400',
  '450',
  '500',
  '550',
  '600',
  '650',
  '700',
  '750',
  '800',
  '850',
  '900',
  '950',
  '1000',
];


export const convertBools = (obj: unknown) => {
  const newObj = {};

  if (typeof obj !== 'object') {
    // console.log('no objcect');
    return obj;
  }

  for (const prop in obj) {
    // console.log('object');
    if (!obj.hasOwnProperty(prop)) {
      continue;
    }
    if (Array.isArray(obj[prop])) {
      newObj[prop] = obj[prop].map((val: unknown) => convertBools(val));
    } else if (obj[prop] === 'true') {
      newObj[prop] = true;
    } else if (obj[prop] === 'false') {
      newObj[prop] = false;
    } else {
      newObj[prop] = convertBools(obj[prop]);
    }
  }

  return newObj;
}

function solve(width:number|undefined, height:number|undefined, numerator:number|undefined, denominator:number|undefined) {
	var value;
	// solve for width
	if ('undefined' !== typeof width) {
			value = round() ? Math.round(width / (numerator / denominator)) : width / (numerator / denominator);
	}
	// solve for height
	else if ('undefined' !== typeof height) {
			value = round() ? Math.round(height * (numerator / denominator)) : height * (numerator / denominator);
	}
	return value;
}

function round() {
	return 1;
}

export const ratio2cssType = (numerator:number, denominator:number) => {
	//alert(" numerator:"+numerator +" denominator:" + denominator+" container_width:" + container_width+" container_height:" + container_height);
	var width, height;
	width = 660;
	height = solve(width, undefined, numerator, denominator);

	if (height > 1000) {
			height = 1000;
			width = solve(undefined, height, numerator, denominator);
	}

	return {
			width: width,
			height: height,
			lineHeight: height
	};
}
