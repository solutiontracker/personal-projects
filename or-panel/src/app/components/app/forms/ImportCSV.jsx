import React, { Component } from 'react';
import CSVReader from 'react-csv-reader';
import Select from 'react-select';
import { service } from 'services/service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation, withTranslation } from "react-i18next";
import { confirmAlert } from "react-confirm-alert";
import Loader from '@/app/forms/Loader';
import { CSVLink } from "react-csv";
import { ReactSVG } from 'react-svg';

function validateFields(selecteditems, valdiation) {
  valdiation = valdiation.split(',');
  selecteditems.forEach((item) => {
    if (valdiation.includes(item.dataset.selected)) {
      var index = valdiation.indexOf(item.dataset.selected);
      if (index !== -1) valdiation.splice(index, 1);
    }
  })
  return valdiation;
}

const DataReturn = ({ data, type, column = [] }) => {
  return (
    <Translation>
      {
        t =>
        <React.Fragment>
          <span style={{ display: 'flex', justifyContent: 'flex-end', paddingTop: '10px'  }}>
            <CSVLink
              data={data.map(function (value) {
                return { ...value, 'error' : value.error };
              })}
              className="btn_csvImport"
            >
              <ReactSVG wrapper="span" className="icons" src={require('img/export-csv.svg')} />
            </CSVLink>
          </span>
            <div className={`${type} wrapper-tables`}>
              <div className="tables-header tables-row">
                {column.map((name, key) => {
                    return (
                      <React.Fragment>
                        {name !== '' && name !== null && name !== '-1' && (
                          <div className="table-box" key={key}>
                            {name.replace(/_/g, ' ')}
                          </div>
                        )}
                      </React.Fragment>
                    );
                })}
                {type === 'errors' && (
                  <React.Fragment>
                    <div className="table-box">
                      {t('IM_ERROR')}
                    </div>
                  </React.Fragment>  
                )}
              </div>
              {data.length > 0 && data && (
                <div className="tables-body">
                  {data.map((item, key) => {
                    return (
                      <div key={key} className="tables-row">
                        {column.map((name, k) => {
                            const listname = name.toLowerCase().replace(/ /g, '_');
                            return (
                              <React.Fragment>
                                {listname !== '' && listname !== null && name !== '-1' && (
                                  <div className="table-box" key={k}>
                                    {item[listname]}
                                  </div>
                                )}
                              </React.Fragment>
                            );
                          })
                        }
                        {type === 'errors' && (
                          <div className="table-box" key={key} dangerouslySetInnerHTML={{ __html: item.error }}></div>
                        )}
                      </div>
                    )
                  })}
                </div>
              )}
            </div>
          </React.Fragment>
      }
    </Translation>
  )
}

class ImportCSV extends Component {

  constructor(props) {
    super(props);
    this.state = {
      data: undefined,
      file: undefined,
      speaker: (this.props.speaker !== undefined ? 1 : 0),
      column: [],
      validateMap: this.props.validate !== undefined ? this.props.validate : undefined,
      errorFields: false,
      responseData: false,
      activeStep: 1,
      delimeter: ';',

      //errors & loading
      message: false,
      success: true,
      errors: {},
      isLoader: false,
      preLoader: false
    };
  }

  componentDidMount() {
    var inputfile = document.getElementById("inputfile");
    if (inputfile) {
      inputfile.click();
    }
  }

  closeCSV = e => {
    e.preventDefault();
    this.props.onClick();
  };

  handleImportRecord = e => {
    e.preventDefault();
    const valdiation = this.props.validate;
    const selecteditems = document.querySelectorAll('.drop-down-label');
    const errors = validateFields(selecteditems, valdiation.toString());
    if (errors.length === 0) {
      const data = this.state.data;
      var newarray = [];
      const items = document.querySelectorAll('.data-title p');
      for (let i = 0; i < items.length; i++) {
        var elementvalue = selecteditems[i].dataset.selected;
        if (elementvalue) {
          newarray.push(elementvalue);
        } else {
          newarray.push("")
        }
      }
      const maindata = [];
      data.map((element, k) => {
        let namekey = Object.keys(element);
        const innerObject = {};
        namekey.map((name, v) => {
          for (let i = 0; i < newarray.length; i++) {
            const elementname = newarray[i];
            const keyname = Object.keys(elementname);
            if (name === keyname.toString()) {
              var newobject = [];
              newobject[elementname[keyname]] = element[name];
              return Object.assign(innerObject, newobject)
            }
          }
          return innerObject;
        });
        return maindata.push(innerObject);
      });
      this.setState({ column: newarray, isLoader: true, preLoader: true, errorFields: false });
      setTimeout(() => {
        service._import(this.props.apiUrl, this.state)
          .then(
            response => {
              if (response.success) {
                this.setState({
                  message: response.message,
                  success: true,
                  isLoader: false,
                  preLoader: false,
                  errors: {},
                  responseData: response.data

                });
              } else {
                this.setState({
                  message: response.message,
                  success: false,
                  isLoader: false,
                  preLoader: false,
                  errors: response.errors
                });
              }
            },
            error => { }
          );
      }, 50)
    } else {
      this.setState({
        errorFields: errors
      });

    }
  }

  render() {

    const handleForce = data => {
      if (data.length !== 0) {
        var input = document.getElementById('inputfile');
        var filename = document.getElementById('filetitle');
        this.setState({ data: data, file: input.files[0] });
        if (filename === null) {
          filename = document.createElement('span');
          filename.id = 'filetitle';
          filename.innerHTML = input.value.replace(/^.*\\/, "");
          input.parentNode.insertBefore(filename, input.nextSibling);
        } else {
          filename.innerHTML = input.value.replace(/^.*\\/, "");
        }
      } else {
        confirmAlert({
          customUI: ({ onClose }) => {
            return (
              <Translation>
                {
                  t =>
                    <div className='app-main-popup'>
                      <div className="app-header">
                        <h4>{t('G_WARNING')}</h4>
                      </div>
                      <div className="app-body">
                        <p>{t('G_SORRY_NO_DATA_FOUND')}</p>
                      </div>
                      <div className="app-footer">
                        <button className="btn btn-success" onClick={onClose}>{t('G_OK')}</button>
                      </div>
                    </div>
                }
              </Translation>
            );
          }
        });
      }
    };

    const papaparseOptions = {
      header: true,
      complete: function (file) {
        handleToggle(file);
      },
      dynamicTyping: true,
      skipEmptyLines: true,
      transformHeader: (header) => header,
    };

    const handleToggle = (delimeter) => {
      this.setState({
        delimeter: delimeter.meta.delimiter,
      });
    };

    function handleChangeDrop(e, id) {
      const element = document.getElementById(id);
      element.dataset.selected = e.value
    }

    const MapElement = ({ data, select, required, id }) => {
      var options = select.map((item, index) => {
        return {
          label: required.includes(item.name) ? item.value + ' *' : item.value,
          value: item.name,
          key: index
        }
      });
      const valueFromId = (opts, id) => opts.find(o => o.value === id);
      const getValue = (type) => {
        const fromData = valueFromId(select, data);
        if (fromData !== undefined) {
          if (type === 'value') {
            return {
              label: required.includes(fromData.name) ? fromData.value + ' *' : fromData.value,
              value: fromData.name,
            }
          } else {
            return fromData.name
          }
        }
      }
      const style = {
        control: base => ({
          ...base,
          boxShadow: 'none'
        })
      };
      return (
        <label id={`label-id-${id}`} className="drop-down-label" data-selected={getValue('data')}>
          <Select
            options={options}
            defaultValue={getValue('value')}
            onChange={(e) => handleChangeDrop(e, `label-id-${id}`)}
            placeholder={this.props.t('G_PLEASE_SELECT')}
            components={{ IndicatorSeparator: null }}
            styles={style}
          />
        </label>
      )
    }

    const CsvModule = ({ data }) => {
      var names = Object.keys(data[0]);
      return (
        <Translation>
          {
            t =>
              <div className="csv-container-moudle">
                <div className="data-row">
                  <div className="data-box"><strong>{t('IM_HEADER')}</strong></div>
                  <div className="data-box"><strong>{t('IM_MODULE_FIELDS')}</strong></div>
                  {data.slice(0, 3).map((element, k) => {
                    return (
                      <div className="data-box" key={k}><strong>{t('IM_ROW')} {k + 1}</strong></div>
                    )
                  })}
                </div>
                {names.map((name, key) => {
                  return (
                    <div className="data-row" key={key}>
                      <div className="data-box data-title"><p
                        data-element={name.toLowerCase().replace(/\W/g, "_")}>{name}</p></div>
                      <div className="data-box map-element"><MapElement id={key} data={name}
                        select={this.props.element}
                        required={this.props.validate} /></div>
                      {data.slice(0, 3).map((element, k) => {
                        return (
                          <div className="data-box" key={k}>{element[name]}</div>
                        )
                      })}
                    </div>
                  );
                })}
              </div>
          }
        </Translation>
      )
    }
    
    return (
      <Translation>
        {
          t =>
            <div className="wrapper-import-file-wrapper">
              {this.state.preLoader &&
                <Loader />
              }
              <div className="wrapper-import-file">
                {!this.state.preLoader && (
                  <React.Fragment>
                    <header className="header-import clearfix">
                      {this.props.compName && (
                        <div className="left-header-import float-left">
                          <a download href={this.props.downloadFile && this.props.downloadFile} target="_blank" className="btn-download">{t('G_IMPORT_DOWNLOAD_SAMPLE_FILE')}</a>
                        </div>)}
                      <div className="right-header-import align-items-center d-flex float-right">
                        <CSVReader
                          inputId="inputfile"
                          label={t('G_UPLOAD_CSV')}
                          cssClass="react-csv-input"
                          onFileLoaded={handleForce}
                          parserOptions={papaparseOptions}
                        />
                      </div>
                    </header>
                    <div style={{ maxWidth: '100%' }} className="container">
                      <div className="top-csv-panel clearfix">
                        {this.state.message &&
                          <AlertMessage
                            className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                            title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                            content={this.state.message}
                            icon={this.state.success ? "check" : "info"}
                          />
                        }
                        {this.state.responseData === false && this.state.errorFields &&
                          <AlertMessage
                            className='alert-danger'
                            title={t('EE_OCCURRED')}
                            content={`${t('IM_NOT_MAPPED')} <strong>${this.state.errorFields}</strong>`}
                            icon="info"
                          />
                        }
                      </div>
                    </div>
                    <div id="wrapper-module" className={this.state.responseData ? 'wrapper-module module-tab' : `wrapper-module ${this.state.message && 'errormessage'} ${this.state.errorFields && 'errorFields'}`}>
                      <div style={{ maxWidth: '100%' }} className="container">
                        {this.state.responseData && (
                          <React.Fragment>
                            <div className="wrapper-tab-menu">
                              <div className="header-toggle">
                                <span onClick={() => this.setState({ activeStep: 1 })} className={this.state.activeStep === 1 ? 'active' : ''}>{t('IM_CREATED')} ({this.state.responseData.results.new.length})</span>
                                <span onClick={() => this.setState({ activeStep: 2 })} className={this.state.activeStep === 2 ? 'active' : ''}>{t('IM_DULICATED')} ({this.state.responseData.results.duplicate.length})</span>
                                <span onClick={() => this.setState({ activeStep: 3 })} className={this.state.activeStep === 3 ? 'active' : ''}>{t('IM_ERRORS')} ({this.state.responseData.results.error.length})</span>
                              </div>
                              {this.state.activeStep === 1 && (
                                <React.Fragment>
                                  {this.state.responseData.results.new.length === 0 ? (
                                    <p className="no-data-found">{t('IM_NO_RECORD_FOUND')}</p>
                                  ) : (
                                    <DataReturn column={this.state.column} type="imported" data={this.state.responseData.results.new} />
                                  )}
                                </React.Fragment>
                              )}
                              {this.state.activeStep === 2 && (
                                <React.Fragment>
                                  {this.state.responseData.results.duplicate.length === 0 ? (
                                    <p className="no-data-found">{t('IM_NO_RECORD_FOUND')}</p>
                                  ) : (
                                    <DataReturn column={this.state.column} type="errors" data={this.state.responseData.results.duplicate} />
                                  )}
                                </React.Fragment>
                              )}
                              {this.state.activeStep === 3 && (
                                <React.Fragment>
                                  {this.state.responseData.results.error.length === 0 ? (
                                    <p className="no-data-found">{t('IM_NO_RECORD_FOUND')}</p>
                                  ) : (
                                    <DataReturn column={this.state.column} type="errors" data={this.state.responseData.results.error} />
                                  )}
                                </React.Fragment>
                              )}
                            </div>
                          </React.Fragment>
                        )}
                        {this.state.data && !this.state.responseData && <CsvModule data={this.state.data} />}
                      </div>
                    </div>
                    <div className="bottom-component-panel bottom-panel-import">
                      {this.state.responseData ? (
                        <button onClick={this.closeCSV.bind(this)} className="btn btn-import">{t('IM_COMPLETED')}</button>
                      ) : (
                        <React.Fragment>
                          <button onClick={this.closeCSV.bind(this)} className="btn btn-cancel">{t('G_IMPORT_CANCEL')}</button>
                          <button data-type="save-next" className={`btn btn-import ${!this.state.data && 'disabled'}`} disabled={this.state.isLoader ? true : false} onClick={this.handleImportRecord.bind(this)}>{this.state.isLoader ? <span className="spinner-border spinner-border-sm"></span> : t('IM_RECORD')}</button>
                        </React.Fragment>
                      )}
                    </div>
                  </React.Fragment>
                )}
              </div>
            </div>
        }
      </Translation>
    )
  }
}

export default withTranslation()(ImportCSV)