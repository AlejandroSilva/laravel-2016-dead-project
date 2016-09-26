import React from 'react'
import moment from 'moment'
moment.locale('es')

// Componentes
import InputFecha from '../shared/InputFecha.jsx'
import InputHora from '../shared/InputHora.jsx'
import InputDotacionMultiple from '../shared/InputDotacionSimple.jsx'
import InputStock from '../shared/InputStock.jsx'
import Select from '../shared/Select.jsx'
import { SelectLider } from '../shared/SelectLider.jsx'
import { SelectCaptadores } from '../shared/SelectCaptadores.jsx'

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
            if(this.props.inventario.ndia_habilitada=="1"){
                this.inputDotacionDiaTotal.focus()
            }else if(this.props.inventario.nnoche_habilitada=="1"){
                this.inputDotacionNocheTotal.focus();
            }
        }else if(elemento==='dotacionOperadores'){
            // hace focus al primer input de la nomina que este habilitada
            if(this.props.inventario.ndia_habilitada=="1"){
                this.inputDotacionDiaOperadores.focus()
            }else if(this.props.inventario.nnoche_habilitada=="1"){
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
            this.props.guardarInventario(this.props.inventario.inv_idInventario, cambiosInventario)
        }else{
            console.log('inventario sin cambios, no se actualiza')
        }
    }
    guardarNominaDia() {
        this._guardarNomina(this.props.inventario.ndia_idNomina, {
            inputDotacionTotal: this.inputDotacionDiaTotal.getEstado(),
            inputDotacionOperadores: this.inputDotacionDiaOperadores.getEstado(),
            selectLider: this.selectLiderDia.getEstado(),
            selectSupervisor: this.selectSupervisorDia.getEstado(),
            inputHoraPresentacionLider: this.inputHoraPresentacionLiderDia.getEstado(),
            inputHoraPresentacionEquipo: this.inputHoraPresentacionEquipoDia.getEstado()
        })
    }
    guardarNominaNoche(){
        this._guardarNomina(this.props.inventario.nnoche_idNomina, {
            inputDotacionTotal: this.inputDotacionNocheTotal.getEstado(),
            inputDotacionOperadores: this.inputDotacionNocheOperadores.getEstado(),
            selectLider: this.selectLiderNoche.getEstado(),
            selectSupervisor: this.selectSupervisorNoche.getEstado(),
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
        this.props.lideresDisponibles(this.props.inventario.nnoche_idNomina)
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
        let inv = this.props.inventario
        const idJornada = inv.inv_idJornada
        const inventarioDia = idJornada==2 || idJornada==4
        const inventarioNoche = idJornada==3 || idJornada==4
        // puede eeditar si la nomina esta habilitada, y no esta en estado "informada"(5) o "informada excel"(6)
        let informadaDia   = inv.ndia_idEstadoNomina==5 || inv.ndia_idEstadoNomina==6
        let informadaNoche = inv.nnoche_idEstadoNomina==5 || inv.nnoche_idEstadoNomina==6
        // puede editar la NOMINA si tiene los permisos, esta habilitada, y esta no ha sido informada
        let puedeEditarNominaDia   = this.props.puedeModificar && inv.ndia_habilitada=="1" && !informadaDia
        let puedeEditarNominaNoche = this.props.puedeModificar && inv.nnoche_habilitada=="1" && !informadaNoche
        // el captador se puede editar siempre que se tengan los permisos y que la nomina este habilitada
        // let puedeEditarCaptadorDia = this.props.puedeModificar && inv.ndia_habilitada=="1"
        // let puedeEditarCaptadorNoche = this.props.puedeModificar && inv.nnoche_habilitada=="1"

        // el inventario se puede editar solo si tiene los permisos, y no hay una nomina informada
        let puedeEditar_fecha_turno_stock = this.props.puedeModificar && !informadaDia && !informadaNoche

        let _hrApertura = inv.local_horaApertura.split(':')
        let txtHrApertura = `Apertura a las ${_hrApertura[0]}:${_hrApertura[1]}hrs`
        let _hrCierre = inv.local_horaCierre.split(':')
        let txtHrCierre = `Cierre a las ${_hrCierre[0]}:${_hrCierre[1]}hrs`

        return (
            <tr className={this.props.mostrarSeparador? css.trSeparador: ''}>
                {/* Correlativo */}
                <td className={css.tdCorrelativo}>
                    <p title={inv.inv_idInventario}>{this.props.index+1}</p>
                </td>
                {/* Fecha */}
                <td className={css.tdFecha}>
                    <InputFecha
                        ref={ref=>this.inputDia=ref}
                        diaSemana={ inv.inv_fechaProgramadaDOW }
                        fechaConProblemas={inv.local_topeFechaConAuditoria}
                        fecha={inv.inv_fechaProgramada}
                        onGuardar={this.guardarInventario.bind(this)}
                        puedeModificar={puedeEditar_fecha_turno_stock}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dia')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dia')}/>
                </td>
                {/* Cliente*/}
                <td className={css.tdCliente}>
                    <p>{inv.cliente_nombreCorto}</p>
                </td>
                {/* CECO */}
                <td className={css.tdCeco}>
                    <p className={css.textoConTooltip}>
                        {inv.local_ceco}
                        <span>Tipo de local: {inv.local_formatoLocal} (prod.Sugerida:{inv.local_produccionSugerida})</span>
                    </p>
                </td>
                {/* Region */}
                <td className={css.tdRegion}>
                    <p>{inv.local_region}</p>
                </td>
                {/* Comuna */}
                <td className={css.tdComuna}>
                    <p>{inv.local_comuna}</p>
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
                        seleccionada={''+inv.inv_idJornada}
                        puedeModificar={puedeEditar_fecha_turno_stock}
                    />
                </td>
                {/* Tienda */}
                <td className={css.tdTienda}>
                    <p className={css.textoConTooltip}>
                        {inv.local_nombre}
                        <span>Apertura: {inv.local_horaApertura}, Cierre:{inv.local_horaCierre}</span>
                    </p>
                </td>
                {/* Stock */}
                <td className={css.tdStock}>
                    <InputStock
                        ref={ref=>this.inputStock=ref}
                        asignada={''+inv.inv_stockTeorico}  // pide en string
                        tooltipText={'Stock al ' +(inv.inv_fechaStock)}
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
                        asignada={''+inv.ndia_dotTotal} // pide un string...
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacionTotal')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacionTotal')}
                        puedeModificar={puedeEditarNominaDia}/>
                    <InputDotacionMultiple
                        style={{display: inventarioDia? 'block' : 'none'}}
                        className="pull-right"
                        ref={ref=>this.inputDotacionDiaOperadores=ref}
                        asignada={''+inv.ndia_dotOperadores} // pide un string...
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacionOperadores')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacionOperadores')}
                        puedeModificar={puedeEditarNominaDia}/>

                    {/* Dotacion de Noche*/}
                    <InputDotacionMultiple
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        className="pull-left"
                        ref={ref=>this.inputDotacionNocheTotal=ref}
                        asignada={''+inv.nnoche_dotTotal}  // pide un string...
                        onGuardar={this.guardarNominaNoche.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacionTotal')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacionTotal')}
                        puedeModificar={puedeEditarNominaNoche}/>
                    <InputDotacionMultiple
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        className="pull-right"
                        ref={ref=>this.inputDotacionNocheOperadores=ref}
                        asignada={''+inv.nnoche_dotOperadores}  // pide un string...
                        onGuardar={this.guardarNominaNoche.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacionOperadores')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacionOperadores')}
                        puedeModificar={puedeEditarNominaNoche}/>
                </td>
                {/* Lider */}
                <td className={css.tdUsuario}>
                    <SelectLider visible={inventarioDia}
                                 editable={puedeEditarNominaDia}
                                 selectedValue={inv.ndia_idLider || ''}
                                 selectedLabel={inv.ndia_lider}
                                 onOpenList={this.cargarLideresDisponibles}
                                 ref={ref=>this.selectLiderDia=ref}
                                 onChange={this.guardarNominaDia.bind(this)}
                    />
                    <SelectLider visible={inventarioNoche}
                                 editable={puedeEditarNominaNoche}
                                 selectedValue={inv.nnoche_idLider || ''}
                                 selectedLabel={inv.nnoche_lider}
                                 onOpenList={this.cargarLideresDisponibles}
                                 ref={ref=>this.selectLiderNoche=ref}
                                 onChange={this.guardarNominaNoche.bind(this)}
                    />
                </td>
                {/* Hora Presentación Lider */}
                <td className={css.tdHora}>
                    <InputHora
                        style={{display: inventarioDia? 'block' : 'none'}}
                        ref={ref=>this.inputHoraPresentacionLiderDia=ref}
                        asignada={inv.ndia_hrLider}
                        tooltipText={txtHrApertura}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                        puedeModificar={puedeEditarNominaDia}
                    />
                    <InputHora
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        ref={ref=>this.inputHoraPresentacionLiderNoche=ref}
                        asignada={inv.nnoche_hrLider}
                        tooltipText={txtHrCierre}
                        onGuardar={this.guardarNominaNoche.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                        puedeModificar={puedeEditarNominaNoche}
                    />
                </td>
                {/* Hora Presentación Equipo*/}
                <td className={css.tdHora}>
                    <InputHora
                        style={{display: inventarioDia? 'block' : 'none'}}
                        ref={ref=>this.inputHoraPresentacionEquipoDia=ref}
                        asignada={inv.ndia_hrEquipo}
                        tooltipText={txtHrApertura}
                        onGuardar={this.guardarNominaDia.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                        puedeModificar={puedeEditarNominaDia}
                    />
                    <InputHora
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        ref={ref=>this.inputHoraPresentacionEquipoNoche=ref}
                        asignada={inv.nnoche_hrEquipo}
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
                            seleccionada={ ''+inv.ndia_idSupervisor || ''}
                            onSelect={this.guardarNominaDia.bind(this)}
                            opciones={this.props.opcionesSupervisores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={puedeEditarNominaDia}
                    />
                    <Select style={{display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectSupervisorNoche=ref}
                            seleccionada={ ''+inv.nnoche_idSupervisor || ''}
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={this.props.opcionesSupervisores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={puedeEditarNominaNoche}
                    />
                </td>
                {/* Captador 1 */}
                <td className={css.tdUsuario}>
                    <SelectCaptadores
                        visible={inventarioDia}
                        captadoresDisponibles={this.props.captadoresDisponibles}
                        captadores={inv.ndia_captadores}
                        agregarCaptador={this.props.agregarCaptador.bind(this, inv.ndia_idNomina)}
                        quitarCaptador={this.props.quitarCaptador.bind(this, inv.ndia_idNomina)}
                        cambiarAsignados={this.props.cambiarAsignados.bind(this, inv.ndia_idNomina)}
                    />
                    <SelectCaptadores
                        visible={inventarioNoche}
                        captadoresDisponibles={this.props.captadoresDisponibles}
                        captadores={inv.nnoche_captadores}
                        agregarCaptador={this.props.agregarCaptador.bind(this, inv.nnoche_idNomina)}
                        quitarCaptador={this.props.quitarCaptador.bind(this, inv.nnoche_idNomina)}
                        cambiarAsignados={this.props.cambiarAsignados.bind(this, inv.nnoche_idNomina)}
                    />
                </td>
                {/* Dirección */}
                <td className={css.tdDireccion}>
                    {inv.local_direccion}
                </td>
                {/* Nómina*/}
                <td className={css.tdNomina}>
                    {/* Nomina de Día */}
                    <a href={`nomina/${inv.ndia_idNomina}`}
                       className={cx(
                            'label',
                            // color depende del valor de la fechaSubida
                            labelNominas[inv.ndia_idEstadoNomina],
                            inventarioDia? 'center-block' : 'hide'
                        )}
                       target="_blank">
                        {estadoNominas[inv.ndia_idEstadoNomina]}
                    </a>
                    {/* Nomina de Noche */}
                    <a href={`nomina/${inv.nnoche_idNomina}`}
                       className={cx(
                            'label',
                            // color depende del valor de la fechaSubida
                            labelNominas[inv.nnoche_idEstadoNomina],
                            inventarioNoche? 'center-block' : 'hide'
                        )}
                       target="_blank">
                        {estadoNominas[inv.nnoche_idEstadoNomina]}
                    </a>
                </td>
                {/* Patentes */}
                <td className={css.tdPatentes}>
                    {inv.inv_patentes}
                </td>
                {/* Unidades */}
                <td className={css.tdUnidadesReales}>
                    {inv.inv_unidadesReales}
                </td>
                <td className={css.tdUnidadesTeoricas}>
                    {inv.inv_unidadesTeorico}
                </td>
                {/* Nómina de pago */}
                <td className={css.tdNominaPago}>
                    {/* Día */}
                    <div style={{width: '60px'}}>
                        {inv.ndia_urlNominaPago==''?
                            <span className={cx('label label-default', inventarioDia? 'center-block' : 'hide')}>
                                Pendiente
                            </span>
                            :
                            <a href={inv.ndia_urlNominaPago} target="_blank"
                               className={cx('label label-primary', inventarioDia? 'center-block' : 'hide')}>
                                Disponible
                            </a>
                        }
                    </div>
                    {/* Noche */}
                    <div style={{width: '60px'}}>
                        {inv.nnoche_urlNominaPago==''?
                            <span className={cx('label label-default', inventarioNoche? 'center-block' : 'hide')}>
                                Pendiente
                            </span>
                            :
                            <a href={inv.nnoche_urlNominaPago} target="_blank"
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
    captadoresDisponibles: React.PropTypes.array.isRequired,
    mostrarSeparador: React.PropTypes.bool.isRequired,
    // Metodos
    lideresDisponibles: React.PropTypes.func.isRequired,
    guardarInventario: React.PropTypes.func.isRequired,
    guardarNomina: React.PropTypes.func.isRequired,
    focusRow: React.PropTypes.func.isRequired,
    agregarCaptador: React.PropTypes.func.isRequired,
    quitarCaptador: React.PropTypes.func.isRequired,
    cambiarAsignados: React.PropTypes.func.isRequired
}
RowInventario.defaultProps = {
    mostrarSeparador: false
}

export default RowInventario