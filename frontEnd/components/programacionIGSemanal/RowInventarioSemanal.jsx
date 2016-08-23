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
import {SelectLider} from '../shared/SelectLider.jsx'

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
        }else if(elemento==='dotacionTotal'){
            // hace focus al primer input de la nomina que este habilitada
            if(this.props.inventario.nomina_dia.habilitada=="1"){
                this.inputDotacionDiaTotal.focus()
            }else if(this.props.inventario.nomina_noche.habilitada=="1"){
                this.inputDotacionNocheTotal.focus();
            }
        }else if(elemento==='dotacionOperadores'){
            // hace focus al primer input de la nomina que este habilitada
            if(this.props.inventario.nomina_dia.habilitada=="1"){
                this.inputDotacionDiaOperadores.focus()
            }else if(this.props.inventario.nomina_noche.habilitada=="1"){
                this.inputDotacionNocheOperadores.focus();
            }
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

        // LOS DOS CAMPOS DE DOTACION AHORA VAN EL LA NOMINA
        // // la DOTACION es valida y ha cambiado?
        // let estadoInputDotacionTotal = this.inputDotacionTotal.getEstado()
        // if (estadoInputDotacionTotal.valid && estadoInputDotacionTotal.dirty) {
        //     cambiosInventario.dotacionAsignadaTotal = estadoInputDotacionTotal.valor
        // } else if (estadoInputDotacionTotal.valid === false) {
        //     return console.log(`dotacion total: ${estadoInputDotacionTotal.valor} invalida`)
        // }

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
            inputDotacionTotal: this.inputDotacionDiaTotal.getEstado(),
            inputDotacionOperadores: this.inputDotacionDiaOperadores.getEstado(),
            selectLider: this.selectLiderDia.getEstado(),
            selectSupervisor: this.selectSupervisorDia.getEstado(),
            selectCaptador1: this.selectCaptador1Dia.getEstado(),
            inputHoraPresentacionLider: this.inputHoraPresentacionLiderDia.getEstado(),
            inputHoraPresentacionEquipo: this.inputHoraPresentacionEquipoDia.getEstado()
        })
    }
    guardarNominaNoche(){
        this._guardarNomina(this.props.inventario.nomina_noche.idNomina, {
            inputDotacionTotal: this.inputDotacionNocheTotal.getEstado(),
            inputDotacionOperadores: this.inputDotacionNocheOperadores.getEstado(),
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
        if (estados.inputDotacionTotal.valid && estados.inputDotacionTotal.dirty)
            cambiosNomina.dotacionTotal = estados.inputDotacionTotal.valor
        if (estados.inputDotacionOperadores.valid && estados.inputDotacionOperadores.dirty)
            cambiosNomina.dotacionOperadores = estados.inputDotacionOperadores.valor

        // el LIDER es valido y ha cambiado? ("deberia" ser valido siempre y cuando no seleccionen la opcion "sin seleccion")
        if (estados.selectLider.dirty)
            cambiosNomina.idLider = estados.selectLider.seleccionUsuario===null? '' : estados.selectLider.seleccionUsuario

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

    cargarLideresDisponibles = (callback)=>{
        callback({options: [], complete: false})
        this.props.lideresDisponibles(this.props.inventario.nomina_noche.idNomina)
            .then(usuarios=>{
                // el callback modifica el state del componente SelectLider
                callback({
                    options: usuarios.map(usuario=>({
                        label: usuario.nombre,
                        value: usuario.idUsuario,
                        enabled: usuario.disponible
                    })),
                    complete: true
                })
            })
    }

    render(){
        const idJornada = this.props.inventario.idJornada
        const inventarioDia = idJornada==2 || idJornada==4
        const inventarioNoche = idJornada==3 || idJornada==4
        let _ndia = this.props.inventario.nomina_dia
        let _nnoche = this.props.inventario.nomina_noche
        // puede eeditar si la nomina esta habilitada, y no esta en estado "informada"(5) o "informada excel"(6)
        let informadaDia   =_ndia.idEstadoNomina==5 || _ndia.idEstadoNomina==6
        let informadaNoche =_nnoche.idEstadoNomina==5 || _nnoche.idEstadoNomina==6
        // puede editar la NOMINA si tiene los permisos, esta habilitada, y esta no ha sido informada
        let puedeEditarNominaDia   = this.props.puedeModificar && _ndia.habilitada=="1" && !informadaDia
        let puedeEditarNominaNoche = this.props.puedeModificar && _nnoche.habilitada=="1" && !informadaNoche
        // el captador se puede editar siempre que se tengan los permisos y que la nomina este habilitada
        let puedeEditarCaptadorDia = this.props.puedeModificar && _ndia.habilitada=="1"
        let puedeEditarCaptadorNoche = this.props.puedeModificar && _nnoche.habilitada=="1"

        // el inventario se puede editar solo si tiene los permisos, y no hay una nomina informada
        let puedeEditar_fecha_turno_stock = this.props.puedeModificar && !informadaDia && !informadaNoche

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
                        puedeModificar={puedeEditar_fecha_turno_stock}
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
                        overlay={<Tooltip id="yyy">{`Tipo de local: ${this.props.inventario.local.formato_local.nombre} (prod.Sugerida:${this.props.inventario.local.formato_local.produccionSugerida})`}</Tooltip>}>
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
                            {valor:'1', texto:'ND'},
                            {valor:'2', texto:'D'},
                            {valor:'3', texto:'N'},
                            {valor:'4', texto:'DyN'}
                        ]}
                        seleccionada={this.props.inventario.idJornada}
                        puedeModificar={puedeEditar_fecha_turno_stock}
                    />
                </td>
                {/* Tienda */}
                <td className={css.tdTienda}>
                    <OverlayTrigger
                        placement="left"
                        delay={0}
                        overlay={<Tooltip id="yyy">{`Apertura: ${this.props.inventario.local.horaApertura}, Cierre:${this.props.inventario.local.horaCierre}`}</Tooltip>}>
                        <p>{this.props.inventario.local.nombre}</p>
                    </OverlayTrigger>
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
                        puedeModificar={puedeEditar_fecha_turno_stock}
                    />
                </td>
                {/* Dotación Total */}
                <td className={css.tdDotacionTotal}>
                    {/* Dotaion de Dia */}
                    <InputDotacionMultiple
                        style={{display: inventarioDia? 'block' : 'none'}}
                        className="pull-left"
                        ref={ref=>this.inputDotacionDiaTotal=ref}
                        asignada={this.props.inventario.nomina_dia.dotacionTotal}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacionTotal')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacionTotal')}
                        puedeModificar={puedeEditarNominaDia}/>
                    <InputDotacionMultiple
                        style={{display: inventarioDia? 'block' : 'none'}}
                        className="pull-right"
                        ref={ref=>this.inputDotacionDiaOperadores=ref}
                        asignada={this.props.inventario.nomina_dia.dotacionOperadores}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacionOperadores')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacionOperadores')}
                        puedeModificar={puedeEditarNominaDia}/>

                    {/* Dotacion de Noche*/}
                    <InputDotacionMultiple
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        className="pull-left"
                        ref={ref=>this.inputDotacionNocheTotal=ref}
                        asignada={this.props.inventario.nomina_noche.dotacionTotal}
                        onGuardar={this.guardarNominaNoche.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacionTotal')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacionTotal')}
                        puedeModificar={puedeEditarNominaNoche}/>
                    <InputDotacionMultiple
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        className="pull-right"
                        ref={ref=>this.inputDotacionNocheOperadores=ref}
                        asignada={this.props.inventario.nomina_noche.dotacionOperadores}
                        onGuardar={this.guardarNominaNoche.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacionOperadores')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacionOperadores')}
                        puedeModificar={puedeEditarNominaNoche}/>
                </td>
                {/* Lider */}
                <td className={css.tdUsuario}>
                    <SelectLider visible={inventarioDia}
                                 selectedValue={this.props.inventario.nomina_dia.idLider || ''}
                                 selectedLabel={
                                     // idealmente esto deberia calcularse en el backend...
                                     this.props.inventario.nomina_dia.lider?
                                         `${this.props.inventario.nomina_dia.lider.nombre1} ${this.props.inventario.nomina_dia.lider.apellidoPaterno}`
                                         :
                                         '--'
                                 }
                                 onOpenList={this.cargarLideresDisponibles}
                                 ref={ref=>this.selectLiderDia=ref}
                                 onChange={this.guardarNominaDia.bind(this)}
                    />
                    <SelectLider visible={inventarioNoche}
                                 selectedValue={this.props.inventario.nomina_noche.idLider || ''}
                                 selectedLabel={
                                     // idealmente esto deberia calcularse en el backend...
                                     this.props.inventario.nomina_noche.lider?
                                         `${this.props.inventario.nomina_noche.lider.nombre1} ${this.props.inventario.nomina_noche.lider.apellidoPaterno}`
                                         :
                                         '--'
                                 }
                                 onOpenList={this.cargarLideresDisponibles}
                                 ref={ref=>this.selectLiderNoche=ref}
                                 onChange={this.guardarNominaNoche.bind(this)}
                    />
                    {/*
                    <Select style={{display: inventarioDia? 'block' : 'none'}}
                            ref={ref=>this.selectLiderDia=ref}
                            seleccionada={ this.props.inventario.nomina_dia.idLider || ''}
                            onSelect={this.guardarNominaDia.bind(this)}
                            opciones={this.props.opcionesLideres}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={puedeEditarNominaDia}
                    />
                    <Select style={{display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectLiderNoche_=ref}
                            seleccionada={ this.props.inventario.nomina_noche.idLider}
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={this.props.opcionesLideres}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={puedeEditarNominaNoche}
                    />
                    */}
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
                        puedeModificar={puedeEditarNominaDia}
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
                        puedeModificar={puedeEditarNominaNoche}
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
                        puedeModificar={puedeEditarNominaDia}
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
                        puedeModificar={puedeEditarNominaNoche}
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
                            puedeModificar={puedeEditarNominaDia}
                    />
                    <Select style={{display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectSupervisorNoche=ref}
                            seleccionada={ this.props.inventario.nomina_noche.idSupervisor || ''}
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={this.props.opcionesSupervisores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={puedeEditarNominaNoche}
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
                            puedeModificar={puedeEditarCaptadorDia}
                    />
                    <Select style={{display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectCaptador1Noche=ref}
                            seleccionada={this.props.inventario.nomina_noche.idCaptador1 || ''}
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={this.props.opcionesCaptadores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={puedeEditarCaptadorNoche}
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
                            labelNominas[this.props.inventario.nomina_dia.idEstadoNomina],
                            inventarioDia? 'center-block' : 'hide'
                        )}
                       target="_blank">
                        {estadoNominas[this.props.inventario.nomina_dia.idEstadoNomina]}
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
                {/* Patentes */}
                <td className={css.tdPatentes}>
                    {this.props.inventario.patentes}
                </td>
                {/* Unidades */}
                <td className={css.tdUnidadesReales}>
                    {this.props.inventario.unidadesReal}
                </td>
                <td className={css.tdUnidadesTeoricas}>
                    {this.props.inventario.unidadesTeorico}
                </td>
                {/* Nómina de pago */}
                <td className={css.tdNominaPago}>
                    {/* Día */}
                    <div style={{width: '60px'}}>
                        {this.props.inventario.nomina_dia.urlNominaPago==''?
                            <span className={cx('label label-default', inventarioDia? 'center-block' : 'hide')}>
                                Pendiente
                            </span>
                            :
                            <a href={this.props.inventario.nomina_dia.urlNominaPago} target="_blank"
                               className={cx('label label-primary', inventarioDia? 'center-block' : 'hide')}>
                                Disponible
                            </a>
                        }
                    </div>
                    {/* Noche */}
                    <div style={{width: '60px'}}>
                        {this.props.inventario.nomina_noche.urlNominaPago==''?
                            <span className={cx('label label-default', inventarioNoche? 'center-block' : 'hide')}>
                                Pendiente
                            </span>
                            :
                            <a href={this.props.inventario.nomina_noche.urlNominaPago} target="_blank"
                               className={cx('label label-primary', inventarioNoche? 'center-block' : 'hide')}>
                                Disponible
                            </a>
                        }
                    </div>
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
    lideresDisponibles: React.PropTypes.func.isRequired,
    guardarInventario: React.PropTypes.func.isRequired,
    guardarNomina: React.PropTypes.func.isRequired,
    focusRow: React.PropTypes.func.isRequired
}
RowInventario.defaultProps = {
    mostrarSeparador: false
}

export default RowInventario