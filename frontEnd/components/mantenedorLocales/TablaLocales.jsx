// Librerias
import React from 'react'
// Componentes
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'
import RowLocales from './RowLocales.jsx'
import * as css from './TablaLocales.css'

class TablaLocales extends React.Component{
    render(){
        return <StickyContainer type={React.DOM.table} className={"table table-bordered table-condensed "+css.tableFixed}>
            <colgroup>
                <col className={css.id}/>
                <col className={css.formatoLocal}/>
                <col className={css.jornada}/>
                <col className={css.numero}/>
                <col className={css.nombre}/>
                <col className={css.horaApertura}/>
                <col className={css.horaCierre}/>
                <col className={css.emailContacto}/>
                <col className={css.telefono1}/>
                <col className={css.telefono2}/>
                <col className={css.stock}/>
                <col className={css.fechaStock}/>
                <col className={css.comuna}/>
                <col className={css.direccion}/>
            </colgroup>
            <thead>
                {/* TR que se pega al top de la pagina, es una TR, con instancia de 'Sticky' */}
                <Sticky
                    topOffset={-50}
                    type={React.DOM.tr}
                    stickyStyle={{top: '50px'}}>

                    <th className={css.id}>id</th>
                    <th className={css.formatoLocal}>Formato Local</th>
                    <th className={css.jornada}>Jornada Sugerida</th>
                    <th className={css.numero}>Numero</th>
                    <th className={css.nombre}>Nombre</th>
                    <th className={css.horaApertura}>Hr.Apertura</th>
                    <th className={css.horaCierre}>Hr.Cierre</th>
                    <th className={css.emailContacto}>Email</th>
                    <th className={css.telefono1}>Telefono 1</th>
                    <th className={css.telefono2}>Telefono 2</th>
                    <th className={css.stock}>Stock</th>
                    <th className={css.fechaStock}>Fecha Stock</th>
                    <th className={css.comuna}>Comuna</th>
                    <th className={css.direccion}>Dirección</th>
                </Sticky>
            </thead>

            <tbody>
                {this.props.localesFiltrados.length===0
                    ? <tr><td colSpan="16" style={{textAlign: 'center'}}><b>Sin locales</b></td></tr>
                    : this.props.localesFiltrados.map((local, index)=>
                        <RowLocales
                            key={index}
                            index={index}
                            local={local}
                        />
                    )
                }
            </tbody>
        </StickyContainer>
    }
}

TablaLocales.propTypes = {
    localesFiltrados: React.PropTypes.array.isRequired
}
export default TablaLocales