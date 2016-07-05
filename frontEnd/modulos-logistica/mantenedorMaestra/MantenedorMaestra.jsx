// Librerias
import React from 'react'
//let PropTypes = React.PropTypes
// Componentes
import { TablaMaestra } from './TablaMaestra.jsx'


export class MantenedorMaestra extends React.Component {
    constructor(props) {
        super(props)

        this.changeData = function changeData(a,b,c){
            console.log('change data ', a,b,c)
        }
    }
    render(){
        return (
            <div>
                <h4>Maestra de productos</h4>
                <TablaMaestra
                    productosMaestra={this.props.productosMaestra}
                    changeData={this.changeData}
                />
            </div>
        )
    }
}

MantenedorMaestra.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}