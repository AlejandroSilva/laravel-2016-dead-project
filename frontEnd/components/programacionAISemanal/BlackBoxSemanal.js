import _ from 'lodash'

export default class BlackBoxSemanal{
    constructor(){
        this.lista = []
        this.filtroAuditores = []
        this.filtroRegiones = []
        this.filtroComunas = []
        this.filtroLocales = []
    }
    reset() {
        this.lista = []
        this.filtroAuditores = []
        this.filtroRegiones = []
        this.filtroComunas = []
        this.filtroLocales = []
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

    // Todo modificar el listado de clientes
    actualizarAuditoria(auditoriaActualizada){
        this.lista = this.lista.map(auditoria=> {
            if (auditoria.idAuditoria == auditoriaActualizada.idAuditoria) {
                //auditoria = Object.assign(auditoria, auditoriaActualizada)
                return auditoriaActualizada
            }
            return auditoria
        })
        // una vez que se realizaron los cambios del inventario, se actualizan los filtros
        this.actualizarFiltros()
    }

    /*** ################### FILTROS ################### ***/

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

    actualizarFiltros(){
        // ##### Filtro Regiones (ordenado por codRegion)
        this.filtroRegiones = _.chain(this.lista)
            .map(auditoria=>{
                let valor = auditoria.local.direccion.comuna.provincia.region.cutRegion
                let texto = auditoria.local.direccion.comuna.provincia.region.numero

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroRegiones, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('valor')    // ordenado por numero de region
            .value()

        // ##### Filtro Comunas (ordenado por codComuna)
        this.filtroComunas = _.chain(this.lista)
            .map(auditoria=>{
                let valor = auditoria.local.direccion.cutComuna
                let texto = auditoria.local.direccion.comuna.nombre

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroComunas, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('texto')
            .value()

        // ##### Filtro Auditores
        this.filtroAuditores = _.chain(this.lista)
            .map(auditoria=>{
                let valor = auditoria.idAuditor
                let texto = auditoria.auditor? `${auditoria.auditor.nombre1} ${auditoria.auditor.apellidoPaterno}` : '-- NO ASIGNADO --'

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroAuditores, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('texto')
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
            auditoriasFiltradas: _.chain(this.lista)
                .filter(auditoria=>{
                    return _.find(this.filtroRegiones, {'valor': auditoria.local.direccion.comuna.provincia.region.cutRegion, 'seleccionado': true})
                })
                .filter(auditoria=>{
                    return _.find(this.filtroComunas, {'valor': auditoria.local.direccion.cutComuna, 'seleccionado': true})
                })
                .filter(auditoria=>{
                    return _.find(this.filtroAuditores, {'valor': auditoria.idAuditor, 'seleccionado': true})
                })
                .value(),
            filtros: {
                filtroRegiones: this.filtroRegiones,
                filtroComunas: this.filtroComunas,
                filtroAuditores: this.filtroAuditores
            }
        }
    }
}