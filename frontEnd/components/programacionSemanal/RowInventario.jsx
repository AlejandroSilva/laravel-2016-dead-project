import React from 'react'
import numeral from 'numeral'

// Componentes
import Tooltip from 'react-bootstrap/lib/Tooltip'
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'
import InputFecha from './InputFecha.jsx'
import InputDotacion from './InputDotacion.jsx'
import InputDotacionCaptador from './InputDotacionCaptador.jsx'
import Select from './Select.jsx'

// Styles
//import styles from './RowInventario.css'
//import styleShared from '../shared/shared.css'

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
            let [anno, mes, _dia] = this.props.inventario.fechaProgramada.split('-')
            cambiosInventario.fechaProgramada = `${anno}-${mes}-${estadoInputDia.dia}`
        } else if (estadoInputDia.valid === false) {
            return console.log(`fecha ${estadoInputDia.dia} invalida`)
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
            selectCaptador2: this.selectCaptador2Dia.getEstado()
        })
    }
    guardarNominaNoche(){
        this._guardarNomina(this.props.inventario.nomina_noche.idNomina, {
            inputDotacion: this.inputDotacionNoche.getEstado(),
            selectLider: this.selectLiderNoche.getEstado(),
            selectSupervisor: this.selectSupervisorNoche.getEstado(),
            selectCaptador1: this.selectCaptador1Noche.getEstado(),
            selectCaptador2: this.selectCaptador2Noche.getEstado()
        })
    }

    _guardarNomina(idNomina, estados){
        let cambiosNomina = {}

        // la DOTACION es valida y ha cambiado?
        if (estados.inputDotacion.valid && estados.inputDotacion.dirty) {
            cambiosNomina.dotacionAsignada = estados.inputDotacion.dotacion
        } else if (estados.inputDotacion.valid === false) {
            return console.log(`dotacion de la nomina: ${estados.inputDotacion.dotacion} invalida`)
        }

        // el LIDER es valido y ha cambiado? ("deberia" ser valido siempre y cuando no seleccionen la opcion "sin seleccion")
        if (estados.selectLider.dirty) {
            cambiosNomina.idLider = estados.selectLider.seleccionUsuario
        }
        
        // el SUPERVISOR es valido y ha cambiado? ("deberia" ser valido siempre y cuando no seleccionen la opcion "sin seleccion")
        if (estados.selectSupervisor.dirty) {
            cambiosNomina.idLider = estados.selectSupervisor.seleccionUsuario
        }

        //queria seguir con select supervidor, pero no hay ningun supervisor disponible
        

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
                <td className={"asd"}>
                    <InputFecha
                        ref={ref=>this.inputDia=ref}
                        fecha={this.props.inventario.fechaProgramada}
                        onGuardar={this.guardarInventario.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dia')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dia')}/>
                </td>
                {/* Cliente*/}
                <td className={''}>
                    <p><small>{this.props.inventario.local.cliente.nombreCorto}</small></p>
                </td>
                {/* CECO */}
                <td className={''}>
                    <p><small>{this.props.inventario.local.numero}</small></p>
                </td>
                {/* Region */}
                <td className={'a'}>
                    <p><small>{this.props.inventario.local.nombreRegion}</small></p>
                </td>
                {/* Comuna */}
                <td className={'a'}>
                    <p><small>{this.props.inventario.local.nombreComuna}</small></p>
                </td>
                {/* Turno */}
                <td className={'a'}>
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
                <td className={'a'}>
                    <p><small>{this.props.inventario.local.nombre}</small></p>
                </td>
                {/* Stock */}
                <td className={'a'}>
                    <OverlayTrigger
                        placement="left"
                        delay={0}
                        overlay={<Tooltip id="yyy">{'Stock al '+(this.props.inventario.local.fechaStock)}</Tooltip>}>
                        <p><small>{numeral(this.props.inventario.local.stock).format('0,0')}</small></p>

                    </OverlayTrigger>
                </td>
                {/* Dotación Total */}
                <td className={'a'}>
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
                <td className={'a'}>
                    <Select style={{width: '120px', display: inventarioDia? 'block' : 'none'}}
                            ref={ref=>this.selectLiderDia=ref}
                            seleccionada={ this.props.inventario.nomina_dia.idLider || "-1"}
                            onSelect={this.guardarNominaDia.bind(this)}
                            opciones={opcionesLideres}
                            opcionNula={true}
                    />
                    <Select style={{width: '120px', display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectLiderNoche=ref}
                            seleccionada={ this.props.inventario.nomina_noche.idLider || "-1"}
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={opcionesLideres}
                            opcionNula={true}
                    />
                </td>
                {/* Supervisor */}
                <td className='a'>
                    <Select style={{width: '120px', display: inventarioDia? 'block' : 'none'}}
                            ref={ref=>this.selectSupervisorDia=ref}
                            seleccionada={''}                                // ToDo: arreglar esto (agregar supervisores)
                            onSelect={this.guardarNominaDia.bind(this)}
                            opciones={opcionesSupervisores}
                            opcionNula={true}
                    />
                    <Select style={{width: '120px', display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectSupervisorNoche=ref}
                            seleccionada={''}                                // ToDo: arreglar esto (agregar supervisores)
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={opcionesSupervisores}
                            opcionNula={true}
                    />
                </td>
                {/* Captador 1 */}
                <td className={'a'}>
                    <Select style={{width: '120px', display: inventarioDia? 'block' : 'none'}}
                            ref={ref=>this.selectCaptador1Dia=ref}
                            seleccionada={ ''+this.props.captadores[0].id}     // ToDo: arreglar esto
                            onSelect={this.guardarInventario.bind(this)}
                            opciones={opcionesCaptadores}
                    />
                    <Select style={{width: '120px', display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectCaptador1Noche=ref}
                            seleccionada={ ''+this.props.captadores[0].id}     // ToDo: arreglar esto
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={opcionesCaptadores}
                    />
                </td>
                {/* DotacionCaptador 1 */}
                <td className={'a'}>
                    <InputDotacionCaptador
                        style={{display: inventarioDia? 'block' : 'none'}}
                        asignada="3"/>
                    <InputDotacionCaptador
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        asignada="3"/>
                </td>
                {/* Captador 2 */}
                <td className={'a'}>
                    <Select style={{width: '120px', display: inventarioDia? 'block' : 'none'}}
                            ref={ref=>this.selectCaptador2Dia=ref}
                            seleccionada={ ''+this.props.captadores[0].id}     // ToDo: arreglar esto
                            onSelect={this.guardarNominaDia.bind(this)}
                            opciones={opcionesCaptadores}
                    />
                    <Select style={{width: '120px', display: inventarioNoche? 'block' : 'none'}}
                            ref={ref=>this.selectCaptador2Noche=ref}
                            seleccionada={ ''+this.props.captadores[0].id}     // ToDo: arreglar esto
                            onSelect={this.guardarNominaNoche.bind(this)}
                            opciones={opcionesCaptadores}
                    />
                </td>
                {/* DotacionCaptador 2 */}
                <td className={'a'}>
                    <InputDotacionCaptador
                        style={{display: inventarioDia? 'block' : 'none'}}
                        asignada="3"/>
                    <InputDotacionCaptador
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        asignada="3"/>
                </td>
                {/* Hora Presentación */}
                <td className={'a'}>
                    <p style={{display: inventarioDia? 'block' : 'none'}}>
                        <input type="time" defaultValue={this.props.inventario.horaLlegada}/>
                    </p>
                    <p style={{display: inventarioNoche? 'block' : 'none'}}>
                        <input type="time" defaultValue={this.props.inventario.horaLlegada}/>
                    </p>
                </td>
                {/* Dirección */}
                <td className={'a'}>
                    <p><small>{this.props.inventario.local.direccion.direccion}</small></p>
                </td>
                {/* Nómina*/}
                <td className={''}>
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