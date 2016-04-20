// Librerias
import React from 'react'
// Componentes
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'

export class TablaAuditoriasPendientes extends React.Component{

    render() {
        return (
            <StickyContainer type={React.DOM.table}
                             //className={"table table-bordered table-condensed "+css.tableFixed}
            >
                <colgroup>
                    <col className={null}/>
                    <col className={null}/>
                    <col className={null}/>
                    <col className={null}/>
                </colgroup>
                <thead>

                </thead>
                <tbody>
                    {this.props.children}
                </tbody>
            </StickyContainer>
        )
    }
}