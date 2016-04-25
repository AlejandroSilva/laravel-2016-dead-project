import React from 'react'
import numeral from 'numeral'
import moment from 'moment'
moment.locale('es')

// Componentes
import Tooltip from 'react-bootstrap/lib/Tooltip'
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'
import InputFecha from '../shared/InputFecha.jsx'
import Select from '../shared/Select.jsx'

// Styles
import * as css from './TablaAuditoriaSemanal.css'

class RowAuditoriaSemanal extends React.Component{
    focusElemento(elemento){
        if(elemento==='dia'){
            this.inputFecha.focus()
        }else{
            console.error(`focusElemento(): Elemento "${elemento}" no encontrado`)
        }
    }

    guardarAuditoria() {
        if(!this.props.puedeModificar)
            return alert('no tiene permisos para realizar esta acción')

        let cambiosAuditoria = {}

        // la FECHA es valida, y ha cambiado?
        let estadoInputFecha = this.inputFecha.getEstado()
        if (estadoInputFecha.valid && estadoInputFecha.dirty) {
            cambiosAuditoria.fechaProgramada = estadoInputFecha.fecha
        } else if (estadoInputFecha.valid === false) {
            return console.log(`fecha ${estadoInputFecha.fecha} invalida`)
        }

        // el AUDITOR ha cambiado?
        let estadoSelectAuditor = this.selectAuditor.getEstado()
        if(estadoSelectAuditor.dirty)
            cambiosAuditoria.idAuditor = estadoSelectAuditor.seleccionUsuario

        // APROBADA ha cambiado?
        let estadoSelectAprobada = this.selectAprobada.getEstado()
        if(estadoSelectAprobada.dirty)
            cambiosAuditoria.aprovada = estadoSelectAprobada.seleccionUsuario

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

    eliminarAuditoria(){
        if(!this.props.puedeModificar)
            return alert('no tiene permisos para realizar esta acción')

        this.props.eliminarAuditoria(this.props.auditoria)
    }

    render(){
        const opcionesAuditores = this.props.auditores.map(usuario=>{
            return {valor: usuario.id, texto:`${usuario.nombre1} ${usuario.apellidoPaterno}`}
        })
        // SOLUCION TEMPORAL....
        let tooltipCECO = ''
        if(this.props.auditoria.inventarioEnELMismoMes!==null){
            let momentInventario = moment(this.props.auditoria.inventarioEnELMismoMes.fechaProgramada)
            let momentAuditoria = moment(this.props.auditoria.fechaProgramada)
            if(momentInventario.isValid()){
                if(momentAuditoria.isValid()){
                    let diasDiferencia = momentInventario.diff(momentAuditoria, 'days');
                    let textoDiferencia = diasDiferencia>0? `(${diasDiferencia} después)` : (diasDiferencia<0? `(${-diasDiferencia} antes)` : '')
                    tooltipCECO = `Inventario programado para el: \n ${momentInventario.format('DD-MM-YYYY')} ${textoDiferencia}`
                }else{
                    tooltipCECO = `Inventario programado para el: \n ${momentInventario.format('DD-MM-YYYY')}`
                }
            }else {
                let [anno, mes, dia] = this.props.auditoria.inventarioEnELMismoMes.fechaProgramada.split('-')
                tooltipCECO = `Inventario programado para el: 00-${mes}-${anno}`
            }
        }else{
            tooltipCECO = 'Sin inventario en el mismo mes'
        }
        return (
            <tr className={this.props.mostrarSeparador? css.trSeparador: ''}>
                {/* Correlativo */}
                <td className={css.tdCorrelativo}>
                    <p title={this.props.auditoria.idAuditoria}>{this.props.index+1}</p>
                </td>
                {/* Fecha */}
                <td className={css.tdFecha}>
                    <InputFecha
                        puedeModificar={this.props.puedeModificar}
                        ref={ref=>this.inputFecha=ref}
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
                    <OverlayTrigger
                        placement="right"
                        delay={0}
                        overlay={<Tooltip id="yyy">
                        {tooltipCECO}</Tooltip>}>
                        <p>
                            <small><b>{this.props.auditoria.local.numero}</b></small>
                        </p>
                    </OverlayTrigger>
                </td>
                {/* Region */}
                <td className={css.tdRegion}>
                    <p>{this.props.auditoria.local.direccion.comuna.provincia.region.numero}</p>
                </td>
                {/* Comuna */}
                <td className={css.tdComuna}>
                    <p style={{margin:0}}><b><small>{this.props.auditoria.local.direccion.comuna.nombre}</small></b></p>
                </td>
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
                <td className={css.tdAuditor}>
                    <Select
                            ref={ref=>this.selectAuditor=ref}
                            seleccionada={this.props.auditoria.idAuditor || ''}
                            onSelect={this.guardarAuditoria.bind(this)}
                            opciones={opcionesAuditores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={this.props.puedeModificar}/>
                </td>
                {/* (informado) Fecha Auditoria */}
                <td className={css.tdRealizadaAprobada}>
                    {this.props.auditoria.fechaAuditoria==='0000-00-00'?
                        <span className="label label-default">Pendiente</span> :
                        <span className="label label-primary">{this.props.auditoria.fechaAuditoria}</span>
                    }
                </td>
                {/* Revisado (antes llamado "Aprobada" */}
                <td className={css.tdRealizadaAprobada}>
                    <Select
                        ref={ref=>this.selectAprobada=ref}
                        seleccionada={this.props.auditoria.aprovada}
                        onSelect={this.guardarAuditoria.bind(this)}
                        opciones={[
                            {valor: '0', texto: 'Pendiente'},
                            {valor: '1', texto: 'Revisada'}
                        ]}
                        opcionNula={false}
                        opcionNulaSeleccionable={false}
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
                {/* Dirección */}
                <td className={css.tdDireccion}>
                    <p>{this.props.auditoria.local.direccion.direccion}</p>
                </td>
                {/* Opciones */}
                <td className={css.tdOpciones}>
                    {this.props.puedeModificar?
                        < button className="btn btn-xs btn-primary btn-block"
                            tabIndex="-1"
                            onClick={this.eliminarAuditoria.bind(this)}>
                            Eliminar
                        </button>
                        :
                        null
                    }
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
    mostrarSeparador: React.PropTypes.bool.isRequired,
    // Metodos
    guardarAuditoria: React.PropTypes.func.isRequired,
    eliminarAuditoria: React.PropTypes.func.isRequired,
    focusRow: React.PropTypes.func.isRequired
}
RowAuditoriaSemanal.defaultProps = {
    mostrarSeparador: false
}

export default RowAuditoriaSemanal