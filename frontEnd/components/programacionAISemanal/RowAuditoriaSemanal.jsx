import React from 'react'

// Componentes
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
        let cambiosAuditoria = {}

        if(this.props.puedeModificar){
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
        }

        if(this.props.puedeModificar || this.props.puedeRevisar){
            // APROBADA ha cambiado?
            let estadoSelectAprobada = this.selectAprobada.getEstado()
            if(estadoSelectAprobada.dirty)
                cambiosAuditoria.aprovada = estadoSelectAprobada.seleccionUsuario
        }

        // almenos uno de los ementos debe estar "dirty" para guardar los cambios
        if(JSON.stringify(cambiosAuditoria)!=="{}"){
            console.log(cambiosAuditoria)
            this.props.guardarAuditoria(this.props.auditoria.aud_idAuditoria, cambiosAuditoria)
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
        return (
            <tr className={this.props.mostrarSeparador? css.trSeparador: ''}>
                {/* Correlativo */}
                <td className={css.tdCorrelativo}>
                    <p title={this.props.auditoria.aud_idAuditoria}>{this.props.index+1}</p>
                </td>
                {/* Fecha */}
                <td className={css.tdFecha}>
                    <InputFecha
                        puedeModificar={this.props.puedeModificar}
                        ref={ref=>this.inputFecha=ref}
                        diaSemana={this.props.auditoria.aud_fechaProgramadaDOW}
                        fechaConProblemas={this.props.auditoria.local_topeFechaConInventario}
                        fecha={this.props.auditoria.aud_fechaProgramada}
                        onGuardar={this.guardarAuditoria.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dia')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dia')}/>
                </td>
                {/* Cliente*/}
                <td className={css.tdCliente}>
                    <p>{this.props.auditoria.cliente_nombreCorto}</p>
                </td>
                {/* CECO */}
                <td className={css.tdCeco}>
                    <small><b>{this.props.auditoria.local_ceco}</b></small>
                </td>
                {/* Region */}
                <td className={css.tdRegion}>
                    <p>{this.props.auditoria.local_region}</p>
                </td>
                {/* Comuna */}
                <td className={css.tdComuna}>
                    <p style={{margin:0}}><b><small>{this.props.auditoria.local_comuna}</small></b></p>
                </td>
                {/* Local */}
                <td className={css.tdTienda}>
                    <p><small><b>{this.props.auditoria.local_nombre}</b></small></p>
                </td>
                {/* Stock */}
                <td className={css.tdStock}>
                    <p className={css.textoConTooltip}>
                        {this.props.auditoria.local_stockF}
                        <span>Stock al {this.props.auditoria.local_fechaStock}</span>
                    </p>
                </td>
                {/* Auditor */}
                <td className={css.tdAuditor}>
                    <Select
                            ref={ref=>this.selectAuditor=ref}
                            seleccionada={this.props.auditoria.aud_idAuditor? ''+this.props.auditoria.aud_idAuditor : ''}   // debe ser string
                            onSelect={this.guardarAuditoria.bind(this)}
                            opciones={opcionesAuditores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={this.props.puedeModificar}/>
                </td>
                {/* (informado) Fecha Auditoria */}
                <td className={css.tdFechaInformada}>
                    {this.props.auditoria.aud_fechaAuditoria==='0000-00-00'?
                        <span className="label label-default">Pendiente</span> :
                        <span className="label label-primary">{this.props.auditoria.aud_fechaAuditoria}</span>
                    }
                </td>
                {/* Revisado (antes llamado "Aprobada" */}
                <td className={css.tdRevisada}>
                    <Select
                        ref={ref=>this.selectAprobada=ref}
                        seleccionada={''+this.props.auditoria.aud_aprobada} // debe ser string
                        onSelect={this.guardarAuditoria.bind(this)}
                        opciones={[
                            {valor: '0', texto: 'Pendiente'},
                            {valor: '1', texto: 'Revisada'}
                        ]}
                        opcionNula={false}
                        opcionNulaSeleccionable={false}
                        puedeModificar={this.props.puedeRevisar}/>
                </td>
                {/* Hora de Apertura del local */}
                <td className={css.tdAperturaCierre}>
                    <input type="time" value={this.props.auditoria.local_horaApertura} disabled/>
                </td>
                {/* hora de Cierre de local */}
                <td className={css.tdAperturaCierre}>
                    <input type="time" value={this.props.auditoria.local_horaCierre} disabled/>
                </td>
                {/* Dirección */}
                <td className={css.tdDireccion}>
                    {this.props.auditoria.local_direccion}
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
    puedeRevisar: React.PropTypes.bool.isRequired,
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