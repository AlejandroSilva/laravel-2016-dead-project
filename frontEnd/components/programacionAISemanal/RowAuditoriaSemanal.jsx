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
            this.inputDia.focus()
        }else if(elemento==='stock'){
            this.inputStock.focus()
        }
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

        // REALIZADA ha cambiado?
        let estadoSelectRealizada = this.selectRealizada.getEstado()
        if(estadoSelectRealizada.dirty)
            cambiosAuditoria.realizada = estadoSelectRealizada.seleccionUsuario

        // APROVADA ha cambiado?
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
        this.props.eliminarAuditoria(this.props.auditoria)
    }

    render(){
        const opcionesAuditores = this.props.auditores.map(usuario=>{
            return {valor: usuario.id, texto:`${usuario.nombre1} ${usuario.apellidoPaterno}`}
        })
        return (
            <tr className={this.props.mostrarSeparador? css.trSeparador: ''}>
                {/* Correlativo */}
                <td className={css.tdCorrelativo}>
                    {this.props.index+1}
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
                {/* Realizada */}
                <td className={css.tdRealizadaAprobada}>
                    <Select
                        ref={ref=>this.selectRealizada=ref}
                        seleccionada={this.props.auditoria.realizada}
                        onSelect={this.guardarAuditoria.bind(this)}
                        opciones={[
                            {valor: '0', texto: 'Pendiente'},
                            {valor: '1', texto: 'Realizada'}
                        ]}
                        opcionNula={false}
                        opcionNulaSeleccionable={false}
                        puedeModificar={this.props.puedeModificar}/>
                </td>
                {/* Aprobada */}
                <td className={css.tdRealizadaAprobada}>
                    <Select
                        ref={ref=>this.selectAprobada=ref}
                        seleccionada={this.props.auditoria.aprovada}
                        onSelect={this.guardarAuditoria.bind(this)}
                        opciones={[
                            {valor: '0', texto: 'Pendiente'},
                            {valor: '1', texto: 'Aprobada'}
                        ]}
                        opcionNula={false}
                        opcionNulaSeleccionable={false}
                        puedeModificar={this.props.puedeModificar && this.props.auditoria.realizada==="1"}/>
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
    // lideres: React.PropTypes.array.isRequired,
    // captadores: React.PropTypes.array.isRequired,
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