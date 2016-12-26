// Librerias
import React from 'react'
import { AutoSizer, Table, Column } from 'react-virtualized'
// Componentes
import Modal from 'react-bootstrap/lib/Modal.js'
// Style
import classNames from 'classnames/bind'
import * as cssModal from './modal.css'
import * as cssTabla from './mantenedorLocal.css'
let cx = classNames.bind(cssModal)

// function as a children FTW! :D
export class ModalTrigger extends React.Component{
    constructor(props){
        super(props)
        this.state = { visible: false }
        this.show  = ()=> this.setState({visible: true})
        this.hide  = ()=> this.setState({visible: false})
    }
    render(){
        return this.props.children(this.state.visible, this.show, this.hide)
    }
}

export class ModalAgregarLocal extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            local: {
                idCliente: this.props.idCliente,
                nombre: '',
                numero: '',
                idFormatoLocal: 4,
                idJornadaSugerida: 1,
                horaApertura: '00:00',
                horaCierre: '00:00',
                emailContacto: '',
                telefono1: '',
                telefono2: '',
                stock: 1,
                cutComuna: 13101,
                direccion: ''
            },
            error: {}
        }
        this.onInputChange = (key, evt)=>{
            let actualizado = {}
            actualizado[key] = evt.target.value
            this.setState({ local: Object.assign({}, this.state.local, actualizado)})
        }
        this.handleSubmit = (evt)=>{
            evt.preventDefault()
            evt.stopPropagation()
            this.setState({error: {}}, ()=>{
                this.props.agregarLocal(this.state.local)
                    .then(()=>
                        this.props.hideModal()
                    )
                    .catch(error=>{
                        this.setState({error: error.data})
                    })
            })
        }
    }
    render() {
        return (
            <Modal show={true} onHide={this.props.hideModal}>
                <Modal.Header closeButton>
                    <Modal.Title>Nuevo Local</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <form className="form-horizontal" action="" onSubmit={this.handleSubmit}>
                        {/* Numero */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Numero</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control" placeholder="CECO"
                                       value={this.state.local.numero} onChange={this.onInputChange.bind(this, 'numero')}
                                />
                                {this.state.error.numero && <span className={cx('form__error-message')}>{this.state.error.numero[0]}</span>}
                            </div>
                        </div>
                        {/* Nombre */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Nombre</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control" placeholder="Nombre de local"
                                       value={this.state.local.nombre} onChange={this.onInputChange.bind(this, 'nombre')}
                                />
                                {this.state.error.nombre && <span className={cx('form__error-message')}>{this.state.error.nombre[0]}</span>}
                            </div>
                        </div>
                        {/* Formato */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Formato</label>
                            <div className="col-sm-5">
                                <select className="form-control"
                                        value={this.state.local.idFormatoLocal}
                                        onChange={this.onInputChange.bind(this, 'idFormatoLocal')}
                                >
                                    {this.props.formatoLocales.map(formato=>
                                        <option key={formato.idFormatoLocal} value={formato.idFormatoLocal}>{formato.nombre}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                        {/* Jornada */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Jornada</label>
                            <div className="col-sm-5">
                                <select className="form-control"
                                        value={this.state.local.idJornadaSugerida}
                                        onChange={this.onInputChange.bind(this, 'idJornadaSugerida')}
                                >
                                    {this.props.jornadas.map(jornada=>
                                        <option key={jornada.idJornada} value={jornada.idJornada}>{jornada.nombre}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                        {/* Hora Apertura */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Hora Apertura</label>
                            <div className="col-sm-5">
                                <input type="time" className="form-control"
                                       value={this.state.local.horaApertura} onChange={this.onInputChange.bind(this, 'horaApertura')}
                                />
                                {this.state.error.horaApertura && <span className={cx('form__error-message')}>{this.state.error.horaApertura[0]}</span>}
                            </div>
                        </div>
                        {/* Hora Cierre */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Hora Cierre</label>
                            <div className="col-sm-5">
                                <input type="time" className="form-control"
                                       value={this.state.local.horaCierre} onChange={this.onInputChange.bind(this, 'horaCierre')}
                                />
                                {this.state.error.horaCierre && <span className={cx('form__error-message')}>{this.state.error.horaCierre[0]}</span>}
                            </div>
                        </div>
                        {/* Email */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Email</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.emailContacto} onChange={this.onInputChange.bind(this, 'emailContacto')}
                                />
                                {this.state.error.emailContacto && <span className={cx('form__error-message')}>{this.state.error.emailContacto[0]}</span>}
                            </div>
                        </div>
                        {/* Telefono */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Telefonos</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.telefono1} onChange={this.onInputChange.bind(this, 'telefono1')}
                                />
                                {this.state.error.telefono1 && <span className={cx('form__error-message')}>{this.state.error.telefono1[0]}</span>}
                            </div>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.telefono2} onChange={this.onInputChange.bind(this, 'telefono2')}
                                />
                                {this.state.error.telefono2 && <span className={cx('form__error-message')}>{this.state.error.telefono2[0]}</span>}
                            </div>
                        </div>
                        {/* Stock */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Stock</label>
                            <div className="col-sm-5">
                                <input type="number" className="form-control"
                                       value={this.state.local.stock} onChange={this.onInputChange.bind(this, 'stock')}
                                />
                                {this.state.error.stock && <span className={cx('form__error-message')}>{this.state.error.stock[0]}</span>}
                            </div>
                        </div>
                        {/* Comuna */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Comuna</label>
                            <div className="col-sm-5">
                                <select className="form-control"
                                        value={this.state.local.cutComuna}
                                        onChange={this.onInputChange.bind(this, 'cutComuna')}
                                >
                                    {this.props.comunas.map(comuna=>
                                        <option key={comuna.cutComuna} value={comuna.cutComuna}>{comuna.nombre}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                        {/* Dirección */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Dirección</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.direccion} onChange={this.onInputChange.bind(this, 'direccion')}
                                />
                                {this.state.error.direccion && <span className={cx('form__error-message')}>{this.state.error.direccion[0]}</span>}
                            </div>
                        </div>

                        <div className={cx('form-group', 'form__control-label')}>
                            <a className={cx("btn btn-block btn-default", 'btn-50-perc')} href="#" onClick={this.props.hideModal}>Cancelar</a>
                            <button className={cx("btn btn-block btn-primary", 'btn-50-perc')} type="submit">Actualizar</button>
                        </div>
                    </form>
                </Modal.Body>
            </Modal>
        )
    }
}

export class ModalEditarLocal extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            local: this.props.local,
            error: {}
        }
        this.onInputChange = (key, evt)=>{
            let actualizado = {}
            actualizado[key] = evt.target.value
            this.setState({ local: Object.assign({}, this.state.local, actualizado)})
        }
        this.handleSubmit = (evt)=>{
            evt.preventDefault()
            evt.stopPropagation()
            this.setState({error: {}}, ()=>{
                this.props.actualizarLocal(this.state.local)
                    .then(()=>
                        this.props.hideModal()
                    )
                    .catch(error=>{
                        this.setState({error: error.data})
                    })
            })
        }
    }
    render(){
        return (
            <Modal show={true} onHide={this.props.hideModal}>
                <Modal.Header closeButton>
                    <Modal.Title>Editando local: {this.props.local.cliente.nombreCorto} {this.props.local.numero}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <form className="form-horizontal" action="" onSubmit={this.handleSubmit}>
                        {/* Nombre */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Nombre</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control" placeholder="Nombre de local"
                                       value={this.state.local.nombre} onChange={this.onInputChange.bind(this, 'nombre')}
                                />
                                {this.state.error.nombre && <span className={cx('form__error-message')}>{this.state.error.nombre[0]}</span>}
                            </div>
                        </div>
                        {/* Formato */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Formato</label>
                            <div className="col-sm-5">
                                <select className="form-control"
                                        value={this.state.local.idFormatoLocal}
                                        onChange={this.onInputChange.bind(this, 'idFormatoLocal')}
                                >
                                    {this.props.formatoLocales.map(formato=>
                                        <option key={formato.idFormatoLocal} value={formato.idFormatoLocal}>{formato.nombre}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                        {/* Jornada */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Jornada</label>
                            <div className="col-sm-5">
                                <select className="form-control"
                                        value={this.state.local.idJornadaSugerida}
                                        onChange={this.onInputChange.bind(this, 'idJornadaSugerida')}
                                >
                                    {this.props.jornadas.map(jornada=>
                                        <option key={jornada.idJornada} value={jornada.idJornada}>{jornada.nombre}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                        {/* Hora Apertura */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Hora Apertura</label>
                            <div className="col-sm-5">
                                <input type="time" className="form-control"
                                       value={this.state.local.horaApertura} onChange={this.onInputChange.bind(this, 'horaApertura')}
                                />
                                {this.state.error.horaApertura && <span className={cx('form__error-message')}>{this.state.error.horaApertura[0]}</span>}
                            </div>
                        </div>
                        {/* Hora Cierre */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Hora Cierre</label>
                            <div className="col-sm-5">
                                <input type="time" className="form-control"
                                       value={this.state.local.horaCierre} onChange={this.onInputChange.bind(this, 'horaCierre')}
                                />
                                {this.state.error.horaCierre && <span className={cx('form__error-message')}>{this.state.error.horaCierre[0]}</span>}
                            </div>
                        </div>
                        {/* Email */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Email</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.emailContacto} onChange={this.onInputChange.bind(this, 'emailContacto')}
                                />
                                {this.state.error.emailContacto && <span className={cx('form__error-message')}>{this.state.error.emailContacto[0]}</span>}
                            </div>
                        </div>
                        {/* Telefono */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Telefonos</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.telefono1} onChange={this.onInputChange.bind(this, 'telefono1')}
                                />
                                {this.state.error.telefono1 && <span className={cx('form__error-message')}>{this.state.error.telefono1[0]}</span>}
                            </div>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.telefono2} onChange={this.onInputChange.bind(this, 'telefono2')}
                                />
                                {this.state.error.telefono2 && <span className={cx('form__error-message')}>{this.state.error.telefono2[0]}</span>}
                            </div>
                        </div>
                        {/* Stock */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Stock</label>
                            <div className="col-sm-5">
                                <input type="number" className="form-control"
                                       value={this.state.local.stock} onChange={this.onInputChange.bind(this, 'stock')}
                                />
                                {this.state.error.stock && <span className={cx('form__error-message')}>{this.state.error.stock[0]}</span>}
                            </div>
                        </div>
                        {/* Comuna */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Comuna</label>
                            <div className="col-sm-5">
                                <select className="form-control"
                                        value={this.state.local.cutComuna}
                                        onChange={this.onInputChange.bind(this, 'cutComuna')}
                                >
                                    {this.props.comunas.map(comuna=>
                                        <option key={comuna.cutComuna} value={comuna.cutComuna}>{comuna.nombre}</option>
                                    )}
                                </select>
                            </div>
                        </div>
                        {/* Dirección */}
                        <div className={cx('form-group', 'form__control-label')}>
                            <label className="col-sm-2 control-label">Dirección</label>
                            <div className="col-sm-5">
                                <input type="text" className="form-control"
                                       value={this.state.local.direccion} onChange={this.onInputChange.bind(this, 'direccion')}
                                />
                                {this.state.error.direccion && <span className={cx('form__error-message')}>{this.state.error.direccion[0]}</span>}
                            </div>
                        </div>

                        <div className={cx('form-group', 'form__control-label')}>
                            <a className={cx("btn btn-block btn-default", 'btn-50-perc')} href="#" onClick={this.props.hideModal}>Cancelar</a>
                            <button className={cx("btn btn-block btn-primary", 'btn-50-perc')} type="submit">Actualizar</button>
                        </div>
                    </form>
                </Modal.Body>
            </Modal>
        )
    }
}

export class ModalPegarLocales extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            locales: [],
            todosValidos: false,
            errores: ''
        }
        this.onPaste = (evt)=>{
            evt.preventDefault()
            evt.clipboardData.items[0].getAsString(texto=>{
                const getTime = ((string='00:00')=>(
                    /^[0-9]{2}:[0-9]{2}:[0-9]{2}$/.test(string) ?
                        string.replace(/:[0-9]{2}$/, '')
                        :
                        ( /^[0-9]{2}:[0-9]{2}$/.test(string) ? string : '00:00' )
                ))
                let locales = texto.trim()
                    .split('\n')
                    .filter(row=>row!=='')
                    .map(row=> row.trim().split('\t'))
                    .map(row => {
                        return {
                            idCliente: this.props.idCliente,
                            numero:             row[0] || '',
                            nombre:             row[1] || '',
                            idJornadaSugerida:  row[2] || 3,
                            jornada:            this.props.jornadas.find(jor=>jor.idJornada==(row[2]||3)),
                            idFormatoLocal:     4,
                            horaApertura:       getTime(row[3]),
                            horaCierre:         getTime(row[4]),
                            emailContacto:      row[5] || '',
                            telefono1:          row[6] || '',
                            telefono2:          row[7] || '',
                            stock:              1,
                            cutComuna:          row[8] || 13101,
                            comuna:             this.props.comunas.find(com=>com.cutComuna==(row[8] || 7301)),
                            direccion:          row[9] || ''
                            }
                    })
                this.setState({
                    locales,
                    todosValidos: locales.filter(local=> (!local.jornada || !local.comuna) ).length==0 && locales.length>0,
                    errores: ''
                })
            })
        }

        this.agregarLocales = ()=>{
            this.setState({
                errores: ''
            },()=>{
                this.props.agregarLocales(this.state.locales)
                    .then(this.props.hideModal)
                    .catch(err=>{
                        this.setState({
                            errores: err.data.join('. ')
                        })
                    })
            })
        }
    }
    componentDidMount(){
        this.refInputPaste.focus()
    }
    render(){
        let {locales} = this.state
        return (
            <Modal show={true} onHide={this.props.hideModal} dialogClassName={cx('modalPegarLocales')} >
                <Modal.Header closeButton>
                    <Modal.Title>
                        <small className="col-md-8">Pegar las columnas: Ceco, Nombre, Jornada, Hora Apertura, Hora Cierre, Email, telefono1, telefono2, CUT Comuna y Direccion desde excel</small>
                        <input className="col-md-3" type="text" value='' onPaste={this.onPaste} ref={ref=>this.refInputPaste=ref}/>
                    </Modal.Title>
                </Modal.Header>
                <Modal.Body className={cx('tabla-pegar-locales')}>
                    <AutoSizer>
                        {({height, width}) =>
                            <Table
                                    // general
                                    height={height}
                                    width={width}
                                    // headers
                                    headerHeight={30}
                                    headerClassName={cssTabla.headerColumn}
                                    // rows
                                    rowCount={locales.length}
                                    rowGetter={({index})=> locales[index]}
                                    rowHeight={40}
                                    rowClassName={({index})=> index<0? cssTabla.headerRow : ((index%2===0)? cssTabla.evenRow : cssTabla.oddRow)}
                                >
                                    {/* Cliente */}
                                    {/* Ceco */}
                                    <Column
                                        dataKey='numero' label="CECO"
                                        cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                                        width={40}
                                    />
                                    {/* Nombre */}
                                    <Column
                                        dataKey='nombre' label="Nombre"
                                        cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                                        width={120}
                                    />
                                    {/* Jornada */}
                                    <Column
                                        dataKey='idJornadaSugerida' label="Jornada"
                                        cellRenderer={({ dataKey, rowData }) =>
                                            rowData.jornada? rowData.jornada.nombre : <span className="label label-danger">INVALIDA</span>
                                        }
                                        width={70}
                                    />
                                    {/* Hora Apertura */}
                                    <Column
                                        dataKey='horaApertura' label="Apertura"
                                        cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                                        width={70}
                                    />
                                    {/* Hora Cierre */}
                                    <Column
                                        dataKey='horaCierre' label="Cierre"
                                        cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                                        width={50}
                                    />
                                    {/* Email */}
                                    <Column
                                        dataKey='emailContacto' label="Email"
                                        cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                                        width={100}
                                    />
                                    {/* Telefono 1 */}
                                    <Column
                                        dataKey='telefono1' label="Telefono1"
                                        cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                                        width={85}
                                    />
                                    {/* Telefono 2 */}
                                    <Column
                                        dataKey='telefono2' label="Telefono2"
                                        cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                                        width={85}
                                    />
                                    {/* Comuna */}
                                    <Column
                                        dataKey='cutComuna' label="CUT Comuna"
                                        cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) =>
                                            rowData.comuna? rowData.comuna.nombre : <span className="label label-danger">INVALIDA</span>
                                        }
                                        width={100}
                                    />
                                    {/* Direccion */}
                                    <Column
                                        dataKey='direccion' label="Dirección"
                                        cellRenderer={({ cellData, columnData, dataKey, rowData, rowIndex }) => rowData[dataKey] }
                                        width={200}
                                        flexGrow={1}
                                    />
                                </Table>
                        }
                    </AutoSizer>
                </Modal.Body>
                <Modal.Footer>
                    <div className={"col-md-6 "+cssModal.textAlignLeft}>
                        <p>* JORNADA, corresponde a: (1) No definido, (2) Día, (3) Noche, (4) Día y Noche</p>
                        <p>* CUT COMUNA, corresponde a un el Código Unico Territorial. <a href="http://www.ine.cl/canales/chile_estadistico/territorio/division_politico_administrativa/xls/240111/codigoterr.xls">ver códigos</a></p>
                    </div>
                    <div className="col-md-3">
                        {this.state.errores && (<div className="alert alert-danger">{this.state.errores}</div>)}
                    </div>
                    <div className="col-md-3 pull-right">
                        <button className="btn btn-md btn-default"
                                onClick={this.props.hideModal}
                        >Cancelar</button>
                        <button className="btn btn-md btn-primary"
                                disabled={this.state.todosValidos? '' : 'disabled'}
                                onClick={this.agregarLocales}
                        >Agregar</button>
                    </div>
                </Modal.Footer>
            </Modal>
        )
    }
}