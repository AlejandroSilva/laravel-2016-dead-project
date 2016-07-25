import moment from 'moment'
import _ from 'lodash'

export default class BlackBoxIGSemanal{
    constructor(){
        this.lista = []
        // this.filtroClientes = []
        this.filtroRegiones = []
        this.filtroComunas = []
        this.filtroLideres = []
        this.filtroCaptadores = []
        this.filtroCeco = []
        this.filtroFechas = []
        //this.filtroFechaSubidaNomina = []
    }
    reset() {
        this.lista = []
        // this.filtroClientes = []
        this.filtroRegiones = []
        this.filtroComunas = []
        this.filtroLideres = []
        this.filtroCaptadores = []
        this.filtroCeco = []
        this.filtroFechas = []
        //this.filtroFechaSubidaNomina = []
    }
    // Todo: Optimizar
    // Todo Modificar: el listado de clientes
    add(inventario){
        this.lista.push(inventario)
    }

    // Todo modificar el listado de clientes
    actualizarInventario(inventarioActualizado) {
        this.lista = this.lista.map(inventario=> {
            if (inventario.idInventario == inventarioActualizado.idInventario) {
                return inventarioActualizado
            }
            return inventario
        })
        this.actualizarFiltros()
    }

    /*** ################### FILTROS ################### ***/
    ordenarLista(){
        let porFechaProgramada = (inventario)=> {
            let dateA = new Date(inventario.fechaProgramada)
            if (dateA == 'Invalid Date') {
                let [annoA, mesA, diaA] = inventario.fechaProgramada.split('-')
                // OJO: (new Date('2016-04')) - (new Date('2016-03-31')), resulta en 0, y los ordena mal, por eso se resta 1
                return new Date(`${annoA}-${mesA}`) - 1
            } else {
                return dateA
            }
        }
        let porStockTeorico = (inventario)=>{
            return inventario.stockTeorico*1
        }
        
        // ordenar por fechaprogramada, y por stock
        this.lista = _.orderBy(this.lista, [porFechaProgramada, porStockTeorico], ['asc', 'desc'])
    }

    actualizarFiltros(){
        // ##### Filtro fechas
        this.filtroFechas = _.chain(this.lista)
            .map(inventario=>{
                let momentFecha = moment(inventario.fechaProgramada)
                let valor = inventario.fechaProgramada
                let texto = momentFecha.isValid()? momentFecha.format('dddd DD MMMM') : `-- ${inventario.fechaProgramada} --`

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroFechas, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('valor')
            .value()

        // ##### Filtro Regiones
        this.filtroRegiones = _.chain(this.lista)
            .map(inventario=>{
                let valor = inventario.local.direccion.comuna.provincia.region.cutRegion
                let texto = inventario.local.direccion.comuna.provincia.region.numero

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroRegiones, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('valor')    // ordenado por numero de region
            .value()

        // ##### Filtro Comunas (ordenado por codComuna)
        this.filtroComunas = _.chain(this.lista)
            // solo dejar las comunas en las que su respectiva region este seleccionada
            .filter(inventario=>{
                return _.find(this.filtroRegiones, {'valor': inventario.local.direccion.comuna.provincia.region.cutRegion, 'seleccionado': true})
            })
            .map(inventario=>{
                let valor = inventario.local.direccion.cutComuna
                let texto = inventario.local.direccion.comuna.nombre

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroComunas, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('texto')
            .value()

        // ##### Filtro Lideres, considerar el lider solo si la jornada de la nomina se encuentra activa
        let lideresDia = _.chain(this.lista)
            // considerar la nomina de dia solo cuando la jornada sea 'dia (2)' o 'dia y noche (4)'
            .filter( inventario=>(inventario.idJornada=='2' || inventario.idJornada=='4') )
            .map(inventario=>{
                let lider = inventario.nomina_dia.lider
                let valor = inventario.nomina_dia.idLider
                let texto = lider? `${lider.nombre1} ${lider.apellidoPaterno}` : '-- NO ASIGNADO --'

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroLideres, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .value()
        let lideresNoche = _.chain(this.lista)
            // considerar la nomina de noche solo cuando la jornada sea 'noche (3)' o 'dia y noche (4)'
            .filter( inventario=>(inventario.idJornada=='3' || inventario.idJornada=='4') )
            .map(inventario=>{
                let lider = inventario.nomina_noche.lider
                let valor = inventario.nomina_noche.idLider
                let texto = lider? `${lider.nombre1} ${lider.apellidoPaterno}` : '-- NO ASIGNADO --'

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroLideres, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .value()
        // unir ambos arreglos, sacar los repetidos, y ordenar por nombre
        this.filtroLideres = _.chain(lideresDia)
            .unionBy(lideresNoche, 'valor')
            .uniqBy('valor')
            .sortBy('texto')
            .value()

        // ##### Filtro Captador, considerar el lider solo si la jornada de la nomina se encuentra activa
        let captadoresDia = _.chain(this.lista)
            // considerar la nomina de dia solo cuando la jornada sea 'dia (2)' o 'dia y noche (4)'
            .filter( inventario=>(inventario.idJornada=='2' || inventario.idJornada=='4') )
            .map(inventario=>{
                let captador = inventario.nomina_dia.captador
                let valor = inventario.nomina_dia.idCaptador1

                let texto = captador? `${captador.nombre1} ${captador.apellidoPaterno}` : '-- NO ASIGNADO --'

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroCaptadores, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .value()
        let captadoresNoche = _.chain(this.lista)
            // considerar la nomina de noche solo cuando la jornada sea 'noche (3)' o 'dia y noche (4)'
            .filter( inventario=>(inventario.idJornada=='3' || inventario.idJornada=='4') )
            .map(inventario=>{
                let captador = inventario.nomina_noche.captador
                let valor = inventario.nomina_noche.idCaptador1
                let texto = captador? `${captador.nombre1} ${captador.apellidoPaterno}` : '-- NO ASIGNADO --'

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroCaptadores, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .value()
        // unir ambos arreglos, sacar los repetidos, y ordenar por nombre
        this.filtroCaptadores = _.chain(captadoresDia)
            .unionBy(captadoresNoche, 'valor')
            .uniqBy('valor')
            .sortBy('texto')
            .value()

        // this.filtroFechaSubidaNomina = _.chain(this.lista)
        //     .map(inventario=>{
        //         let valor = inventario.nomina_dia.fechaSubidaNomina
        //         let texto = valor=='0000-00-00'? `-- PENDIENTE --` : moment(valor).format('dddd DD MMMM')
        //
        //         // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
        //         let opcion = _.find(this.filtroFechaSubidaNomina, {'valor': valor})
        //         return opcion? opcion : {valor, texto, seleccionado: true}
        //     })
        //     .uniqBy('valor')
        //     .sortBy('valor')
        //     .value()

        // ##### Filtro Numero de Local
        this.filtroCeco = _.chain(this.lista)
            .map(auditoria=>{
                let valor = ''+auditoria.local.numero
                let texto = ''+auditoria.local.numero // debe ser un string

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroCeco, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy(op=>op.valor.length, 'valor') // ordenar primero los numeros de dos digitos, luego los de 3 digitos...
            .value()
    }
    reemplazarFiltro(nombreFiltro, filtroActualizado) {
        if(this[nombreFiltro]) {
            this[nombreFiltro] = filtroActualizado
        }else{
            console.error(`Filtro ${nombreFiltro} no existe.`)
        }
    }

    getListaFiltrada(){
        this.actualizarFiltros()

        return {
            inventariosFiltrados: _.chain(this.lista)
                // Filtrar por Ceco
                .filter(inventario=>{
                    return _.find(this.filtroCeco, {'valor': ''+inventario.local.numero, 'seleccionado': true})
                })
                // Filtrar por Regiones
                .filter(inventario=>{
                    return _.find(this.filtroRegiones, {'valor': inventario.local.direccion.comuna.provincia.region.cutRegion, 'seleccionado': true})
                })
                // Filtrar por Comunas
                .filter(inventario=>{
                    return _.find(this.filtroComunas, {'valor': inventario.local.direccion.cutComuna, 'seleccionado': true})
                })
                // Filtrar por Lideres
                .filter(inventario=>{
                    if(inventario.idJornada=='1'){
                        // la jornada no ha sido seleccionada, no hay ninguna nomina activa, entonces no hay lider seleccionado
                        return true
                    }else if(inventario.idJornada=='2'){
                        // si la jornada es de dia, revisar si el lider esta en la nomina_dia
                        return _.find(this.filtroLideres, {'valor': inventario.nomina_dia.idLider, 'seleccionado': true})
                    }else if(inventario.idJornada=='3'){
                        // si la jornada es 'noche', se debe buscar el lider en la nomina_noche
                        return _.find(this.filtroLideres, {'valor': inventario.nomina_noche.idLider, 'seleccionado': true})
                    }else if(inventario.idJornada=='4'){
                        // si la jornada es 'dia y noche', se debe buscar el lider en la nomina_dia y nomina_noche
                        return _.find(this.filtroLideres, {'valor': inventario.nomina_dia.idLider, 'seleccionado': true})
                        || _.find(this.filtroLideres, {'valor': inventario.nomina_noche.idLider, 'seleccionado': true})
                    }
                })
                // Filtrar por Captadores
                .filter(inventario=>{
                    if(inventario.idJornada=='1'){
                        // la jornada no ha sido seleccionada, no hay ninguna nomina activa, entonces no hay lider seleccionado
                        return true
                    }else if(inventario.idJornada=='2'){
                        // si la jornada es de dia, revisar si el lider esta en la nomina_dia
                        return _.find(this.filtroCaptadores, {'valor': inventario.nomina_dia.idCaptador1, 'seleccionado': true})
                    }else if(inventario.idJornada=='3'){
                        // si la jornada es 'noche', se debe buscar el lider en la nomina_noche
                        return _.find(this.filtroCaptadores, {'valor': inventario.nomina_noche.idCaptador1, 'seleccionado': true})
                    }else if(inventario.idJornada=='4'){
                        // si la jornada es 'dia y noche', se debe buscar el lider en la nomina_dia y nomina_noche
                        return _.find(this.filtroCaptadores, {'valor': inventario.nomina_dia.idCaptador1, 'seleccionado': true})
                            || _.find(this.filtroCaptadores, {'valor': inventario.nomina_noche.idCaptador1, 'seleccionado': true})
                    }
                })
                // Filtrar por Fecha (dejar de lo ultimo, ya que es la mas lenta -comparacion de strings-)
                .filter(inventario=>{
                    return _.find(this.filtroFechas, {'valor': inventario.fechaProgramada, 'seleccionado': true})
                })
                // Filtrar por Fecha subida nomina (ya no se ocupa)
                // .filter(inventario=>{
                //     return _.find(this.filtroFechaSubidaNomina, {'valor': inventario.nomina_dia.fechaSubidaNomina, 'seleccionado': true})
                // })
                .value(),
            filtros: {
                filtroRegiones: this.filtroRegiones,
                filtroComunas: this.filtroComunas,
                filtroLideres: this.filtroLideres,
                filtroCaptadores: this.filtroCaptadores,
                filtroCeco: this.filtroCeco,
                filtroFechas: this.filtroFechas
                // filtroFechaSubidaNomina: this.filtroFechaSubidaNomina
            }
        }
    }
}