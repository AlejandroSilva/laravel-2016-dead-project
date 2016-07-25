// Librerias
import React from 'react'


export class InputBarra extends React.Component {
    constructor(props){
        super(props)
        this.state = {
            errorMessage: ''
        }
        this.errorCallback = (errorMessage)=>{
            this.setState({errorMessage})
        }
    }
    focus(){
        this.refInput.focus()
    }
    render(){
        return (
            <div>
                <div>
                    <input
                        type="text"
                        style={{width:'50%'}}
                        placeholder="Ingresar Código Barra"
                        ref={ r=>this.refInput=r }
                        onKeyPress={ evt=>{
                            if(evt.key=='Enter'){
                                let value = this.refInput.value
                                this.refInput.value = ''
                                this.refBarra.value = value
                                // enviar el valor
                                // limpiar cualquier error
                                this.setState({errorMessage: ''})
                                if(value!=='')
                                    this.props.onScan(value, this.errorCallback)
                            }
                       }}
                    />
                    <input
                        style={{width:'50%'}}
                        ref={ref=>this.refBarra=ref}
                        disabled
                    />
                </div>
                <div>
                    <p style={{
                        textAlign: 'right',
                        color: 'red',
                        margin: 0
                    }}>&nbsp;{this.state.errorMessage}</p>
                </div>
            </div>
        )
    }
}

InputBarra.propTypes = {
    onScan: React.PropTypes.func.isRequired,
    errorMessage: React.PropTypes.string,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}