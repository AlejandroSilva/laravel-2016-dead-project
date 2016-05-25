import moment from 'moment'
import _ from 'lodash'

export default class BlackBox{
    constructor(clientes){
        this.clientes = clientes
        this.lista = []
        this.filtroClientes = []
        this.filtroRegiones = []
        this.filtroComunas = []
        this.filtroLocales = []
        this.filtroFechas = []
        this.idDummy = 1    // valor unico, sirve para identificar un dummy cuando un idInventario no ha sido fijado
    }
    // Todo Modificar
    reset(){
        this.lista = []
        this.filtroClientes = []
        this.filtroRegiones = []
        this.filtroComunas = []
        this.filtroLocales = []
        this.filtroFechas = []
        this.idDummy = 1
    }
    // Todo Modificar: el listado de clientes
    addNuevo(inventario){
        // al agregar un elemento unico, se debe ubicar: DESPUES de los inventarios sin fecha, y ANTES que los inventarios con fecha
        // buscar el indice del primer inventario con fecha
        let indexConFecha = this.lista.findIndex(invent=>{
            let dia = invent.fechaProgramada.split('-')[2]
            return dia!=='00'
        })
        if(indexConFecha>=0){
            // se encontro el indice
            this.lista.splice(indexConFecha, 0, inventario)
        }else{
            // no hay ninguno con fecha, agregar al final
            this.lista.push(inventario)
        }
    }
    // igual que el anterior, pero con un arreglo
    addNuevos(inventarios){
        // al agregar un elemento unico, se debe ubicar: DESPUES de los inventarios sin fecha, y ANTES que los inventarios con fecha
        // buscar el indice del primer inventario con fecha
        let indexConFecha = this.lista.findIndex(invent=>{
            let dia = invent.fechaProgramada.split('-')[2]
            return dia!=='00'
        })
        if(indexConFecha>=0){
            // se encontro el indice
            this.lista.splice(indexConFecha, 0, ...inventarios)
        }else{
            // no hay ninguno con fecha, agregar al final
            this.lista = this.lista.concat(inventarios)
        }
    }
    addInicio(inventario){
        // unshift agrega un elemento al inicio del array
        this.lista.unshift(inventario)
    }
    addFinal(inventario){
        this.lista.push(inventario)
    }
    // Todo Modificar: el listado de clientes
    remove(idDummy){
        let index = this.lista.findIndex(inventario=>inventario.idDummy===idDummy)
        if(index>=0) this.lista.splice(index, 1)
    }
    yaExiste(idLocal, annoMesDia){
        let inventariosDelLocal = this.lista.filter(inventario=>inventario.idLocal===idLocal)
        // buscar si se tiene inventarios para este local
        if(inventariosDelLocal.length===0){
            return false
        }
        // buscar si alguna fecha coincide
        let existe = false
        inventariosDelLocal.forEach(inventario=>{
            if(inventario.fechaProgramada===annoMesDia){
                // se tiene el mismo local, con la misma fecha inventariado
                console.log('ya programado')
                existe = true
                return true
            }
        })
        return existe
    }
    
    // Metodos de alto nivel
    __crearDummy(annoMesDia, idLocal){
        return {
            idDummy: this.idDummy++,    // asignar e incrementar
            idInventario: null,
            idLocal: idLocal,
            fechaProgramada: annoMesDia,
            horaLlegada: null,
            stockTeorico: '0',
            dotacionAsignadaTotal: '0',
            local: {
                idLocal: idLocal,
                nombre: '-',
                numero: '-',
                stock: 0,
                fechaStock: 'YYYY-MM-DD',
                nombreComuna: '-',
                nombreProvincia: '-',
                nombreRegion: '-',
                dotacionSugerida: 0,
                formato_local: {
                    produccionSugerida: 0
                },
                cliente:{
                    nombreCliente: '-'
                },
                direccion: {
                    direccion: '-',
                    comuna: {
                        nombre: '',
                        provincia:{
                            region:{
                                numero: '-'
                            }
                        }
                    }
                }
            }
        }
    }
    crearDummy(idCliente, numeroLocal, annoMesDia){
        // ########### Revisar Cliente ###########
        // el usuario no selecciono uno en el formulario
        if(idCliente==='-1' || idCliente==='' || !idCliente){
            return [{
                idCliente,
                numeroLocal,
                errorIdCliente: 'Seleccione un Cliente'
            }, null]
        }
        // dio un idCliente, pero no existe
        let cliente = this.clientes.find(cliente=>cliente.idCliente==idCliente)
        if(!cliente){
            return [{
                idCliente,
                numeroLocal,
                errorIdCliente: 'Cliente no Existe'
            }, null]
        }

        // ########### Revisar Local ###########
        // revisar que el local exista
        let local = cliente.locales.find(local=>local.numero==numeroLocal)
        if(local===undefined){
            return [{
                idCliente,
                numeroLocal,
                errorNumeroLocal: numeroLocal===''? 'Digite un numero de local.' : `El local '${numeroLocal}' no existe.`
            }, null]
        }

        // revisar que no exista en la lista
        if( this.yaExiste(local.idLocal, annoMesDia) ) {
            return[{
                idCliente,
                numeroLocal,
                errorNumeroLocal: `${numeroLocal} ya ha sido agendado esa fecha.`
            }, null]
        }

        // ########### ok, se puede crear el inventario "vacio" ###########
        let dummy = this.__crearDummy(annoMesDia, local.idLocal)
        return[ null, dummy]
    }

    // Todo modificar el listado de clientes
    actualizarDatosLocal(local){
        // actualizar los datos del local de todos los inventarios que lo tengan
        this.lista = this.lista.map(inventario=>{
            if(inventario.local.idLocal==local.idLocal){

                inventario.local = Object.assign(inventario.local, local)
                // si no hay una dotacion asignada, ver la sugerida
                if(inventario.dotacionAsignada===null){
                    inventario.dotacionAsignada = local.dotacionSugerida
                }
            }
            return inventario
        })
        this.actualizarFiltros()
    }
    // Todo modificar el listado de clientes
    actualizarDatosInventario(idDummy, inventarioActualizado){
        this.lista = this.lista.map(inventario=> {
            if (inventario.idDummy == idDummy) {
                inventario = Object.assign(inventario, inventarioActualizado)
            }
            return inventario
        })
    }

    /*** ################### FILTROS ################### ***/
    ordenarLista(){
        let porFechaProgramada = (inventario)=>{
            let dateA = new Date(inventario.fechaProgramada)
            if(dateA=='Invalid Date'){
                let [annoA, mesA, diaA] = inventario.fechaProgramada.split('-')
                // OJO: (new Date('2016-04')) - (new Date('2016-03-31')), resulta en 0, y los ordena mal, por eso se resta 1
                return new Date(`${annoA}-${mesA}`) - 1
            }else{
                return dateA
            }
        }
        let porStockTeorico = (inventario)=>{
            return inventario.stockTeorico*1
        }

        // ordenar por fechaprogramada, por stock teorico
        this.lista = _.orderBy(this.lista, [porFechaProgramada, porStockTeorico], ['asc', 'desc'])
    }

    actualizarFiltros(){
        // ##### Filtro fechas
        this.filtroFechas = _.chain(this.lista)
            .map(inventario=>{
                let momentFecha = moment(inventario.fechaProgramada)
                let valor = inventario.fechaProgramada
                let texto = momentFecha.isValid()? momentFecha.format('dddd DD MMMM') : `-- ${inventario.fechaProgramada} --`

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroFechas, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('valor')
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
            //inventariosFiltrados: this.lista,
            inventariosFiltrados: _.chain(this.lista)
                // .filter(auditoria=>{
                //     return _.find(this.filtroClientes, {'valor': auditoria.local.idCliente, 'seleccionado': true})
                // })
                // Filtro por Region
                .filter(inventario=>{
                    return _.find(this.filtroRegiones, {'valor': inventario.local.direccion.comuna.provincia.region.cutRegion, 'seleccionado': true})
                })
                // Filtro por Comuna
                .filter(inventario=>{
                    return _.find(this.filtroComunas, {'valor': inventario.local.direccion.cutComuna, 'seleccionado': true})
                })
                // Filtrar por Fecha (dejar de lo ultimo, ya que es la mas lenta -comparacion de strings-)
                .filter(inventario=>{
                    return _.find(this.filtroFechas, {'valor': inventario.fechaProgramada, 'seleccionado': true})
                })
                .value(),
            filtros: {
                filtroClientes: this.filtroClientes,
                filtroRegiones: this.filtroRegiones,
                filtroComunas: this.filtroComunas,
                filtroFechas: this.filtroFechas
            }
        }
    }
}