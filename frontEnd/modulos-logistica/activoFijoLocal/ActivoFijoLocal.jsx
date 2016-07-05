// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// API
import api from '../../apiClient/v1'
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
import { InputBarra } from '../shared/InputBarra.jsx'
import { TablaProductosAF } from './TablaProductosAF.jsx'
import { TablaPreguias } from './TablaPreguias.jsx'
import { ModalContainer } from './ModalContainer.jsx'
import { Transferencia } from './Transferencia.jsx'
import { NuevoAlmacen } from './NuevoAlmacen.jsx'
// Styles
// import classNames from 'classnames/bind'

export class ActivoFijoLocal extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            almacenes: this.props.almacenes,
            almacenSeleccionado: 0,
            almacenProductos: [],
            productosTodos: [],
            responsables: []
        }
        // ########## Metodos
        // mostrar los productos asignados a un almacen
        // buscar un productoAF
        this.buscarCodigo = (codigo)=>{
            return api.activoFijo.local(this.props.local.idLocal).buscarProducto(codigo)
        }
        this.realizarTransferencia = (productos)=>{
            return api.activoFijo.local(this.props.local.idLocal).transferir(productos)
                .then(datos=>{
                    // cuando la transferencia sea exitosa, actualizar los productos
                    this.seleccionarAlmacen(this.state.almacenSeleccionado)
                })
        }
        // responsables
        this.fetchResponsables = ()=>{
            console.log('buscando responsables')
            if(this.state.responsables.length==0){
                api.activoFijo.local(this.props.local.idLocal).responsables()
                    .then(responsables=>{
                        this.setState({responsables})
                    })
                // catch error...
            }
        }
        // Almacenes
        this.seleccionarAlmacen = (idAlmacen)=>{
            this.setState({
                almacenSeleccionado: idAlmacen,
                almacenProductos: []
            })
            api.activoFijo.local(this.props.local.idLocal).almacen(idAlmacen).productos()
                .then(productos=>{
                    this.setState({
                        almacenProductos: productos
                    })
                })
                .catch(error=>{
                    //almacenProductos: []
                })
        }
        this.fetchAlmacenes = ()=>{
            return api.activoFijo.local(this.props.local.idLocal).almacenes.fetch()
                .then(almacenes=>{
                    this.setState({almacenes})
                })
        }
        this.agregarAlmacen = (nombre, idResponsable)=>{
            return api.activoFijo.local(this.props.local.idLocal).almacenes.nuevo({nombre, idResponsable})
                .then(nuevoAlmacen=>{
                    // al crear un nuevo almacen, se actualiza la lista completa de almacenes...
                    this.fetchAlmacenes()
                })
        }
    }

    componentWillMount(){
        // seleccionar un almacen por defecto si existe
        let almacenes = this.state.almacenes
        if(almacenes[0])
            this.seleccionarAlmacen(almacenes[0].idAlmacenAF)
    }

    render(){
        return (
            <div className="row">
                <div className="col-sm-2">
                    <h4>Almacenes</h4>
                    <SelectorAlmacenes
                        almacenes={this.state.almacenes}
                        almacenSeleccionado={this.state.almacenSeleccionado}
                        seleccionarAlmancen={this.seleccionarAlmacen}
                        responsables={this.state.responsables}
                        fetchResponsables={this.fetchResponsables}
                        agregarAlmacen={this.agregarAlmacen}
                    />
                </div>

                <div className="col-sm-6">
                    <h4>Productos en el Almacen (¿saldo?)</h4>

                    <ModalContainer
                        titulo="Transferir productos"
                        buttonComponent={
                            <a className="btn btn-primary btn-xs pull-right">
                                Transferir productos
                            </a>
                        }
                    >
                        <Transferencia
                            almacenes={this.state.almacenes}
                            almacenOrigen={this.state.almacenSeleccionado}
                            buscarCodigo={this.buscarCodigo}
                            realizarTransferenia={this.realizarTransferencia}
                        />
                        {/*<FormularioPreGuia />*/}
                    </ModalContainer>

                    <TablaProductosAF
                        productos={this.state.almacenProductos}
                    />

                    <h4>Pre-guias de despacho</h4>
                    <TablaPreguias
                        preguias={[]}
                    />
                </div>
            </div>
        )
    }
}

ActivoFijoLocal.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}



const SelectorAlmacenes = (props)=>{
    let {almacenes, almacenSeleccionado, seleccionarAlmancen, responsables, fetchResponsables, agregarAlmacen} = props
    return (
        <div className="list-group">
            <button type="button" className={"list-group-item "+(almacenSeleccionado==0? 'active':'')}
                    onClick={seleccionarAlmancen.bind(this, 0)}
            >Todos</button>
            {almacenes.map(almacen=>
                <button type="button" className={"list-group-item "+(almacenSeleccionado==almacen.idAlmacenAF? 'active':'')}
                        key={almacen.idAlmacenAF}
                        onClick={seleccionarAlmancen.bind(this, almacen.idAlmacenAF)}
                >{almacen.nombre}</button>
            )}

            <ModalContainer
                titulo={"Agregar Almacen"}
                buttonComponent={
                    <button type="button"
                            className="list-group-item default list-group-item-success">
                        ** Agregar Almacen **
                    </button>
                }
            >
                <NuevoAlmacen
                    responsables={responsables}
                    fetchResponsables={fetchResponsables}
                    agregarAlmacen={agregarAlmacen}
                />
            </ModalContainer>
        </div>
    )
}