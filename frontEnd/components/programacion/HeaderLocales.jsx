//import React from 'react'
//let PropTypes = React.PropTypes
//
//// Componentes
//import Sticky from '../shared/react-sticky/sticky.js'
//import Cabecera from './Cabecera.jsx'
//
//// Styles
//import styles from './TablaLocalesMensual.css'
//
//class HeaderLocales extends React.Component{
//    render(){
//
//        return(
//            <Sticky
//                topOffset={-50}
//                type={React.DOM.tr}
//                stickyStyle={{top: '50px'}}
//            >
//                <th className={styles.thCorrelativo}>#</th>
//                <th className={styles.thFecha}>Fecha</th>
//                <th className={styles.thCliente}>Cliente</th>
//                <th className={styles.thCeco}>Ceco</th>
//                <th className={styles.thLocal}>Local</th>
//                <th className={styles.thZonaSei}>Zona SEI</th>
//                <th className={styles.thRegion}>
//                    <Cabecera
//                        nombre="Región"
//                        opciones={this.props.zonas}
//                        onAceptarFiltro={opciones=>{
//                            console.log('se aceptaro: ', opciones)
//                        }}
//                    />
//                </th>
//                <th className={styles.thComuna}>Comuna</th>
//                <th className={styles.thStock}>Stock</th>
//                <th className={styles.thDotacion}>Dotación</th>
//                <th className={styles.thJornada}>Jornada</th>
//                <th className={styles.thEstado}>Estado</th>
//                <th className={styles.thOpciones}>Opciones</th>
//            </Sticky>
//        )
//    }
//}
//HeaderLocales.propTypes = {
//    zonas: PropTypes.arrayOf(PropTypes.string).isRequired
//}
//
//export default HeaderLocales