// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes

export class NuevoAlmacen extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            nombre: '',
            idResponsable: '0'
        }
        this.seleccionarResponsable = (evt)=>{
            this.setState({idResponsable: evt.target.value})
        }
        this.changeNombre = (evt=>{
            this.setState({nombre: evt.target.value})
        })
        this.agregarAlmacen = ()=>{
            this.props.agregarAlmacen(this.state.nombre, this.state.idResponsable)
                .then( ()=>{
                    this.context.$_hideModal()
                })
        }
        
    }
    componentWillMount(){
        this.props.fetchResponsables()
    }
    render(){
        let nombreValido = this.state.nombre!=''
        let responsableValido = this.state.idResponsable!='0'
        return (
            <div className="form-horizontal">
                {/* Nombre */}
                <div className="form-group">
                    <label className="col-xs-3">Nombre</label>
                    <div className="col-xs-9">
                        <input type="text" className="form-control"
                               value={this.state.nombre}
                               onChange={this.changeNombre}
                        />
                    </div>
                </div>

                {/* Responsable */}
                <div className="form-group">
                    <label className="col-xs-3">Responsable</label>
                    <div className="col-xs-9">
                        <select className="form-control"
                                value={this.state.idResponsable}
                                onChange={this.seleccionarResponsable}
                        >
                            <option value="0" disabled>--</option>
                            {this.props.responsables.map(responsable=>
                                <option key={responsable.id} value={responsable.id}>{responsable.nombre}</option>
                            )}
                        </select>
                    </div>
                </div>

                {/* Botones Cancelar/Agregar */}
                <div>
                    <button className="btn btn-default btn-block"
                            onClick={this.context.$_hideModal}>
                        Cancelar
                    </button>
                    <button className="btn btn-primary btn-block"
                            disabled={ !nombreValido || !responsableValido }
                            onClick={this.agregarAlmacen}>
                        Agregar
                    </button>
                </div>
            </div>
        )
    }
}
NuevoAlmacen.contextTypes = {
    $_showModal: React.PropTypes.func,
    $_hideModal: React.PropTypes.func
}
NuevoAlmacen.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}