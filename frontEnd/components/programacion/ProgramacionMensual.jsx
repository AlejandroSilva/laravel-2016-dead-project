// Libs
import React from 'react'
let PropTypes = React.PropTypes
import moment from 'moment'
moment.locale('es')

// Component
//import Multiselect from 'react-widgets/lib/Multiselect'
import { Tab, Tabs, TabList, TabPanel } from 'react-tabs'
import TablaLocalesMensual from './TablaLocalesMensual.jsx'
import AgregarManualmente from './AgregarManualmente.jsx'
//import AgregarPegarDesdeExcel from './AgregarPegarDesdeExcel.jsx'

class ProgramacionMensual extends React.Component{
    constructor(props) {
        super(props)
        let meses = []
        // mostrar en el selector, los proximos 12 meses
        for (let desface = 0; desface < 12; desface++) {
            let mes = moment().add(desface, 'month')
            meses.push({
                valor: mes.format('MM-YYYY'),
                texto: mes.format('MMMM  YYYY')
            })
        }
        this.state = {
            meses
        }
        this.submitLocal = this.submitLocal.bind(this)
    }
    componentDidMount(){
        this.tablaLocalesMensual.agregarInventario(this.props.clientes[1].locales[1], '07-2016')
        //this.tablaLocalesMensual.agregarInventario(this.props.clientes[1].locales[2], '04-2016')
        this.tablaLocalesMensual.agregarInventario(this.props.clientes[1].locales[4], '07-2016')
    //    this.tablaLocalesMensual.agregarInventario(this.props.clientes[0].locales[5], '05-2016')
    //    this.tablaLocalesMensual.agregarInventario(this.props.clientes[0].locales[6], '05-2016')
        this.tablaLocalesMensual.agregarInventario(this.props.clientes[1].locales[8], '09-2016')
        this.tablaLocalesMensual.agregarInventario(this.props.clientes[0].locales[12], '08-2016')
    }

    submitLocal(local, mesAnno){
        console.log("agregarndo el local: ", local, mesAnno)
        this.tablaLocalesMensual.agregarInventario(local, mesAnno)
    }

    render(){
        return (
            <div>
                <h1>Programación mensual</h1>

                <AgregarManualmente
                    ref={ref=>this.AgregarManualmente=ref}
                    clientes={this.props.clientes}
                    meses={this.state.meses}
                    onFormSubmit={this.submitLocal}
                />

                <div className="row">
                    <h4 className="page-header" style={{marginTop: '1em'}}>Locales a programar:</h4>
                    <TablaLocalesMensual
                        ref={ref=>this.tablaLocalesMensual=ref}
                    />
                </div>
            </div>
        )
    }
}

ProgramacionMensual.protoTypes = {
    clientes: PropTypes.array.isRequired
}

export default ProgramacionMensual