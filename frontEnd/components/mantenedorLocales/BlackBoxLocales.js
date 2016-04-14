//import R from 'ramda'

export default class BlackBoxLocales{
    constructor(){
        this.lista = []
    }
    reset() {
        this.lista = []
    }

    add(local){
        this.lista.push(local)
    }

    getListaFiltrada(){
        // Todo: filtrar por clientes
        // Todo: filtrar por regiones
        return this.lista
    }

    ordenarLista(){
        console.error('ordenar locales pendientes')
    }
    
    actualizarLocal(localActualizado){
        this.lista = this.lista.map(local=> {
            if (local.idLocal == localActualizado.idLocal) {
                return localActualizado
            }
            return local
        })
    }
}