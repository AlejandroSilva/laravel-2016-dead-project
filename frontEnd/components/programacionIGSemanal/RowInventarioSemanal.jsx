import React from 'react'
import moment from 'moment'
moment.locale('es')

// Componentes
import Tooltip from 'react-bootstrap/lib/Tooltip'
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'
import InputFecha from '../shared/InputFecha.jsx'
import InputHora from '../shared/InputHora.jsx'
import InputDotacionMultiple from '../shared/InputDotacionSimple.jsx'
import InputStock from '../shared/InputStock.jsx'
import Select from '../shared/Select.jsx'

// Styles
import * as css from './TablaSemanal.css'
import classNames from 'classnames/bind'
let cx = classNames.bind(css)

let estadoNominas = [
    '-',                // 0 - nunca ocurre
    'Deshabilitada',    // 1 - Deshabilitada
    'Pendiente',        // 2 - Pendiente
    'Recibida',         // 3 - Recibida
    'Aprobada',         // 4 - Aprobada
    'Informada',        // 5 - Informada
    'Inform. Excel'   // 6 - Informada con Excel (plataforma antigua)
]
let labelNominas = [
    'label-default',    // 0 - nunca ocurre
    'label-default',    // 1 - Deshabilitada
    'label-default',    // 2 - Pendiente
    'label-success',    // 3 - Recibida (verde)
    'label-info',       // 4 - Aprobada (celeste)
    'label-primary',    // 5 - Informada (azul)
    'label-primary'     // 6 - Informada con Excel (plataforma antigua)
]

class RowInventario extends React.Component{
    focusElemento(elemento){
        if(elemento==='dia'){
            this.inputDia.focus()
        }else if(elemento==='dotacion'){
            this.inputDotacionTotal.focus()
        }else if(elemento==='stock'){
            this.inputStock.focus()
        }else{
            console.error(`focusElemento(): Elemento "${elemento}" no encontrado`)
        }
    }

    guardarInventario() {
        if(!this.props.puedeModificar)
            return alert("no tiene permitido modificar el inventario")
        let cambiosInventario = {}

        // el DIA es valido, y ha cambiado?
        let estadoInputDia = this.inputDia.getEstado()
        if (estadoInputDia.valid && estadoInputDia.dirty) {
            cambiosInventario.fechaProgramada = estadoInputDia.fecha
        } else if (estadoInputDia.valid === false) {
            return console.log(`fecha ${estadoInputDia.fecha} invalida`)
        }

        // la DOTACION es valida y ha cambiado?
        let estadoInputDotacionTotal = this.inputDotacionTotal.getEstado()
        if (estadoInputDotacionTotal.valid && estadoInputDotacionTotal.dirty) {
            cambiosInventario.dotacionAsignadaTotal = estadoInputDotacionTotal.valor
        } else if (estadoInputDotacionTotal.valid === false) {
            return console.log(`dotacion total: ${estadoInputDotacionTotal.valor} invalida`)
        }

        // la JORNADA es valida y ha cambiado
        let estadoSelectJornada = this.selectJornada.getEstado()
        if(estadoSelectJornada.dirty)
            cambiosInventario.idJornada = estadoSelectJornada.seleccionUsuario

        // el STOCK es valido y ha cambiado?
        let estadoInputStock = this.inputStock.getEstado()
        if (estadoInputStock.valid && estadoInputStock.dirty)
            cambiosInventario.stockTeorico = estadoInputStock.valor

        // almenos uno de los ementos debe estar "dirty" para guardar los cambios
        if(JSON.stringify(cambiosInventario)!=="{}"){
            console.log(cambiosInventario)
            this.props.guardarInventario(this.props.inventario.idInventario, cambiosInventario)
        }else{
            console.log('inventario sin cambios, no se actualiza')
        }
    }
    guardarNominaDia() {
        this._guardarNomina(this.props.inventario.nomina_dia.idNomina, {
            inputDotacion: this.inputDotacionDia.getEstado(),
            selectLider: this.selectLiderDia.getEstado(),
            selectSupervisor: this.selectSupervisorDia.getEstado(),
            selectCaptador1: this.selectCaptador1Dia.getEstado(),
            inputHoraPresentacionLider: this.inputHoraPresentacionLiderDia.getEstado(),
            inputHoraPresentacionEquipo: this.inputHoraPresentacionEquipoDia.getEstado()
        })
    }
    guardarNominaNoche(){
        this._guardarNomina(this.props.inventario.nomina_noche.idNomina, {
            inputDotacion: this.inputDotacionNoche.getEstado(),
            selectLider: this.selectLiderNoche.getEstado(),
            selectSupervisor: this.selectSupervisorNoche.getEstado(),
            selectCaptador1: this.selectCaptador1Noche.getEstado(),
            inputHoraPresentacionLider: this.inputHoraPresentacionLiderNoche.getEstado(),
            inputHoraPresentacionEquipo: this.inputHoraPresentacionEquipoNoche.getEstado()
        })
    }
    _guardarNomina(idNomina, estados){
        if(!this.props.puedeModificar)
            return alert("no tiene permitido modificar el inventario")

        let cambiosNomina = {}

        // la DOTACION es valida y ha cambiado?
        if (estados.inputDotacion.valid && estados.inputDotacion.dirty)
            cambiosNomina.dotacionAsignada = estados.inputDotacion.valor
        //else if (estados.inputDotacion.valid === false)
        //    return console.log(`dotacion de la nomina: ${estados.inputDotacion.dotacion} invalida`)

        // el LIDER es valido y ha cambiado? ("deberia" ser valido siempre y cuando no seleccionen la opcion "sin seleccion")
        if (estados.selectLider.dirty)
            cambiosNomina.idLider = estados.selectLider.seleccionUsuario

        // el SUPERVISOR es valido y ha cambiado? ("deberia" ser valido siempre y cuando no seleccionen la opcion "sin seleccion")
        if (estados.selectSupervisor.dirty)
            cambiosNomina.idSupervisor = estados.selectSupervisor.seleccionUsuario

        // el CAPTADOR es valido y ha cambiado? ("deberia" ser valido siempre y cuando no seleccionen la opcion "sin seleccion")
        if (estados.selectCaptador1.dirty)
            cambiosNomina.idCaptador1 = estados.selectCaptador1.seleccionUsuario

        // La HORA de llegada del equipo ha cambiado?
        if(estados.inputHoraPresentacionLider.dirty)
            cambiosNomina.horaPresentacionLider = estados.inputHoraPresentacionLider.hora
        if(estados.inputHoraPresentacionEquipo.dirty)
            cambiosNomina.horaPresentacionEquipo = estados.inputHoraPresentacionEquipo.hora


        // almenos uno de los ementos debe estar "dirty" para guardar los cambios
        if(JSON.stringify(cambiosNomina)!=='{}'){
            console.log(cambiosNomina)
            this.props.guardarNomina(idNomina, cambiosNomina)
        }else{
            console.log('nomina sin cambios, no se actualiza')
        }
    }

    render(){
        const idJornada = this.props.inventario.idJornada
        const inventarioDia = idJornada==2 || idJornada==4
        const inventarioNoche = idJornada==3 || idJornada==4

        let _hrApertura = this.props.inventario.local.horaApertura.split(':')
        let txtHrApertura = `Apertura a las ${_hrApertura[0]}:${_hrApertura[1]}hrs`
        let _hrCierre = this.props.inventario.local.horaCierre.split(':')
        let txtHrCierre = `Cierre a las ${_hrCierre[0]}:${_hrCierre[1]}hrs`
        return (
            <tr className={this.props.mostrarSeparador? css.trSeparador: ''}>
                {/* Correlativo */}
                <td className={css.tdCorrelativo}>
                    <p title={this.props.inventario.idInventario}>{this.props.index+1}</p>
                </td>
                {/* Fecha */}
                <td className={css.tdFecha}>
                    <InputFecha
                        ref={ref=>this.inputDia=ref}
                        diaSemana={moment(this.props.inventario.fechaProgramada).format('dd')}
                        fecha={this.props.inventario.fechaProgramada}
                        onGuardar={this.guardarInventario.bind(this)}
                        puedeModificar={this.props.puedeModificar}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dia')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dia')}/>
                </td>
                {/* Cliente*/}
                <td className={css.tdCliente}>
                    <p>{this.props.inventario.local.cliente.nombreCorto}</p>
                </td>
                {/* CECO */}
                <td className={css.tdCeco}>
                    <OverlayTrigger
                        placement="left"
                        delay={0}
                        overlay={<Tooltip id="yyy">{`Tipo de local: ${this.props.inventario.local.formato_local.nombre}`}</Tooltip>}>
                        <p>{this.props.inventario.local.numero}</p>
                    </OverlayTrigger>
                </td>
                {/* Region */}
                <td className={css.tdRegion}>
                    <p>{this.props.inventario.local.direccion.comuna.provincia.region.numero}</p>
                </td>
                {/* Comuna */}
                <td className={css.tdComuna}>
                    <p>{this.props.inventario.local.direccion.comuna.nombre}</p>
                </td>
                {/* Turno */}
                <td className={css.tdTurno}>
                    <Select
                        ref={ref=>this.selectJornada=ref}
                        onSelect={this.guardarInventario.bind(this)}
                        opciones={[
                            {valor:'1', texto:'no definido'},
                            {valor:'2', texto:'día'},
                            {valor:'3', texto:'noche'},
                            {valor:'4', texto:'día y noche'}
                        ]}
                        seleccionada={this.props.inventario.idJornada}
                        puedeModificar={this.props.puedeModificar}
                    />
                </td>
                {/* Tienda */}
                <td className={css.tdTienda}>
                    <p>{this.props.inventario.local.nombre}</p>
                </td>
                {/* Stock */}
                <td className={css.tdStock}>
                    {/*<p><small>{numeral(this.props.inventario.local.stock).format('0,0')}</small></p>*/}
                    <InputStock
                        ref={ref=>this.inputStock=ref}
                        asignada={this.props.inventario.stockTeorico}
                        tooltipText={'Stock al ' +(this.props.inventario.fechaStock)}
                        onGuardar={this.guardarInventario.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'stock')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'stock')}
                        puedeModificar={this.props.puedeModificar}
                    />
                </td>
                {/* Dotación Total */}
                <td className={css.tdDotacionTotal}>
                    <InputDotacionMultiple
                        /*style={{display: (idJornada==2 || idJornada==3)? 'block' : 'none'}}*/
                        className="pull-left"
                        ref={ref=>this.inputDotacionTotal=ref}
                        asignada={this.props.inventario.dotacionAsignadaTotal}
                        onGuardar={this.guardarInventario.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacion')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacion')}
                        puedeModificar={this.props.puedeModificar}/>
                    <InputDotacionMultiple
                        style={{display: inventarioDia? 'block' : 'none'}}
                        className="pull-right"
                        ref={ref=>this.inputDotacionDia=ref}
                        asignada={this.props.inventario.nomina_dia.dotacionAsignada}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacion')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacion')}
                        puedeModificar={this.props.puedeModificar}/>
                    <InputDotacionMultiple
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        className="pull-right"
                        ref={ref=>this.inputDotacionNoche=ref}
                        asignada={this.props.inventario.nomina_noche.dotacionAsignada}
                        onGuardar={this.guardarNominaNoche.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacion')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacion')}
                        puedeModificar={this.props.puedeModificar}/>
                </td>
                {/* Lider */}
                <td className={css.tdUsuario}>
                    <Select style={{display: inventarioDia? 'block' : 'none'}}
                            ref={ref=>this.selectLiderDia=ref}
                            seleccionada={ this.props.inventario.nomina_dia.idLider || ''}
                            onSelect={this.guardarNominaDia.bind(this)}
                            opciones={this.props.opcionesLideres}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={this.props.puedeModificar}
                    />
                    <Select style={{display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectLiderNoche=ref}
                            seleccionada={ this.props.inventario.nomina_noche.idLider || ''}
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={this.props.opcionesLideres}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={this.props.puedeModificar}
                    />
                </td>
                {/* Supervisor */}
                <td className={css.tdUsuario}>
                    <Select style={{display: inventarioDia? 'block' : 'none'}}
                            ref={ref=>this.selectSupervisorDia=ref}
                            seleccionada={ this.props.inventario.nomina_dia.idSupervisor || ''}
                            onSelect={this.guardarNominaDia.bind(this)}
                            opciones={this.props.opcionesSupervisores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={this.props.puedeModificar}
                    />
                    <Select style={{display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectSupervisorNoche=ref}
                            seleccionada={ this.props.inventario.nomina_noche.idSupervisor || ''}
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={this.props.opcionesSupervisores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={this.props.puedeModificar}
                    />
                </td>
                {/* Captador 1 */}
                <td className={css.tdUsuario}>
                    <Select style={{display: inventarioDia? 'block' : 'none'}}
                            ref={ref=>this.selectCaptador1Dia=ref}
                            seleccionada={this.props.inventario.nomina_dia.idCaptador1 || ''}
                            onSelect={this.guardarNominaDia.bind(this)}
                            opciones={this.props.opcionesCaptadores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={this.props.puedeModificar}
                    />
                    <Select style={{display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectCaptador1Noche=ref}
                            seleccionada={this.props.inventario.nomina_noche.idCaptador1 || ''}
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={this.props.opcionesCaptadores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={this.props.puedeModificar}
                    />
                </td>
                {/* Hora Presentación Lider */}
                <td className={css.tdHora}>
                    {/*<p style={{display: inventarioDia? 'block' : 'none'}}>{this.props.inventario.hliderDia}</p>*/}
                    <InputHora
                        style={{display: inventarioDia? 'block' : 'none'}}
                        ref={ref=>this.inputHoraPresentacionLiderDia=ref}
                        asignada={this.props.inventario.nomina_dia.horaPresentacionLider}
                        tooltipText={txtHrApertura}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                        puedeModificar={this.props.puedeModificar}
                    />
                    {/*<p style={{display: inventarioNoche? 'block' : 'none'}}>{this.props.inventario.hliderNoche}</p>*/}
                    <InputHora
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        ref={ref=>this.inputHoraPresentacionLiderNoche=ref}
                        asignada={this.props.inventario.nomina_noche.horaPresentacionLider}
                        tooltipText={txtHrCierre}
                        onGuardar={this.guardarNominaNoche.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                        puedeModificar={this.props.puedeModificar}
                    />

                </td>
                {/* Hora Presentación Equipo*/}
                <td className={css.tdHora}>
                    {/*<p style={{display: inventarioDia? 'block' : 'none'}}>{this.props.inventario.hequipoDia}</p>*/}
                    <InputHora
                        style={{display: inventarioDia? 'block' : 'none'}}
                        ref={ref=>this.inputHoraPresentacionEquipoDia=ref}
                        asignada={this.props.inventario.nomina_dia.horaPresentacionEquipo}
                        tooltipText={txtHrApertura}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                        puedeModificar={this.props.puedeModificar}
                    />
                    {/*<p style={{display: inventarioNoche? 'block' : 'none'}}>{this.props.inventario.hequipoNoche}</p>*/}
                    <InputHora
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        ref={ref=>this.inputHoraPresentacionEquipoNoche=ref}
                        asignada={this.props.inventario.nomina_noche.horaPresentacionEquipo}
                        tooltipText={txtHrCierre}
                        onGuardar={this.guardarNominaNoche.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                        puedeModificar={this.props.puedeModificar}
                    />
                </td>
                {/* Dirección */}
                <td className={css.tdDireccion}>
                    {this.props.inventario.local.direccion.direccion}
                </td>
                {/* Nómina*/}
                <td className={css.tdNomina}>
                    {/* Nomina de Día */}
                    <a href={`nomina/${this.props.inventario.nomina_dia.idNomina}`}
                       className={cx(
                            'label',
                            // color depende del valor de la fechaSubida
                            labelNominas[this.props.inventario.nomina_noche.idEstadoNomina],
                            inventarioDia? 'center-block' : 'hide'
                        )}
                       target="_blank">
                        {estadoNominas[this.props.inventario.nomina_noche.idEstadoNomina]}
                    </a>
                    {/* Nomina de Noche */}
                    <a href={`nomina/${this.props.inventario.nomina_noche.idNomina}`}
                       className={cx(
                            'label',
                            // color depende del valor de la fechaSubida
                            labelNominas[this.props.inventario.nomina_noche.idEstadoNomina],
                            inventarioNoche? 'center-block' : 'hide'
                        )}
                       target="_blank">
                        {estadoNominas[this.props.inventario.nomina_noche.idEstadoNomina]}
                    </a>
                </td>
                {/* Unidades */}
                <td className={css.tdUnidadesReales}>
                    {this.props.inventario.unidadesReal}
                </td>
                <td className={css.tdUnidadesTeoricas}>
                    {this.props.inventario.unidadesTeorico}
                </td>
            </tr>
        )
    }
}

RowInventario.propTypes = {
    // Objetos
    puedeModificar: React.PropTypes.bool.isRequired,
    index: React.PropTypes.number.isRequired,
    inventario: React.PropTypes.object.isRequired,
    opcionesLideres: React.PropTypes.array.isRequired,
    opcionesSupervisores: React.PropTypes.array.isRequired,
    opcionesCaptadores: React.PropTypes.array.isRequired,
    mostrarSeparador: React.PropTypes.bool.isRequired,
    // Metodos
    guardarInventario: React.PropTypes.func.isRequired,
    guardarNomina: React.PropTypes.func.isRequired,
    focusRow: React.PropTypes.func.isRequired
}
RowInventario.defaultProps = {
    mostrarSeparador: false
}

export default RowInventario


// falta: en la tabla agregar campo: fecha + unidades teoricas + unidades reaales
// falta: crear la api de esteban