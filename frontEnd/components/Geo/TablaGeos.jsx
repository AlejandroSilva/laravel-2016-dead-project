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
                    <Sticky
                        topOffset={-79}
                        type={React.DOM.tr}
                        stickyStyle={{top: '79px'}}>
                            <th className={css.colNumeral}>#</th>
                            <th className={css.colZona}>Nombre</th>
                            <th className={css.colRegion}>Nombre</th>
                            <th className={css.colComunaNombre}>Nombre</th>
                            <th className={css.colComunaLocales}># Locales</th>
                            <th className={css.colGeoNombre}>Nombre</th>
                            <th className={css.colGeoMin}>Min</th>
                            <th className={css.colGeoMax}>Max</th>
                            <th className={css.colSubgeoNombre}>Nombre</th>
                            <th className={css.colSubgeoMin}>Min</th>
                            <th className={css.colSubgeoMax}>Max</th>
                    </Sticky>

                     {/*
                     <Sticky
                         topOffset={-107}
                         type={React.DOM.tr}
                         stickyStyle={{top: '107px'}}>
                             <th colSpan="5" className={css.colUNO}>UNO</th>
                             <th colSpan="3" className={css.colDOS}>DOS</th>
                             <th colSpan="3" className={css.colTRES}>TRES</th>
                     </Sticky>
                     */}
                </thead>
                <tbody>
                    {this.props.comunas.map((comuna, index)=>{
                        return <tr key={index}>
                            <td>{index+1}</td>
                            {/* ZONA */}
                            <td>{comuna.zona}</td>
                            {/* REGION */}
                            <td>{comuna.region}</td>
                            {/* COMUNA */}
                            <td>{comuna.comuna}</td>
                            <td>{comuna.totalLocales}</td>
                            {/* GEO */}
                            <td>{comuna.geo}</td>
                            <td>Min</td>
                            <td>Max</td>
                            {/* SUB GEO*/}
                            <td>{comuna.geo}</td>
                            <td>Min</td>
                            <td>Max</td>
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