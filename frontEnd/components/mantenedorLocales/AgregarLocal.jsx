import React from 'react'

class AgregarLocal extends React.Component{

    onSeleccionarCliente(evt){
        let idCliente = evt.target.value
        this.props.seleccionarCliente(idCliente)
    }

    render(){
        return <div className="form">
            {/*<div className="row">
                <h4 className="page-header" style={{marginTop: '1em'}}>Agregar locales:</h4>
            </div>*/}

            <div className="row">
                {/* Cliente */}
                <div className={'col-sm-2 form-group '}>
                    <label className="control-label" htmlFor="cliente">Cliente</label>
                    <select className="form-control"  name="cliente" defaultValue="-1"
                            ref={ref=>this.inputIdCliente=ref}
                            onChange={this.onSeleccionarCliente.bind(this)}
                    >
                        <option value="-1" disabled>--</option>
                        {this.props.clientes.map((cliente, index)=>{
                            return <option key={index} value={cliente.idCliente}>{`${cliente.nombreCorto} - ${cliente.nombre}`}</option>
                        })}
                    </select>
                </div>
            </div>
        </div>
    }
}

AgregarLocal.propTypes = {
    clientes: React.PropTypes.array.isRequired,
    jornadas: React.PropTypes.array.isRequired,
    formatoLocales: React.PropTypes.array.isRequired,
    seleccionarCliente: React.PropTypes.func.isRequired
}
export default AgregarLocal