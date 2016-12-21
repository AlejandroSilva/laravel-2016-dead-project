// Librerias
import React from 'react'
let PropTypes = React.PropTypes
import { AutoSizer, Table, Column } from 'react-virtualized'
// Componentes
import {ModalTrigger, ModalEditarUsuario, ModalBloquearUsuario, ModalCambiarContrasena, ModalHistorial } from './Modales.jsx'
import { HeaderConBusqueda } from '../../components/shared/HeaderConBusqueda.jsx'
// Styles
import * as css from './tablaPersonal.css'
import classNames from 'classnames/bind'
let cx = classNames.bind(css)

export class TablaPersonal extends React.Component {
    render(){
        return (
            <div style={{height: '90%'}}>
                <AutoSizer>
                    {({ height, width }) =>
                        <Table
                            // general
                            height={height}
                            width={width}
                            // headers
                            headerHeight={30}
                            headerClassName={css.headerColumn}
                            // rows
                            rowCount={this.props.usuarios.length}
                            rowGetter={({index})=> this.props.usuarios[index]}
                            rowHeight={40}
                            rowClassName={({index})=> index<0? css.headerRow : ((index%2===0)? css.evenRow : css.oddRow)}
                        >
                            <Column
                                dataKey='#'
                                label={'#'}
                                disableSort={true}
                                cellRenderer={({rowIndex}) => rowIndex}
                                width={25}
                            />
                            <Column
                                dataKey='RUN'
                                label={'RUN'}
                                cellRenderer={({ rowData, dataKey }) => rowData[dataKey]}
                                headerRenderer={()=>
                                    <HeaderConBusqueda
                                        nombre="RUN"
                                        busqueda={this.props.busquedaRUN}
                                        realizarBusqueda={this.props.realizarBusquedaRUN}
                                    />
                                }
                                width={70}
                            />
                            <Column
                                dataKey='nombres'
                                label={'nombres'}
                                headerRenderer={()=>
                                    <HeaderConBusqueda
                                        nombre="Nombres"
                                        busqueda={this.props.busquedaNombre}
                                        realizarBusqueda={this.props.realizarBusquedaNombre}
                                    />
                                }
                                cellRenderer={({ rowData }) =>
                                    <div>
                                        <p style={{margin: 0}}>{rowData.nombre1}</p>
                                        <p style={{margin: 0}}>{rowData.nombre2}</p>
                                    </div>
                                }
                                width={90}
                            />
                            <Column
                                dataKey='apellidos'
                                label={'Apellidos'}
                                headerRenderer={()=>
                                    <HeaderConBusqueda
                                        nombre="Apellidos"
                                        busqueda={this.props.busquedaApellido}
                                        realizarBusqueda={this.props.realizarBusquedaApellido}
                                    />
                                }
                                cellRenderer={({ rowData }) =>
                                    <div>
                                        <p style={{margin: 0}}>{rowData.apellidoPaterno}</p>
                                        <p style={{margin: 0}}>{rowData.apellidoMaterno}</p>
                                    </div>
                                }
                                width={90}
                            />
                            <Column
                                dataKey='region'
                                label={'Región'}
                                cellRenderer={({ dataKey, rowData }) => rowData[dataKey]}
                                width={90}
                            />
                            <Column
                                dataKey='comuna'
                                label={'Comuna'}
                                cellRenderer={({ dataKey, rowData }) => rowData[dataKey]}
                                width={90}
                            />
                            <Column
                                dataKey='email'
                                label={'Email'}
                                cellRenderer={({ dataKey, rowData }) => rowData[dataKey]}
                                width={190}
                            />
                            <Column
                                dataKey='bloqueado'
                                label={'Estado'}
                                cellRenderer={({ dataKey, rowData }) => rowData[dataKey]? 'Bloqueado' : ''}
                                width={60}
                            />
                            <Column
                                dataKey='opciones'
                                label={'Opciones'}
                                cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => (
                                    <div>
                                        {/* Editar */}
                                        <BotonEditar
                                            usuario={rowData}
                                            actualizarUsuario={this.props.actualizarUsuario.bind(rowData.id)}
                                        />
                                        {/* Bloquear */}
                                        <BotonBloquear
                                            idUsuario={rowData.id}
                                            estaBloqueado={rowData.bloqueado}
                                            bloquearUsuario={this.props.bloquearUsuario}
                                        />
                                        {/* Cambiar contraseña */}
                                        <BotonCambiarContrasena
                                            idUsuario={rowData.id}
                                            cambiarContrasena={this.props.cambiarContrasena}
                                        />
                                        {/* Historial nomina */}
                                        <BotonVerHistorial
                                            idUsuario={rowData.id}
                                            verHistorial={this.props.verHistorial.bind(this, rowData.id)}
                                        />
                                    </div>
                                )}
                                width={280}
                            />
                        </Table>
                    }
                </AutoSizer>
            </div>
        )
        console.log(this.props.usuarios.length)
    }
}
TablaPersonal.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    usuarios: PropTypes.arrayOf(PropTypes.object).isRequired,
    // Metodos
    bloquearUsuario: PropTypes.func.isRequired,
    cambiarContrasena: PropTypes.func.isRequired,
}

const BotonEditar = ({usuario, actualizarUsuario})=>
    <ModalTrigger>
        {(isVisible, showModal, hideModal)=>
            <button className="btn btn-xs btn-primary" onClick={showModal}>
                Editar
                {isVisible && <ModalEditarUsuario
                    usuario={usuario}
                    actualizarUsuario={actualizarUsuario}
                    hideModal={hideModal}
                />}
            </button>
        }
    </ModalTrigger>

const BotonBloquear = ({idUsuario, estaBloqueado, bloquearUsuario})=>
    estaBloqueado==false?
        <ModalTrigger>
            {(isVisible, showModal, hideModal)=>
                <button className="btn btn-xs btn-primary" onClick={showModal}>
                    Bloquear
                    {isVisible && (
                        <ModalBloquearUsuario
                            hideModal={hideModal}
                            onBloquear={()=>{
                                hideModal()
                                bloquearUsuario(idUsuario)
                            }}
                        />
                    )}
                </button>
            }
        </ModalTrigger>
    :
        <button className={cx("btn btn-xs btn-default")} disabled>Bloquear</button>

const BotonCambiarContrasena = ({idUsuario, cambiarContrasena})=>
    <ModalTrigger>
        {(isVisible, showModal, hideModal)=>
            <button className="btn btn-xs btn-primary" onClick={showModal}>
                Cambiar contraseña
                {isVisible && (
                    <ModalCambiarContrasena
                        hideModal={hideModal}
                        onAceptar={(contrasena)=>{
                            hideModal()
                            cambiarContrasena(idUsuario, contrasena)
                        }}
                    />
                )}
            </button>
        }
    </ModalTrigger>

const BotonVerHistorial = ({idUsuario, verHistorial})=>
    <ModalTrigger>
        {(isVisible, showModal, hideModal)=>
            <button className="btn btn-xs btn-primary" onClick={showModal}>
                Historial
                {isVisible && (
                    <ModalHistorial
                        hideModal={hideModal}
                        verHistorial={verHistorial}
                    />
                )}
            </button>
        }
    </ModalTrigger>
