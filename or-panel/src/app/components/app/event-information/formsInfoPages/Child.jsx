import React from "react";
import Img from 'react-image';
import { Droppable, Draggable } from "react-beautiful-dnd";
import { Translation } from "react-i18next";

const getItemStyle = (isDragging, draggableStyle) => ({
  // change background colour if dragging
  background: isDragging ? "#F0F0F0" : "white",
  margin: "3px 0",
  // styles we need to apply on draggables
  ...draggableStyle,
});

const getListStyle = (isDraggingOver) => ({
  background: isDraggingOver ? "white" : "white",
  margin: "3px 0",
});

export default class Child extends React.Component {
  handleDrowon = e => {
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
    return (
      <Translation>
        {(t) => (
          <Droppable droppableId={this.props.type} type={`${this.props.data}`}>
            {(provided, snapshot) => (
              <div
                ref={provided.innerRef}
                style={getListStyle(snapshot.isDraggingOver)}
                className="practical-data-wrapper"
              >
                {this.props.subItems.map((item, index) => (
                  <Draggable
                    key={item.id}
                    draggableId={item.id.toString()}
                    index={index}
                  >
                    {(provided, snapshot) => (
                      <div
                        ref={provided.innerRef}
                        {...provided.draggableProps}
                        className="practical-data-list-wrapp"
                        style={getItemStyle(
                          snapshot.isDragging,
                          provided.draggableProps.style
                        )}
                      >
                        <div className="practical-data-list">
                          <span
                            {...provided.dragHandleProps}
                            className="handle-drag"
                          >
                            <i className="material-icons">more_vert more_vert</i>
                          </span>
                          {item.type && (
                            <span className="file-icons">
                              <i className="icons"><Img
                                src={require(`img/ico-${item.type}-dark.svg`)} /></i>
                            </span>
                          )}
                          {item.detail.name}
                          {this.props.displayPanel && (
                            <div className="practical-edit-panel">
                              <span
                                onClick={this.props.onUpdate(
                                  item.type,
                                  this.props.form_container,
                                  item
                                )}
                                className="btn_edit"
                              >
                                <i className="icons"><Img
                                  src={require("img/ico-edit.svg")} /></i>
                              </span>
                              <span
                                onClick={this.props.onRemove(
                                  item.id,
                                  item.type,
                                  (item.section_id === undefined)
                                )}
                                className="btn_delete"
                              >
                                <i className="icons"><Img
                                  src={require("img/ico-delete.svg")} /></i>
                              </span>
                              {item.type === "folder" && (
                                <div className="wrapp_add_button">
                                  <span
                                    onClick={this.handleDrowon.bind(this)}
                                    className="btn_addmore"
                                  >
                                    <i className="icons"><Img
                                      src={require("img/ico-dots.svg")} /></i>
                                  </span>
                                  <div className="drop_down_panel">
                                    {this.props.cms !== "information-pages" && <button
                                      onClick={this.props.onClick(
                                        "folder",
                                        this.props.form_container,
                                        item.id
                                      )}
                                      className="btn"
                                    >
                                      <i className="icons"><Img
                                        src={require("img/ico-addfolder-lg.svg")} /></i>
                                      {t("PI_ADD_FOLDER")}
                                    </button>}
                                    <button
                                      onClick={this.props.onClick(
                                        "link",
                                        this.props.form_container,
                                        item.id
                                      )}
                                      className="btn"
                                    >
                                      <i className="icons"><Img
                                        src={require("img/ico-addlink-lg.svg")} /></i>
                                      {t("PI_ADD_LINK")}
                                    </button>
                                    <button
                                      onClick={this.props.onClick(
                                        "page",
                                        this.props.form_container,
                                        item.id
                                      )}
                                      className="btn"
                                    >
                                      <i className="icons"><Img
                                        src={require("img/ico-addpage-lg.svg")} /></i>
                                      {t("PI_ADD_PAGE")}
                                    </button>
                                  </div>
                                </div>
                              )}
                            </div>
                          )}
                        </div>
                        {item.subMenuItems && (
                          <div className="inner-droppable">
                            <Child
                              subItems={item.subMenuItems}
                              type={`${this.props.type}-${item.id}`}
                              onClick={this.props.onClick}
                              onRemove={this.props.onRemove}
                              onUpdate={this.props.onUpdate}
                              displayPanel={this.props.displayPanel}
                              form_container={this.props.form_container}
                              data={`${this.props.data}.${index}.subItems`}
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
        )}
      </Translation>
    );
  }
}
