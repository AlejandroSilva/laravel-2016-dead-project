import _ from 'lodash'

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
    remove(idAuditoria){
        let index = this.lista.findIndex(auditoria=>auditoria.idAuditoria===idAuditoria)
        if(index>=0) this.lista.splice(index, 1)
    }

    orderByFechaProgramadaYLider(a,b){
        // si se parsea la fecha sin dia (EJ. 2016-01-00) resulta un 'Invalid Date'

        let dateA = new Date(a.fechaProgramada)
        if(dateA=='Invalid Date'){
            let [annoA, mesA, diaA] = a.fechaProgramada.split('-')
            // OJO: (new Date('2016-04')) - (new Date('2016-03-31')), resulta en 0, y los ordena mal, por eso se resta 1
            dateA = new Date(`${annoA}-${mesA}`) - 1
        }

        let dateB = new Date(b.fechaProgramada)
        if(dateB=='Invalid Date'){
            let [annoB, mesB, diaB] = a.fechaProgramada.split('-')
            dateB = new Date(`${annoB}-${mesB}`) - 1
        }

        if((dateA-dateB)===0){
            // Si tienen la misma fecha, ordenar por nombre de Lider
            let auditorA = a.auditor? `${a.auditor.nombre1} ${a.auditor.apellidoPaterno}` : '--'
            let auditorB = b.auditor? `${b.auditor.nombre1} ${b.auditor.apellidoPaterno}` : '--'
            if(auditorA===auditorB){
                // si tienen la misma fecha, y el mismo auditor, ordenar por idAuditoria
                return a.idAuditoria-b.idAuditoria
            }else{
                return auditorA>=auditorB
            }
        }else{
            // fecha ordenada de de menor a mayor (A-B)
            return dateA - dateB
        }
    }

    ordenarLista(){
        let porFechaProgramada = (auditoria)=>{
            let dateA = new Date(auditoria.fechaProgramada)
            if(dateA=='Invalid Date'){
                let [annoA, mesA, diaA] = auditoria.fechaProgramada.split('-')
                // OJO: (new Date('2016-04')) - (new Date('2016-03-31')), resulta en 0, y los ordena mal, por eso se resta 1
                return new Date(`${annoA}-${mesA}`) - 1
            }else{
                return dateA
            }
        }
        let porAuditor = (auditoria)=>{
            return auditoria.auditor? `${auditoria.auditor.nombre1} ${auditoria.auditor.apellidoPaterno}` : '--'
        }
        let porComuna = (auditoria)=>auditoria.local.direccion.comuna.cutComuna

        // ordenar por fechaprogramada, por auditor, y finalmente por comuna
        this.lista = _.sortBy(this.lista, porFechaProgramada, porAuditor, porComuna)
    }

    getListaFiltrada(){
        //this.actualizarFiltros()

        // Todo: filtrar por clientes
        // Todo: filtrar por regiones
        // let listaDiaFIjadoOrdenado = R.sort(orderByFechaProgramadaStock, this.lista)

        return {
            auditoriasFiltradas: this.lista
        }
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