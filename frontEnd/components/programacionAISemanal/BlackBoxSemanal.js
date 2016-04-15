import _ from 'lodash'
import R from 'ramda'

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
        //***  Se asume que la lista esta ordenada

        // ##### Filtro Regiones (ordenado por codRegion)
        let regiones = this.lista
            .sort((aud1, aud2)=> aud1.local.direccion.comuna.provincia.region.cutRegion-aud2.local.direccion.comuna.provincia.region.cutRegion)
            .map(auditoria=>auditoria.local.direccion.comuna.provincia.region.numero)
        let regionesUnicasOrdenadas = R.uniq(regiones)
        this.filtroRegiones = regionesUnicasOrdenadas.map(textoUnico=>{
            // si no existe la opcion, se crea y se selecciona por defecto
            return this.filtroRegiones.find(opc=>opc.texto===textoUnico) || { texto: textoUnico, seleccionado: true}
        })

        // ##### Filtro Comunas (ordenado por codComuna)
        let comunas = this.lista
            //.sort((aud1, aud2)=> aud1.local.direccion.comuna.cutComuna-aud2.local.direccion.comuna.cutComuna)
            .map(auditoria=>auditoria.local.direccion.comuna.nombre)
        let comunasUnicasOrdenadas = R.uniq(comunas)
        this.filtroComunas = comunasUnicasOrdenadas.map(textoUnico=>{
            // si no existe la opcion, se crea y se selecciona por defecto
            return this.filtroComunas.find(opc=>opc.texto===textoUnico) || { texto: textoUnico, seleccionado: true}
        })

        // ##### Filtro Auditores
        let auditoresUnicos = _.chain(this.lista)
            .map(auditoria=>{
                let auditor = auditoria.auditor
                return auditor? `${auditor.nombre1} ${auditor.apellidoPaterno}` : '-- NO FIJADO --'
            })
            .uniq().sortBy().value()

        this.filtroAuditores = auditoresUnicos.map(textoUnico=>{
            // si no existe la opcion, se crea y se selecciona por defecto
            return this.filtroAuditores.find(opc=>opc.texto===textoUnico) || { texto: textoUnico, seleccionado: true}
        })
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

        // Filtrar por Regiones
        let regionesSeleccionadas = this.filtroRegiones.filter(opcion=>opcion.seleccionado).map(opcion=>opcion.texto)
        let filtradaPorRegiones = R.filter(inventario=>{
            return R.contains(inventario.local.direccion.comuna.provincia.region.numero, regionesSeleccionadas)
        }, this.lista)

        // Filtrar por Comunas
        let comunasSeleccionadas = this.filtroComunas.filter(opcion=>opcion.seleccionado).map(opcion=>opcion.texto)
        let filtradaPorRegionesComunas = R.filter(inventario=>{
            return R.contains(inventario.local.direccion.comuna.nombre, comunasSeleccionadas)
        }, filtradaPorRegiones)

        // Filtrar por Auditor
        let auditoresSeleccionados = this.filtroAuditores.filter(opcion=>opcion.seleccionado).map(opcion=>opcion.texto)
        let listaFiltradaPorRegionesComunasAuditores = filtradaPorRegionesComunas.filter(auditoria=>{
            let auditor = auditoria.auditor
            let nombreAuditor = auditor? `${auditor.nombre1} ${auditor.apellidoPaterno}` : '-- NO FIJADO --'
            return R.contains(nombreAuditor, auditoresSeleccionados)
        })

        return {
            //auditoriasFiltradas: this.lista,
            auditoriasFiltradas: listaFiltradaPorRegionesComunasAuditores,
            filtroRegiones: this.filtroRegiones,
            filtroComunas: this.filtroComunas,
            filtroAuditores: this.filtroAuditores
        }
    }

}