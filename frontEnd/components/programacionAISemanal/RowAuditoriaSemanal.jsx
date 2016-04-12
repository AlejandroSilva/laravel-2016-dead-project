import React from 'react'
import numeral from 'numeral'
import moment from 'moment'
moment.locale('es')

// Componentes
import Tooltip from 'react-bootstrap/lib/Tooltip'
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'
import InputFecha from './InputFecha.jsx'
import InputHora from './InputHora.jsx'
import InputDotacion from './InputDotacion.jsx'
import InputStock from './InputStock.jsx'
import Select from './Select.jsx'

// Styles
import * as css from './TablaAuditoriaSemanal.css'

class RowAuditoriaSemanal extends React.Component{
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

    guardarAuditoria() {
        let cambiosAuditoria = {}

        // el DIA es valido, y ha cambiado?
        let estadoInputDia = this.inputDia.getEstado()
        if (estadoInputDia.valid && estadoInputDia.dirty) {
            cambiosAuditoria.fechaProgramada = estadoInputDia.fecha
        } else if (estadoInputDia.valid === false) {
            return console.log(`fecha ${estadoInputDia.fecha} invalida`)
        }

        // el AUDITOR ha cambiado?
        let estadoSelectAuditor = this.selectAuditor.getEstado()
        if(estadoSelectAuditor.dirty)
            cambiosAuditoria.idAuditor = estadoSelectAuditor.seleccionUsuario

        // la HORA DE LLEGADA DEL AUDITOR es valida y ha cambiado?
        // let estadoHoraAuditor = this.inputHoraAuditor.getEstado()
        // console.log('hora auditor ', estadoHoraAuditor)
        // if (estadoHoraAuditor.dirty)
        //     cambiosAuditoria.horaPresentacionAuditor = estadoHoraAuditor.hora

        // almenos uno de los ementos debe estar "dirty" para guardar los cambios
        if(JSON.stringify(cambiosAuditoria)!=="{}"){
            console.log(cambiosAuditoria)
            this.props.guardarAuditoria(this.props.auditoria.idAuditoria, cambiosAuditoria)
        }else{
            console.log('auditoria sin cambios, no se actualiza')
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
        const opcionesAuditores = this.props.auditores.map(usuario=>{
            return {valor: usuario.id, texto:`${usuario.nombre1} ${usuario.apellidoPaterno}`}
        })

        //let _hrApertura = this.props.auditoria.local.horaApertura.split(':')
        //let _hrCierre = this.props.auditoria.local.horaCierre.split(':')
        //let txtHrCierre = `Cierre a las ${_hrCierre[0]}:${_hrCierre[1]}hrs`
        return (
            <tr className={this.props.mostrarSeparador? css.trSeparador: ''}>
                {/* Correlativo */}
                <td className={css.tdCorrelativo}>
                    {this.props.index}
                </td>
                {/* Fecha */}
                <td className={css.tdFecha}>
                    <InputFecha
                        puedeModificar={this.props.puedeModificar}
                        ref={ref=>this.inputDia=ref}
                        diaSemana={moment(this.props.auditoria.fechaProgramada).format('dddd')}
                        fecha={this.props.auditoria.fechaProgramada}
                        onGuardar={this.guardarAuditoria.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dia')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dia')}/>
                </td>
                {/* Cliente*/}
                <td className={css.tdCliente}>
                    <p>{this.props.auditoria.local.cliente.nombreCorto}</p>
                </td>
                {/* CECO */}
                <td className={css.tdCeco}>
                    <p><small><b>{this.props.auditoria.local.numero}</b></small></p>
                </td>
                {/* Region */}
                <td className={css.tdRegion}>
                    <p>{this.props.auditoria.local.direccion.comuna.provincia.region.numero}</p>
                </td>
                {/* Comuna */}
                <td className={css.tdComuna}>
                    <p style={{margin:0}}><b><small>{this.props.auditoria.local.direccion.comuna.nombre}</small></b></p>
                </td>
                {/* Turno */}
                {/*
                <td className={css.tdTurno}>
                    <Select
                        ref={ref=>this.selectJornada=ref}
                        onSelect={this.guardarAuditoria.bind(this)}
                        opciones={[
                            {valor:'1', texto:'no definido'},
                            {valor:'2', texto:'día'},
                            {valor:'3', texto:'noche'},
                            {valor:'4', texto:'día y noche'}
                        ]}
                        seleccionada={this.props.auditoria.idJornada}
                    />
                </td>
                */}
                {/* Local */}
                <td className={css.tdTienda}>
                    <p><small><b>{this.props.auditoria.local.nombre}</b></small></p>
                </td>
                {/* Stock */}
                <td className={css.tdStock}>
                    <OverlayTrigger
                        placement="left"
                        delay={0}
                        overlay={<Tooltip id="yyy">{'Stock al '+(this.props.auditoria.local.fechaStock)}</Tooltip>}>
                        <p><small>{numeral(this.props.auditoria.local.stock).format('0,0')}</small></p>
                    </OverlayTrigger>
                </td>
                {/* Auditor */}
                <td className={css.tdLider}>
                    <Select
                            ref={ref=>this.selectAuditor=ref}
                            seleccionada={this.props.auditoria.idAuditor || ''}
                            onSelect={this.guardarAuditoria.bind(this)}
                            opciones={opcionesAuditores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={this.props.puedeModificar}/>
                </td>

                {/* Hora de Apertura del local */}
                <td className={css.tdAperturaCierre}>
                    <input type="time"
                           value={this.props.auditoria.local.horaApertura}
                           disabled/>
                </td>
                {/* hora de Cierre de local */}
                <td className={css.tdAperturaCierre}>
                    <input type="time"
                           value={this.props.auditoria.local.horaCierre}
                           disabled/>
                </td>
                
                {/* Hora Presentación Auditoria */}
                {/*
                <td className={css.tdHora}>
                    <p>{this.props.auditoria.HORAAUDITOR}</p>
                    <InputHora
                        ref={ref=>this.inputHoraAuditor=ref}
                        asignada={this.props.auditoria.horaPresentacionAuditor}
                        tooltipText={`Apertura a las ${this.props.auditoria.local.horaApertura}hrs`}
                        onGuardar={this.guardarAuditoria.bind(this)}
                        focusRowAnterior={()=>{}}
                        focusRowSiguiente={()=>{}}
                    />
                </td>
                */}
                {/* Dirección */}
                <td className={css.tdDireccion}>
                    <p>{this.props.auditoria.local.direccion.direccion}</p>
                </td>
                {/* Opciones */}
                <td className={css.tdOpciones}>
                    <button className="btn btn-xs btn-primary btn-block"
                            tabIndex="-1"
                            disabled
                    >Eliminar</button>
                </td>
            </tr>
        )
    }
}

RowAuditoriaSemanal.propTypes = {
    // Objetos
    puedeModificar: React.PropTypes.bool.isRequired,
    index: React.PropTypes.number.isRequired,
    auditoria: React.PropTypes.object.isRequired,
    auditores: React.PropTypes.array.isRequired,
    // lideres: React.PropTypes.array.isRequired,
    // captadores: React.PropTypes.array.isRequired,
    mostrarSeparador: React.PropTypes.bool.isRequired,
    // Metodos
    guardarAuditoria: React.PropTypes.func.isRequired,
    focusRow: React.PropTypes.func.isRequired
}
RowAuditoriaSemanal.defaultProps = {
    mostrarSeparador: false
}

export default RowAuditoriaSemanal