import React, { Component } from 'react';

export default class EditorPreview extends Component {
    render() {
        return (
            <div className="editor-preview">
                <h2>Rendered content</h2>
                <div dangerouslySetInnerHTML={{ __html: this.props.data }}></div>
            </div>
        );
    }
}

