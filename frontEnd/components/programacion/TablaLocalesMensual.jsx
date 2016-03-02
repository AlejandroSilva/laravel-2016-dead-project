import React from 'react'
let PropTypes = React.PropTypes
import api from '../../apiClient/v1'
import moment from 'moment'

// Componentes
import StickyContainer from '../shared/react-sticky/container.js'
import RowLocales from './RowLocales.jsx'
import HeaderLocales from './HeaderLocales.jsx'

// Styles
import sharedStyles from '../shared/shared.css'
import styles from './TablaLocalesMensual.css'

class TablaLocalesMensual extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            locales: []
        }
        // referencia a todos las entradas de fecha de los locales a inventariar
        this.inputFecha = []

        this.focusFilaSiguiente = this.focusFilaSiguiente.bind(this)
        this.focusFilaAnterior = this.focusFilaAnterior.bind(this)
    }
    agregarLocal(nuevoLocal, mesAnno){
        let [mes, anno] = mesAnno.split('-')
        let locales = this.state.locales

        let localNoExiste = this.state.locales.find(local=>local.idLocal===nuevoLocal.idLocal)===undefined
        if( localNoExiste ){
            // buscar asincronicamente la informacion completa al servidor
            this.obtenerDatosLocal(nuevoLocal.idLocal)
            nuevoLocal.mesProgramado = mes
            nuevoLocal.annoProgramado = anno

            // actualizar la lista de locales
            locales.push(nuevoLocal)
            // actualizar la lista con los nuevos
            this.setState({
                locales: locales
            })
        }
        return localNoExiste
    }
    obtenerDatosLocal(idLocal){
        api.locales.getVerbose(idLocal)
            .then(informacionLocal=>{
                this.setState({
                    // actualizar los datos de la lista con la informacion obtenida por el api
                    locales: this.state.locales.map(local=>{
                        if(local.idLocal===informacionLocal.idLocal)
                            // mezclar los objetos
                            return Object.assign(local, informacionLocal)
                        else
                            return local
                    })
                })
            })
            .catch(error=>console.error(`error al obtener los datos de ${idLocal}`, error))
    }

    focusFilaSiguiente(indexActual){
        let nextIndex = (indexActual+1)%this.inputFecha.length
        let nextRow = this.inputFecha[nextIndex]
        nextRow.focusFecha()
    }
    focusFilaAnterior(indexActual){
        let prevIndex = indexActual===0? this.inputFecha.length-1 : indexActual-1
        let prevRow = this.inputFecha[prevIndex]
        prevRow.focusFecha()
    }

    render(){
        return (
            <div>
                {/* Table */}
                <StickyContainer type={React.DOM.table}  className="table table-bordered table-condensed">
                    <thead>
                        {/* TR que se pega al top de la pagina, es una TR, con instancia de 'Sticky' */}
                        <HeaderLocales />
                    </thead>
                    <tbody>
                    {this.state.locales.map((local, index)=>{
                        let direccion = local.direccion || {}
                        let comuna = direccion.comuna || {}
                        let provincia = comuna.provincia || {}
                        let region = provincia.region || {}
                        let zona = region.zona || {}

                        return <RowLocales
                            key={index}
                            index={index}
                            mesProgramado={local.mesProgramado}
                            ultimoDiaMes={moment(`${local.annoProgramado}${local.mesProgramado}`, 'YYYYMM').daysInMonth()}
                            annoProgramado={local.annoProgramado}
                            nombreCliente={local.cliente? local.cliente.nombreCorto : '...'}
                            ceco={local.numero? local.numero : '...'}
                            nombreLocal={local.nombre? local.nombre : '...'}
                            zona={zona.nombre? zona.nombre : '...'}
                            region={region.nombreCorto? region.nombreCorto : '...'}
                            comuna={comuna.nombre? comuna.nombre : '...'}
                            stock={local.stock? local.stock : '...'}
                            dotacionSugerida={98}
                            jornada={local.jornada? local.jornada.nombre : '(...jornada)'}
                            focusFilaSiguiente={this.focusFilaSiguiente}
                            focusFilaAnterior={this.focusFilaAnterior}
                            ref={ref=>this.inputFecha[index]=ref}
                        />
                    })}
                    </tbody>
                </StickyContainer>
            </div>
        )
    }
}

TablaLocalesMensual.protoTypes = {
    //localesAgregados: PropTypes.array.required
}
export default TablaLocalesMensual