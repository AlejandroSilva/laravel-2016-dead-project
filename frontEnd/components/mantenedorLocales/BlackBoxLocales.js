import _ from 'lodash'

export default class BlackBoxLocales{
    constructor(){
        this.lista = []
        // Filtros
        this.filtroCliente = []
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
    // actualizarLocal(localActualizado){
    //     this.lista = this.lista.map(local=> {
    //         if (local.idLocal == localActualizado.idLocal) {
    //             return localActualizado
    //         }
    //         return local
    //     })
    // }
    /*** ################### FILTROS ################### ***/

    ordenarLista(){
        console.error('ordenar locales pendientes')
    }

    actualizarFiltros(){
        // ##### Filtro fechas
        this.filtroFechas = _.chain(this.lista)
            .map(auditoria=>{
                let valor = auditoria.fechaProgramada
                let texto = momentFecha.isValid()? momentFecha.format('dddd DD MMMM') : `-- ${auditoria.fechaProgramada} --`

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroFechas, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('valor')
            .value()
    }
    getListaFiltrada(){
        // Todo: filtrar por clientes
        // Todo: filtrar por regiones
        return this.lista
    }
}