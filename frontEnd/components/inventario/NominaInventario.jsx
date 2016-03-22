import React from 'react'
let PropTypes = React.PropTypes


class NominaInventario extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            //stockInicial: props.stockInicial,
            //stock: props.stockInicial,
            dotacionSugerida: props.dotacionSugerida,
            dotacion: props.dotacionSugerida
        }
        this.dotacionChanged = this.dotacionChanged.bind(this)
    }
    componentWillReceiveProps(nextProps){
        // actualizar el stock y la dotacion sugerida
        this.setState({
            //stockInicial: nextProps.stockInicial,
            //dotacionSugerida: nextProps.dotacionSugerida,
            stock: nextProps.stockInicial,
            dotacion: nextProps.dotacionSugerida
        })
    }

    dotacionChanged(event){
        let dotacion = event.target.value
        this.setState({
            dotacion
        })
    }

    dotacionChanged(event){
        let dotacion = event.target.value
        this.setState({
            dotacion
        })
    }

    render() {
        if(!this.props.habilitado){
            return <div></div>
        }
        return (
            <div className="col-sm-6">
                <div className="row">
                    <h4 className="page-header" style={{margin: '10px'}}>{this.props.titulo}</h4>
                </div>
                {/* Stock (y fecha de stock) */}
                {/*
                <div className="form-group">
                    <label className="col-sm-4 control-label" htmlFor="stockDia">Stock </label>
                    <div className="col-sm-4">
                        <input className="form-control" type="number" name="stockDia" value="64587" readOnly/>
                    </div>
                </div>
                */}

                {/* Lider */}
                <div className="form-group">
                    <label className="col-sm-4 control-label" htmlFor="idLiderDia">Lider </label>
                    <div className="col-sm-8">
                        <select className="form-control" name="idLiderDia" name="idLiderDia"
                                onChange={ ()=>{ alert("sad") } }>
                            <option value="1">Pedro</option>
                            <option value="2">Juan</option>
                            <option value="3">Diego</option>
                        </select>
                    </div>
                </div>

                {/* Supervisor */}
                <div className="form-group">
                    <label className="col-sm-4 control-label" htmlFor="idSupervisorDia">Supervisor</label>
                    <div className="col-sm-8">
                        <select className="form-control" name="idSupervisorDia" name="idSupervisorDia"
                                onChange={ ()=>{ alert("sad") } }>
                            <option value="1">Pedro</option>
                            <option value="2">Juan</option>
                            <option value="3">Diego</option>
                        </select>
                    </div>
                </div>

                {/* Dotacion */}
                <div className="form-group">
                    <label className="col-sm-4 control-label" htmlFor="dotacionAsignadaDia">Dotación</label>
                    <div className="col-sm-8">
                        <div className="input-group">
                            <div className="input-group-addon">Sugerida: <b>{this.state.dotacionSugerida}</b></div>

                            <input className="form-control" type="number" name="dotacionAsignadaDia"
                                   value={this.state.dotacion} onChange={this.dotacionChanged}
                            />
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}
NominaInventario.propTypes = {
    titulo: PropTypes.string.isRequired,
    //stockInicial: PropTypes.number.isRequired,
    dotacionSugerida: PropTypes.number.isRequired,
    habilitado: PropTypes.bool
}
NominaInventario.defaultProps = {
    habilitado: true
}

export default NominaInventario


/**
 * todo: hora de llegada sugerida   ************
 * todo: supervisor y lider NO DEBEN ser campos, deben estan en una tabla aparte
 * todo: supervisor y lider no son ROLES FIJOS, un PERSONAL puede tener diferentes CARGOS
 *
 *
 * MIGRAR LA WEA DE SERVICIO!!!
 * avisar de los correos sin espacio disponible
 */