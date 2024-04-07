import React, { Component } from "react";
export default function asyncComponent(getComponent) {
    class AsyncComponent extends Component {
        _isMounted = false;
        static Component = null;
        state = { Component: AsyncComponent.Component };

        componentDidMount() {
            this._isMounted = true;
            if (!this.state.Component) {
                if (this._isMounted) {
                    getComponent().then(Component => {
                        AsyncComponent.Component = Component
                        this.setState({ Component })
                    });
                }
            }
        }

        componentWillUnmount() {
            this._isMounted = false;
        }

        render() {
            const { Component } = this.state
            if (Component) {
                return <Component {...this.props} />
            }
            return null;
        }
    }
    return AsyncComponent;
}