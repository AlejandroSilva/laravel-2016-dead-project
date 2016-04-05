import React from 'react'
import numeral from 'numeral'

// Componentes
import Tooltip from 'react-bootstrap/lib/Tooltip'
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'
import InputFecha from './InputFecha.jsx'
import InputHora from './InputHora.jsx'
import InputDotacion from './InputDotacion.jsx'
import InputStock from './InputStock.jsx'
import Select from './Select.jsx'

// Styles
import css from './TablaSemanal.css'

class RowInventario extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            inputDia: 0,
            inputMes: 0,
            inputAnno: 0,
            inputDotacion: 0,
            selectJornada: 4
        }
        // Refs disponibles: this.inputDia, this.inputDotacion
    }

    guardarInventario() {
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
            cambiosInventario.dotacionAsignadaTotal = estadoInputDotacionTotal.dotacion
        } else if (estadoInputDotacionTotal.valid === false) {
            return console.log(`dotacion total: ${estadoInputDotacionTotal.dotacion} invalida`)
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
            // selectSupervisor: this.selectSupervisorDia.getEstado(),
            selectCaptador1: this.selectCaptador1Dia.getEstado(),
            // selectCaptador2: this.selectCaptador2Dia.getEstado(),
            // inputDotacionCaptador1: this.inputDotacionCaptador1Dia.getEstado(),
            // inputDotacionCaptador2: this.inputDotacionCaptador2Dia.getEstado(),
            inputHoraPresentacionLider: this.inputHoraPresentacionLiderDia.getEstado(),
            inputHoraPresentacionEquipo: this.inputHoraPresentacionEquipoDia.getEstado()
        })
    }
    guardarNominaNoche(){
        this._guardarNomina(this.props.inventario.nomina_noche.idNomina, {
            inputDotacion: this.inputDotacionNoche.getEstado(),
            selectLider: this.selectLiderNoche.getEstado(),
            // selectSupervisor: this.selectSupervisorNoche.getEstado(),
            selectCaptador1: this.selectCaptador1Noche.getEstado(),
            // selectCaptador2: this.selectCaptador2Noche.getEstado(),
            // inputDotacionCaptador1: this.inputDotacionCaptador1Noche.getEstado(),
            // inputDotacionCaptador2: this.inputDotacionCaptador2Noche.getEstado(),
            inputHoraPresentacionLider: this.inputHoraPresentacionLiderNoche.getEstado(),
            inputHoraPresentacionEquipo: this.inputHoraPresentacionEquipoNoche.getEstado()
        })
    }

    _guardarNomina(idNomina, estados){
        let cambiosNomina = {}

        // la DOTACION es valida y ha cambiado?
        if (estados.inputDotacion.valid && estados.inputDotacion.dirty)
            cambiosNomina.dotacionAsignada = estados.inputDotacion.dotacion
        //else if (estados.inputDotacion.valid === false)
        //    return console.log(`dotacion de la nomina: ${estados.inputDotacion.dotacion} invalida`)

        // el LIDER es valido y ha cambiado? ("deberia" ser valido siempre y cuando no seleccionen la opcion "sin seleccion")
        if (estados.selectLider.dirty)
            cambiosNomina.idLider = estados.selectLider.seleccionUsuario
        
        // el SUPERVISOR es valido y ha cambiado? ("deberia" ser valido siempre y cuando no seleccionen la opcion "sin seleccion")
        // if (estados.selectSupervisor.dirty)
        //     cambiosNomina.idSupervisor = estados.selectSupervisor.seleccionUsuario

        // el CAPTADOR es valido y ha cambiado? ("deberia" ser valido siempre y cuando no seleccionen la opcion "sin seleccion")
        if (estados.selectCaptador1.dirty)
            cambiosNomina.idCaptador1 = estados.selectCaptador1.seleccionUsuario
        // if (estados.selectCaptador2.dirty)
        //     cambiosNomina.idCaptador2 = estados.selectCaptador2.seleccionUsuario

        // La DOTACION del CAPTADOR es valida y ha cambiado?
        // if (estados.inputDotacionCaptador1.valid && estados.inputDotacionCaptador1.dirty)
        //     cambiosNomina.dotacionCaptador1 = estados.inputDotacionCaptador1.dotacion
        // else if (estados.inputDotacionCaptador1.valid === false)
        //     return console.log(`dotacion del captador1: ${estados.inputDotacionCaptador1.dotacion} invalida`)

        // if (estados.inputDotacionCaptador2.valid && estados.inputDotacionCaptador2.dirty)
        //     cambiosNomina.dotacionCaptador2 = estados.inputDotacionCaptador2.dotacion
        // else if (estados.inputDotacionCaptador2.valid === false)
        //     return console.log(`dotacion del captador2: ${estados.inputDotacionCaptador2.dotacion} invalida`)

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

    focusElemento(elemento){
        if(elemento==='dia'){
            this.inputDia.focus()
        }else if(elemento==='dotacion'){
            this.inputDotacionTotal.focus()
        }else if(elemento==='stock'){
            this.inputStock.focus()
        }
    }

    render(){
        const idJornada = this.props.inventario.idJornada
        const inventarioDia = idJornada==2 || idJornada==4
        const inventarioNoche = idJornada==3 || idJornada==4
        const opcionesLideres = this.props.lideres.map(usuario=>{
            return {valor: usuario.id, texto:`${usuario.nombre1} ${usuario.apellidoPaterno}`}
        })
        const opcionesSupervisores = this.props.supervisores.map(usuario=>{
            return {valor: usuario.id, texto:`${usuario.nombre1} ${usuario.apellidoPaterno}`}
        })
        const opcionesCaptadores = this.props.captadores.map(usuario=>{
            return {valor: usuario.id, texto:`${usuario.nombre1} ${usuario.apellidoPaterno}`}
        })
        return (
            <tr>
                {/* Fecha */}
                <td className={css.tdFecha}>
                    <InputFecha
                        ref={ref=>this.inputDia=ref}
                        fecha={this.props.inventario.fechaProgramada}
                        onGuardar={this.guardarInventario.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dia')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dia')}/>
                </td>
                {/* Cliente*/}
                <td className={css.tdCliente}>
                    <p>{this.props.inventario.local.cliente.nombreCorto}</p>
                </td>
                {/* CECO */}
                <td className={css.tdCeco}>
                    <p>{this.props.inventario.local.numero}</p>
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
                    />
                </td>
                {/* Tienda */}
                <td className={css.tdTienda}>
                    <p><small>{this.props.inventario.local.nombre}</small></p>
                </td>
                {/* Stock */}
                <td className={css.tdStock}>
                    <p><small>{numeral(this.props.inventario.local.stock).format('0,0')}</small></p>
                    <InputStock
                        ref={ref=>this.inputStock=ref}
                        asignada={this.props.inventario.stockTeorico}
                        tooltipText={'Stock al ' +(this.props.inventario.local.fechaStock)}
                        onGuardar={this.guardarInventario.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'stock')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'stock')}
                    />
                </td>
                {/* Dotación Total */}
                <td className={css.tdDotacionTotal}>
                    <InputDotacion
                        /*style={{display: (idJornada==2 || idJornada==3)? 'block' : 'none'}}*/
                        className="pull-left"
                        ref={ref=>this.inputDotacionTotal=ref}
                        asignada={this.props.inventario.dotacionAsignadaTotal}
                        onGuardar={this.guardarInventario.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacion')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacion')}/>
                    <InputDotacion
                        style={{display: inventarioDia? 'block' : 'none'}}
                        className="pull-right"
                        ref={ref=>this.inputDotacionDia=ref}
                        asignada={this.props.inventario.nomina_dia.dotacionAsignada}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacion')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacion')}/>
                    <InputDotacion
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        className="pull-right"
                        ref={ref=>this.inputDotacionNoche=ref}
                        asignada={this.props.inventario.nomina_noche.dotacionAsignada}
                        onGuardar={this.guardarNominaNoche.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacion')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacion')}/>
                </td>
                {/* Lider */}
                <td className={css.tdLider}>
                    <Select style={{display: inventarioDia? 'block' : 'none'}}
                            ref={ref=>this.selectLiderDia=ref}
                            seleccionada={ this.props.inventario.nomina_dia.idLider || ''}
                            onSelect={this.guardarNominaDia.bind(this)}
                            opciones={opcionesLideres}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                    />
                    <Select style={{display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectLiderNoche=ref}
                            seleccionada={ this.props.inventario.nomina_noche.idLider || ''}
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={opcionesLideres}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                    />
                </td>
                {/* Supervisor */}
                {/*
                <td className={css.tdLider}>
                    <Select style={{display: inventarioDia? 'block' : 'none'}}
                            ref={ref=>this.selectSupervisorDia=ref}
                            seleccionada={this.props.inventario.nomina_dia.idSupervisor || ''}
                            onSelect={this.guardarNominaDia.bind(this)}
                            opciones={opcionesSupervisores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                    />
                    <Select style={{display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectSupervisorNoche=ref}
                            seleccionada={this.props.inventario.nomina_noche.idSupervisor || ''}
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={opcionesSupervisores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                    />
                </td>
                 */}
                {/* Captador 1 */}
                <td className={css.tdLider}>
                    <Select style={{display: inventarioDia? 'block' : 'none'}}
                            ref={ref=>this.selectCaptador1Dia=ref}
                            seleccionada={this.props.inventario.nomina_dia.idCaptador1 || ''}
                            onSelect={this.guardarNominaDia.bind(this)}
                            opciones={opcionesCaptadores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                    />
                    <Select style={{display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectCaptador1Noche=ref}
                            seleccionada={this.props.inventario.nomina_noche.idCaptador1 || ''}
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={opcionesCaptadores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                    />
                </td>
                {/* DotacionCaptador 1 */}
                {/*
                <td className={css.tdDotacion }>
                    <InputDotacion
                        style={{display: inventarioDia? 'block' : 'none'}}
                        ref={ref=>this.inputDotacionCaptador1Dia=ref}
                        asignada={this.props.inventario.nomina_dia.dotacionCaptador1}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                    />
                    <InputDotacion
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        ref={ref=>this.inputDotacionCaptador1Noche=ref}
                        asignada={this.props.inventario.nomina_noche.dotacionCaptador1}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                    />
                </td>
                 */}
                {/* Captador 2 */}
                {/*
                <td className={css.tdLider}>
                    <Select style={{display: inventarioDia? 'block' : 'none'}}
                            ref={ref=>this.selectCaptador2Dia=ref}
                            seleccionada={this.props.inventario.nomina_dia.idCaptador2 || ''}
                            onSelect={this.guardarNominaDia.bind(this)}
                            opciones={opcionesCaptadores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                    />
                    <Select style={{display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectCaptador2Noche=ref}
                            seleccionada={this.props.inventario.nomina_noche.idCaptador2 || ''}
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={opcionesCaptadores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                    />
                </td>
                 */}
                {/* DotacionCaptador 2 */}
                {/*
                <td className={css.tdDotacion}>
                    <InputDotacion
                        style={{display: inventarioDia? 'block' : 'none'}}
                        ref={ref=>this.inputDotacionCaptador2Dia=ref}
                        asignada={this.props.inventario.nomina_dia.dotacionCaptador2}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                    />

                    <InputDotacion
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        ref={ref=>this.inputDotacionCaptador2Noche=ref}
                        asignada={this.props.inventario.nomina_noche.dotacionCaptador2}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                    />
                </td>
                 */}
                {/* Hora Presentación Lider */}
                <td className={css.tdHora}>
                    <InputHora
                        style={{display: inventarioDia? 'block' : 'none'}}
                        ref={ref=>this.inputHoraPresentacionLiderDia=ref}
                        asignada={this.props.inventario.nomina_dia.horaPresentacionLider}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                    />
                    <InputHora
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        ref={ref=>this.inputHoraPresentacionLiderNoche=ref}
                        asignada={this.props.inventario.nomina_noche.horaPresentacionLider}
                        onGuardar={this.guardarNominaNoche.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                    />

                </td>
                {/* Hora Presentación Equipo*/}
                <td className={css.tdHora}>
                    <InputHora
                        style={{display: inventarioDia? 'block' : 'none'}}
                        ref={ref=>this.inputHoraPresentacionEquipoDia=ref}
                        asignada={this.props.inventario.nomina_dia.horaPresentacionEquipo}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                    />
                    <InputHora
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        ref={ref=>this.inputHoraPresentacionEquipoNoche=ref}
                        asignada={this.props.inventario.nomina_noche.horaPresentacionEquipo}
                        onGuardar={this.guardarNominaNoche.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                    />
                </td>
                {/* Dirección */}
                {/*
                <td className={css.tdDireccion}>
                    <p>{this.props.inventario.local.direccion.direccion}</p>
                </td>
                 */}
                {/* Nómina*/}
                <td className={css.tdNomina}>
                    <button className="btn btn-xs btn-primary btn-block" tabIndex="-1">Ver</button>
                </td>
            </tr>
        )
    }
}

RowInventario.propTypes = {
    // Objetos
    index: React.PropTypes.number.isRequired,
    inventario: React.PropTypes.object.isRequired,
    lideres: React.PropTypes.array.isRequired,
    captadores: React.PropTypes.array.isRequired,
    // Metodos
    guardarInventario: React.PropTypes.func.isRequired,
    guardarNomina: React.PropTypes.func.isRequired,
    focusRow: React.PropTypes.func.isRequired
}

export default RowInventario