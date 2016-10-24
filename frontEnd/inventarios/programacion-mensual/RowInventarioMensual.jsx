import React from 'react'

// Componentes
import InputFecha from '../../components/shared/InputFecha.jsx'
import InputStock from '../../components/shared/InputStock.jsx'

// Styles
import * as css from './TablaMensual.css'

class RowInventarioMensual extends React.Component{
    focusElemento(elemento){
        if(elemento==='dia'){
            this.inputFecha.focus()
        }else if(elemento==='stock'){
            this.inputStock.focus()
        }
    }
    guardarInventario(){
        if(!this.props.puedeModificar)
            return alert("no tiene permitido modificar el inventario")

        let cambiosInventario = {}

        // el DIA es valido, y ha cambiado?
        let estadoInputFecha = this.inputFecha.getEstado()
        if (estadoInputFecha.valid && estadoInputFecha.dirty) {
            cambiosInventario.fechaProgramada = estadoInputFecha.fecha
        } else if (estadoInputFecha.valid === false) {
            return console.log(`fecha ${estadoInputFecha.fecha} invalida`)
        }

        // la DOTACION es valida y ha cambiado?
        // let estadoInputDotacionTotal = this.inputDotacionTotal.getEstado()
        // if (estadoInputDotacionTotal.valid && estadoInputDotacionTotal.dirty) {
        //     cambiosInventario.dotacionAsignadaTotal = estadoInputDotacionTotal.valor
        // } else if (estadoInputDotacionTotal.valid === false) {
        //     return console.log(`dotacion total: ${estadoInputDotacionTotal.valor} invalida`)
        // }

        // almenos uno de los ementos debe estar "dirty" para guardar los cambios
        if(JSON.stringify(cambiosInventario)!=="{}"){
            console.log('cambios en el inventario ',cambiosInventario)
            this.props.actualizarInventario(this.props.inventario.inv_idInventario, cambiosInventario, this.props.inventario.idDummy)
        }else{
            console.log('inventario sin cambios, no se actualiza')
        }
    }
    quitarInventario(){
        this.props.quitarInventario(this.props.inventario.idDummy)
    }
    eliminarInventario(){
        if(!this.props.puedeModificar)
            return alert("no tiene permitido eliminar el inventario")
        this.props.eliminarInventario(this.props.inventario)
    }

    render(){
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
                        diaSemana={this.props.inventario.inv_fechaProgramadaDOW}
                        fechaConProblemas={this.props.inventario.local_topeFechaConAuditoria}
                        fecha={this.props.inventario.inv_fechaProgramada}
                        onGuardar={this.guardarInventario.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dia')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dia')}/>
                </td>
                {/* Cliente*/}
                <td className={css.tdCliente}>
                    <p><small>{this.props.inventario.cliente_nombreCorto}</small></p>
                </td>
                {/* CECO */}
                <td className={css.tdCeco}>
                    <p className={css.textoConTooltip}>
                        <b>{this.props.inventario.local_ceco}</b>
                        <span>Tipo de local: {this.props.inventario.local_formatoLocal}</span>
                    </p>
                </td>
                {/* Local */}
                <td className={css.tdLocal}>
                    <p><small><b>{this.props.inventario.local_nombre}</b></small></p>
                </td>
                {/* Region*/}
                <td className={css.tdRegion}>
                    <p style={{margin:0}}><small>{ this.props.inventario.local_region }</small></p>
                </td>
                {/* Comuna */}
                <td className={css.tdComuna}>
                    <p className={css.textoConTooltip}>
                        <b><small>{this.props.inventario.local_comuna}</small></b>
                        <span>Dirección: {this.props.inventario.local_direccion}</span>
                    </p>
                </td>
                {/* Stock */}
                <td className={css.tdStock}>
                    <InputStock
                        ref={ref=>this.inputStock=ref}
                        asignada={''+this.props.inventario.inv_stockTeorico}
                        tooltipText={'Stock al ' +(this.props.inventario.inv_fechaStock)}
                        onGuardar={this.guardarInventario.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'stock')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'stock')}
                        puedeModificar={false}
                    />
                </td>
                {/* Opciones    */}
                <td className={css.tdOpciones}>
                    {
                        this.props.inventario.inv_idInventario ? (
                            // si esta creado, puede eliminar el inventario
                            this.props.puedeModificar===true?
                                <button className="btn btn-xs btn-primary"
                                        tabIndex="-1"
                                        onClick={this.eliminarInventario.bind(this)}>
                                    Eliminar inventario
                                </button>
                                :
                                null
                        )
                        :
                            // si no esta creado, solo puede quitarlo de la lista
                            <button className="btn btn-xs btn-danger"
                                      tabIndex="-1"
                                      onClick={this.quitarInventario.bind(this)}>
                                X
                            </button>
                     }
                </td>
            </tr>
        )
    }
}

RowInventarioMensual.propTypes = {
    // Objetos
    index: React.PropTypes.number.isRequired,
    inventario: React.PropTypes.object.isRequired,
    mostrarSeparador: React.PropTypes.bool.isRequired,
    puedeModificar: React.PropTypes.bool.isRequired,
    // Metodos
    actualizarInventario: React.PropTypes.func.isRequired,
    quitarInventario: React.PropTypes.func.isRequired,
    eliminarInventario: React.PropTypes.func.isRequired,
    focusRow: React.PropTypes.func.isRequired
}
RowInventarioMensual.defaultProps = {
    mostrarSeparador: false
}

export default RowInventarioMensual