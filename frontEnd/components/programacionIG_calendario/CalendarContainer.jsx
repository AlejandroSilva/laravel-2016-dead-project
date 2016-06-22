// Librerias
import React from 'react'
import moment from 'moment'
// Utils
import api from '../../apiClient/v1'
import {StateMachine} from './StateMachine.js'
// Estilos
import classNames from 'classnames/bind'
import * as css from './calendarContainer.css'
let cx = classNames.bind(css)
// Componentes
import { Calendar } from './Calendar.jsx'

export class CalendarContainer extends React.Component {
    constructor(props) {
        super(props)
        let month = moment().month();
        let year = moment().year();

        // Calendario "limpio"
        this.blackbox = new StateMachine()
        this.blackbox.build_calendar(year, month)

        // Estado inicial del state
        this.state = {
            loading: false,
            calendar: this.blackbox.get_state()
        }

        // Metodos
        this.fetch = (idCliente, mes)=>{
            this.setState({loading: true})
            api.vistaGeneral.fetch(mes)
                .then(datos=>{
                    this.blackbox.clean_calendar()
                    this.blackbox.set_usuarios(datos.lideres, datos.auditores)
                    this.blackbox.set_nominas(datos.nominas)
                    this.blackbox.set_auditorias(datos.auditorias)
                    this.setState({
                        calendar: this.blackbox.get_state(),
                        loading: false
                    })
                })
                // .catch(err=>{
                //     console.log(err)
                // })
        }

        this.selectUser = (idUser)=> {
            let that = this
            return function () {
                console.log('seleccionando ', idUser)
                that.blackbox.selectUsuario(idUser)
                that.setState({calendar: that.blackbox.get_state(),})
            }
        }
        this.showNominas = (setSelect)=>{
            this.blackbox.selectNominas(setSelect)
            this.setState({calendar: this.blackbox.get_state()})
        }
        this.showAuditorias = (setSelect)=>{
            this.blackbox.selectAuditorias(setSelect)
            this.setState({calendar: this.blackbox.get_state()})
        }
        this.showLideres = (mostrar)=>{
            this.blackbox.filtrarLideres(mostrar)
            this.setState({calendar: this.blackbox.get_state()})
        }
        this.showAuditores = (mostrar)=>{
            this.blackbox.filtrarAuditores(mostrar)
            this.setState({calendar: this.blackbox.get_state()})
        }
    }

    render(){
        return (
            <div>
                <HerramientasCalendario
                    loading={this.state.loading}
                    meses={this.props.meses}
                    fetch={this.fetch}
                    showNominas={this.showNominas}
                    showAuditorias={this.showAuditorias}
                    showLideres={this.showLideres}
                    showAuditores={this.showAuditores}
                />
                <Calendar
                    calendar={this.state.calendar}
                    selectUser={this.selectUser}
                />
            </div>
        )
    }
}

CalendarContainer.propTypes = {
    // inventarios: React.PropTypes.arrayOf(React.PropTypes.object).isRequired
    // numero: React.PropTypes.number.isRequired,
    // texto: React.PropTypes.string.isRequired,
    // objeto: React.PropTypes.object.isRequired,
}

/* ************************************************ */

// Librerias
class HerramientasCalendario extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            idCliente: 0,
            mesSeleccionado: this.props.meses[0].valor,
            mostrarNominas: true,
            mostrarAuditorias: true,
            mostrarLideres: true,
            mostrarAuditores: true
        }
        this.checkboxNominasChanged = ()=>{
            let select = this.state.mostrarNominas
            this.setState({mostrarNominas: !select})
            this.props.showNominas(!select)
        }
        this.checkboxAuditoriasChanged = ()=>{
            let select = this.state.mostrarAuditorias
            this.setState({mostrarAuditorias: !select})
            this.props.showAuditorias(!select)
        }
        this.checkboxLideresChanged = ()=>{
            let select = this.state.mostrarLideres
            this.setState({mostrarLideres: !select})
            this.props.showLideres(!select)
        }
        this.checkboxAuditoresChanged = ()=>{
            let select = this.state.mostrarAuditores
            this.setState({mostrarAuditores: !select})
            this.props.showAuditores(!select)
        }
        this.fetch = ()=>{
            this.props.fetch(this.state.idCliente, this.state.mesSeleccionado)
        }
        this.selectMesChanged = (evt)=>{
            let seleccion = evt.target.value
            let index = this.props.meses.findIndex(mes=> mes.valor===seleccion)
            if(index>=0){
                let mes = this.props.meses[index]
                this.setState({
                    mesSeleccionado: mes.valor,
                    // cuando se piden nuevos datos, vienen todos seleccionados por defecto
                    mostrarNominas: true,
                    mostrarAuditorias: true
                }, ()=>{
                    this.fetch()
                })
            }
        }
    }
    componentWillMount(){
        this.fetch()
    }

    render(){
        return (
            <div className="row">
                <div className="col-md-2">
                    {/*
                    <div className="form-group">
                        <select className="form-control">
                            <option>Disabled select</option>
                        </select>
                    </div>
                     */}
                    <select className='form-control'
                            value={this.state.mesSeleccionado}
                            onChange={this.selectMesChanged}
                            disabled={this.props.loading}
                    >
                        {this.props.meses.map((mes,i)=>
                            <option key={i} value={mes.valor}>{mes.texto}</option>
                        )}
                    </select>
                </div>
                <div className="col-md-2">
                    <div className="checkbox">
                        <label>
                            <input type="checkbox"
                                   checked={this.state.mostrarNominas}
                                   onChange={this.checkboxNominasChanged}
                                   disabled={this.props.loading}
                            />Nominas
                        </label>
                    </div>
                    <div className="checkbox">
                        <label>
                            <input type="checkbox"
                                   checked={this.state.mostrarAuditorias}
                                   onChange={this.checkboxAuditoriasChanged}
                                   disabled={this.props.loading}
                            />
                            Auditorias
                        </label>
                    </div>
                </div>
                <div className="col-md-2">
                    <div className="checkbox">
                        <label>
                            <input type="checkbox"
                                   checked={this.state.mostrarLideres}
                                   onChange={this.checkboxLideresChanged}
                                   disabled={this.props.loading}
                            />Lideres
                        </label>
                    </div>
                    <div className="checkbox">
                        <label>
                            <input type="checkbox"
                                   checked={this.state.mostrarAuditores}
                                   onChange={this.checkboxAuditoresChanged}
                                   disabled={this.props.loading}
                            />Auditores
                        </label>
                    </div>
                </div>
                <div className="col-md-2">
                    <button className="btn btn-primary btn-xs"
                            onClick={this.fetch}
                            disabled={this.props.loading}
                    >Actualizar</button>
                </div>
                <div className="col-md-2"></div>
                <div className="col-md-2"></div>
            </div>
        )
    }
}