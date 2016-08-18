// Librerias
import React from 'react'
let PropTypes = React.PropTypes
import PureRenderMixin from 'react-addons-pure-render-mixin'
// Estilos
import classNames from 'classnames/bind'
import * as css from './calendar.css'
let cx = classNames.bind(css)
// Compoentes
import { Card, CardHeader, CardBody } from './Card.jsx'


export class Calendar extends React.Component {
    constructor(props) {
        super(props)
        this.shouldComponentUpdate = PureRenderMixin.shouldComponentUpdate.bind(this)
    }
    render(){
        return (
            <div className={cx('month')}>
                {/* Header con los días de la semana */}
                <CalendarHeader/>

                {this.props.calendar.weeks.map(week=>{
                    return <Week
                        key={week.idWeek}
                        week={week}
                        selectUser={this.props.selectUser}
                    />
                })}
            </div>
        )
    }
}
Calendar.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    calendar: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}

const CalendarHeader = ()=>
    <div className={cx('header-flex-row')} >
        <Card className={cx('card-header-summary')}><CardHeader>Summary</CardHeader></Card>
        <Card className={cx('card-header-weekday')}><CardHeader>Lunes</CardHeader></Card>
        <Card className={cx('card-header-weekday')}><CardHeader>Martes</CardHeader></Card>
        <Card className={cx('card-header-weekday')}><CardHeader>Miércoles</CardHeader></Card>
        <Card className={cx('card-header-weekday')}><CardHeader>Jueves</CardHeader></Card>
        <Card className={cx('card-header-weekday')}><CardHeader>Viernes</CardHeader></Card>
        <Card className={cx('card-header-weekday')}><CardHeader>Sábado</CardHeader></Card>
        <Card className={cx('card-header-weekday')}><CardHeader>Domingo</CardHeader></Card>
    </div>

const Week = ({week, selectUser})=>
        <div className={cx('week-flex-row')}>
            <CardSummary
                summary={week.summary}
                selectUser={selectUser}
            />
            {week.days.map((day)=>
                <CardDay
                    key={day.idDay}
                    day={day}
                />
            )}
        </div>

const CardSummary = ({summary, selectUser})=>
    <Card className={cx('card-week-summary')}>
        <CardHeader>
            &nbsp;
        </CardHeader>
        <CardBody>
            <table className={cx('table-card')} >
                <thead>
                    <tr><th>Lider</th><th>T.Nom</th><th>T.Aud</th></tr>
                </thead>
                <tbody>
                    {summary.map(summary=>
                        <SummaryRow
                            key={summary.id}
                            summary={summary}
                            selectUser={selectUser}
                        />
                    )}
                </tbody>
            </table>
        </CardBody>
    </Card>

const SummaryRow = ({summary, selectUser})=>(
        summary.idUsuario!=-1?
            <tr className={cx('')}>
                <td className={cx('td-checkbox')}>
                    <label>
                        <input type="checkbox"
                               checked={summary.isUserSelected}
                               onChange={selectUser(summary.idUsuario)}
                        />
                        <p>{summary.nombre}</p>
                    </label>

                </td>
                <td><p>{summary.totalNominas}</p></td>
                <td><p>{summary.totalAuditorias}</p></td>
            </tr>
            :
            <tr className={cx('')}>
                <td></td>
                <td><p></p></td>
                <td><p>&nbsp;</p></td>
            </tr>
)


const CardDay = ({day})=>
    <Card className={cx('day')}>
        <CardHeader
            className={cx({
                'dayNumber-normal': day.sameMonth,
                'dayNumber-feriado': day.isWeekend,
                'dayNumber-otherMonth': !day.sameMonth
            })}
        >
            {day.number}
        </CardHeader>
        <CardBody>
            <table className={cx('table-card')}>
                <thead>
                    <tr><th>CE</th><th>Comuna</th><th>Dot</th></tr>
                </thead>
                <tbody>
                    {day.rows.map((evento, index)=>(
                        <EventRow key={evento.id} evento={evento} />
                    ))}
                </tbody>
            </table>
        </CardBody>
    </Card>

/*
class EventRow extends React.Component {
    constructor(props) {
        super(props)
        this.shouldComponentUpdate = PureRenderMixin.shouldComponentUpdate.bind(this)
    }
    render(){
        return (
            <tr className={cx({
                'tr-unselected': !this.props.evento.selected
            })}>
                <td><p>{this.props.evento.col1}</p></td>
                <td><p>{this.props.evento.col2}</p></td>
                <td><p>{this.props.evento.col3}</p></td>
            </tr>
        )
    }
}
*/
const EventRow = ({evento})=>
    <tr className={cx({
        'tr-unselected': !evento.selected
    })}>
        <td><p>{evento.col1}</p></td>
        <td><p>{evento.col2}</p></td>
        <td><p>{evento.col3}</p></td>
    </tr>