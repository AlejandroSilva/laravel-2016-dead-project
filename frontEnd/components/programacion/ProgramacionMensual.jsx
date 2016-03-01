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
import AgregarPegarDesdeExcel from './AgregarPegarDesdeExcel.jsx'

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

    submitLocal(local, mesAnno){
        console.log("saludos desde el padre", local, mesAnno)
        return this.tablaLocalesMensual.agregarLocal(local, mesAnno)
    }

    render(){
        return (
            <div>
                <h1>Programación mensual</h1>

                <Tabs selectedIndex={0}>
                    <TabList>
                        <Tab>Ingresar manualmente</Tab>
                        <Tab>Pegar desde Excel</Tab>
                    </TabList>
                    <TabPanel>
                        {/* Agregar manualmente */}
                        <AgregarManualmente
                            ref={ref=>this.AgregarManualmente=ref}
                            clientes={this.props.clientes}
                            meses={this.state.meses}
                            onFormSubmit={this.submitLocal}
                        />
                    </TabPanel>
                    <TabPanel>
                        {/* Agregar desde Excel */}
                        <AgregarPegarDesdeExcel
                            ref={ref=>this.AgregarPegarDesdeExcel=ref}
                            clientes={this.props.clientes}
                            meses={this.state.meses}
                            onFormSubmit={this.submitLocal}
                        />
                    </TabPanel>
                </Tabs>

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