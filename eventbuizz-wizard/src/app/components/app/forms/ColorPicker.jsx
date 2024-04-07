import * as React from 'react';
import reactCSS from 'reactcss'
import { SketchPicker  } from 'react-color'

export default class ColorPicker extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      displayColorPicker: false,
      value : props.value,
      random:  Math.floor((Math.random() * 10) + 1),
      color: {
        r: '241',
        g: '112',
        b: '19',
        a: '1',
      },
    };
  }
  handleClick = () => {
    this.setState({ displayColorPicker: !this.state.displayColorPicker })
  };

  handleClose = () => {
    this.setState({ displayColorPicker: false })
  };

  handleChange = (color) => {
    this.setState({ color: color, value: color.hex });
    const e = new Event('input', { bubbles: true })
    const input = document.querySelector(`#js-testInput${this.state.random}`)
    input.dispatchEvent(e)
  };
  hextorgb = (hex) => {
     var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
     return result
       ? {
           r: parseInt(result[1], 16),
           g: parseInt(result[2], 16),
           b: parseInt(result[3], 16)
         }
       : null;
  }
  render() {
    const { label, value, required, onChange } = this.props;
    const styles = reactCSS({
      default: {
        color: {
          width: "50px",
          height: "27px",
          borderRadius: "4px",
          background: `${value}`
        },
        swatch: {
          width: "50px",
          height: "27px",
          borderRadius: "4px",
          display: "inline-block",
          cursor: "pointer",
          background: `${value}`
        },
        popover: {
          position: "absolute",
          zIndex: "99",
          top: 'auto',
          bottom: '60%'
        },
        cover: {
          position: "fixed",
          top: "0px",
          right: "0px",
          bottom: "0px",
          left: "0px",
        }
      }
    });

    return (
      <div className="color-picker">
        {label && (
          <span className="title-picker">
            {label}
            {required && <em className="req">*</em>}
          </span>
        )}
        <div className="label-input-color">
          <div
            className="patch-box"
            style={styles.swatch}
            onClick={this.handleClick}
          >
            <div style={styles.color} />
          </div>
          <div className="c-box hex-box">
            <span className="title">HEX</span>
            <input
              id={`js-testInput${this.state.random}`}
              onChange={onChange}
              onFocus={this.handleClick}
              type="text"
              placeholder=" "
              defaultValue={this.props.value}
              readOnly
            />
          </div>
          <div className="c-box rgb-box">
            <span className="title">R</span>
            <span onClick={this.handleClick} className="value-rgb">
              {this.props.value && this.hextorgb(this.props.value).r}
            </span>
          </div>
          <div className="c-box rgb-box">
            <span className="title">G</span>
            <span onClick={this.handleClick} className="value-rgb">
              {this.props.value && this.hextorgb(this.props.value).g}
            </span>
          </div>
          <div className="c-box rgb-box">
            <span className="title">B</span>
            <span onClick={this.handleClick} className="value-rgb">
              {this.props.value && this.hextorgb(this.props.value).b}
            </span>
          </div>
        </div>
        {this.state.displayColorPicker ? (
          <div style={styles.popover}>
            <div style={styles.cover} onClick={this.handleClose} />
            <SketchPicker
              disableAlpha={true}
              color={value}
              onChange={onChange}
            />
          </div>
        ) : null}
      </div>
    );
  }
}

