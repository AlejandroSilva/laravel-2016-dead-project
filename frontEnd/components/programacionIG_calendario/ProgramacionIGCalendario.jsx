// Librerias
import React from 'react'
let PropTypes = React.PropTypes
import moment from 'moment'

import {BlackBox} from './Blackbox.js'
// Componentes
import classNames from 'classnames/bind'
import * as css from './index.css'
let cx = classNames.bind(css)

export class ProgramacionIGCalendario extends React.Component {
    constructor(props) {
        super(props)
        let month = moment().month();
        let year = moment().year();

        // Calendario "limpio"
        this.blackbox = new BlackBox()

        this.blackbox.build_calendar(year, month)
        // una vez creado, agregar los datos entregados por defecto
        this.blackbox.set_inventarios(this.props.inventarios)
        this.state = {
            calendar: this.blackbox.get_state()
        }
    }
    componentWillReceiveProps(nextProps){
        this.blackbox.set_inventarios(nextProps.inventarios)
        this.setState({
            calendar: this.blackbox.get_state()
        })
    }

    render(){
        return (
            <div className={cx('container')}>
                <button onClick={()=>{
                    // console.time('set_inventarios')
                    this.blackbox.set_inventarios(this.props.inventarios)
                    // console.timeEnd('set_inventarios')
                    // console.time('get_state')
                    this.setState({calendar: this.blackbox.get_state()})
                    // console.timeEnd('get_state')
                }}>Recalcular</button>
                <div className={cx('month')}>
                    {/* Header con los días de la semana */}
                    <div className={cx('header-row')} >
                        <div className={cx('header-summary', 'card')}>Summary</div>
                        <div className={cx('header-weekday', 'card')}>Lunes</div>
                        <div className={cx('header-weekday', 'card')}>Martes</div>
                        <div className={cx('header-weekday', 'card')}>Miércoles</div>
                        <div className={cx('header-weekday', 'card')}>Jueves</div>
                        <div className={cx('header-weekday', 'card')}>Viernes</div>
                        <div className={cx('header-weekday', 'card')}>Sábado</div>
                        <div className={cx('header-weekday', 'card')}>Domingo</div>
                    </div>

                    {this.state.calendar.weeks.map(week=>{

                        let weekSumary = <div className={cx('week-summary', 'card', 'card-none')} >
                            {/* sin card-header, porque tiene una tabla con cabecera*/}
                            <div className={cx('card-body')}>
                                <table className={'table table-condensed ' + cx('table')} >
                                    <thead>
                                        <tr><th>Lider</th><th>Total</th></tr>
                                    </thead>
                                    <tbody>
                                        {week.summary.usuarios.map(usuario=>{
                                            return <tr key={usuario.id}>
                                                <td>{usuario.nombre}</td><td>{usuario.totalSemana}</td>
                                            </tr>
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        let days_divs = week.days.map((day)=>{
                            // imprimir div del DIA
                            return(
                                <div key={day.idDay}
                                     className={cx('day', 'card', {
                                        'card-default': day.sameMonth,
                                        'card-grey': !day.sameMonth,
                                        'card-warning': day.isWeekend
                                     })}
                                >
                                    <div className={cx("card-header")} >
                                        {day.number}
                                    </div>
                                    <div className={cx('card-body')}>
                                        <table className={'table table-condensed ' + cx('table')} >
                                            <tbody>
                                                {day.rows.map((nom, index)=>{
                                                    return nom?
                                                        <tr key={nom.id}>
                                                            <td>{nom.local}</td>
                                                            <td>{nom.ciudad}</td>
                                                            <td>{nom.dTotal}</td>
                                                        </tr>
                                                        :
                                                        <tr key={index}><td>&nbsp;</td><td></td><td></td></tr>
                                                })}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )
                        })

                        return (
                            <div key={week.idWeek} className={cx('week-row')}>
                                {weekSumary}
                                {days_divs}
                            </div>
                        )
                    })}
                </div>
            </div>
        )
    }
}

ProgramacionIGCalendario.propTypes = {
    inventarios: PropTypes.arrayOf(PropTypes.object).isRequired
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
}