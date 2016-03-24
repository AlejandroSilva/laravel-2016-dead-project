import React from 'react'
import numeral from 'numeral'

// Componentes
import Tooltip from 'react-bootstrap/lib/Tooltip'
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'
import InputFecha from './InputFecha.jsx'
import InputDotacion from './InputDotacion.jsx'
import SelectLider from './SelectLider.jsx'
import SelectCaptador from './SelectCaptador.jsx'
import SelectJornada from './SelectJornada.jsx'
import InputDotacionCaptador from './InputDotacionCaptador.jsx'

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

    guardarOCrear(){
        // validar DIA
        let estadoInputDia = this.inputDia.getEstado()
        if(estadoInputDia.valid==false)
            return console.log(`fecha ${estadoInputDia.dia} invalida`)
        // validar DOTACION
        let estadoInputDotacion = this.inputDotacion.getEstado()
        if(estadoInputDotacion.valid==false)
            return console.log(`dotacion ${estadoInputDotacion.dia} invalida`)

        // "validar" Jornada
        let estadoSelectJornada = this.selectJornada.getEstado()

        // almenos uno de los ementos debe estar "dirty" para guardar los cambios
        if(estadoInputDia.dirty===true || estadoInputDotacion.dirty===true || estadoSelectJornada.dirty===true){
            let [anno, mes, _dia] = this.props.inventario.fechaProgramada.split('-')
            let dia = estadoInputDia.dia
            let dotacion = estadoInputDotacion.dotacion
            console.log("guardando inventario")

            this.props.guardarInventario({
                idInventario: this.props.inventario.idInventario,
                fechaProgramada: `${anno}-${mes}-${dia}`,
                idJornada: estadoSelectJornada.seleccionUsuario,
    //            horaLlegada: this.props.inventario.horaLlegada,
    //            stockTeorico: this.props.inventario.local.stock,
               dotacionAsignada: dotacion
            })
        }else{
            console.log('no han cambiado')
        }
    }

    focusElemento(elemento){
        if(elemento==='dia'){
            this.inputDia.focus()
        }else if(elemento==='dotacion'){
            this.inputDotacion.focus()
        }
    }

    render(){
        //console.log("render: prop.local.dotSug, state.inputDot", this.props.inventario.local.dotacionSugerida, this.state.inputDotacion)
        let idJornada = this.props.inventario.idJornada
        return (
            <tr>
                {/* Fecha */}
                <td className={"asd"}>
                    <InputFecha
                        ref={ref=>this.inputDia=ref}
                        fecha={this.props.inventario.fechaProgramada}
                        guardarOCrear={this.guardarOCrear.bind(this)}
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
                    <SelectJornada
                        ref={ref=>this.selectJornada=ref}
                        onSelect={this.guardarOCrear.bind(this)}
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
                {/* Produccion */}
                <td className={'a'}>
                    <p><small>{numeral(this.props.inventario.local.formato_local.produccionSugerida).format('0,0')}</small></p>
                </td>
                {/* Dotacion */}
                <td className={'a'}>
                    <InputDotacion
                        style={{width: '100%', display: (idJornada==2 || idJornada==4)? 'block' : 'none'}}
                        ref={ref=>this.inputDotacion=ref}
                        asignada={this.props.inventario.dotacionAsignada}
                        guardarOCrear={this.guardarOCrear.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacion')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacion')}/>

                    <InputDotacion
                        style={{width: '100%', display: (idJornada==3 || idJornada==4)? 'block' : 'none'}}
                        ref={ref=>this.inputDotacion2=ref}
                        asignada={this.props.inventario.dotacionAsignada}
                        guardarOCrear={this.guardarOCrear.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacion')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacion')}/>
                </td>
                {/* Lider */}
                <td className={'a'}>
                    <SelectLider
                        style={{width: '120px', display: (idJornada==2 || idJornada==4)? 'block' : 'none'}}
                        lideres={this.props.lideres}/>
                    <SelectLider
                        style={{width: '120px', display: (idJornada==3 || idJornada==4)? 'block' : 'none'}}
                        lideres={this.props.lideres}/>
                </td>
                {/* Captador 1 */}
                <td className={'a'}>
                    <SelectCaptador
                        style={{width: '120px', display: (idJornada==2 || idJornada==4)? 'block' : 'none'}}
                        captadores={this.props.captadores}
                    />
                    <SelectCaptador
                        style={{width: '120px', display: (idJornada==3 || idJornada==4)? 'block' : 'none'}}
                        captadores={this.props.captadores}
                    />
                </td>
                {/* DotacionCaptador 1 */}
                <td className={'a'}>
                    <InputDotacionCaptador
                        style={{width: '100%', display: (idJornada==2 || idJornada==4)? 'block' : 'none'}}
                        asignada="3"/>
                    <InputDotacionCaptador
                        style={{width: '100%', display: (idJornada==3 || idJornada==4)? 'block' : 'none'}}
                        asignada="3"/>
                </td>
                {/* Captador 2 */}
                <td className={'a'}>
                    <SelectCaptador
                        style={{width: '120px', display: (idJornada==2 || idJornada==4)? 'block' : 'none'}}
                        captadores={this.props.captadores}
                    />
                    <SelectCaptador
                        style={{width: '120px', display: (idJornada==3 || idJornada==4)? 'block' : 'none'}}
                        captadores={this.props.captadores}
                    />
                </td>
                {/* DotacionCaptador 2 */}
                <td className={'a'}>
                    <InputDotacionCaptador
                        style={{width: '100%', display: (idJornada==2 || idJornada==4)? 'block' : 'none'}}
                        asignada="3"/>
                    <InputDotacionCaptador
                        style={{width: '100%', display: (idJornada==3 || idJornada==4)? 'block' : 'none'}}
                        asignada="3"/>
                </td>
                {/* Hora llegada */}
                <td className={'a'}>
                    <p style={{width: '100%', display: (idJornada==2 || idJornada==4)? 'block' : 'none'}}>
                        <small>{this.props.inventario.horaLlegada}</small>
                    </p>
                    <p style={{width: '100%', display: (idJornada==3 || idJornada==4)? 'block' : 'none'}}>
                        <small>{this.props.inventario.horaLlegada}</small>
                    </p>
                </td>
                {/* Direccion */}
                <td className={'a'}>
                    <p><small>{this.props.inventario.local.direccion.direccion}</small></p>
                </td>
                {/* Opciones    */}
                <td className={''}>
                    <button className="btn btn-xs btn-primary btn-block" tabIndex="-1">Editar local</button>
                    <button className="btn btn-xs btn-primary btn-block" tabIndex="-1">Editar Inventario</button>
                    <button className="btn btn-xs btn-primary btn-block" tabIndex="-1">Editar Nomina</button>
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
    focusRow: React.PropTypes.func.isRequired
}

export default RowInventario