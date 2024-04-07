import React, { Component } from "react";
import Img from "react-image";
import Cropper from "react-cropper";
import { confirmAlert } from "react-confirm-alert";
import { Translation } from "react-i18next";
import Loader from "@/app/forms/Loader";
import "cropperjs/dist/cropper.css";

function ValidateSingleInput(oInput, _validFileExtensions) {
  if (oInput.type === "file") {
    var sFileName = oInput.value;
    if (sFileName.length > 0) {
      var blnValid = false;
      for (var j = 0; j < _validFileExtensions.length; j++) {
        var sCurExtension = _validFileExtensions[j];
        if (
          sFileName
            .substr(
              sFileName.length - sCurExtension.length,
              sCurExtension.length
            )
            .toLowerCase() === sCurExtension.toLowerCase()
        ) {
          blnValid = true;
          break;
        }
      }

      if (!blnValid) {
        confirmAlert({
          customUI: ({ onClose }) => {
            return (
              <Translation>
                {t => (
                  <div className="app-main-popup">
                    <div className="app-header">
                      <h4>{t("EE_WARNING")}</h4>
                    </div>
                    <div className="app-body">
                      <p>
                        {t("EE_UPLOAD_MESSAGE")}
                        <br />
                        {_validFileExtensions.length === 1
                          ? t("EE_WARNING_MESSAGE")
                          : t("EE_WARNING_MESSAGES")}
                        <strong>{_validFileExtensions.join(", ")}</strong>
                      </p>
                    </div>
                    <div className="app-footer">
                      <button className="btn btn-cancel" onClick={onClose}>
                        {t("G_OK")}
                      </button>
                    </div>
                  </div>
                )}
              </Translation>
            );
          }
        });
        oInput.value = "";
        return false;
      }
    }
  }
  return true;
}
export default class FileUpload extends Component {
  constructor(props) {
    super(props);
    this.state = {
      src: "",
      cropResult: this.props.value ? this.props.value : null,
      imageCropper: false,
      isLoading: true
    };
  }

  onChange = e => {
    e.preventDefault();
    const validate = ValidateSingleInput(e.target, this.props.imgExtension);
    if (!validate) return;
    let files;
    if (e.dataTransfer) {
      files = e.dataTransfer.files;
    } else if (e.target) {
      files = e.target.files;
    }
    const reader = new FileReader();
    reader.onload = () => {
      this.setState({ src: reader.result, imageCropper: true });
    };
    reader.readAsDataURL(files[0]);
  };

  cropImage() {
    if (typeof this.cropper.getCroppedCanvas() === "undefined") {
      return;
    }
    this.setState({
      cropResult: this.cropper
        .getCroppedCanvas({
          maxWidth: 1800,
          minWidth: this.props.dimenstion.split(" x ")[0],
          imageSmoothingEnabled: true,
          imageSmoothingQuality: "high"
        })
        .toDataURL(),
      imageCropper: false
    }, () => {
      this.props.onChange(this.props.stateName, this.state.cropResult);
    });

  }

  handleShowImg = () => {
    this.setState({
      imageCropper: true,
      cropResult: this.props.value
    });
  };

  handleRemove = () => {
    confirmAlert({
      customUI: ({ onClose }) => {
        return (
          <Translation>
            {
              t =>
                <div className='app-main-popup'>
                  <div className="app-header">
                    <h4>{t('G_DELETE')}</h4>
                  </div>
                  <div className="app-body">
                    <p>{t('EE_ON_DELETE_ALERT_MSG')}</p>
                  </div>
                  <div className="app-footer">
                    <button className="btn btn-cancel" onClick={onClose}>{t('G_CANCEL')}</button>
                    <button className="btn btn-success"
                      onClick={() => {
                        onClose();
                        this.setState({
                          src: "",
                          cropResult: null,
                          imageCropper: false
                        }, () => {
                          this.props.onChange(this.props.stateName, "remove");
                        });
                      }}
                    >
                      {t('G_DELETE')}
                    </button>
                  </div>
                </div>
            }
          </Translation>
        );
      }
    });
  };
  handleClose = e => {
    e.preventDefault();
    const fileslist = document.querySelectorAll(".wrapp-file-upload input");
    for (let i = 0; i < fileslist.length; i++) {
      const element = fileslist[i];
      element.value = "";
    }
    this.setState({ imageCropper: false, cropResult: null });
  };
  render() {
    const {
      title,
      className,
      dimenstion,
      imgExtension,
      maxFileSize,
      multiple,
      value,
      onChange,
      acceptFiles,
      cropper,
      tooltip,
      lock
    } = this.props;
    const Change =
      this.props.multiple || !cropper ? onChange : this.onChange.bind(this);
    const imgdimension =
      dimenstion !== undefined ? dimenstion.split(" x ") : "";

    return (
      <Translation>
        {t => (
          <React.Fragment>
            {!this.setState.imageCropper && (
              <div
                className={
                  className
                    ? `${className} imgupload_wrapper`
                    : "imgupload_wrapper"
                }
              >
                <div className="title-description">
                  <strong className="tooltipHeading">
                    {title && title}
                    {tooltip && (
                      <em className="app-tooltip"><i className="material-icons">info</i>
                        <div className="app-tooltipwrapper">{tooltip}</div>
                      </em>
                    )}
                  </strong>
                  {dimenstion && (
                    <span className="img-dimension">{dimenstion} </span>
                  )}
                  {maxFileSize && (
                    <span className="img-size">
                      {t("G_MAX_SIZE_OF")} {maxFileSize / 1024 / 1024}MB
                    </span>
                  )}
                  {imgExtension && (
                    <React.Fragment>
                      <span>&nbsp;</span>
                      <br></br>
                      <span>{t('G_FORMAT')}</span>
                      <span className="img-extension">
                        &nbsp;{imgExtension.join(', ').toLowerCase()}
                      </span>
                    </React.Fragment>
                  )}
                </div>
                {value !== undefined &&
                  value &&
                  multiple !== true &&
                  cropper ? (
                    <div className={lock ? 'multi-file-wrapp lockitem' : 'multi-file-wrapp'}>
                      <p>
                        <span className="cropper-display-image">
                          <Img
                            alt=""
                            src={this.props.value}
                            onClick={this.handleShowImg.bind(this)}
                          />
                        </span>
                        <span className="file-name">
                          <span
                            data-index={1}
                            data-value="remove-file"
                            onClick={this.handleRemove.bind(this)}
                          >
                            <i className="icon"><Img src={require("img/ico-bin.svg")} alt="" /></i>
                            {`img_${this.props.stateName}`}
                          </span>
                        </span>
                      </p>
                      {lock &&
                        <label className="wrapp-file-upload">
                          <i className="icons">
                            <Img src={require("img/ico-lock.svg")} alt="" />
                          </i>
                        </label>
                      }
                    </div>
                  ) : value !== undefined &&
                    value &&
                    multiple !== true &&
                    !cropper ? (
                      <div className={lock ? 'multi-file-wrapp lockitem' : 'multi-file-wrapp'}>
                        <p>
                          <span
                            title={value.name ? value.name : value}
                            className="file-name"
                          >
                            <span
                              data-index={1}
                              data-value="remove-file"
                              onClick={onChange}
                            >
                              <i className="icon"><Img src={require("img/ico-bin.svg")} alt="" /></i>
                              {value.name ? value.name : value}
                            </span>
                          </span>
                        </p>
                      </div>
                    ) : value !== undefined && multiple === true ? (
                      <div className={lock ? 'multi-file-wrapp lockitem' : 'multi-file-wrapp'}>
                        {this.props.display !== undefined &&
                          this.props.display &&
                          this.props.display.map(row => (
                            <p key={row.id}>
                              <span className="cropper-display-image">
                                <Img
                                  alt=""
                                  src={`${process.env.REACT_APP_EVENTCENTER_URL}/assets/eventsite_banners/${row.image}`}
                                  onClick={() => {
                                    this.setState({
                                      imageCropper: true,
                                      cropResult: `${process.env.REACT_APP_EVENTCENTER_URL}/assets/eventsite_banners/${row.image}`
                                    });
                                  }}
                                />
                              </span>
                              <span className="file-name">
                                <span
                                  data-index={row.id}
                                  data-value="remove-file"
                                  onClick={this.props.onRemove}
                                >
                                  <i className="icon"><Img data-index={row.id} src={require("img/ico-bin.svg")} alt="" /></i>
                                  {`img_${row.image}`}
                                </span>
                              </span>
                            </p>
                          ))}
                        {Object.keys(value).map((data, k) => (
                          <React.Fragment key={k}>
                            {value[data].name !== undefined && value[data].name && (
                              <p key={k}>
                                <span
                                  className="file-name"
                                  title={value[data].name}
                                >
                                  <span
                                    data-index={data}
                                    data-value="remove-file"
                                    onClick={onChange}
                                  >
                                    <i className="icon"><Img src={require("img/ico-bin.svg")} alt="" /></i>
                                    {value[data].name}
                                  </span>
                                </span>
                              </p>
                            )}
                          </React.Fragment>
                        ))}
                        <label className="wrapp-file-upload">
                          <i className="icons">
                            <Img src={require(lock ? "img/ico-lock.svg" : "img/ico-download-lg.svg")} alt="" />
                          </i>
                          {!lock &&
                            <input
                              type="file"
                              multiple={multiple}
                              onChange={onChange}
                              accept={
                                acceptFiles
                                  ? acceptFiles
                                  : "image/x-png,image/gif,image/jpeg"
                              }
                            />
                          }
                        </label>
                      </div>
                    ) : (
                        <label className={lock ? 'wrapp-file-upload lockfile' : 'wrapp-file-upload'}>
                          <i className="icons">
                            <Img src={require(lock ? "img/ico-lock.svg" : "img/ico-download-lg.svg")} alt="" />
                          </i>
                          {!lock &&
                            <input
                              type="file"
                              multiple={multiple}
                              onChange={Change}
                              accept={
                                acceptFiles
                                  ? acceptFiles
                                  : "image/x-png,image/gif,image/jpeg"
                              }
                            />
                          }
                        </label>
                      )}
              </div>
            )}
            {this.state.imageCropper && (
              <div className="wrapper-cropper">
                <div className="container">
                  <div className="wrapper-box">
                    <div className="cropper-header">
                      <h3>
                        {!this.state.cropResult
                          ? t("G_UPLOAD_IMAGES")
                          : t("G_IMAGE_DETAIL")}
                      </h3>
                      <span
                        onClick={this.handleClose.bind(this)}
                        className="cropper-close material-icons"
                      >
                        close
                      </span>
                    </div>
                    <div className="cropper-body">
                      {!this.state.cropResult && (
                        <Cropper
                          style={{
                            maxHeight: "350px",
                            width: "auto",
                            maxWidth: "650px"
                          }}
                          aspectRatio={imgdimension[0] / imgdimension[1]}
                          guides={true}
                          src={this.state.src}
                          ref={cropper => {
                            this.cropper = cropper;
                          }}
                          viewMode={2}
                          autoCropArea={1}
                          dragMode="move"
                          cropBoxMovable={true}
                        />
                      )}
                      {this.state.cropResult && this.state.isLoading && (
                        <Loader />
                      )}
                      {this.state.cropResult && (
                        <div
                          className={`cropper-img-wrapper ${this.state
                            .isLoading && "LoadingImage"}`}
                        >
                          <img
                            alt=""
                            onLoad={() => this.setState({ isLoading: false })}
                            src={this.state.cropResult}
                          />
                        </div>
                      )}
                    </div>
                    {!this.state.cropResult && (
                      <div className="cropper-footer">
                        <label className="wrapp-file-upload btn">
                          <i className="icons">
                            <Img
                              src={require("img/ico-upload-white.svg")}
                              alt=""
                            />
                          </i>
                          {t("G_CHOOSE")}
                          <input
                            type="file"
                            multiple={multiple}
                            onChange={Change}
                            accept={
                              acceptFiles
                                ? acceptFiles
                                : "image/x-png,image/gif,image/jpeg"
                            }
                          />
                        </label>
                        <button
                          onClick={this.cropImage.bind(this)}
                          className="btn btn-default"
                        >
                          {t("G_SAVE")}
                        </button>
                      </div>
                    )}
                  </div>
                </div>
              </div>
            )}
          </React.Fragment>
        )}
      </Translation>
    );
  }
}
