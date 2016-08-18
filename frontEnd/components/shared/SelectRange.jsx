// Librerias
import React from 'react'
import moment from 'moment'
moment.locale('es')
import { DateRange } from 'react-date-range'

class SelectRange extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            visible: false,
            startDate: this.props.startDateSelected,
            endDate: this.props.endDateSelected
        }

        this.onChangeDate = (fechas)=>{
            this.setState({
                startDate: fechas.startDate,
                endDate: fechas.endDate
            })
        }
        this.toggleSelector = ()=>{
            if(this.state.visible)
                this.onCancel()
            else
                this._showSelector()
        }
        this._showSelector = ()=>{
            this.setState({visible:true})
        }
        this._hideSelector = ()=>{
            this.setState({visible:false})
        }

        this.onAccept = ()=>{
            this._hideSelector()
            this.props.onRangeSelected(this.state.startDate, this.state.endDate)
        }
        this.onCancel = ()=>{
            this._hideSelector()
            // dejar el valor original
            this.setState({
                startDate: this.props.startDateSelected,
                endDate: this.props.endDateSelected
            })
        }
    }
    componentWillReceiveProps(nextProps){
        this.state = {
            startDate: nextProps.startDateSelected,
            endDate: nextProps.endDateSelected
        }
    }
    
    render(){
        let textoFecha = (this.state.startDate.format('YYYY')===this.state.endDate.format('YYYY'))?
            `${this.state.startDate.format('dddd DD MMMM')}, a ${this.state.endDate.format('dddd DD MMMM')}`
            :
            `${this.state.startDate.format('dddd DD MMMM YYYY')}, a ${this.state.endDate.format('dddd DD MMMM YYYY')}`
        return <div>
            <button className="btn btn-default"
                    style={{width:"100%"}}
                    type="button"
                    onClick={this.toggleSelector}
            >
                <div className="pull-left">
                    <span className="glyphicon glyphicon-calendar"></span>
                </div>
                <div className="pull-right">
                    <span>{textoFecha}</span>
                    <span className="caret"></span>
                </div>
            </button>
            <DateRange
                style={{display: this.state.visible? 'block':'none'}}
                theme={{
                    DateRange: {
                        position: 'absolute',
                        width: '280px',
                        zIndex: '99'
                    }
                }}
                startDate={this.state.startDate}
                endDate={this.state.endDate}
                onChange={this.onChangeDate}
                calendars={1}
            />
            <div style={{
                width: '280px',
                position: 'absolute',
                top: '355px'
            }}>
                <button type="button"
                        className="btn btn-default"
                        style={{
                            width:"50%",
                            display: this.state.visible? 'inline-block':'none'
                        }}
                        onClick={this.onCancel}
                >Cancelar</button>
                <button className="btn btn-primary" type="button"
                        style={{
                            width:"50%",
                            display: this.state.visible? 'inline-block':'none'
                        }}
                        onClick={this.onAccept}
                >Aceptar</button>
            </div>
        </div>
    }
    
}
SelectRange.propTypes = {
    // Objtos
    startDateSelected: React.PropTypes.object.isRequired,
    endDateSelected: React.PropTypes.object.isRequired,
    // Funciones
    onRangeSelected: React.PropTypes.func.isRequired
}
// SelectRange.defaultProps = {}

export default SelectRange