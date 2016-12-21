// Libs
import React from 'react'
import api from '../../apiClient/v1'

export class MantenedorCorreos extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            idCliente: 2,
            correos: [],
            nuevoCorreo: '',
        }
        this.seleccionarCliente = (evt) => {
            const idCliente = evt.target.value
            this.setState({
                idCliente: idCliente,
                locales: []
            }, () => {
                this.fetchCorreos()
            })
        }
        this.fetchCorreos = () => {
            api.cliente(this.state.idCliente).getCorreos()
                .then(correos => {
                    this.setState({
                        correos,
                        nuevoCorreo: ''
                    })
                })
                .catch(meh => {})
        }
        this.quitarCorreo = (idCorreo)=>{
            api.cliente(this.state.idCliente).quitarCorreo(idCorreo)
                .then(()=> this.fetchCorreos() )
        }
        // agregar correo
        this.onNuevoCorreoChange = (evt)=>{
            this.setState({
                nuevoCorreo: evt.target.value
            })
        }
        this.agregarCorreo = () => {
            if(this.state.nuevoCorreo=='')
                return
            api.cliente(this.state.idCliente).agregarCorreo({correo: this.state.nuevoCorreo})
                .then(this.fetchCorreos)
                .catch(meh => {})
        }
    }
    componentWillMount(){
        this.fetchCorreos()
    }
    render(){
        return (
            <div className="container">
                <div className="row">
                    <div className="col-md-6">
                        <h1>Correos de clientes</h1>
                    </div>
                </div>
                <div className="row">
                    <div className="col-sm-3 col-sm-offset-3">
                        <SelectorCliente
                            idClienteSeleccionado={this.state.idCliente}
                            seleccionarCliente={this.seleccionarCliente}
                            clientes={this.props.clientes}
                        />
                    </div>
                    <div className="col-sm-3">
                        <div className="form-group ">
                            <label className="control-label" htmlFor="cliente">Correos</label>
                            <ul className="list-group">
                                {this.state.correos.map(correo=>
                                    <li key={correo.idCorreo} className="list-group-item">
                                        {correo.correo}
                                        <button className="btn btn-xs btn-danger pull-right"
                                                onClick={this.quitarCorreo.bind(this, correo.idCorreo)}
                                        >Eliminar</button>
                                    </li>
                                )}
                                <li className="list-group-item">
                                    <input type="text" className="control-form"
                                           value={this.state.nuevoCorreo}
                                           onChange={this.onNuevoCorreoChange}
                                           onKeyDown={evt=>{ if(evt.keyCode==13) this.agregarCorreo() }}
                                    />
                                    <button className="btn btn-xs btn-success pull-right"
                                            onClick={this.agregarCorreo}
                                    >Agregar</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}

const SelectorCliente = ({idClienteSeleccionado, seleccionarCliente, clientes})=>
        <div className="form-group ">
            <label className="control-label" htmlFor="cliente">Cliente</label>
            <select className="form-control" name="cliente"
                    value={idClienteSeleccionado}
                    onChange={seleccionarCliente}
            >
                <option value="-1" disabled>--</option>
                {clientes.map(cliente=>
                    <option key={cliente.idCliente} value={cliente.idCliente}>{`${cliente.nombreCorto} - ${cliente.nombre}`}</option>
                )}
            </select>
        </div>