import _ from 'lodash'

export default class BlackBoxSemanal{
    constructor(){
        this.lista = []
        this.filtroFechas = []
        this.filtroCeco = []
        this.filtroRegiones = []
        this.filtroComunas = []
        this.filtroAuditores = []
        this.filtroFechaAuditoria = []

        this.filtroAprobadas = []
    }
    reset() {
        this.lista = []
        this.filtroFechas = []
        this.filtroCeco = []
        this.filtroRegiones = []
        this.filtroComunas = []
        this.filtroAuditores = []
        this.filtroFechaAuditoria = []

        this.filtroAprobadas = []
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
            if (auditoria.aud_idAuditoria == auditoriaActualizada.aud_idAuditoria) {
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
            let dateA = new Date(auditoria.aud_fechaProgramada)
            if(dateA=='Invalid Date'){
                let [annoA, mesA, diaA] = auditoria.aud_fechaProgramada.split('-')
                // OJO: (new Date('2016-04')) - (new Date('2016-03-31')), resulta en 0, y los ordena mal, por eso se resta 1
                return new Date(`${annoA}-${mesA}`) - 1
            }else{
                return dateA
            }
        }
        let porAuditor = (auditoria)=>{
            return auditoria.aud_auditor
        }
        let porComuna = (auditoria)=>auditoria.local_cutComuna

        // ordenar por fechaprogramada, por auditor, y finalmente por comuna
        this.lista = _.sortBy(this.lista, porFechaProgramada, porAuditor, porComuna)
    }

    actualizarFiltros(){
        // ##### Filtro fechas
        this.filtroFechas = _.chain(this.lista)
            .map(auditoria=>{
                let valor = auditoria.aud_fechaProgramada
                let texto = auditoria.aud_fechaProgramadaFbreve

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroFechas, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('valor')
            .value()

        // ##### Filtro CECO (ordenado por numero)
        this.filtroCeco = _.chain(this.lista)
            .map(auditoria=>{
                let valor = ''+auditoria.local_ceco
                let texto = ''+auditoria.local_ceco

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroCeco, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .sortBy((ob)=>ob.valor.length, 'valor') // ordenar primero los numeros de dos digitos, luego los de 3 digitos...
            .value()

        // ##### Filtro Regiones (ordenado por codRegion)
        this.filtroRegiones = _.chain(this.lista)
            .map(auditoria=>{
                let valor = auditoria.local_cutRegion
                let texto = auditoria.local_region

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
            .filter(auditoria=>{
                return _.find(this.filtroRegiones, {'valor': auditoria.local_cutRegion, 'seleccionado': true})
            })
            .map(auditoria=>{
                let valor = auditoria.local_cutComuna
                let texto = auditoria.local_comuna

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
                let valor = auditoria.aud_idAuditor
                let texto = auditoria.aud_auditor

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroAuditores, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('texto')
            .value()

        // ##### Filtro Fecha Auditoria
        this.filtroFechaAuditoria = _.chain(this.lista)
            .map(auditoria=>{
                let valor = auditoria.aud_fechaAuditoria
                let texto = auditoria.aud_fechaAuditoria==='0000-00-00'? 'PENDIENTE' : auditoria.aud_fechaAuditoria

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroFechaAuditoria, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('valor')
            .value()

        // ##### Filtro Revisada (antes "Aprobadas")
        this.filtroAprobadas = _.chain([
                {valor: '1', texto: 'Revisada'},
                {valor: '0', texto: 'Pendiente'}
            ])
            .map(opcionAprobada=>{
                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroAprobadas, {'valor': opcionAprobada.valor})
                return opcion? opcion : {valor: opcionAprobada.valor, texto: opcionAprobada.texto, seleccionado: true}
            })
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
                // Filtrar por Fecha
                .filter(auditoria=>{
                    return _.find(this.filtroFechas, {'valor': auditoria.aud_fechaProgramada, 'seleccionado': true})
                })
                // Filtrar por Ceco
                .filter(auditoria=>{
                    return _.find(this.filtroCeco, {'valor': ''+auditoria.local_ceco, 'seleccionado': true})
                })
                // Filtrar por Region
                .filter(auditoria=>{
                    return _.find(this.filtroRegiones, {'valor': auditoria.local_cutRegion, 'seleccionado': true})
                })
                // Filtrar por Comuna
                .filter(auditoria=>{
                    return _.find(this.filtroComunas, {'valor': auditoria.local_cutComuna, 'seleccionado': true})
                })
                // Filtrar por Auditor
                .filter(auditoria=>{
                    return _.find(this.filtroAuditores, {'valor': auditoria.aud_idAuditor, 'seleccionado': true})
                })
                // Filtrar por Fecha de Subida de Nomina
                .filter(auditoria=>{
                    return _.find(this.filtroFechaAuditoria, {'valor': auditoria.aud_fechaAuditoria, 'seleccionado': true})
                })
                // Filtrar por Aprobada
                .filter(auditoria=>{
                    return _.find(this.filtroAprobadas, {'valor': ''+auditoria.aud_aprobada, 'seleccionado': true})
                })
                .value(),
            filtros: {
                filtroFechas: this.filtroFechas,
                filtroCeco: this.filtroCeco,
                filtroRegiones: this.filtroRegiones,
                filtroComunas: this.filtroComunas,
                filtroAuditores: this.filtroAuditores,
                filtroFechaAuditoria: this.filtroFechaAuditoria,
                filtroAprobadas: this.filtroAprobadas
            }
        }
    }
}