// Librerias
import React from 'react'
import ReactDOM from 'react-dom'
let PropTypes = React.PropTypes
// Moment
import moment from 'moment'
let momentLocalizer = require('react-widgets/lib/localizers/moment')
momentLocalizer(moment)
// Validador Rut
import { validarRUN, obtenerVerificador } from '../../shared/ValidadorRUN'
// Forms
import Form from 'react-formal'
import formalInputs from '../../shared/react-formal-inputs/react-formal-inputs'
Form.addInputTypes(formalInputs)
import yup from 'yup'
// Componentes
import * as styles from './formularioUsuario.css'
import classNames from 'classnames/bind'
let cx = classNames.bind(styles)


const listaBancos = ['Banco Estado', 'Banco de Chile', 'Banco Internacional',  'Scotiank-Desarrollo',
    'Banco de Credito e Inversiones', 'CorpBanca', 'Banco Bice',  'HSBC Bank Chile',  'Banco Santander', 'Banco Itaú',
    'The Bank of Tokyo-Mitsubishi', 'Banco Security', 'Banco Falabella', 'Banco Ripley',  'Banco Consorcio', 'Banco Paris',
    'Banco BBVA', 'Copeuch', 'Banco Penta', 'Scotiabank Chile', 'Deutsche Bank', 'Rabobank Chile']
const listaTipoCuentas = ['Cuenta Corriente', 'Cuenta Vista', 'Cuenta Ahorro', 'Chequera Electronica', 'Cuenta de Gastos', 'Cuenta RUT']


export class FormularioUsuario extends React.Component {
    usuarioSchema  = yup.object({
        usuarioRUN: yup.string().default(this.props.usuario.usuarioRUN)
            .min(1, 'El RUN debe tener un minimo de 1 caracteres')
            .max(8, 'El RUN debe tener un máximo de 8 caracteres'),
        usuarioDV: yup.string()
            .default(this.props.usuario.usuarioDV)
            .required('Ingrese el digito verificador del RUN')
            .test('verificarUsuarioDV', 'El digito verificador es incorrecto', function(value){
                // Hack sucio... cuando se hace una validacion con delay, se sacan los valores de options, si no, de parent
                let valores = this.options.value? this.options.value : this.parent
                return validarRUN(valores.usuarioRUN, value)
            })
            .max(1, 'El máximo es 1 digito'),
        email: yup.string().default(this.props.usuario.email)
            .max(60, 'El máximo es 60 caracteres')   // no obligatorio
            //.email('Debe ser un email valido'),
            .test('correo', 'Debe ingresar un email valido', function(value){
                var rEmail = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i;
                return value==='' || rEmail.test(value)
            }),
        emailPersonal: yup.string().default(this.props.usuario.emailPersonal)
            .max(60, 'El máximo es 60 caracteres')
            //.email('Debe ser un email valido'),
            .test('correo', 'Debe ingresar un email valido', function(value){
                var rEmail = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i;
                return value==='' || rEmail.test(value)
            }),
        nombre1: yup.string().default(this.props.usuario.nombre1)
            .required('Ingrese el primer nombre')
            .min(3, 'El minimo es de 3 caracteres')
            .max(15, 'El máximo es 15 caracteres'),
        nombre2: yup.string().default(this.props.usuario.nombre2)
            .max(15, 'El máximo es 15 caracteres'),
        apellidoPaterno: yup.string().default(this.props.usuario.apellidoPaterno)
            .required('Ingrese el apellido')
            .min(3, 'El minimo es de 3 caracteres')
            .max(20, 'El máximo es 20 caracteres'),
        apellidoMaterno: yup.string().default(this.props.usuario.apellidoMaterno)
            .max(20, 'El máximo es 20 caracteres'),  // no obligatorio
        telefono: yup.string().default(this.props.usuario.telefono)
            .max(20, 'El máximo es 20 caracteres'),  // no obligatorio
        telefonoEmergencia: yup.string().default(this.props.usuario.telefonoEmergencia)
            .max(20, 'El máximo es 20 caracteres'),  // no obligatorio
        // TODO: este validador no permite que existan fechas "invalidas" como un string vacio, arreglar esto eventualmente....
        fechaNacimiento: yup.date().default( new Date(this.props.usuario.fechaNacimiento) )
            .test('fechaNacimiento', 'Mensaje de error', function (){
                return true
            }),
            //.required('Ingrese la fecha de nacimiento'),
        direccion: yup.string().default(this.props.usuario.direccion)
            .max(150, 'El máximoes de 5 caracteres'),
        // cutRegion: yup.number()
        //     .required('Seleccione una Región'),
        cutComuna: yup.number().default( parseInt(this.props.usuario.cutComuna) )   // es un number, pero lo retornan como string
            .required('Seleccione una Comuna'),
        tipoContrato: yup.string().default(this.props.usuario.tipoContrato)
        // .required('Escriba el Tipo de Contrato, o seleccionelo de la lista')
            .max(30, 'El máximo es 30 caracteres'),
        banco: yup.string().default(this.props.usuario.banco)
        // .required('Escriba el Nombre del Banco, o seleccionelo de la lista')
            .max(30, 'El máximo es 30 caracteres'),
        tipoCuenta: yup.string().default(this.props.usuario.tipoCuenta)
        // .required('Escriba el Tipo de Cuenta, o seleccionelo de la lista')
            .max(30, 'El máximo es 30 caracteres'),
        numeroCuenta: yup.string().default(this.props.usuario.numeroCuenta)
        // .required('Escriba el Numero de Cuenta')
            .max(30, 'El máximo es 30 caracteres')
    })

    enviarFormularioInvalido(datos){
        console.log('enviando invalido ', datos)
    }
    siguienteComponente(refComponente, evt){
        if(evt.key=='Enter'){
            evt.preventDefault()
            if(refComponente==='refFechaNacimiento') {
                //this.refFechaNacimiento.refs.input.focus() // OK para el wrapper
                this.refFechaNacimiento.refs.input.focus() // OK para el date2
            }else if(refComponente==='refComuna'){
                this.refComuna.refs.input.refs.inner.open() // OK para el date2
            }else{
                ReactDOM.findDOMNode(this[refComponente].refs.input).select()
            }
            // console.log( ReactDOM.findDOMNode(this.refNombre1).focus() ) // OK
            // console.log( ReactDOM.findDOMNode(this.refNombre1.refs.input).focus() ) // OK
            return
        }
    }

    // componentDidMount(){
    //     // seleccionar DV al mostrar el componente
    //     ReactDOM.findDOMNode(this['refUsuarioDV'].refs.input).select()
    // }

    render(){
        return(
            <div>
                <Form
                    className="form-horizontal"
                    schema={this.usuarioSchema}
                    defaultValue={this.usuarioSchema.default()}
                    //delay={1000}
                    //noValidate={true}
                    onSubmit={this.props.enviarFormulario.bind(this, this.props.usuario.id)}
                    onInvalidSubmit={this.enviarFormularioInvalido.bind(this)}>

                    <div className="row">
                        <div className="col-md-7">
                            <h4>Datos Personales</h4>
                            {/* RUN */}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-2 control-label">RUN (*)</label>
                                <div className="col-sm-3">
                                    <Form.Field className="form-control" errorClass={cx('form__field--error')}
                                                name='usuarioRUN'
                                        //                                                disabled={true}
                                        //                                                 mapValue={{
                                        //                                                     usuarioRUN: usuarioRUN=>usuarioRUN,
                                        //                                                     usuarioDV: usuarioRUN=>obtenerVerificador(usuarioRUN)
                                        //                                                 }}
                                                onKeyPress={this.siguienteComponente.bind(this, 'refUsuarioDV')}
                                    />
                                </div>
                                <div className="col-sm-2">
                                    <Form.Field className="form-control" errorClass={cx('form__field--error')}
                                                name='usuarioDV'
                                        //disabled={true}
                                                ref={ref=>this.refUsuarioDV=ref}
                                                onKeyPress={this.siguienteComponente.bind(this, 'refNombre1')}
                                    />
                                </div>
                                <div className="col-sm-5">
                                    <Form.Message for={['usuarioRUN', 'usuarioDV']} errorClass={cx('form__message-error')}/>
                                </div>
                            </div>
                            {/* Nombres */}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-2 control-label">Nombres (*)</label>
                                <div className="col-sm-5">
                                    <Form.Field className="form-control" errorClass={cx('form__field--error')} placeholder='Primer Nombre'
                                                name='nombre1'
                                                ref={ref=>this.refNombre1=ref}
                                                onKeyPress={this.siguienteComponente.bind(this, 'refNombre2')}
                                    />
                                    <Form.Message for={'nombre1'} errorClass={cx('form__message-error')}/>
                                </div>
                                <div className="col-sm-5">
                                    <Form.Field className="form-control" placeholder='Segundo Nombre'
                                                name='nombre2'
                                                ref={ref=>this.refNombre2=ref}
                                                onKeyPress={this.siguienteComponente.bind(this, 'refApellidoPaterno')}
                                    />
                                    <Form.Message for={'nombre2'} errorClass={cx('form__message-error')}/>
                                </div>
                            </div>
                            {/* Apellidos */}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-2 control-label">Apellidos (*)</label>
                                <div className="col-sm-5">
                                    <Form.Field className="form-control" errorClass={cx('form__field--error')} placeholder='Apellido Paterno'
                                                name='apellidoPaterno'
                                                ref={ref=>this.refApellidoPaterno=ref}
                                                onKeyPress={this.siguienteComponente.bind(this, 'refApellidoMaterno')}
                                    />
                                    <Form.Message for={'apellidoPaterno'} errorClass={cx('form__message-error')}/>
                                </div>
                                <div className="col-sm-5">
                                    <Form.Field className="form-control" placeholder='Apellido Materno'
                                                name='apellidoMaterno'
                                                ref={ref=>this.refApellidoMaterno=ref}
                                                onKeyPress={this.siguienteComponente.bind(this, 'refFechaNacimiento')}
                                    />
                                    <Form.Message for={'apellidoMaterno'} errorClass={cx('form__message-error')}/>
                                </div>
                            </div>

                            {/* Fecha Nacimiento */}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-2 control-label">Fecha Nacimiento (*)</label>
                                <div className="col-sm-5">
                                    {/*<input type="date"/>*/}
                                    <Form.Field type='date2' time={false} errorClass={cx('form__field--error')} placeholder='Ej. 31-07-2016'
                                                name='fechaNacimiento'
                                                format='D-M-YYYY'
                                                editFormat='D-M-YYYY'
                                                ref={ref=>this.refFechaNacimiento=ref}
                                                onKeyPress={this.siguienteComponente.bind(this, 'refTelefono')}
                                    />
                                    <Form.Message for={'fechaNacimiento'} errorClass={cx('form__message-error')}/>
                                </div>
                            </div>

                            <h4>Contacto</h4>
                            {/* Telefono */}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-2 control-label">Telefono</label>
                                <div className="col-sm-5">
                                    <Form.Field className="form-control" placeholder='Telefono'
                                                name='telefono'
                                                ref={ref=>this.refTelefono=ref}
                                                onKeyPress={this.siguienteComponente.bind(this, 'refTelefonoEmergencia')}
                                    />
                                    <Form.Message for={'telefono'} errorClass={cx('form__message-error')}/>
                                </div>
                                <div className="col-sm-5">
                                    <Form.Field className="form-control" placeholder='Telefono de Emergencia'
                                                name='telefonoEmergencia'
                                                ref={ref=>this.refTelefonoEmergencia=ref}
                                                onKeyPress={this.siguienteComponente.bind(this, 'refEmail')}
                                    />
                                    <Form.Message for={'telefonoEmergencia'} errorClass={cx('form__message-error')}/>
                                </div>
                            </div>
                            {/* Email */}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-2 control-label">Email</label>
                                <div className="col-sm-5">
                                    <Form.Field className="form-control" placeholder='Email SEI'
                                                name='email'
                                                ref={ref=>this.refEmail=ref}
                                                onKeyPress={this.siguienteComponente.bind(this, 'refEmailPersonal')}
                                    />
                                    <Form.Message for={'email'} errorClass={cx('form__message-error')}/>
                                </div>
                                <div className="col-sm-5">
                                    <Form.Field className="form-control" placeholder='Email personal'
                                                name='emailPersonal'
                                                ref={ref=>this.refEmailPersonal=ref}
                                                onKeyPress={this.siguienteComponente.bind(this, 'refComuna')}
                                    />
                                    <Form.Message for={'emailPersonal'} errorClass={cx('form__message-error')}/>
                                </div>
                            </div>
                            {/* Comuna */}
                            <div className={cx("form-group", 'form__control-label')}>
                                {/*
                                 <label className="col-sm-2 control-label">Región</label>
                                 <div className="col-sm-4">
                                 <Form.Field name='cutRegion' type='select'>
                                 <option >Select a color...</option>
                                 <option value={0}>Red</option>
                                 <option value={1}>Yellow</option>
                                 <option value={2}>Blue</option>
                                 <option value={3}>other</option>
                                 </Form.Field>
                                 <Form.Message for={'cutRegion'} errorClass={cx('form__message-error')}/>
                                 </div>
                                 */}

                                <label className="col-sm-2 control-label">Comuna (*)</label>
                                <div className="col-sm-5">
                                    <Form.Field type='dropdownlist' errorClass={cx('form__field--error')}
                                                name='cutComuna'
                                                data={this.props.comunas}
                                                valueField='cutComuna'
                                                textField='nombre'
                                        //groupBy='cutProvincia'
                                                filter='startsWith'
                                                mapValue={{'cutComuna': comuna => comuna.cutComuna}}
                                                duration={10}
                                                ref={ref=>this.refComuna=ref}
                                                onKeyPress={this.siguienteComponente.bind(this, 'refDireccion')}
                                    />
                                    <Form.Message for={'cutComuna'} errorClass={cx('form__message-error')}/>
                                </div>
                            </div>
                            {/* Direccion */}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-2 control-label">Dirección</label>
                                <div className="col-sm-10">
                                    <Form.Field className="form-control" placeholder='Dirección'
                                                name='direccion'
                                                ref={ref=>this.refDireccion=ref}
                                        //onKeyPress={this.siguienteComponente.bind(this, 'refDireccion')}
                                    />
                                    <Form.Message for={'direccion'} errorClass={cx('form__message-error')}/>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-5">
                            <h4>Datos Laborales</h4>
                            {/* Tipo Contrato */}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-4 control-label">Tipo contrato</label>
                                <div className="col-sm-8">
                                    <Form.Field name='tipoContrato' type='combobox'
                                                data={['Plazo', 'Honorario']}
                                                duration={0}
                                    />
                                    <Form.Message for={'tipoContrato'} errorClass={cx('form__message-error')}/>
                                </div>
                            </div>
                            {/* Fecha inicio contrato */}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-4 control-label">Fecha inicio contrato</label>
                                <div className="col-sm-8">

                                </div>
                            </div>

                            <h4>Pagos</h4>
                            {/* Banco */}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-4 control-label">Banco</label>
                                <div className="col-sm-8">
                                    <Form.Field name='banco' type='combobox'
                                                data={listaBancos}
                                                duration={0}
                                    />
                                    <Form.Message for={'banco'} errorClass={cx('form__message-error')}/>
                                </div>
                            </div>
                            {/* Tipo Cuenta */}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-4 control-label">Tipo Cuenta</label>
                                <div className="col-sm-8">
                                    <Form.Field name='tipoCuenta' type='combobox'
                                                data={listaTipoCuentas}
                                                duration={0}
                                    />
                                    <Form.Message for={'tipoCuenta'} errorClass={cx('form__message-error')}/>
                                </div>
                            </div>
                            {/* numero Cuenta*/}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-4 control-label">Numero Cuenta</label>
                                <div className="col-sm-8">
                                    <Form.Field className="form-control" name='numeroCuenta' placeholder='Ej. 123-456-789-9'/>
                                    <Form.Message for={'numeroCuenta'} errorClass={cx('form__message-error')}/>
                                </div>
                            </div>

                            <h4>Otros</h4>
                            {/* Certificado Antecedentes */}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-4 control-label">Certificado Antecedentes</label>
                                <div className="col-sm-8">
                                    <input id="input-1" type="file" className="file" disabled/>
                                </div>
                            </div>
                            {/* Fotografia personal */}
                            <div className={cx("form-group", 'form__control-label')}>
                                <label className="col-sm-4 control-label">Fotografia</label>
                                <div className="col-sm-8">
                                    <input id="input-1" type="file" className="file" disabled/>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div className="row">
                        <div className={cx("col-sm-12", 'form__footer')}>
                            <a className="btn btn-default" onClick={this.props.cancelarFormulario}>Cancelar</a>
                            <Form.Button type='submit' className="btn btn-primary">Modificar</Form.Button>
                        </div>
                    </div>
                </Form>
            </div>
        )
    }
}

FormularioUsuario.propTypes = {
    comunas: PropTypes.arrayOf(PropTypes.object).isRequired,
    enviarFormulario: PropTypes.func.isRequired,
    cancelarFormulario: PropTypes.func.isRequired
}