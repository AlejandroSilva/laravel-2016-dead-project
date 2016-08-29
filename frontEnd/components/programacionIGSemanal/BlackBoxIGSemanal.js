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
    }
    reset() {
        this.lista = []
        this.filtroRegiones = []
        this.filtroComunas = []
        this.filtroLideres = []
        this.filtroCaptadores = []
        this.filtroCeco = []
        this.filtroFechas = []
    }
    // Todo: Optimizar
    // Todo Modificar: el listado de clientes
    add(inventario){
        this.lista.push(inventario)
    }

    // Todo modificar el listado de clientes
    actualizarInventario(inventarioActualizado) {
        this.lista = this.lista.map(inventario=> {
            if (inventario.inv_idInventario == inventarioActualizado.inv_idInventario) {
                return inventarioActualizado
            }
            return inventario
        })
        this.actualizarFiltros()
    }

    /*** ################### FILTROS ################### ***/
    ordenarLista(){
        let porFechaProgramada = (inventario)=> {
            let dateA = new Date(inventario.inv_fechaProgramada)
            if (dateA == 'Invalid Date') {
                let [annoA, mesA, diaA] = inventario.inv_fechaProgramada.split('-')
                // OJO: (new Date('2016-04')) - (new Date('2016-03-31')), resulta en 0, y los ordena mal, por eso se resta 1
                return new Date(`${annoA}-${mesA}`) - 1
            } else {
                return dateA
            }
        }
        let porCliente = (inventario)=> inventario.cliente_idCliente
        let porStockTeorico = (inventario)=> inventario.inv_stockTeorico
        
        // ordenar por fechaprogramada, y por stock
        this.lista = _.orderBy(this.lista, [porFechaProgramada, porCliente, porStockTeorico], ['asc', 'asc', 'desc'])
    }

    actualizarFiltros(){
        // ##### Filtro fechas
        this.filtroFechas = _.chain(this.lista)
            .map(inventario=>{
                let momentFecha = moment(inventario.inv_fechaProgramada)
                let valor = inventario.inv_fechaProgramada
                let texto = momentFecha.isValid()? momentFecha.format('dddd DD MMMM') : `-- ${inventario.inv_fechaProgramada} --`

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
                let valor = inventario.local_cutRegion
                let texto = inventario.local_region

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
                return _.find(this.filtroRegiones, {'valor': inventario.local_cutRegion, 'seleccionado': true})
            })
            .map(inventario=>{
                let valor = inventario.local_cutComuna
                let texto = inventario.local_comuna

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
            .filter( inventario=>(inventario.inv_idJornada=='2' || inventario.inv_idJornada=='4') )
            .map(inventario=>{

                let valor = inventario.ndia_idLider
                let texto = inventario.ndia_lider       //'-- NO ASIGNADO --'

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroLideres, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .value()
        let lideresNoche = _.chain(this.lista)
            // considerar la nomina de noche solo cuando la jornada sea 'noche (3)' o 'dia y noche (4)'
            .filter( inventario=>(inventario.inv_idJornada=='3' || inventario.inv_idJornada=='4') )
            .map(inventario=>{
                let valor = inventario.nnoche_idLider
                let texto = inventario.nnoche_lider     //  '-- NO ASIGNADO --'

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
            .filter( inventario=>(inventario.inv_idJornada=='2' || inventario.inv_idJornada=='4') )
            .map(inventario=>{
                let valor = inventario.ndia_idCaptador1
                let texto = inventario.ndia_captador1       //'-- NO ASIGNADO --'

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroCaptadores, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .value()
        let captadoresNoche = _.chain(this.lista)
            // considerar la nomina de noche solo cuando la jornada sea 'noche (3)' o 'dia y noche (4)'
            .filter( inventario=>(inventario.inv_idJornada=='3' || inventario.inv_idJornada=='4') )
            .map(inventario=>{
                let valor = inventario.nnoche_idCaptador1
                let texto = inventario.nnoche_captador1     // '-- NO ASIGNADO --'

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

        // ##### Filtro Numero de Local
        this.filtroCeco = _.chain(this.lista)
            .map(auditoria=>{
                let valor = ''+auditoria.local_ceco
                let texto = ''+auditoria.local_ceco // debe ser un string

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
                    return _.find(this.filtroCeco, {'valor': ''+inventario.local_ceco, 'seleccionado': true})
                })
                // Filtrar por Regiones
                .filter(inventario=>{
                    return _.find(this.filtroRegiones, {'valor': inventario.local_cutRegion, 'seleccionado': true})
                })
                // Filtrar por Comunas
                .filter(inventario=>{
                    return _.find(this.filtroComunas, {'valor': inventario.local_cutComuna, 'seleccionado': true})
                })
                // Filtrar por Lideres
                .filter(inventario=>{
                    if(inventario.inv_idJornada=='1'){
                        // la jornada no ha sido seleccionada, no hay ninguna nomina activa, entonces no hay lider seleccionado
                        return true
                    }else if(inventario.inv_idJornada=='2'){
                        // si la jornada es de dia, revisar si el lider esta en la nomina_dia
                        return _.find(this.filtroLideres, {'valor': inventario.ndia_idLider, 'seleccionado': true})
                    }else if(inventario.inv_idJornada=='3'){
                        // si la jornada es 'noche', se debe buscar el lider en la nomina_noche
                        return _.find(this.filtroLideres, {'valor': inventario.nnoche_idLider, 'seleccionado': true})
                    }else if(inventario.inv_idJornada=='4'){
                        // si la jornada es 'dia y noche', se debe buscar el lider en la nomina_dia y nomina_noche
                        return _.find(this.filtroLideres, {'valor': inventario.ndia_idLider, 'seleccionado': true})
                        || _.find(this.filtroLideres, {'valor': inventario.nnoche_idLider, 'seleccionado': true})
                    }
                })
                // Filtrar por Captadores
                .filter(inventario=>{
                    if(inventario.inv_idJornada=='1'){
                        // la jornada no ha sido seleccionada, no hay ninguna nomina activa, entonces no hay lider seleccionado
                        return true
                    }else if(inventario.inv_idJornada=='2'){
                        // si la jornada es de dia, revisar si el lider esta en la nomina_dia
                        return _.find(this.filtroCaptadores, {'valor': inventario.ndia_idCaptador1, 'seleccionado': true})
                    }else if(inventario.inv_idJornada=='3'){
                        // si la jornada es 'noche', se debe buscar el lider en la nomina_noche
                        return _.find(this.filtroCaptadores, {'valor': inventario.nnoche_idCaptador1, 'seleccionado': true})
                    }else if(inventario.inv_idJornada=='4'){
                        // si la jornada es 'dia y noche', se debe buscar el lider en la nomina_dia y nomina_noche
                        return _.find(this.filtroCaptadores, {'valor': inventario.ndia_idCaptador1, 'seleccionado': true})
                            || _.find(this.filtroCaptadores, {'valor': inventario.nnoche_idCaptador1, 'seleccionado': true})
                    }
                })
                // Filtrar por Fecha (dejar de lo ultimo, ya que es la mas lenta -comparacion de strings-)
                .filter(inventario=>{
                    return _.find(this.filtroFechas, {'valor': inventario.inv_fechaProgramada, 'seleccionado': true})
                })
                .value(),
            filtros: {
                filtroRegiones: this.filtroRegiones,
                filtroComunas: this.filtroComunas,
                filtroLideres: this.filtroLideres,
                filtroCaptadores: this.filtroCaptadores,
                filtroCeco: this.filtroCeco,
                filtroFechas: this.filtroFechas
            }
        }
    }
}