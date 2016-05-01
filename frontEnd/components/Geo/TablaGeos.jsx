// Libs
import React from 'react'
// Component
import Sticky from '../shared/react-sticky/sticky.js'
import StickyContainer from '../shared/react-sticky/container.js'
import * as css from './TablaGeos.css'

export class TablaGeos extends React.Component{
    // constructor(props) {
    //     super(props)
    // }

    render(){
        return (
            <StickyContainer type={React.DOM.table}  className={"table table-bordered table-condensed table-hover "+css.tablaGeos}>
                <colgroup>
                    <col className={css.colNumeral}/>
                    <col className={css.colZona}/>
                    <col className={css.colRegion}/>
                    <col className={css.colComunaNombre}/>
                    <col className={css.colComunaLocales}/>
                    <col className={css.colGeoNombre}/>
                    <col className={css.colGeoMin}/>
                    <col className={css.colGeoMax}/>
                    <col className={css.colSubgeoNombre}/>
                    <col className={css.colSubgeoMin}/>
                    <col className={css.colSubgeoMax}/>
                </colgroup>
                <thead>
                {/*
                    <Sticky
                        topOffset={-50}
                        type={React.DOM.tr}
                        stickyStyle={{top: '50px'}}>
                            <th colSpan="1" className={css.colNumeral}></th>
                            <th colSpan="1" className={css.colZona}>Zona</th>
                            <th colSpan="1" className={css.colRegion}>Región</th>
                            <th colSpan="2" className={css.colComuna}>Comuna</th>
                            <th colSpan="3" className={css.colGeo}>Geo</th>
                            <th colSpan="3" className={css.colSubgeo}>Sub-geo</th>
                    </Sticky>
                 */}
                    <Sticky
                        topOffset={-79}
                        type={React.DOM.tr}
                        stickyStyle={{top: '79px'}}>
                            <th className={css.colNumeral}>#</th>
                            {/* ZONA */}
                            <th className={css.colZona}>Nombre</th>
                            {/* REGION */}
                            <th className={css.colRegion}>Nombre</th>
                            {/* COMUNA */}
                            <th className={css.colComunaNombre}>Nombre</th>
                            <th className={css.colComunaLocales}># Locales</th>
                            {/* GEO */}
                            <th className={css.colGeoNombre}>Nombre</th>
                            <th className={css.colGeoMin}>Min</th>
                            <th className={css.colGeoMax}>Max</th>
                            {/* SUB GEO*/}
                            <th className={css.colSubgeoNombre}>Nombre</th>
                            <th className={css.colSubgeoMin}>Min</th>
                            <th className={css.colSubgeoMax}>Max</th>
                    </Sticky>
                </thead>
                <tbody>
                    {this.props.comunas.map((comuna, index)=>{
                        return <tr key={index}>
                            <td className={css.colNumeral}>
                                {index+1}
                            </td>
                            {/* ZONA */}
                            <td className={css.colZona}>
                                {comuna.zona}
                            </td>
                            {/* REGION */}
                            <td className={css.colRegion}>
                                {comuna.region}
                            </td>
                            {/* COMUNA */}
                            <td className={css.colComunaNombre}>
                                {comuna.comuna}
                            </td>
                            <td className={css.colComunaLocales}>
                                {comuna.totalLocales}
                            </td>
                            {/* GEO */}
                            <td className={css.colGeoNombre}>
                                {comuna.geo}
                            </td>
                            <td className={css.colGeoMin}>
                                Min
                            </td>
                            <td className={css.colGeoMax}>
                                Max
                            </td>
                            {/* SUB GEO*/}
                            <td className={css.colSubgeoNombre}>
                                {comuna.geo}
                            </td>
                            <td className={css.colSubgeoMin}>
                                Min
                            </td>
                            <td className={css.colSubgeoMax}>
                                Max
                            </td>
                        </tr>
                    })}
                </tbody>
            </StickyContainer>
        )
    }
}

TablaGeos.propTypes = {
    comunas: React.PropTypes.array.isRequired,
}