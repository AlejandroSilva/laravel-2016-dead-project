// Librerias
import React from 'react'
// Componentes
//import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'
import HeaderConFiltro from '../shared/HeaderConFiltro.jsx'
import { RowLocal }from './RowLocal.jsx'
import * as css from './TablaLocales.css'

class TablaLocales extends React.Component{
    constructor(props){
        super(props)
        this.opcionesFormatos = this.props.formatoLocales.map(formato=>
            <option key={formato.idFormatoLocal} value={formato.idFormatoLocal}>{formato.nombre}</option>
        )
        this.opcionesJornadas = this.props.jornadas.map(jornada=>
            <option key={jornada.idJornada} value={jornada.idJornada}>{jornada.nombre}</option>
        )
        this.opcionesComunas = this.props.comunas.map(comuna=>
            <option key={comuna.cutComuna} value={comuna.cutComuna}>{comuna.nombre}</option>
        )
        this.rows = []  // referencia a cada una de las rows
    }

    componentWillReceiveProps(nextProps){
        // cuando se reciben nuevos elementos, se generand posiciones "vacias" en el arreglo de rows
        this.rows = this.rows.filter(input=>input!==null)
    }
    focusRow(index, nombreElemento){
        let ultimoIndex = this.rows.length-1
        if(index<0){
            // al seleccionar "antes de la primera", se seleciona el ultimo
            this.rows[ultimoIndex].focusElemento(nombreElemento)
        }else if(index>ultimoIndex){
            // al seleccionar "despues de la ultima", se selecciona el primero
            this.rows[ index%this.rows.length ].focusElemento(nombreElemento)
        }else{
            // no es ni el ultimo, ni el primero
            this.rows[index].focusElemento(nombreElemento)
        }
    }

    render(){
        return <StickyContainer type={React.DOM.table} className={"table table-bordered table-condensed "+css.tableFixed}>
            <colgroup>
                <col className={css.id}/>
                <col className={css.cliente}/>
                <col className={css.numero}/>
                <col className={css.nombre}/>
                <col className={css.formatoLocal}/>
                <col className={css.jornada}/>
                <col className={css.horaApertura}/>
                <col className={css.horaCierre}/>
                <col className={css.emailContacto}/>
                <col className={css.telefono1}/>
                <col className={css.telefono2}/>
                <col className={css.stock}/>
                <col className={css.fechaStock}/>
                <col className={css.comuna}/>
                <col className={css.direccion}/>
                <col className={css.opciones}/>
            </colgroup>
            <thead>
                <tr>
                    <th className={css.id}>id</th>
                    <th className={css.cliente}>
                        <HeaderConFiltro
                            nombre='CL'
                            filtro={this.props.filtros.filtroCliente || []}
                            actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroCliente')}
                            //ordenarLista={this.props.ordenarAuditorias}
                        />
                    </th>
                    <th className={css.numero}>
                        <HeaderConFiltro
                            nombre='CE'
                            filtro={this.props.filtros.filtroCeco || []}
                            actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroCeco')}
                            busquedaExacta={true}
                        />
                    </th>
                    <th className={css.nombre}>
                        <HeaderConFiltro
                            nombre='Nombre'
                            filtro={this.props.filtros.filtroNombre || []}
                            actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroNombre')}
                        />
                    </th>
                    <th className={css.formatoLocal}>
                        <HeaderConFiltro
                            nombre='Formato Local'
                            filtro={this.props.filtros.filtroFormatoLocal || []}
                            actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroFormatoLocal')}
                        />
                    </th>
                    <th className={css.jornada}>Jornada Sugerida</th>
                    <th className={css.horaApertura}>Hr.Apertura</th>
                    <th className={css.horaCierre}>Hr.Cierre</th>
                    <th className={css.emailContacto}>Email</th>
                    <th className={css.telefono1}>Telefono 1</th>
                    <th className={css.telefono2}>Telefono 2</th>
                    <th className={css.stock}>Stock</th>
                    <th className={css.fechaStock}>Fecha Stock</th>
                    <th className={css.comuna}>
                        <HeaderConFiltro
                            nombre='Comuna'
                            filtro={this.props.filtros.filtroComuna || []}
                            actualizarFiltro={this.props.actualizarFiltro.bind(this, 'filtroComuna')}
                        />
                    </th>
                    <th className={css.direccion}>Dirección</th>
                    <th className={css.opciones}>Opciones</th>
                </tr>
            </thead>

            <tbody>
                {/* Mostrar lista de locales */}
                {this.props.localesFiltrados.length===0 ?
                    <tr>
                        <td colSpan="16" style={{textAlign: 'center'}}><b>Sin locales</b></td>
                    </tr>
                    :
                    this.props.localesFiltrados.map((local, index)=>
                        <RowLocal
                            key={local.idLocal}
                            index={index}
                            local={local}
                            ref={ref=>this.rows[index]=ref}
                            // Opciones
                            opcionesFormatos={this.opcionesFormatos}
                            opcionesJornadas={this.opcionesJornadas}
                            opcionesComunas={this.opcionesComunas}
                            // Metodos
                            focusRow={this.focusRow.bind(this)}
                            actualizar={this.props.apiActualizar}
                        />
                    )
                }

                {/* Formulario para agregar local */}
                {this.props.children}
            </tbody>
        </StickyContainer>
    }
}

TablaLocales.propTypes = {
    // Objetos
    localesFiltrados: React.PropTypes.array.isRequired,
    filtros: React.PropTypes.objectOf(React.PropTypes.array).isRequired,
    // Objetos para genera Opciones de los Select
    jornadas: React.PropTypes.array.isRequired,
    formatoLocales: React.PropTypes.array.isRequired,
    comunas: React.PropTypes.array.isRequired,

    // Metodos
    //ordenarAuditorias: React.PropTypes.func.isRequired,
    actualizarFiltro: React.PropTypes.func.isRequired,
    apiActualizar: React.PropTypes.func.isRequired
}
TablaLocales.childContextTypes = {
    opcionesFormatos: React.PropTypes.array.isRequired,
    opcionesJornadas: React.PropTypes.array.isRequired
}
export default TablaLocales