// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
// Styles
import css from './almacenesStyles.css'

export class ModalNuevoAlmacen extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            modalVisible: false
        }
        this.showModal = ()=>{
            this.setState({modalVisible: true})
        }
        this.hideModal = ()=>{
            this.setState({modalVisible: false})
        }
    }
    render(){
        return (
            <Modal
                show={this.state.modalVisible}
                //onEnter={this.props.onEnter}
                onHide={this.hideModal}
                animation={false}
                dialogClassName={css.modalNuevo}
            >
                <Modal.Header closeButton>
                    <Modal.Title>Agregar Almacen</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    {this.props.children}
                </Modal.Body>
            </Modal>
        )
    }
}

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
                    this.props.hideModal()
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
                            onClick={this.props.hideModal}>
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

NuevoAlmacen.propTypes = {
    // Metodos
    hideModal: PropTypes.func.isRequired,
    fetchResponsables: PropTypes.func.isRequired,
    agregarAlmacen: PropTypes.func.isRequired,
    // objetos
    responsables: PropTypes.arrayOf(PropTypes.object).isRequired
}