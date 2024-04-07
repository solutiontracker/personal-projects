"use client"
import * as React from 'react';
import Select from 'react-select';
import { defaultTheme } from 'react-select';

const { colors } = defaultTheme;
class DropDown extends React.Component {

  state = { isOpen: false };

  toggleOpen = () => {
    this.setState(state => ({ isOpen: !state.isOpen }));
  };

  componentDidUpdate(prevProps) {
    if (prevProps.selectedlabel !== this.props.selectedlabel) {
      this.setState({
        isOpen: false
      })
    }
  }
  render() {
    const { label, selected, required, onChange, className, selectedlabel } = this.props;
    let {listitems } = this.props;
  
    const { isOpen } = this.state;
    const isSearchable =
      this.props.isSearchable !== undefined
        ? this.props.isSearchable
        : true;
    const isDisabled =
      this.props.isDisabled !== undefined
        ? this.props.isDisabled
        : false;
    const isMulti =
      this.props.isMulti !== undefined
        ? this.props.isMulti
        : false;
   let options = listitems.map((item, index) => {
          return {
            label: item.name?item.name:item.attendee_type?item.attendee_type:'',
            value: item.id,
            key: index
          }
        });
    options =
      this.props.isGroup !== undefined
        ? listitems
        : listitems.map((item, index) => {
          return {
            label: item.name?item.name:item.attendee_type?item.attendee_type:'',
            value: item.id,
            key: index
          }
        });
    
    
    const isAttendee =
      this.props.type !== undefined && this.props.type=='attendee_type'
        ? options.unshift({
          label: 'Select attendee type',
          value: 'select',
          key: 'select'
        })
        : false;
    const style = {
      control: base => ({
        ...base,
        border: 0,
        boxShadow: 'none'
      })
    };
    const Blanket = props => (
      <div
        className="blanket-wrapper"
        {...props}
      />
    );
    const DropdownIndicator = () => (
      <div css={{ color: colors.neutral20, height: 24, width: 32 }}>
        <Svg>
          <path
            d="M16.436 15.085l3.94 4.01a1 1 0 0 1-1.425 1.402l-3.938-4.006a7.5 7.5 0 1 1 1.423-1.406zM10.5 16a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11z"
            fill="currentColor"
            fillRule="evenodd"
          />
        </Svg>
      </div>
    );
    const Svg = p => (
      <svg
        width="24"
        height="24"
        viewBox="0 0 24 24"
        focusable="false"
        role="presentation"
        {...p}
      />
    );
    return (

      <React.Fragment>
        {selected && selectedlabel && !isMulti ? (
          <div
            className={`${
              selected && !label
                ? "no-label wrapper-select isSelected"
                : selected
                ? "wrapper-select isSelected"
                : "wrapper-select"
            } ${isOpen && "isOpen"} ${isDisabled && "isDisabled"} ${
              !isSearchable && "searchFalse"
            }`}
          >
            <label
              onClick={this.toggleOpen}
              className="label-wrapper-select"
            >
              <div className="btn-wrapper">{selectedlabel}</div>
              {label && (
                <span className="label-text">
                  {label} {required && <em className="req">*</em>}{" "}
                </span>
              )}
              <i className="icon-right material-icons">
                {isDisabled ? "lock" : "keyboard_arrow_down"}
              </i>
              {this.props.isClearable && 
              <div onClick={onChange} aria-hidden="true" className=" dropdown-clearall">
                <svg style={{ pointerEvents: 'none' }} height="20" width="20" viewBox="0 0 20 20" aria-hidden="true" focusable="false" className="css-6q0nyr-Svg">
                  <path d="M14.348 14.849c-0.469 0.469-1.229 0.469-1.697 0l-2.651-3.030-2.651 3.029c-0.469 0.469-1.229 0.469-1.697 0-0.469-0.469-0.469-1.229 0-1.697l2.758-3.15-2.759-3.152c-0.469-0.469-0.469-1.228 0-1.697s1.228-0.469 1.697 0l2.652 3.031 2.651-3.031c0.469-0.469 1.228-0.469 1.697 0s0.469 1.229 0 1.697l-2.758 3.152 2.758 3.15c0.469 0.469 0.469 1.229 0 1.698z"></path>
                </svg>
              </div>
              }
            </label>
            {isOpen && !isDisabled && (
              <Select
                autoFocus
                backspaceRemovesValue={false}
                className={className}
                components={{ DropdownIndicator, IndicatorSeparator: null }}
                value={
                  selected
                    ? { label: selectedlabel, value: selected }
                    : false
                }
                onChange={onChange}
                options={options}
                promptTextCreator={false}
                placeholder="Search"
                styles={style}
                controlShouldRenderValue={false}
                isClearable={false}
                hideSelectedOptions={false}
                isSearchable={isSearchable}
                isDisabled={isDisabled}
                menuIsOpen
              />
            )}

            {isOpen && !isDisabled ? (
              <Blanket onClick={this.toggleOpen} />
            ) : null}
          </div>
        ) : isMulti ? (
            <label className={`${isDisabled && "isDisabled"} wrapper-select select-multi`}>
            <Select
              autoFocus={isDisabled ? false : true }
              components={{ IndicatorSeparator: null }}
              backspaceRemovesValue={false}
              className={className}
              value={selected}
              onChange={onChange}
              options={options}
              promptTextCreator={false}
              placeholder={label ? label : "Search"}
              styles={style}
              controlShouldRenderValue={isMulti ? true : false}
              hideSelectedOptions={false}
              isClearable={false}
              isSearchable={true}
              isMulti={isMulti}
              />
            </label>
        ) : (
          <div
            className={`wrapper-select ${isOpen && "isOpen"} ${
              !isSearchable && "searchFalse"
            } ${isDisabled && "isDisabled"}`}
          >
            <label
              onClick={this.toggleOpen}
              className="label-wrapper-select"
            >
              <div className="btn-wrapper">
                {label} {required && <em className="req">*</em>}
              </div>
              {label && (
                <span className="label-text">
                  {label} {required && <em className="req">*</em>}{" "}
                </span>
              )}
              <i className="icon-right material-icons">
                {isDisabled ? "lock" : "keyboard_arrow_down"}
              </i>
            </label>
            {isOpen && !isDisabled && (
              <Select
                autoFocus
                components={{ DropdownIndicator, IndicatorSeparator: null }}
                backspaceRemovesValue={false}
                className={className}
                value={selected}
                onChange={onChange}
                options={options}
                promptTextCreator={false}
                placeholder="Search"
                styles={style}
                controlShouldRenderValue={isMulti ? true : false}
                hideSelectedOptions={false}
                isSearchable={isSearchable}
                isClearable={false}
                isMulti={isMulti}
                menuIsOpen
              />
            )}
            {isOpen && !isDisabled ? (
              <Blanket onClick={this.toggleOpen} />
            ) : null}
          </div>
        )}
      </React.Fragment>

    );
  }
}

export default DropDown;

