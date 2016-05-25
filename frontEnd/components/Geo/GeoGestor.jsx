// Libs
import React from 'react'
import api from '../../apiClient/v1'
// Component
import { MapaChile } from './MapaChile.jsx'
import { TablaGeos } from './TablaGeos.jsx'

export class GeoGestor extends React.Component{
    constructor(props) {
        super(props)
        this.state = {
            comunas: []
        }
    }

    componentWillMount(){
        // cargar las comunas apenas se abra la vista
        api.geo.comunas()
            .then(comunas=>{
                this.setState({
                    comunas
                })
            }).catch(error=> alert('Ocurrio un problema al buscar las comunas'))
    }

    render(){
        return (
            <div className="row">
                <h1>GEO</h1>
                <div>
                    <div className="col-sm-4">
                        <MapaChile
                            // comunas
                        />
                    </div>
                    <div className="col-sm-8">
                        <TablaGeos
                            comunas={this.state.comunas}
                        />
                    </div>
                </div>

            </div>
        )
    }
}

GeoGestor.propTypes = {
    // clientes: React.PropTypes.array.isRequired,
}