import _ from 'lodash'
import moment from 'moment'

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
        // ##### Filtro fechas
        this.filtroFechas = _.chain(this.lista)
            .map(auditoria=>{
                let momentFecha = moment(auditoria.fechaProgramada)
                let valor = auditoria.fechaProgramada
                let texto = momentFecha.isValid()? momentFecha.format('dddd DD MMMM') : `-- ${auditoria.fechaProgramada} --`

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
                let valor = ''+auditoria.local.numero
                let texto = ''+auditoria.local.numero

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroCeco, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .sortBy((ob)=>ob.valor.length, 'valor') // ordenar primero los numeros de dos digitos, luego los de 3 digitos...
            .value()

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
            // solo dejar las comunas en las que su respectiva region este seleccionada
            .filter(auditoria=>{
                return _.find(this.filtroRegiones, {'valor': auditoria.local.direccion.comuna.provincia.region.cutRegion, 'seleccionado': true})
            })
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

        // ##### Filtro Fecha Auditoria
        this.filtroFechaAuditoria = _.chain(this.lista)
            .map(auditoria=>{
                let momentFecha = moment(auditoria.fechaAuditoria)
                let valor = auditoria.fechaAuditoria
                let texto = momentFecha.isValid()? momentFecha.format('dddd DD MMMM') : `-- PENDIENTE --`

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
                    return _.find(this.filtroFechas, {'valor': auditoria.fechaProgramada, 'seleccionado': true})
                })
                // Filtrar por Ceco
                .filter(auditoria=>{
                    return _.find(this.filtroCeco, {'valor': ''+auditoria.local.numero, 'seleccionado': true})
                })
                // Filtrar por Region
                .filter(auditoria=>{
                    return _.find(this.filtroRegiones, {'valor': auditoria.local.direccion.comuna.provincia.region.cutRegion, 'seleccionado': true})
                })
                // Filtrar por Comuna
                .filter(auditoria=>{
                    return _.find(this.filtroComunas, {'valor': auditoria.local.direccion.cutComuna, 'seleccionado': true})
                })
                // Filtrar por Auditor
                .filter(auditoria=>{
                    return _.find(this.filtroAuditores, {'valor': auditoria.idAuditor, 'seleccionado': true})
                })
                // Filtrar por Fecha de Subida de Nomina
                .filter(auditoria=>{
                    return _.find(this.filtroFechaAuditoria, {'valor': auditoria.fechaAuditoria, 'seleccionado': true})
                })
                // Filtrar por Aprobada
                .filter(auditoria=>{
                    return _.find(this.filtroAprobadas, {'valor': ''+auditoria.aprovada, 'seleccionado': true})
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