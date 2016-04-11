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
    add(auditoria){
        this.lista.push(auditoria)
    }
    
    getListaFiltrada(){
        //this.actualizarFiltros()

        // Todo: filtrar por clientes
        // Todo: filtrar por regiones
        
        // let orderByFechaProgramadaStock = (a,b)=>{
        //     let dateA = new Date(a.fechaProgramada)
        //     let dateB = new Date(b.fechaProgramada)
        //
        //     if((dateA-dateB)===0){
        //         // stock ordenado de mayor a menor (B-A)
        //         return b.stockTeorico - a.stockTeorico
        //     }else{
        //         // fecha ordenada de de menor a mayor (A-B)
        //         return dateA - dateB
        //     }
        // }
        // let listaDiaFIjadoOrdenado = R.sort(orderByFechaProgramadaStock, this.lista)

        return {
            auditoriasFiltradas: this.lista
        }
    }

    ordenarLista(){
        let orderByFechaProgramadaStock = (a,b)=>{
            // si se parsea la fecha sin dia (EJ. 2016-01-00) resulta un 'Invalid Date'

            let dateA = new Date(a.fechaProgramada)
            if(dateA=='Invalid Date'){
                let [annoA, mesA, diaA] = a.fechaProgramada.split('-')
                // OJO: (new Date('2016-04')) - (new Date('2016-03-31')), resulta en 0, y los ordena mal, por eso se resta 1
                dateA = new Date(`${annoA}-${mesA}`) - 1
                //if(dateA=='Invalid Date')  console.log('invalida :', `${annoA}-${mesA}`)
            }

            let dateB = new Date(b.fechaProgramada)
            if(dateB=='Invalid Date'){
                let [annoB, mesB, diaB] = a.fechaProgramada.split('-')
                dateB = new Date(`${annoB}-${mesB}`) - 1
                //if(dateB=='Invalid Date')  console.log('invalida :', `${annoB}-${mesB}`)
            }

            if((dateA-dateB)===0){
                // stock ordenado de mayor a menor (B-A)
                return b.stockTeorico - a.stockTeorico
            }else{
                // fecha ordenada de de menor a mayor (A-B)
                return dateA - dateB
            }
        }
        this.lista = R.sort(orderByFechaProgramadaStock, this.lista)
    }
    
    // Todo modificar el listado de clientes
    actualizarAuditoria(auditoriaActualizada){
        this.lista = this.lista.map(auditoria=> {
            if (auditoria.idAuditoria == auditoriaActualizada.idAuditoria) {
                //auditoria = Object.assign(auditoria, auditoriaActualizada)
                return auditoriaActualizada
            }
            return auditoria
        })
    }
}