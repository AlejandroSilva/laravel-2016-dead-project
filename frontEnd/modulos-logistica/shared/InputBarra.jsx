// Librerias
import React from 'react'
// let PropTypes = React.PropTypes
// Componentes


export class InputBarra extends React.Component {
    render(){
        return (
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
                            if(value!==''){
                                this.props.onScan(value)
                            }
                        }
                   }}
                />
                <input
                    style={{width:'50%'}}
                    ref={ref=>this.refBarra=ref}
                    disabled
                />
            </div>
        )
    }
}

InputBarra.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    // arreglo: PropTypes.arrayOf(PropTypes.object).isRequired
}