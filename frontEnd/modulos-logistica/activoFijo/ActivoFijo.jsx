// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// API
import api from '../../apiClient/v1'
// Componentes
import { TablaArticulosAF } from './TablaArticulosAF.jsx'
import { TablaPreguias } from './TablaPreguias.jsx'
import { ModalContainer } from './ModalContainer.jsx'
import { Transferencia } from './Transferencia.jsx'
import { EntregaArticulos } from './EntregaArticulos.jsx'
import { NuevoAlmacen } from './NuevoAlmacen.jsx'
// Styles
// import classNames from 'classnames/bind'

export class ActivoFijo extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            cargandoDatos: false,
            almacenes: this.props.almacenes,
            almacenSeleccionado: 0,
            almacenArticulos: [],
            almacenPreguias: [],
            responsables: []
        }
        // ########## Metodos
        // mostrar los productos asignados a un almacen
        // buscar un productoAF
        this.buscarBarra = (barra)=>{
            return api.activoFijo.articulos.buscarBarra(barra)
        }
        this.realizarTransferencia = (request)=>{
            return api.activoFijo.articulos.transferir(request)
                // cuando la transferencia sea exitosa, actualizar los productos y las guias
                .then(()=>{
                    this.seleccionarAlmacen(this.state.almacenSeleccionado)
                })
        }
        this.realizarEntrega = (request)=>{
            return api.activoFijo.articulos.entregar(request)
            // cuando la transferencia sea exitosa, actualizar los productos y las guias
                .then(()=>{
                    this.seleccionarAlmacen(this.state.almacenSeleccionado)
                })
        }
        // responsables
        this.fetchResponsables = ()=>{
            if(this.state.responsables.length==0){
                api.activoFijo.responsables.buscar()
                    .then(responsables=>{
                        this.setState({responsables})
                    })
                // catch error...
            }
        }
        // Almacenes
        this.seleccionarAlmacen = (idAlmacen)=>{
            // si se estan cargando los datos, no permitir que se seleccione otro almacen
            if(this.state.cargandoDatos)
                return

            this.setState({
                cargandoDatos: true,
                almacenSeleccionado: idAlmacen,
                almacenArticulos: [],
                almacenPreguias: []
            })
            api.activoFijo.almacen(idAlmacen).articulos()
                .then(articulos=>{
                    this.setState({
                        cargandoDatos: false,
                        almacenArticulos: articulos
                    })
                })
                .catch(()=>{
                    this.setState({
                        cargandoDatos: false,
                        almacenArticulos: []
                    })
                })
            api.activoFijo.almacen(idAlmacen).preguias()
                .then(preguias=>{
                    this.setState({
                        // cargandoDatos: false,
                        almacenPreguias: preguias
                    })
                })
                .catch(()=>{
                    //this.setState({cargandoDatos: false})
                })
        }
        this.fetchAlmacenes = ()=>{
            return api.activoFijo.almacenes.buscar()
                .then(almacenes=>{
                    this.setState({almacenes})
                })
        }
        this.agregarAlmacen = (nombre, idResponsable)=>{
            return api.activoFijo.almacenes.nuevo({nombre, idResponsable})
                .then(nuevoAlmacen=>{
                    // al crear un nuevo almacen, se actualiza la lista completa de almacenes...
                    this.fetchAlmacenes()
                })
        }
        // Preguias
        this.fetchPreguia = (idPreguia)=>{
            return api.activoFijo.preguia(idPreguia).fetch()
        }
        this.devolverArticulos =(idPreguia, datos)=>{
            return api.activoFijo.preguia(idPreguia).devolver(datos)
                .then(()=>{
                    // actualizar articulos y guias
                    this.seleccionarAlmacen(this.state.almacenSeleccionado)
                })
        }

    }

    componentWillMount(){
        // seleccionar un almacen por defecto si existe
        // let almacenes = this.state.almacenes
        // if(almacenes[0])
            this.seleccionarAlmacen(this.state.almacenSeleccionado)
    }

    render(){
        return (
            <div className="row">
                <div className="col-sm-2">
                    <h4>Almacenes</h4>
                    <SelectorAlmacenes
                        cargandoDatos={this.state.cargandoDatos}
                        almacenes={this.state.almacenes}
                        almacenSeleccionado={this.state.almacenSeleccionado}
                        seleccionarAlmancen={this.seleccionarAlmacen}
                        responsables={this.state.responsables}
                        fetchResponsables={this.fetchResponsables}
                        agregarAlmacen={this.agregarAlmacen}
                    />
                </div>

                <div className="col-sm-6">
                    {/* ################# Productos ################ */}
                    <h4>Articulos en Stock</h4>
                    <ModalContainer
                        titulo="Transferir productos"
                        buttonComponent={
                            <a className="btn btn-primary btn-xs pull-right" disabled={this.state.almacenSeleccionado<2}>Transferir productos</a>
                        }
                    >
                        <Transferencia
                            almacenes={this.state.almacenes.filter(alm=>alm.idAlmacenAF!=1)}
                            almacenOrigen={this.state.almacenSeleccionado}
                            buscarBarra={this.buscarBarra}
                            realizarTransferenia={this.realizarTransferencia}
                        />
                        {/*<FormularioPreGuia />*/}
                    </ModalContainer>
                    <ModalContainer
                        titulo="Entregar articulos"
                        buttonComponent={
                            <a className="btn btn-primary btn-xs pull-right" disabled={this.state.almacenSeleccionado<2}>Entregar articulos</a>
                        }
                    >
                        <EntregaArticulos
                            almacenes={this.state.almacenes.filter(alma=>alma.idAlmacenAF!=1)}
                            buscarBarra={this.buscarBarra}
                            almacenDestino={this.state.almacenSeleccionado}
                            realizarEntrega={this.realizarEntrega}
                        />
                        {/*<FormularioPreGuia />*/}
                    </ModalContainer>

                    <TablaArticulosAF
                        articulos={this.state.almacenArticulos}
                    />

                    {/* ############ PRE-GUIAS DESPACHO ############ */}
                    <h4>Pre-guias de despacho</h4>
                    <TablaPreguias
                        preguias={this.state.almacenPreguias}
                        fetchPreguia={this.fetchPreguia}
                        devolverArticulos={this.devolverArticulos}
                        // devolverArticulos={this.refModalDevolucion.devolverArticulos}
                        // devolverArticulos={this.refModalDevolucion.devolverArticulos}
                    />
                </div>
            </div>
        )
    }
}

ActivoFijo.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}



const SelectorAlmacenes = (props)=>{
    let {almacenes, almacenSeleccionado, seleccionarAlmancen,
        responsables, fetchResponsables, agregarAlmacen, cargandoDatos} = props
    return (
        <div className="list-group">
            <button type="button" className={"list-group-item "+(almacenSeleccionado==0? 'active':'')}
                    onClick={seleccionarAlmancen.bind(this, 0)}
                    disabled={cargandoDatos==true}
            >Todos</button>
            {almacenes.map(almacen=>
                <button type="button" className={"list-group-item "+(almacenSeleccionado==almacen.idAlmacenAF? 'active':'')}
                        key={almacen.idAlmacenAF}
                        onClick={seleccionarAlmancen.bind(this, almacen.idAlmacenAF)}
                        disabled={cargandoDatos==true}
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