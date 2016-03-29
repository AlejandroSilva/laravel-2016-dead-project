import R from 'ramda'

export default class BlackBoxSemanal{
    constructor(){
        this.lista = []
    }
    reset() {
        this.lista = []
    }
    // Todo: Optimizar
    // Todo Modificar: el listado de clientes
    add(inventario){
        this.lista.push(inventario)
    }
    
    getListaFiltrada(){
        //this.actualizarFiltros()

        // Todo: filtrar por clientes
        // Todo: filtrar por regiones
        
        let orderByFechaProgramadaStock = (a,b)=>{
            let dateA = new Date(a.fechaProgramada)
            let dateB = new Date(b.fechaProgramada)

            if((dateA-dateB)===0){
                // stock ordenado de mayor a menor (B-A)
                return b.local.stock - a.local.stock
            }else{
                // fecha ordenada de de menor a mayor (A-B)
                return dateA - dateB
            }
        }
        let listaDiaFIjadoOrdenado = R.sort(orderByFechaProgramadaStock, this.lista)

        return {
            inventariosFiltrados: listaDiaFIjadoOrdenado
            // inventariosFiltrados: this.lista
        }
    }
    
    // Todo modificar el listado de clientes
    actualizarInventario(inventarioActualizado){
        this.lista = this.lista.map(inventario=> {
            if (inventario.idInventario == inventarioActualizado.idInventario) {
                //inventario = Object.assign(inventario, inventarioActualizado)
                return inventarioActualizado
            }
            return inventario
        })
    }
}