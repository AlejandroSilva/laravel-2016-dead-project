// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { InputTexto } from '../modulos-logistica/shared/InputTexto.jsx'
import { InputNumber } from '../modulos-logistica/shared/InputNumber.jsx'
// Libs
import api from '../apiClient/v1'
// Styles
import classNames from 'classnames/bind'
import * as css from './PanelDatosActa.css'
let cx = classNames.bind(css)

export class PanelDatosActa extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            acta: {},
            editandoActa: false
        }
    }
    getChildContext(){
        return {
            iniciarEdicion: ()=>
                this.setState({editandoActa: true}),
            finalizarEdicion: ()=>
                this.setState({editandoActa: false}),
            publicar: ()=> {
                api.inventario.acta.publicar(this.props.idInventario)
                    .then(actaActualizada=>{
                        console.log(actaActualizada)
                        this.setState({acta: actaActualizada})
                    })
            },
            despublicar: ()=> {
                api.inventario.acta.despublicar(this.props.idInventario)
                    .then(actaActualizada=>{
                        console.log(actaActualizada)
                        this.setState({acta: actaActualizada})
                    })
            },
            actualizarActa: (campo, estado, tres)=>{
                console.log('editando acta ', campo, estado)
                if(estado.dirty && estado.valid){
                    let datos = {}
                    datos[campo] = estado.valor
                    api.inventario.acta.actualizar(this.props.idInventario, datos)
                        .then(actaActualizada=>{
                            console.log(actaActualizada)
                            this.setState({acta: actaActualizada})
                        })
                        // catch
                }
            }
        }
    }
    componentWillMount(){
        api.inventario.acta.get(this.props.idInventario)
            .then(acta=>{
                console.log('acta: ', acta)
                this.setState({acta})
            })
        // catch
    }
    render(){
        return (
            <div className='panel panel-default'>
                <PanelHeading
                    publicada={this.state.acta.publicada}
                    publicadaPor={this.state.acta.publicadaPor}
                    fechaPublicacion={this.state.acta.fechaPublicacion}
                    editandoActa={this.state.editandoActa}
                />
                <PanelBody
                    acta={this.state.acta}
                    editar={this.state.editandoActa}
                />
            </div>
        )
    }
}
PanelDatosActa.propTypes = {
    idInventario: PropTypes.number.isRequired,
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}
PanelDatosActa.childContextTypes = {
    iniciarEdicion: React.PropTypes.func,
    finalizarEdicion: React.PropTypes.func,
    publicar: React.PropTypes.func,
    despublicar: React.PropTypes.func,
    actualizarActa: React.PropTypes.func
};

const PanelHeading = ({publicada, publicadaPor, fechaPublicacion, editandoActa}, context)=>{
        const puedeEditar = true
        return <div className={cx("panel-heading", 'panel-heading')}>
            <span className="glyphicon glyphicon-stats"/> &nbsp;
            {publicada?
                `Acta Inventario publicada por ${publicadaPor} el ${fechaPublicacion}`
                :
                `Acta Inventario pendiente de publicación`
            }
            <div className="pull-right">
                {editandoActa?
                    <button className={cx('btn btn-primary btn-xs', 'opcion-edicion')}
                            disabled={puedeEditar == false} onClick={context.finalizarEdicion}>
                        Finalizar Edición
                    </button>
                    :
                    <button className={cx('btn btn-default btn-xs', 'opcion-edicion')}
                            disabled={puedeEditar == false} onClick={context.iniciarEdicion}>
                        Editar Acta
                    </button>
                }
                {publicada?
                    <button className={cx("btn btn-default btn-xs", 'opcion-publicacion')}
                            disabled={puedeEditar == false} onClick={context.despublicar}>
                        Despublicar
                    </button>
                    :
                    <button className={cx("btn btn-primary btn-xs", 'opcion-publicacion')}
                            disabled={puedeEditar == false} onClick={context.publicar}>
                        Publicar
                    </button>
                }
            </div>
        </div>
}
PanelHeading.contextTypes = {
    iniciarEdicion: React.PropTypes.func,
    finalizarEdicion: React.PropTypes.func,
    publicar: React.PropTypes.func,
    despublicar: React.PropTypes.func
};


const PanelBody = ({acta, editar}, ctx)=>
    <div className="panel-body">
        {acta.inv_idInventario?
            <div>
                <div className="col-md-3"> {/* columna 1/4 */}
                    <div className={cx('tabla-datos')}>
                        <div className={cx('tr-header')} >
                            <p>HITOS IMPORTANTES</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'fecha_toma'">Fecha Inventario</p>
                            <Texto editarCampo={editar} valor={acta.fechaTomaInventario}
                                   actualizarActa={ctx.actualizarActa.bind(this, 'fechaTomaInventario')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'nombre_empresa'">Cliente</p>
                            <Texto editarCampo={editar} valor={acta.cliente}
                                   actualizarActa={ctx.actualizarActa.bind(this, 'cliente')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'cod_local'">CECO</p>
                            <Numero editarCampo={editar} valor={acta.ceco}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'ceco')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'usuario'">Supervisor</p>
                            <Texto editarCampo={editar} valor={acta.supervisor}
                                   actualizarActa={ctx.actualizarActa.bind(this, 'supervisor')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'administrador'">QF</p>
                            <Texto editarCampo={editar} valor={acta.qf}
                                   actualizarActa={ctx.actualizarActa.bind(this, 'qf')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'captura_uno'">Inicio Conteo</p>
                            <Texto editarCampo={editar} valor={acta.inicioConteo}
                                   actualizarActa={ctx.actualizarActa.bind(this, 'inicioConteo')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'fin_captura'">Fin Conteo</p>
                            <Texto editarCampo={editar} valor={acta.finConteo}
                                   actualizarActa={ctx.actualizarActa.bind(this, 'finConteo')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'fecha_revision_grilla'">Fin Proceso</p>
                            <Texto editarCampo={editar} valor={acta.finProceso}
                                   actualizarActa={ctx.actualizarActa.bind(this, 'finProceso')}/>
                        </div>

                        <div className={cx('tr-header')} >
                            <p>DURACIÓN</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="Inicio Conteo a Fin Conteo">Duración Conteo</p>
                            <p>{ acta.duracionConteo }</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="Fin Conteo a Fin Proceso">Duración Revisión</p>
                            <p>{ acta.duracionRevision }</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="Inicio Conteo a Fin Proceso">Duración Total Proceso</p>
                            <p>{ acta.duracionTotalProceso }</p>
                        </div>
                    </div>
                </div>
                <div className="col-md-3"> {/* columna 2/4 */}
                    <div className={cx('tabla-datos')}>
                        <div className={cx('tr-header')} >
                            <p>DOTACIONES</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'presupuesto'">Dotación Presupuestada</p>
                            <Numero editarCampo={editar} valor={acta.dotacionPresupuestada}
                                actualizarActa={ctx.actualizarActa.bind(this, 'dotacionPresupuestada')}
                            />
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'efectiva'">Dotación Efectiva</p>
                            <Numero editarCampo={editar} valor={acta.dotacionEfectiva}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'dotacionEfectiva')}/>
                        </div>

                        <div className={cx('tr-header')} >
                            <p>UNIDADES</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'unidades'">Unidades Inventariadas</p>
                            <Numero editarCampo={editar} valor={acta.unidadesInventariadas}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'unidadesInventariadas')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'teorico_unidades'">Unidades Teoricas</p>
                            <Numero editarCampo={editar} valor={acta.unidadesTeoricas}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'unidadesTeoricas')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'aju2'">Unidades Diff Neto</p>
                            <Numero editarCampo={editar} valor={acta.unidadesDiferenciaNeto}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'unidadesDiferenciaNeto')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'diferencia_unid_absoluta'">Unidades Diff Abs.</p>
                            <Numero editarCampo={editar} valor={acta.unidadesDiferenciaAbsoluta}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'unidadesDiferenciaAbsoluta')}/>
                        </div>

                        <div className={cx('tr-header')} >
                            <p>EVALUACIONES</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'nota1'">Nota Presentación</p>
                            <Numero editarCampo={editar} valor={acta.notaPresentacion}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'notaPresentacion')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'nota2'">Nota Supervisor</p>
                            <Numero editarCampo={editar} valor={acta.notaSupervisor}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'notaSupervisor')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'nota2'">Nota Conteo</p>
                            <Numero editarCampo={editar} valor={acta.notaConteo}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'notaConteo')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="promedio de nota1, nota2 y nota3">Nota Promedio</p>
                            <p>{ acta.notaPromedio }</p>
                        </div>

                        <div className={cx('tr-header')} >
                            <p>CONSOLIDADO AUDITORIA</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'aud1'">Consolidado Patentes</p>
                            <Numero editarCampo={editar} valor={acta.consolidadoPatentes}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'consolidadoPatentes')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'aud3'">Consolidado Unidades</p>
                            <Numero editarCampo={editar} valor={acta.consolidadoUnidades}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'consolidadoUnidades')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'aud2'">Consolidado Ítems</p>
                            <Numero editarCampo={editar} valor={acta.consolidadoItems}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'consolidadoItems')}/>
                        </div>
                    </div>
                </div>
                <div className="col-md-3"> {/* columna 3/4 */}
                    <div className={cx('tabla-datos')}>
                        <div className={cx('tr-header')} >
                            <p>AUDITORIA QF</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'ptt_rev_qf'">Auditoria QF Patentes</p>
                            <Numero editarCampo={editar} valor={acta.auditoriaQFPatentes}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'auditoriaQFPatentes')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: '????'">Auditoria QF Unidades</p>
                            <Numero editarCampo={false} valor={acta.auditoriaQFUnidades}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'auditoriaQFUnidades')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'items_rev_qf'">Auditoria QF Ítems</p>
                            <Numero editarCampo={editar} valor={acta.auditoriaQFItems}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'auditoriaQFItems')}/>
                        </div>

                        <div className={cx('tr-header')} >
                            <p>AUDITORIA APOYO 1</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'ptt_rev_apoyo1'">Auditoria Apoyo1 Patentes</p>
                            <Numero editarCampo={editar} valor={acta.auditoriaApoyo1Patentes}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'auditoriaApoyo1Patentes')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: '????'">Auditoria Apoyo1 Unidades</p>
                            <Numero editarCampo={false} valor={acta.auditoriaApoyo1Unidades}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'auditoriaApoyo1Unidades')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'items_rev_apoyo1'">Auditoria Apoyo1 Ítems</p>
                            <Numero editarCampo={editar} valor={acta.auditoriaApoyo1Items}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'auditoriaApoyo1Items')}/>
                        </div>

                        <div className={cx('tr-header')} >
                            <p>AUDITORIA APOYO 2</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'ptt_rev_apoyo2'">Auditoria Apoyo2 Patentes</p>
                            <Numero editarCampo={editar} valor={acta.auditoriaApoyo2Patentes}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'auditoriaApoyo2Patentes')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: '????'">Auditoria Apoyo2 Unidades</p>
                            <Numero editarCampo={false} valor={acta.auditoriaApoyo2Unidades}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'auditoriaApoyo2Unidades')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'items_rev_apoyo2'">Auditoria Apoyo2 Ítems</p>
                            <Numero editarCampo={editar} valor={acta.auditoriaApoyo2Items}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'auditoriaApoyo2Items')}/>
                        </div>

                        <div className={cx('tr-header')} >
                            <p>AUDITORIA SUPERVISOR FCV</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'ptt_rev_supervisor_fcv'">Auditoria Superv. FCV Patentes</p>
                            <Numero editarCampo={editar} valor={acta.auditoriaSupervisorPatentes}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'auditoriaSupervisorPatentes')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: '????'">Auditoria Superv. FCV Unidades</p>
                            <Numero editarCampo={false} valor={acta.auditoriaSupervisorUnidades}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'auditoriaSupervisorUnidades')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: '???'">Auditoria Superv. FCV Ítems</p>
                            <Numero editarCampo={false} valor={acta.auditoriaSupervisorItems}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'auditoriaSupervisorItems')}/>
                        </div>
                    </div>
                </div>
                <div className="col-md-3"> {/* columna 4/4 */}
                    <div className={cx('tabla-datos')}>
                        <div className={cx('tr-header')} >
                            <p>CORRECCIÓN AUDITORÍA</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'aud4'">Correccion Patentes</p>
                            <Numero editarCampo={editar} valor={acta.correccionPatentes}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'correccionPatentes')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'aud5'">Correccion Ítems</p>
                            <Numero editarCampo={editar} valor={acta.correccionItems}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'correccionItems')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'aud6'">Correccion Unidades Neto</p>
                            <Numero editarCampo={editar} valor={acta.correccionUnidadesNeto}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'correccionUnidadesNeto')}/>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: 'unid_absoluto_corregido_auditoria'">Correccion Unidades Abolutas</p>
                            <Numero editarCampo={editar} valor={acta.correccionUnidadesAbsolutas}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'correccionUnidadesAbsolutas')}/>
                        </div>

                        <div className={cx('tr-header')} >
                            <p>PORCENTAJE ERROR AUDITORIA</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: '(formula....)'">Porcentaje error SEI</p>
                            <p>{ acta.porcentajeErrorSEI }</p>
                        </div>
                        <div className={cx('tr-datos')}>
                            <p className={cx('td-label')} title="en acta: '(% Error QF CV)'">Porcentaje error QF</p>
                            <Numero editarCampo={editar} valor={acta.porcentajeErrorQF}
                                    actualizarActa={ctx.actualizarActa.bind(this, 'porcentajeErrorQF')}/>
                        </div>
                    </div>
                </div>
            </div>
            :
            <div>
                Sin datos en el acta
            </div>
        }
    </div>
PanelBody.contextTypes = {
    actualizarActa: React.PropTypes.func
}

const Texto = ({valor, editarCampo, actualizarActa})=>{
    return editarCampo?
        <InputTexto
            className={cx('td-inputTexto')}
            asignada={valor}
            onGuardar={actualizarActa}
            focusRowAnterior={()=>{}}
            focusRowSiguiente={()=>{}}
            editable={editarCampo}
        />
        :
        <p className={cx('td-dato')}>{valor}</p>
}
const Numero = ({valor, editarCampo, actualizarActa})=>{
    return editarCampo?
        <InputNumber
            className={cx('td-inputNumero')}
            asignada={valor}
            onGuardar={actualizarActa}
            focusRowAnterior={()=>{}}
            focusRowSiguiente={()=>{}}
            editable={editarCampo}
        />
        :
        <p className={cx('td-dato')} >{valor}</p>
}