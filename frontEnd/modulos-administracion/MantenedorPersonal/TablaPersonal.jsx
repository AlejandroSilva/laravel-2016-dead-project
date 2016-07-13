// Librerias
import React from 'react'
let PropTypes = React.PropTypes
// Componentes
import { Table, Column, Cell } from 'fixed-data-table'
import TouchExampleWrapper from '../../shared/TouchExampleWrapper.jsx'
// Styles
import classNames from 'classnames/bind'
import * as css from './tablaPersonal.css'
let cx = classNames.bind(css)

export class TablaPersonal extends React.Component {
    render(){
        return (
            <TouchExampleWrapper
                tableWidth={1300}
                tableHeight={600}
            >
                <Table
                    // table
                    width={1400}
                    height={600}
                    // header
                    headerHeight={30}
                    // rows
                    rowHeight={42}
                    rowsCount={this.props.usuarios.length}
                >

                    <Column
                        header={<Cell>#</Cell>}
                        cell={({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {rowIndex+1}
                            </Cell>}
                        width={30}
                        fixed={true}
                    />
                    <Column
                        header={<Cell>RUN</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell', 'cell-run')} style={{padding:0}}>
                                {this.props.usuarios[rowIndex].RUN}
                            </Cell> }
                        width={80}
                        fixed={true}
                    />
                    <Column
                        header={<Cell>Nombres</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                <p style={{margin: 0}}>{this.props.usuarios[rowIndex].nombre1}</p>
                                <p style={{margin: 0}}>{this.props.usuarios[rowIndex].nombre2}</p>
                            </Cell> }
                        width={150}
                    />
                    <Column
                        header={<Cell>Apellidos</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                <p style={{margin: 0}}>{this.props.usuarios[rowIndex].apellidoPaterno}</p>
                                <p style={{margin: 0}}>{this.props.usuarios[rowIndex].apellidoMaterno}</p>
                            </Cell>}
                        width={150}
                    />
                    <Column
                        header={<Cell>F.Nac</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.usuarios[rowIndex].fechaNacimiento}
                            </Cell> }
                        width={80}
                    />
                    <Column
                        header={<Cell>Comuna</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.usuarios[rowIndex].comuna}
                            </Cell> }
                        width={80}
                    />
                    <Column
                        header={<Cell>Email</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.usuarios[rowIndex].email}
                            </Cell> }
                        width={130}
                    />
                    <Column
                        header={<Cell>Telefono</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell', 'cell-telefono')}>
                                {this.props.usuarios[rowIndex].telefono}
                            </Cell> }
                        width={80}
                    />
                    <Column
                        header={<Cell>Bloqueado</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                {this.props.usuarios[rowIndex].bloqueado? 'bloqueado' : ''}
                            </Cell> }
                        width={80}
                    />
                    <Column
                        header={<Cell>Documentos</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell', 'cell-documentos')}>
                                <button className="btn btn-xs btn-default">C.ID</button>
                                <button className="btn btn-xs btn-default">DOM</button>
                                <button className="btn btn-xs btn-default">C.NAC</button>
                                <button className="btn btn-xs btn-default">C.ANT</button>
                                <button className="btn btn-xs btn-default">CONT</button>
                                {/*
                                <button className="btn btn-xs btn-default">C.Ident.</button>
                                <button className="btn btn-xs btn-default">Domic</button>
                                <button className="btn btn-xs btn-default">C.Nac.</button>
                                <button className="btn btn-xs btn-default">Cert.Antece.</button>
                                <button className="btn btn-xs btn-default">Contrato</button>
                                */}
                            </Cell> }
                        width={225}
                    />
                    <Column
                        header={<Cell>Opciones</Cell>}
                        cell={ ({rowIndex})=>
                            <Cell className={cx('cell')}>
                                <button className="btn btn-xs btn-primary"
                                        onClick={this.props.seleccionarUsuario.bind(this, this.props.usuarios[rowIndex].id)}
                                >editar</button>
                                <button className="btn btn-xs btn-primary">bloquear</button>
                                <button className="btn btn-xs btn-primary">contraseña</button>
                            </Cell> }
                        width={200}
                    />
                </Table>
            </TouchExampleWrapper>
        )
    }
}

TablaPersonal.propTypes = {
    // numero: PropTypes.number.isRequired,
    // texto: PropTypes.string.isRequired,
    // objeto: PropTypes.object.isRequired,
    usuarios: PropTypes.arrayOf(PropTypes.object).isRequired,
    // Metodos
    seleccionarUsuario: PropTypes.func.isRequired,
}