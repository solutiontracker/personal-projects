import React, { Component } from 'react';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import { SubRegistrationService } from 'services/sub-registration/sub-registration-service';
import { Translation } from "react-i18next";
import { confirmAlert } from 'react-confirm-alert'; // Import

// a little function to help us with reordering the result
const reorder = (list, startIndex, endIndex) => {
    const result = Array.from(list);
    const [removed] = result.splice(startIndex, 1);
    result.splice(endIndex, 0, removed);
    return result;
};


export default class AnswerWidget extends Component {
    constructor(props) {
        super(props);
        this.state = {
            answer: this.props.answer,
            column: this.props.column !== undefined ? true : false
        }
        this.onDragEnd = this.onDragEnd.bind(this);
    }

    onDragEnd(result) {
        if (!result.destination) {
            return;
        }
        const sourceIndex = result.source.index;
        const destIndex = result.destination.index;
        if (result.type === "droppableItem") {
            const answer = reorder(this.state.answer, sourceIndex, destIndex);
            this.setState({ answer });

        }
        setTimeout(() => { this.props.onChange(this.state.answer); }, 50)
    }

    deleteOption = index => e => {
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
                                                e.preventDefault();
                                                var answer = [...this.state.answer];
                                                var option_id = answer[index].id;
                                                answer.splice(index, 1);
                                                this.setState({
                                                    answer: answer
                                                }, () => {
                                                    this.props.onChange(this.state.answer);
                                                    if (option_id !== undefined) {
                                                        if (this.state.column) {
                                                            SubRegistrationService.destroy(this.state, option_id, 'option_matrix');
                                                        } else {
                                                            SubRegistrationService.destroy(this.state, option_id, 'option');
                                                        }
                                                    }
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

        e.preventDefault();
    }

    addOptionElement = e => {
        e.preventDefault();
        var answer = [...this.state.answer];
        const newrow = {
            value: '',
            correct: 0
        }
        answer = answer.concat(newrow);
        this.setState({
            answer: answer
        })

        setTimeout(() => { this.props.onChange(this.state.answer); }, 50)
    }

    updateOptionElement = e => {
        var index = e.target.getAttribute('data-index');
        var answer = [...this.state.answer];
        // else update value property fo all answers.
        answer[index].value = e.target.value;

        this.setState({
            answer
        })
        setTimeout(() => { this.props.onChange(this.state.answer); }, 50)
    }

    render() {
        return (
            <Translation>
                {
                    t =>
                        <div>
                            {this.state.answer && (
                                <DragDropContext onDragEnd={this.onDragEnd}>
                                    <Droppable droppableId="droppable" type="droppableItem">
                                        {(provided, snapshot) => (
                                            <div
                                                ref={provided.innerRef}
                                                className="add-question-wrapper"
                                            >
                                                {this.state.answer.map((item, key) => (
                                                    <Draggable key={key} draggableId={`item-${key}`} index={key}>
                                                        {(provided, snapshot) => (
                                                            <div
                                                                className="questions-data-list"
                                                                ref={provided.innerRef}
                                                                {...provided.draggableProps}
                                                            >
                                                                <span
                                                                    {...provided.dragHandleProps}
                                                                    className="handle-drag"
                                                                >
                                                                    <i className="material-icons">more_vert more_vert</i>
                                                                </span>
                                                                <div className="list">
                                                                    <input
                                                                        onChange={this.updateOptionElement.bind(this)}
                                                                        data-index={key} type="text"
                                                                        placeholder={(this.props.column !== undefined && this.props.column === true) ? t('SR_ADD_COLUMN') : t('SR_ADD_ANSWER')} value={item.value} />
                                                                    {this.state.answer.length > 1 &&
                                                                        <span onClick={this.deleteOption(key)}
                                                                            className="remove"><i
                                                                                className="material-icons">close</i></span>}
                                                                </div>
                                                                {provided.placeholder}
                                                            </div>
                                                        )}
                                                    </Draggable>
                                                ))}
                                                {provided.placeholder}
                                                <button onClick={this.addOptionElement.bind(this)}
                                                    className="btn btn_addquestion">{(this.props.column !== undefined && this.props.column === true) ? t('SR_ADD_A_COLUMN') : t('SR_ADD_AN_OPTION')}
                                                </button>
                                            </div>
                                        )}
                                    </Droppable>
                                </DragDropContext>
                            )}

                        </div>
                }
            </Translation>
        )
    }
}
