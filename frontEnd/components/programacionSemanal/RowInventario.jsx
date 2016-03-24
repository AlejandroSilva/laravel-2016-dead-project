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
        const idJornada = this.props.inventario.idJornada
        const inventarioDia = idJornada==2 || idJornada==4
        const inventarioNoche = idJornada==3 || idJornada==4
        const opcionesLideres = this.props.lideres.map(usuario=>{
            return {valor: usuario.id, texto:`${usuario.nombre1} ${usuario.apellidoPaterno}`}
        })
        const opcionesSupervisores = []
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
                    <Select
                        ref={ref=>this.selectJornada=ref}
                        onSelect={this.guardarOCrear.bind(this)}
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
                        style={{display: inventarioDia? 'block' : 'none'}}
                        ref={ref=>this.inputDotacion=ref}
                        asignada={this.props.inventario.dotacionAsignada}
                        guardarOCrear={this.guardarOCrear.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacion')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacion')}/>

                    <InputDotacion
                        style={{display: inventarioNoche? 'block' : 'none'}}
                        ref={ref=>this.inputDotacion2=ref}
                        asignada={this.props.inventario.dotacionAsignada}
                        guardarOCrear={this.guardarOCrear.bind(this)}
                        focusRowAnterior={()=>this.props.focusRow(this.props.index-1, 'dotacion')}
                        focusRowSiguiente={()=>this.props.focusRow(this.props.index+1, 'dotacion')}/>
                </td>
                {/* Lider */}
                <td className={'a'}>
                    <Select style={{width: '120px', display: inventarioDia? 'block' : 'none'}}
                            seleccionada={ ''+this.props.lideres[0].id}         // Todo: arreglar esto
                            onSelect={this.guardarOCrear.bind(this)}
                            opciones={opcionesLideres}
                    />
                    <Select style={{width: '120px', display: inventarioNoche? 'block' : 'none'}}
                            seleccionada={ ''+this.props.lideres[0].id}         // ToDo: arreglar esto
                            onSelect={this.guardarOCrear.bind(this)}
                            opciones={opcionesLideres}
                    />
                </td>
                {/* Supervisor */}
                <td className='a'>
                    <Select style={{width: '120px', display: inventarioDia? 'block' : 'none'}}
                            seleccionada={''}                                // ToDo: arreglar esto (agregar supervisores)
                            onSelect={this.guardarOCrear.bind(this)}
                            opciones={opcionesSupervisores}
                    />
                    <Select style={{width: '120px', display: inventarioNoche? 'block' : 'none'}}
                            seleccionada={''}                                // ToDo: arreglar esto (agregar supervisores)
                            onSelect={this.guardarOCrear.bind(this)}
                            opciones={opcionesSupervisores}
                    />
                </td>
                {/* Captador 1 */}
                <td className={'a'}>
                    <Select style={{width: '120px', display: inventarioDia? 'block' : 'none'}}
                            seleccionada={ ''+this.props.captadores[0].id}     // ToDo: arreglar esto
                            onSelect={this.guardarOCrear.bind(this)}
                            opciones={opcionesCaptadores}
                    />
                    <Select style={{width: '120px', display: inventarioNoche? 'block' : 'none'}}
                            seleccionada={ ''+this.props.captadores[0].id}     // ToDo: arreglar esto
                            onSelect={this.guardarOCrear.bind(this)}
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
                            seleccionada={ ''+this.props.captadores[0].id}     // ToDo: arreglar esto
                            onSelect={this.guardarOCrear.bind(this)}
                            opciones={opcionesCaptadores}
                    />
                    <Select style={{width: '120px', display: inventarioNoche? 'block' : 'none'}}
                            seleccionada={ ''+this.props.captadores[0].id}     // ToDo: arreglar esto
                            onSelect={this.guardarOCrear.bind(this)}
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
    focusRow: React.PropTypes.func.isRequired
}

export default RowInventario