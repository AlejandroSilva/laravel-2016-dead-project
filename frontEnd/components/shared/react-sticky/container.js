import React from 'react';
import Sticky from './sticky';

class Container extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            topCorrection: 0,
            cumulativeTopCorrection: 0
        };
    }

    getChildContext() {
        return {
            container: this
        }
    }

    componentDidMount() {
        this.updateCumulativeTopCorrection();
    }


    componentDidUpdate() {
        this.updateCumulativeTopCorrection();
    }

    cumulativeTopCorrection() {
        let topCorrection = 0;
        if (this.context.container) {
            let container = this.context.container;
            while (container) {
                topCorrection += container.state.topCorrection;
                container = container.context.container;
            };
        }
        return topCorrection;
    }

    updateCumulativeTopCorrection() {
        let cumulativeTopCorrection = this.cumulativeTopCorrection();
        if (cumulativeTopCorrection !== this.state.cumulativeTopCorrection) {
            this.setState({ cumulativeTopCorrection });
        }
    }

    nextState(state) {
        let topCorrection = state.isSticky ? state.height : 0;
        this.setState({ topCorrection });
    }

    render() {
        let style = Object.assign({}, this.props.style || {});

        let paddingTop = style.paddingTop || 0;
        style.paddingTop = paddingTop + this.state.topCorrection;

        // Mi version: hacer un pull request con esto:
        return this.props.type({
            style: style,
            className: this.props.className,
        }, this.props.children);

        // ORIGINAL
        //return <div {...this.props} style={style}>
        //    {this.props.children}
        //</div>
    }
}

Container.contextTypes = {
    container: React.PropTypes.any
}

Container.childContextTypes = {
    container: React.PropTypes.any
}

// TODO: Hacer un pull request con esto
Container.defaultProps = {
    type: React.DOM.div
}

export default Container;