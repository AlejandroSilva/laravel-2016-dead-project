// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { TablaGeneralArticulos } from './TablasArticulos.jsx'
import { ModalTransferencia, TransferenciaArticulos } from './ModalTransferencia.jsx'
import { ModalEntrega, EntregaArticulos } from './ModalEntrega.jsx'

export class SeccionArticulos extends React.Component {
    constructor(props) {
        super(props)

        this.showModalTransferencia = ()=>{
            this.refModalTransferencia.showModal()
        }
        this.hideModalTransferencia = ()=>{
            this.refModalTransferencia.hideModal()
        }
        this.showModalEntrega = ()=>{
            this.refModalEntrega.showModal()
        }
        this.hideModalEntrega = ()=>{
            this.refModalEntrega.hideModal()
        }
    }

    render(){
        return (
            <div>
                {/* Modales */}
                <ModalTransferencia ref={ref=>this.refModalTransferencia=ref}>
                    <TransferenciaArticulos
                        // metodos
                        hideModal={this.hideModalTransferencia}
                        buscarBarra={this.props.buscarBarra}
                        realizarTransferencia={this.props.realizarTransferencia}
                        // objetos
                        almacenes={this.props.almacenes.filter(alm=>alm.idAlmacenAF!=1)}
                        almacenOrigen={this.props.almacenSeleccionado}
                    />
                </ModalTransferencia>

                <ModalEntrega ref={ref=>this.refModalEntrega=ref}>
                    <EntregaArticulos
                        // Metodos (ordenar por grupos en activo fijo)
                        hideModal={this.hideModalEntrega}
                        realizarEntrega={this.props.realizarEntrega}
                        buscarBarra={this.props.buscarBarra}

                        // Objetos
                        almacenes={this.props.almacenes.filter(alma=>alma.idAlmacenAF!=1)}
                        almacenDestino={this.props.almacenSeleccionado}
                    />
                </ModalEntrega>


                <h4>Articulos en Stock</h4>
                <button className="btn btn-primary btn-xs pull-right"
                        disabled={this.props.almacenSeleccionado<2}
                        onClick={this.showModalTransferencia}
                >
                    Transferir productos
                </button>

                <button className="btn btn-primary btn-xs pull-right"
                        disabled={this.props.almacenSeleccionado<2}
                        onClick={this.showModalEntrega}
                >
                    Entregar articulos
                </button>

                <TablaGeneralArticulos
                    articulos={this.props.almacenArticulos}
                    seleccionarAlmancen={this.props.seleccionarAlmacen}
                />
            </div>
        )
    }
}

SeccionArticulos.propTypes = {
    // metodos
    seleccionarAlmacen: PropTypes.func.isRequired,
    buscarBarra: PropTypes.func.isRequired,
    realizarTransferencia: PropTypes.func.isRequired,
    realizarEntrega: PropTypes.func.isRequired,

    // objetos
    almacenSeleccionado: PropTypes.number.isRequired,
    almacenArticulos: PropTypes.arrayOf(PropTypes.object).isRequired,
    almacenes: PropTypes.arrayOf(PropTypes.object).isRequired
}