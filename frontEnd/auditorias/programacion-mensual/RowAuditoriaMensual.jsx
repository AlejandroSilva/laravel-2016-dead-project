import React from 'react'

// Componentes
import InputFecha from '../../components/shared/InputFecha.jsx'
import Select from '../../components/shared/Select.jsx'

// Styles
import * as css from './TablaMensualAI.css'

class RowAuditoriaMensual extends React.Component{
    focusElemento(elemento){
        if(elemento==='dia'){
            this.inputFecha.focus()
        } else{
            console.error(`focusElemento(): Elemento "${elemento}" no encontrado`)
        }
    }
    
    guardarAuditoria(){
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

        // almenos uno de los ementos debe estar "dirty" para guardar los cambios
        if(JSON.stringify(cambiosAuditoria)!=='{}'){
            console.log('cambiosAuditoria ', cambiosAuditoria)
            this.props.actualizarAuditoria(this.props.auditoria.aud_idAuditoria, cambiosAuditoria, this.props.auditoria.idDummy)
        }else{
            console.log('auditoria sin cambios, no se actualiza')
        }
    }
    quitarAuditoria(){
        this.props.quitarAuditoria(this.props.auditoria.idDummy)
    }
    eliminarAuditoria(){
        if(!this.props.puedeModificar)
            return alert('no tiene permisos para realizar esta acción')
        this.props.eliminarAuditoria(this.props.auditoria)
    }

    render(){
        const auditores = this.props.auditores.map(usuario=>{
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
                    <p><small>{this.props.auditoria.cliente_nombreCorto}</small></p>
                </td>
                {/* CECO */}
                <td className={css.tdCeco}>
                    <b>{this.props.auditoria.local_ceco}</b>
                </td>
                {/* Region*/}
                <td className={css.tdRegion}>
                    <p style={{margin:0}}>
                        <small>{ this.props.auditoria.local_region }</small>
                    </p>
                </td>
                {/* Comuna */}
                <td className={css.tdComuna}>
                    {this.props.auditoria.local_comuna}
                </td>
                {/* Local */}
                <td className={css.tdLocal}>
                    <p><small><b>{this.props.auditoria.local_nombre}</b></small></p>
                </td>

                {/* Stock */}
                 <td className={css.tdStock}>
                     <p className={css.textoConTooltip}>
                         {this.props.auditoria.local_stockF}
                         <span>{'Stock al '+(this.props.auditoria.local_fechaStock)}</span>
                     </p>
                 </td>

                {/* Auditor */}
                <td className={css.tdAuditor}>
                    <Select
                            ref={ref=>this.selectAuditor=ref}
                            seleccionada={ ''+this.props.auditoria.aud_idAuditor}
                            onSelect={this.guardarAuditoria.bind(this)}
                            opciones={auditores}
                            opcionNula={true}
                            opcionNulaSeleccionable={true}
                            puedeModificar={this.props.puedeModificar}
                    />
                </td>
                {/* Hora de Apertura del local */}
                <td className={css.tdAperturaCierre}>
                    <input type="time"
                           value={this.props.auditoria.local_horaApertura}
                           disabled/>
                </td>
                {/* hora de Cierre de local */}
                <td className={css.tdAperturaCierre}>
                    <input type="time"
                           value={this.props.auditoria.local_horaCierre}
                           disabled/>
                </td>

                {/* Direccion */}
                <td className={css.tdDireccion}>
                    {this.props.auditoria.local_direccion}
                </td>
                {/* Opciones    */}
                <td className={css.tdOpciones}>
                    {this.props.puedeModificar===true?
                        <button className="btn btn-xs btn-primary"
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

RowAuditoriaMensual.propTypes = {
    // Objetos
    puedeModificar: React.PropTypes.bool.isRequired,
    index: React.PropTypes.number.isRequired,
    auditoria: React.PropTypes.object.isRequired,
    auditores: React.PropTypes.array.isRequired,
    mostrarSeparador: React.PropTypes.bool.isRequired,
    // Metodos
    actualizarAuditoria: React.PropTypes.func.isRequired,
    quitarAuditoria: React.PropTypes.func.isRequired,
    eliminarAuditoria: React.PropTypes.func.isRequired,
    focusRow: React.PropTypes.func.isRequired
}
RowAuditoriaMensual.defaultProps = {
    mostrarSeparador: false
}

export default RowAuditoriaMensual