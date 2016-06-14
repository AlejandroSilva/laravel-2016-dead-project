// Librerias
import React from 'react'
// Componentes
import * as css from './TablaLocales.css'
import { InputTexto } from './shared/InputTexto.jsx'
import { Select } from './shared/Select.jsx'

export class RowLocal extends React.Component{
    focusElemento(nombreElemento){
        if(nombreElemento==='emailContacto'){
            this.refEmail.focus()
        // }else if(nombreElemento==='telefono1'){
        //     this.refTelefono1.focus()
        // }else if(nombreElemento==='telefono2'){
        //     this.refTelefono2.focus()
        }
    }

    actualizar() {
        console.log('focus lost, guardando')
        let cambios = {}

        // cliente
        // ceco
        // nombre
        let estadoNombre = this.refNombre.getEstado()
        if(estadoNombre.dirty && estadoNombre.valid)
            cambios.nombre = estadoNombre.valor
        // formato
        let estadoFormato = this.refFormato.getEstado()
        if(estadoFormato.dirty)
            cambios.idFormatoLocal = estadoFormato.valor

        // jornada
        let estadoJornada = this.refJornada.getEstado()
        if(estadoJornada.dirty)
            cambios.idJornadaSugerida = estadoJornada.valor

        // hr.apertura
        // hr.cierre
        // emailContacto
        let estadoEmail = this.refEmail.getEstado()
        if(estadoEmail.dirty && estadoEmail.valid)
            cambios.emailContacto = estadoEmail.valor

        // telefono 1
        let estadoTelefono1 = this.refTelefono1.getEstado()
        if(estadoTelefono1.dirty && estadoTelefono1.valid)
            cambios.telefono1 = estadoTelefono1.valor

        // telefono 2
        let estadoTelefono2 = this.refTelefono2.getEstado()
        if(estadoTelefono2.dirty && estadoTelefono2.valid)
            cambios.telefono2 = estadoTelefono2.valor

        // stock
        let estadoStock = this.refStock.getEstado()
        if(estadoStock.dirty && estadoStock.valid)
            cambios.stock = estadoStock.valor

        // fecha Stock
        // comuna
        let estadoComuna = this.refComuna.getEstado()
        if(estadoComuna.dirty)
            cambios.cutComuna = estadoComuna.valor

        // direccion
        let estadoDireccion = this.refDireccion.getEstado()
        if(estadoDireccion.dirty && estadoDireccion.valid)
            cambios.direccion = estadoDireccion.valor

        if(JSON.stringify(cambios)!=="{}"){
            this.props.actualizar(this.props.local.idLocal, cambios)
        }
    }

    render(){
        let keyCodesCallback = []
        keyCodesCallback[38] = ()=>{ this.props.focusRow(this.props.index-1,'emailContacto') }  // flecha arriba
        keyCodesCallback[13] = ()=>{ this.props.focusRow(this.props.index+1,'emailContacto') }  // enter
        keyCodesCallback[40] = ()=>{ this.props.focusRow(this.props.index+1,'emailContacto') }  // flecha abajo

        return (
            <tr key="">
                <td className={css.id}>
                    <p title={this.props.local.idLocal}>{this.props.index+1}</p>
                </td>
                <td className={css.cliente}>
                    {this.props.local.cliente.nombreCorto}
                </td>
                <td className={css.numero}>
                    {this.props.local.numero}</td>
                <td className={css.nombre}>
                    <InputTexto
                        ref={ref=>this.refNombre=ref}
                        defaultClass={css.nombre_input}
                        dirtyClass={css.inputDirty}
                        errorClass={css.inputError}
                        valor={this.props.local.nombre}
                        onFocusLost={this.actualizar.bind(this)}
                        onKeyPress={keyCodesCallback}
                    />
                </td>
                <td className={css.formatoLocal}>
                    <Select
                        ref={ref=>this.refFormato=ref}
                        defaultClass={css.formatoLocal_select}
                        dirtyClass={css.inputDirty}
                        valor={this.props.local.idFormatoLocal}
                        onSelectChange={this.actualizar.bind(this)}>
                            {this.props.opcionesFormatos}
                    </Select>
                </td>
                <td className={css.jornada}>
                    <Select
                        ref={ref=>this.refJornada=ref}
                        defaultClass={css.jornada_select}
                        dirtyClass={css.inputDirty}
                        valor={this.props.local.idJornadaSugerida}
                        onSelectChange={this.actualizar.bind(this)}>
                            {this.props.opcionesJornadas}
                    </Select>
                </td>
                <td className={css.horaApertura}>
                    {this.props.local.horaApertura}</td>
                <td className={css.horaCierre}>
                    {this.props.local.horaCierre}</td>
                <td className={css.emailContacto}>
                    <InputTexto
                        ref={ref=>this.refEmail=ref}
                        defaultClass={css.emailContacto_input}
                        dirtyClass={css.inputDirty}
                        errorClass={css.inputError}
                        valor={this.props.local.emailContacto}
                        onFocusLost={this.actualizar.bind(this)}
                        onKeyPress={keyCodesCallback}
                    />
                </td>
                <td className={css.telefono1}>
                    <InputTexto
                        ref={ref=>this.refTelefono1=ref}
                        defaultClass={css.telefono1_input}
                        dirtyClass={css.inputDirty}
                        errorClass={css.inputError}
                        valor={this.props.local.telefono1}
                        onFocusLost={this.actualizar.bind(this)}
                        onKeyPress={keyCodesCallback}
                    />
                </td>
                <td className={css.telefono2}>
                    <InputTexto
                        ref={ref=>this.refTelefono2=ref}
                        defaultClass={css.telefono2_input}
                        dirtyClass={css.inputDirty}
                        errorClass={css.inputError}
                        valor={this.props.local.telefono2}
                        onFocusLost={this.actualizar.bind(this)}
                        onKeyPress={keyCodesCallback}
                    />
                </td>
                <td className={css.stock}>
                    <InputTexto
                        ref={ref=>this.refStock=ref}
                        defaultClass={css.stock_input}
                        dirtyClass={css.inputDirty}
                        errorClass={css.inputError}
                        valor={this.props.local.stock}
                        onFocusLost={this.actualizar.bind(this)}
                        onKeyPress={keyCodesCallback}
                        validador={texto=> /^[0-9]\d*$/.test(texto) }
                    />
                </td>
                <td className={css.fechaStock}>
                    {this.props.local.fechaStock}</td>
                <td className={css.comuna}>
                    <Select
                        ref={ref=>this.refComuna=ref}
                        defaultClass={css.comuna_select}
                        dirtyClass={css.inputDirty}
                        valor={this.props.local.cutComuna}
                        onSelectChange={this.actualizar.bind(this)}>
                        {this.props.opcionesComunas}
                    </Select>
                </td>
                <td className={css.direccion}>
                    <InputTexto
                        ref={ref=>this.refDireccion=ref}
                        defaultClass={css.direccion_input}
                        dirtyClass={css.inputDirty}
                        errorClass={css.inputError}
                        valor={this.props.local.direccion}
                        onFocusLost={this.actualizar.bind(this)}
                        onKeyPress={keyCodesCallback}
                    />
                </td>
                <td>
                    <button className="btn btn-xs btn-danger">X</button>
                </td>
            </tr>
        )
    }
}

RowLocal.propTypes = {
    // Objetos
    index: React.PropTypes.number.isRequired,
    local: React.PropTypes.object.isRequired,
    // Objeto de los select
    opcionesFormatos: React.PropTypes.array.isRequired,
    opcionesJornadas: React.PropTypes.array.isRequired,
    opcionesComunas: React.PropTypes.array.isRequired,
    // Metodos
    focusRow: React.PropTypes.func.isRequired,
    actualizar: React.PropTypes.func.isRequired
}