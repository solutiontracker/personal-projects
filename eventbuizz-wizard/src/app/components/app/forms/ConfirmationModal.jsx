import React, { Component } from 'react';
import { Translation } from "react-i18next";
import NavigationPrompt from "react-router-navigation-prompt";
import Modal from "react-modal";

const customStyles = {
    overlay: {
        position: 'fixed',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        zIndex: '99',
        backgroundColor: 'rgba(0, 0, 0, 0.75)'
    },
    content: {
        top: '50%',
        left: '50%',
        right: 'auto',
        bottom: 'auto',
        marginRight: '-50%',
        padding: 0,
        border: 'none',
        borderRadius: '5px',
        transform: 'translate(-50%, -50%)'
    }
};

export default class ConfirmationModal extends Component {

    componentWillMount() {
        Modal.setAppElement('body');
    }

    render() {

        return (
            <Translation>
                {t => (
                    <NavigationPrompt when={(crntLocation, nextLocation) =>
                        this.props.update && (nextLocation && !nextLocation.pathname.startsWith(crntLocation.pathname))
                    }>
                        {({ onConfirm, onCancel }) => (
                            <React.Fragment>
                                <Modal
                                    isOpen={this.props.update}
                                    handleClose={this.hideModal}
                                    style={customStyles}
                                >
                                    <div className="react-confirm-alert">
                                        <div className="app-main-popup">
                                            <div className="app-header">
                                                <h4>{t('G_CONFIRM')}</h4>
                                            </div>
                                            <div className="app-body">
                                                <p>{t('G_LEAVE_SCREEN_MESSAGE')}</p>
                                            </div>
                                            <div className="app-footer">
                                                <button style={{ height: '42px' }} className="btn btn-cancel" onClick={onCancel}>{t('G_CANCEL')}</button>
                                                <button style={{ height: '42px' }} className="btn btn-success" onClick={onConfirm}>{t('G_OK')}</button>
                                            </div>
                                        </div>
                                    </div>
                                </Modal>
                            </React.Fragment>
                        )}
                    </NavigationPrompt>
                )}
            </Translation>
        );
    }
}