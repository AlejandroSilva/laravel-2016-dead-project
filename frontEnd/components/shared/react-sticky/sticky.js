import React from 'react';
import ReactDOM from 'react-dom';
import watcher from './watcher';

class Sticky extends React.Component {

    constructor(props) {
        super(props);
        this.state = { };
    }

    /*
     * Anytime new props are received, force re-evaluation
     */
    componentWillReceiveProps() {
        let origin = this.calculateOrigin();
        this.setState({ origin });

        watcher.emit();
    }

    componentDidMount() {
        watcher.on(this.transition.bind(this));

        let origin = this.calculateOrigin();
        this.setState({ origin });
    }

    componentWillUnmount() {
        watcher.off(this.transition.bind(this));
    }

    /*
     * Return the distance of the scrollbar from the
     * top of the window plus the total height of all
     * stuck Sticky instances above this one.
     */
    pageOffset() {
        return (window.pageYOffset || document.documentElement.scrollTop);
    }

    cumulativeTopCorrection() {
        return this.context.container.state.cumulativeTopCorrection;
    }

    /*
     * Returns true/false depending on if this should be sticky.
     */
    shouldBeSticky() {
        let offset = this.pageOffset();
        let origin =  this.state.origin - this.cumulativeTopCorrection();
        let containerNode = ReactDOM.findDOMNode(this.context.container);

        // check conditions
        let stickyTopConditionsMet = offset >= origin + this.props.topOffset;
        let stickyBottomConditionsMet = offset < containerNode.getBoundingClientRect().height + origin;
        return stickyTopConditionsMet && stickyBottomConditionsMet;
    }

    transition() {
        if (this.context.container) {
            this.nextState(this.shouldBeSticky());
        }
    }

    /*
     * Returns the y-coordinate of the top of this element.
     */
    calculateOrigin() {
        let node = React.findDOMNode(this);

        // Do some ugly DOM manipulation to where this element's non-sticky position would be
        let previousPosition = node.style.position;
        node.style.position = '';
        let origin = node.getBoundingClientRect().top + this.pageOffset();
        node.style.position = previousPosition;
        return origin;
    }
    /*
     * If sticky, merge this.props.stickyStyle with this.props.style.
     * If not, just return this.props.style.
     */
    nextStyle(shouldBeSticky) {
        if (shouldBeSticky) {
            let node = ReactDOM.findDOMNode(this);
            let containerNode = ReactDOM.findDOMNode(this.context.container);

            // inherit the boundaries of the container
            var rect = containerNode.getBoundingClientRect();
            var style = Object.assign({}, this.props.style);
            style.position = 'fixed';
            style.left = rect.left;
            style.width = rect.width;
            style.top = this.cumulativeTopCorrection();

            let bottomLimit = rect.bottom - node.getBoundingClientRect().height;
            if (style.top > bottomLimit) style.top = bottomLimit;

            // Finally, override the best-fit style with any user props
            return Object.assign(style, this.props.stickyStyle);
        } else {
            return this.props.style;
        }
    }

    /*
     * If sticky, merge this.props.stickyClass with this.props.className.
     * If not, just return this.props.className.
     */
    nextClassName(shouldBeSticky) {
        var className = this.props.className;
        if (shouldBeSticky) {
            className += ' ' + this.props.stickyClass;
        }
        return className;
    }

    /*
     * Transition to the next state.
     *
     * Updates the isSticky, style, and className state
     * variables.
     *
     * If sticky state is different than the previous,
     * fire the onStickyStateChange callback.
     */
    nextState(shouldBeSticky) {
        var hasChanged = this.state.isSticky !== shouldBeSticky;

        // Update this state
        this.setState({
            isSticky: shouldBeSticky,
            style: this.nextStyle(shouldBeSticky),
            className: this.nextClassName(shouldBeSticky)
        });

        if (hasChanged) {
            // Update container state
            if (this.context.container) {
                this.context.container.nextState({
                    isSticky: shouldBeSticky,
                    height: ReactDOM.findDOMNode(this).getBoundingClientRect().height
                });
            }

            // Publish sticky state change
            this.props.onStickyStateChange(shouldBeSticky);
        }
    }

    /*
     * The special sauce.
     */
    render() {
        // https://github.com/captivationsoftware/react-sticky/commit/e33c4cd873fc3399c96eead97871b2d74235f6b1#diff-18aac22485b5225ec8d6628cce48a7bf
        // "VIEJO", commit "ES6 Update"
        return this.props.type({
            style: this.state.style,
            className: this.state.className
        }, this.props.children);

        // "NUEVO", no permite cambiar el tipo de elemento
        //return (
        //    <div style={this.state.style} className={this.state.className}>
        //        {this.props.children}
        //    </div>
        //);
    }
}

Sticky.contextTypes = {
    type: React.PropTypes.func,
    container: React.PropTypes.any
}


/*
 * Default properties...
 */
Sticky.defaultProps = {
    type: React.DOM.div,
    className: '',
    style: {},
    stickyClass: 'sticky',
    stickyStyle: {},
    topOffset: 0,
    onStickyStateChange: function () {}
}

export default Sticky;