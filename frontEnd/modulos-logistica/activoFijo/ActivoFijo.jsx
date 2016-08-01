// Librerias
import React from 'react'
// API
import api from '../../apiClient/v1'
// Componentes
import { SeccionPreguias } from './SeccionPreguias/SeccionPreguias.jsx'
import { SeccionAlmacenes } from './SeccionAlmacenes/SeccionAlmacenes.jsx'
import { SeccionArticulos } from './SeccionArticulos/SeccionArticulos.jsx'
import { ModalMantenedorMaestra } from './MantenedorMaestra/ModalMantenedorMaestra.jsx'

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
        this.seleccionarAlmacen = (idAlmacen)=>{
            idAlmacen = parseInt(idAlmacen)

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

        // #### SeccionAlmacenes
        this.fetchResponsables = ()=>{
            if(this.state.responsables.length==0){
                api.activoFijo.responsables.buscar()
                    .then(responsables=>{
                        this.setState({responsables})
                    })
                // catch error...
            }
        }
        this.agregarAlmacen = (nombre, idResponsable)=>{
            return api.activoFijo.almacenes.nuevo({nombre, idResponsable})
                .then(nuevoAlmacen=>{
                    // al crear un nuevo almacen, se actualiza la lista completa de almacenes...
                    this._fetchAlmacenes()
                })
        }
        this._fetchAlmacenes = ()=>{
            return api.activoFijo.almacenes.buscar()
                .then(almacenes=>{
                    this.setState({almacenes})
                })
        }

        // #### SeccionArticulos
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

        // Mantenedor Maestra
        this.mostrarMantenedorMaestra = ()=>{
            this.refModalMantenedorproductos.showModal()
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
                {/* Modales */}
                <ModalMantenedorMaestra
                    ref={ref=>this.refModalMantenedorproductos=ref}
                />

                {/* ################# MENU LATERAL ################# */}
                <div className="col-sm-3">
                    <h4>Productos</h4>
                    <button type="button" className="btn btn-sm btn-default btn-block"
                            onClick={this.mostrarMantenedorMaestra}>
                        Maestra de Productos
                    </button>

                    <h4>Almacenes</h4>
                    <SeccionAlmacenes
                        // metodos
                        seleccionarAlmancen={this.seleccionarAlmacen}
                        fetchResponsables={this.fetchResponsables}
                        agregarAlmacen={this.agregarAlmacen}
                        //Objetos
                        cargandoDatos={this.state.cargandoDatos}
                        almacenes={this.state.almacenes}
                        almacenSeleccionado={this.state.almacenSeleccionado}
                        responsables={this.state.responsables}
                    />
                </div>

                {/* ################# CUERPO CENTRAL ################ */}
                <div className="col-sm-6">
                    <SeccionArticulos
                        // metodos
                        seleccionarAlmacen={this.seleccionarAlmacen}
                        buscarBarra={this.buscarBarra}
                        realizarTransferencia={this.realizarTransferencia}
                        realizarEntrega={this.realizarEntrega}

                        // objetos
                        almacenSeleccionado={this.state.almacenSeleccionado}
                        almacenArticulos={this.state.almacenArticulos}
                        almacenes={this.state.almacenes}
                    />


                    {/* ############ PRE-GUIAS DESPACHO ############ */}
                    <h4>Pre-guias de despacho</h4>
                    <SeccionPreguias
                        preguias={this.state.almacenPreguias}
                        fetchPreguia={this.fetchPreguia}
                        devolverArticulos={this.devolverArticulos}
                        // devolverArticulos={this.refModalDevolucion.devolverArticulos}
                        // devolverArticulos={this.refModalDevolucion.devolverArticulos}
                        seleccionarAlmancen={this.seleccionarAlmacen}
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