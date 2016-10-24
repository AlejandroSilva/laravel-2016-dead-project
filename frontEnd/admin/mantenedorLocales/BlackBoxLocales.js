import _ from 'lodash'

export default class BlackBoxLocales{
    constructor(){
        this.lista = []
        // Filtros
        this.filtroCliente = []
        this.filtroFormatoLocal = []
        this.filtroCeco = []
        this.filtroNombre = []
        this.filtroComuna = []
    }
    reset() {
        this.lista = []
    }
    add(local){
        this.lista.push(local)
    }
    remove(idLocal){
        let index = this.lista.findIndex(local=>local.idLocal===idLocal)
        if(index>=0) this.lista.splice(index, 1)
    }
    actualizar(localActualizado){
        this.lista = this.lista.map(local=> {
            if (local.idLocal == localActualizado.idLocal) {
                return localActualizado
            }
            return local
        })
    }
    /*** ################### FILTROS ################### ***/

    ordenarLista(){
        console.error('ordenar locales pendientes')
    }

    actualizarFiltros(){
        // ##### Filtro Cliente
        this.filtroCliente = _.chain(this.lista)
            .map(local=>{
                let valor = local.cliente.idCliente
                let texto = local.cliente.nombreCorto

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroCliente, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('valor')
            .value()

        // ##### Filtro por Formato Local
        this.filtroFormatoLocal = _.chain(this.lista)
            .map(local=>{
                let valor = local.idFormatoLocal
                let texto = local.formatoLocal_nombre

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroFormatoLocal, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('valor')
            .value()

        // ##### Filtro por CECO
        this.filtroCeco = _.chain(this.lista)
            .map(local=>{
                let valor = local.numero
                let texto = local.numero

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroCeco, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy((ob)=>ob.valor.length, 'valor') // ordenar primero los numeros de dos digitos, luego los de 3 digitos...
            .value()

        // ##### Filtro por Nombre
        this.filtroNombre = _.chain(this.lista)
            .map(local=>{
                let valor = local.nombre
                let texto = local.nombre

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroNombre, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('valor')
            .value()

        // ##### Filtro por Comuna
        this.filtroComuna = _.chain(this.lista)
            .map(local=>{
                let valor = local.cutComuna
                let texto = local.comuna_nombre

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroComuna, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('valor')
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
        // Todo: filtrar por regiones
        return {
            // filtrar por clientes
            localesFiltrados: _.chain(this.lista)
                // filtrar por cliente
                .filter(local=> _.find(this.filtroCliente, {'valor': local.cliente.idCliente, 'seleccionado': true}) )
                // filtrar por Formato Local
                .filter(local=> _.find(this.filtroFormatoLocal, {'valor': local.idFormatoLocal, 'seleccionado': true}) )
                // filtrar por CECO
                .filter(local=> _.find(this.filtroCeco, {'valor': local.numero, 'seleccionado': true}) )
                // // filtrar por Nombre
                .filter(local=> _.find(this.filtroNombre, {'valor': local.nombre, 'seleccionado': true}) )
                // // filtrar por Comuna
                .filter(local=> _.find(this.filtroComuna, {'valor': local.cutComuna, 'seleccionado': true}) )
                .value(),
            filtros: {
                filtroCliente: this.filtroCliente,
                filtroFormatoLocal: this.filtroFormatoLocal,
                filtroCeco: this.filtroCeco,
                filtroNombre: this.filtroNombre,
                filtroComuna: this.filtroComuna
            }
        }

    }
}