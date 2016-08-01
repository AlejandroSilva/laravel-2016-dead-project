// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { ModalNuevoAlmacen, NuevoAlmacen } from './ModalNuevo.jsx'
// Styles
import classNames from 'classnames/bind'
import * as css from './almacenesStyles.css'
let cx = classNames.bind(css)

export class SeccionAlmacenes extends React.Component {
    constructor(props) {
        super(props)

        this.showModal = ()=>{
            this.refModalNuevo.showModal()
        }
        this.hideModal = ()=>{
            this.refModalNuevo.hideModal()
        }
    }
    render(){
        let {almacenes, almacenSeleccionado, seleccionarAlmancen,
            responsables, fetchResponsables, agregarAlmacen, cargandoDatos} = this.props
        return (
            <div>
                <ModalNuevoAlmacen ref={ref=>this.refModalNuevo=ref}>
                    <NuevoAlmacen
                        // metodos
                        hideModal={this.hideModal}
                        fetchResponsables={fetchResponsables}
                        agregarAlmacen={agregarAlmacen}
                        // objetos
                        responsables={responsables}
                    />
                </ModalNuevoAlmacen>

                <div className="list-group">
                    <button type="button" className={cx(
                            'list-group-item',
                            'group-item-compact',
                            {'active': almacenSeleccionado==0}
                        )}
                            onClick={seleccionarAlmancen.bind(this, 0)}
                            disabled={cargandoDatos==true}
                    >Todos</button>
                    {almacenes.map(almacen=>
                        <button type="button" className={cx(
                                'list-group-item',
                                'group-item-compact',
                                {'active': almacenSeleccionado==almacen.idAlmacenAF}
                            )}
                                key={almacen.idAlmacenAF}
                                onClick={seleccionarAlmancen.bind(this, almacen.idAlmacenAF)}
                                disabled={cargandoDatos==true}
                        >{almacen.nombre}</button>
                    )}
                    <button type="button"
                            className="list-group-item default list-group-item-success"
                            onClick={ this.showModal }
                    >
                        ** Agregar Almacen **
                    </button>
                </div>
            </div>
        )
    }
}

SeccionAlmacenes.propTypes = {
    seleccionarAlmancen: PropTypes.func.isRequired,
    fetchResponsables: PropTypes.func.isRequired,
    agregarAlmacen: PropTypes.func.isRequired,
    // objetos
    cargandoDatos: PropTypes.bool.isRequired,
    almacenes: PropTypes.arrayOf(PropTypes.object).isRequired,
    almacenSeleccionado: PropTypes.number.isRequired,
    responsables: PropTypes.arrayOf(PropTypes.object).isRequired
}