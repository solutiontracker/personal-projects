import * as React from "react";
import { NavLink } from 'react-router-dom';
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";
import Img from 'react-image';
import Link from "@/app/event-information/forms/Link";
import Folder from "@/app/event-information/forms/Folder";
import Page from "@/app/event-information/forms/Page";
import Child from "@/app/event-information/forms/Child";
import { InformationService } from 'services/information/information-service';
import Loader from '@/app/forms/Loader';
import { confirmAlert } from 'react-confirm-alert'; // Import
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { Translation } from "react-i18next";
import { connect } from 'react-redux';

const in_array = require("in_array");

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/module/sub-registration", "ddirectory": "/event/module/documents" };

function setToValue(obj, path, sourceIndex, destIndex) {
  path = path.split('.');
  for (let i = 0; i < path.length; i++) {
    obj = obj[path[i]];
    if (i === path.length - 2) {
      const reorderedSubItems = reorder(
        obj.subItems,
        sourceIndex,
        destIndex
      );
      obj.subItems = reorderedSubItems;
      return obj.subItems;
    }
  }
}

// a little function to help us with reordering the result
const reorder = (list, startIndex, endIndex) => {
  const result = Array.from(list);
  const [removed] = result.splice(startIndex, 1);
  result.splice(endIndex, 0, removed);
  return result;
};

const getItemStyle = (isDragging, draggableStyle) => ({
  background: isDragging ? "#F0F0F0" : "white",
  margin: '0px 0',
  // styles we need to apply on draggables
  ...draggableStyle
});

const getListStyle = isDraggingOver => ({
  background: isDraggingOver ? "white" : "white",
  margin: '0px 0',
  width: '100%'
});

class AdditionalInformation extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      items: [],
      displayPanel: true,
      link: false,
      folder: false,
      page: false,
      displayInner: false,
      parent_id: '',
      childEditMode: false,
      editData: undefined,
      cms: "additional-info",

      //errors & loading
      preLoader: true
    };

    this.onDragEnd = this.onDragEnd.bind(this);

    document.body.addEventListener('click', this.handleDocument.bind(this));
  }

  componentDidMount() {
    this._isMounted = true;
    this.listing();

    //set next previous
    if (this.props.event.modules !== undefined && this.props.event.modules.length > 0) {
      let modules = this.props.event.modules.filter(function (module, i) {
        return ((!in_array(module.alias, ["polls"]) && Number(module.status) === 1) || (in_array(module.alias, ["subregistration"])));
      });

      let index = modules.findIndex(function (module, i) {
        return module.alias === "additional_info";
      });

      this.setState({
        next: (modules[index + 1] !== undefined && module_routes[modules[index + 1]['alias']] !== undefined  ? module_routes[modules[index + 1]['alias']] : "/event/manage/surveys"),
        prev: (modules[index - 1] !== undefined && module_routes[modules[index - 1]['alias']] !== undefined  ? module_routes[modules[index - 1]['alias']] : (Number(this.props.event.is_registration) === 1 ? "/event/module/eventsite-module-order" : (Number(this.props.event.is_app) === 1 ? "/event/module/event-module-order" : "/event_site/billing-module/manage-orders")))
      });

    }
  }

  handleDocument = e => {
    var query = document.querySelector('.btn_addmore');
    if (query !== null && query.classList !== null && query.classList.contains('active') && e.target.classList !== null && !e.target.classList.contains('btn_addmore')) {
      query.classList.remove('active');
    }
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  onDragEnd(result) {
    if (!result.destination) {
      return;
    }
    const sourceIndex = result.source.index;
    const destIndex = result.destination.index;
    if (result.type === "droppableItem") {
      const items = reorder(this.state.items, sourceIndex, destIndex);
      this.setState({ items: items });
      this.updateOrder(items);
    } else {
      let type = result.type;
      let newItems = [...this.state.items];
      const items = setToValue(newItems, type, sourceIndex, destIndex);
      this.setState({ items: newItems });
      this.updateOrder(items);
    }
  }

  updateOrder = (items) => {
    InformationService.updateOrder(this.state, items)
      .then(
        response => {
          if (response.success) {
            this.listing(true);
          }
        },
        error => { }
      );
  }

  listing = (loader = false) => {
    this.setState({ preLoader: (!loader ? true : false) });
    InformationService.listing(this.state)
      .then(
        response => {
          if (response.success) {
            if (response.data) {
              if (this._isMounted) {
                this.setState({
                  items: response.data,
                  link: false,
                  folder: false,
                  page: false,
                  displayInner: false,
                  displayPanel: true,
                  childEditMode: false,
                  editData: undefined,
                  preLoader: false
                });
              }
            }
          }
        },
        error => { }
      );
  }

  cancel = value => {
    this.setState({
      [value]: false,
      displayPanel: true,
      displayInner: false,
      parent_id: '',
      childEditMode: false,
      editData: undefined
    });
  }

  handleshowInner = (input, index, parent_id) => e => {
    e.preventDefault();
    this.setState({
      [input]: true,
      displayPanel: false,
      displayInner: index,
      parent_id: parent_id
    });
  }

  handleEditMode = (input, index, item) => e => {
    e.preventDefault();
    this.setState({
      [input]: true,
      displayPanel: false,
      displayInner: index,
      childEditMode: true,
      editData: item
    });
  }

  removeItem = (id, type) => e => {
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
                        InformationService.destroy(this.state, id, type)
                          .then(
                            response => {
                              if (response.success) {
                                this.listing();
                              }
                            },
                            error => {
                            }
                          );
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

    e.preventDefault();
  }

  handleshow = input => e => {
    e.preventDefault();
    this.setState({
      [input]: true,
      displayPanel: false
    })
  }

  handleDropdown = e => {
    e.preventDefault();
    if (e.target.classList.contains('active')) {
      e.target.classList.remove('active');
    } else {

      var query = document.querySelectorAll('.btn_addmore');
      for (var i = 0; i < query.length; ++i) {
        query[i].classList.remove('active');
      }
      e.target.classList.add('active');
    }
  }

  render() {

    let module = this.props.event.modules.filter(function (module, i) {
      return in_array(module.alias, ["additional_info"]);
    });

    return (
      <Translation>
        {
          t =>
            <div className="wrapper-content third-step">
              {this.state.preLoader &&
                <Loader />
              }
              {!this.state.preLoader && (
                <React.Fragment>
                  <div style={{ height: '100%' }}>
                    <div style={{ margin: '0' }} className="new-header clearfix">
                      <div className="row">
                        <div className="col-6">
                          <h1 className="section-title">{(module[0]['value'] !== undefined ? module[0]['value'] : t('AI_NAME'))}</h1>
                          <p>{t('AI_SUB_HEADING')} </p>
                        </div>
                        <div className="col-6">
                          {this.state.displayPanel && <div className="new-right-header new-panel-buttons float-right button-large ">
                            <button className="btn_addNew" onClick={this.handleshow('folder')}><span
                              className="icons"><Img src={require("img/ico-addfolder-lg.svg")} /></span>
                              {/* {t('PI_ADD_FOLDER')} */}
                            </button>
                            <button className="btn_addNew" onClick={this.handleshow('link')}>
                              <i className="icons"><Img
                                src={require("img/ico-addlink-lg.svg")} /></i>
                              {/* {t('PI_ADD_LINK')} */}
                            </button>
                            <button className="btn_addNew" onClick={this.handleshow('page')}><i className="icons"><Img
                              src={require("img/ico-addpage-lg.svg")} /></i>
                              {/* {t('PI_ADD_PAGE')} */}
                            </button>
                          </div>}
                        </div>
                      </div>
                    </div>
                    <div>
                      {this.state.link && this.state.displayInner === false && (
                        <div style={{ marginBottom: '15px' }} className="parctical-info-widgets">
                          <Link
                            datamode={t('PI_ADD_LINK')}
                            onCancel={this.cancel}
                            save={this.save}
                            listing={this.listing}
                            cms="additional-info"
                          />
                        </div>
                      )}
                      {this.state.folder && this.state.displayInner === false && (
                        <div style={{ marginBottom: '15px' }} className="parctical-info-widgets">
                          <Folder
                            datamode={t('PI_ADD_FOLDER')}
                            onCancel={this.cancel}
                            save={this.save}
                            listing={this.listing}
                            cms="additional-info"
                          />
                        </div>
                      )}
                      {this.state.page && this.state.displayInner === false && (
                        <div style={{ marginBottom: '15px' }} className="parctical-info-widgets">
                          <Page
                            datamode={t('PI_ADD_PAGE')}
                            onCancel={this.cancel}
                            save={this.save}
                            listing={this.listing}
                            cms="additional-info"
                          />
                        </div>
                      )}
                    </div>
                  {this.state.items && (
                    <DragDropContext onDragEnd={this.onDragEnd}>
                      <Droppable droppableId="droppable" type="droppableItem">
                        {(provided, snapshot) => (
                          <div
                            ref={provided.innerRef}
                            style={getListStyle(snapshot.isDraggingOver)}
                            className="practical-data-wrapper"
                          >
                            {this.state.items.map((item, index) => (
                              <Draggable key={item.id} draggableId={item.id.toString()} index={index}>
                                {(provided, snapshot) => (
                                  <div
                                    ref={provided.innerRef}
                                    className="practical-data-list-wrapp"
                                    {...provided.draggableProps}
                                    style={getItemStyle(
                                      snapshot.isDragging,
                                      provided.draggableProps.style
                                    )}
                                  >
                                    <div className="practical-data-list">
                                      <span
                                        {...provided.dragHandleProps}
                                        className={`${!this.state.displayPanel && 'disable'} handle-drag`}
                                      >
                                        <i className="material-icons">more_vert more_vert</i>
                                      </span>
                                      {item.type && (
                                        <span className="file-icons"><i className="icons"><Img
                                          src={require(`img/ico-${item.type}-dark.svg`)} /></i></span>
                                      )}
                                      {item.detail.name}
                                      {this.state.displayPanel && (
                                        <div className="practical-edit-panel">
                                          <span onClick={this.handleEditMode(item.type, item.id, item)}
                                            className="btn_edit"><i className="icons"><Img
                                              src={require("img/ico-edit.svg")} /></i></span>
                                          <span onClick={this.removeItem(item.id, item.type)}
                                            className="btn_delete"><i className="icons"><Img
                                              src={require("img/ico-delete.svg")} /></i></span>
                                          {item.type === 'folder' && (
                                            <div className="wrapp_add_button">
                                              <span onClick={this.handleDropdown.bind(this)}
                                                className="btn_addmore"><i className="icons"><Img
                                                  src={require("img/ico-dots.svg")} /></i></span>
                                              <div className="drop_down_panel">
                                                <button
                                                  onClick={this.handleshowInner('folder', item.id, item.id)}
                                                  className="btn"><i className="icons"><Img
                                                    src={require("img/ico-addfolder-lg.svg")} /></i>{t('AI_ADD_FOLDER')}
                                                </button>
                                                <button
                                                  onClick={this.handleshowInner('link', item.id, item.id)}
                                                  className="btn"><i className="icons"><Img
                                                    src={require("img/ico-addlink-lg.svg")} /></i>{t('AI_ADD_LINK')}
                                                </button>
                                                <button
                                                  onClick={this.handleshowInner('page', item.id, item.id)}
                                                  className="btn"><i className="icons"><Img
                                                    src={require("img/ico-addpage-lg.svg")} /></i>{t('AI_ADD_PAGE')}
                                                </button>
                                              </div>
                                            </div>
                                          )}
                                        </div>
                                      )}
                                    </div>
                                    {item.subItems && (
                                      <div className="inner-droppable">
                                        <Child
                                          subItems={item.subItems}
                                          type={`item-${item.id}`}
                                          onClick={this.handleshowInner}
                                          onRemove={this.removeItem}
                                          onUpdate={this.handleEditMode}
                                          form_container={item.id}
                                          displayPanel={this.state.displayPanel}
                                          data={`${index}.subItems`}
                                          cms="additional-info"
                                        />
                                      </div>
                                    )}
                                    {this.state.link && this.state.displayInner === item.id && (
                                      <div className="parctical-info-widgets">
                                        <Link
                                          datamode={this.state.childEditMode ? t('PI_EDIT_LINK') : t('AI_ADD_LINK')}
                                          editData={this.state.editData}
                                          onCancel={this.cancel}
                                          save={this.save}
                                          parent_id={this.state.parent_id}
                                          listing={this.listing}
                                          cms="additional-info"
                                        />
                                      </div>
                                    )}
                                    {this.state.folder && this.state.displayInner === item.id && (
                                      <div className="parctical-info-widgets">
                                        <Folder
                                          datamode={this.state.childEditMode ? t('PI_EDIT_FOLDER') : t('PI_ADD_FOLDER')}
                                          onCancel={this.cancel}
                                          save={this.save}
                                          editData={this.state.editData}
                                          parent_id={this.state.parent_id}
                                          listing={this.listing}
                                          cms="additional-info"
                                        />
                                      </div>
                                    )}
                                    {this.state.page && this.state.displayInner === item.id && (
                                      <div className="parctical-info-widgets">
                                        <Page
                                          datamode={this.state.childEditMode ? t('PI_EDIT_PAGE') : t('PI_ADD_PAGE')}
                                          onCancel={this.cancel}
                                          save={this.save}
                                          editData={this.state.editData}
                                          parent_id={this.state.parent_id}
                                          listing={this.listing}
                                          cms="additional-info"
                                        />
                                      </div>
                                    )}
                                    {provided.placeholder}
                                  </div>
                                )}
                              </Draggable>
                            ))}
                            {provided.placeholder}
                          </div>
                        )}
                      </Droppable>
                    </DragDropContext>
                  )}
                  </div>
                  <div className="bottom-component-panel clearfix">
                    <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                      <i className='material-icons'>remove_red_eye</i>
                      {t('G_PREVIEW')}
                    </NavLink>
                    {this.state.prev !== undefined && (
                      <NavLink className="btn btn-prev-step" to={this.state.prev}><span className="material-icons">
                        keyboard_backspace</span></NavLink>
                    )}
                    {this.state.next !== undefined && (
                      <NavLink className="btn btn-next-step" to={this.state.next}>{t('G_NEXT')}</NavLink>
                    )}
                  </div>
                </React.Fragment>
              )}
            </div>
        }
      </Translation>
    );
  }
}

function mapStateToProps(state) {
  const { event, update } = state;
  return {
    event, update
  };
}

export default connect(mapStateToProps)(AdditionalInformation);