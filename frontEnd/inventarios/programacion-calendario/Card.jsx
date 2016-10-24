// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes

// Styles
import classNames from 'classnames/bind'
import * as css from './card.css'
let cx = classNames.bind(css)

export const Card = ({children, className})=>{
    return (
        <div className={cx(className, 'day', 'card')}>
            {children}
        </div>
    )
}

export const CardHeader = ({children, className})=>{
    return (
        <div className={cx(className, "card-header")}>
            {children}
        </div>
    )
}

export const CardBody = ({children, className})=>{
    return (
        <div className={cx(className, 'card-body')}>
            {children}
        </div>
    )
}