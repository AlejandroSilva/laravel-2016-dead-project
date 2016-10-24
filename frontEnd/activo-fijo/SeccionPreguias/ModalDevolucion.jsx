// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { TablaRetornoArticulos } from './TablaRetornoArticulos.jsx'

export class ModalDevolucion extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            preguia: {},
            articulos: []
        }

        this.onCancelar = ()=>{
            this.props.hideModal()
        }
        this.cambiarCantidadDevolucion = (idArticuloAF, evt)=>{
            // buscar el articulo seleccionado y cambiar su seleccion
            this.setState({
                articulos: this.state.articulos.map(articulo=>{
                    if(articulo.idArticuloAF==idArticuloAF)
                        return {
                            ...articulo,
                            stockParaRetornar: evt.target.value
                        }
                    return articulo
                })
            })
        }
        this.onDevolverArticulos = ()=>{
            console.log('devolver articulos', this.state.articulos)
            this.props.devolverArticulos(this.props.idPreguia, {
                articulos: this.state.articulos.map(art=>({
                    idArticuloAF: art.idArticuloAF,
                    stockParaRetornar: art.stockParaRetornar
                }))
            })
                .then(resp=>{
                    // al devolverlos, esconder el modal
                    this.props.hideModal()
                })
        }

    }
    componentWillMount(){
        // al mostar el modal, se buscan los datos de esta preguia
        this.props.fetchPreguia(this.props.idPreguia)
            .then(preguia=>{
                this.setState({
                    preguia,
                    // cuando se reciben los articulos, se agrega un campo para seleccionar cuantos articulos se van a retornar
                    articulos: preguia.articulos.map(articulo=>({
                        ...articulo,
                        stockParaRetornar: 0
                    }))
                })
                console.log('preguia:', preguia)
            })
    }

    render(){
        return (
            <div>
                <TablaRetornoArticulos
                    articulos={this.state.articulos}
                    cambiarCantidadDevolucion={this.cambiarCantidadDevolucion}
                />

                {/* Botones Cancelar/Siguiente */}
                <div className="btn-group btn-group-justified">
                    <button type='button' className="btn btn-default" style={{width: '50%'}}
                            onClick={this.onCancelar}>
                        Cancelar
                    </button>
                    <button type='button' className="btn btn-primary" style={{width: '50%'}}
                            disabled={false}
                            onClick={this.onDevolverArticulos}>
                        Devolver articulos
                    </button>
                </div>
            </div>
        )
    }
}

ModalDevolucion.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
    fetchPreguia: PropTypes.func.isRequired
}