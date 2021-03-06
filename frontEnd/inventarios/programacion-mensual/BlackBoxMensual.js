import moment from 'moment'
import _ from 'lodash'

export default class BlackBox{
    constructor(clientes){
        this.clientes = clientes
        this.lista = []
        this.filtroFechas = []
        this.filtroCeco = []
        this.filtroRegiones = []
        this.filtroComunas = []
        this.idDummy = 1    // valor unico, sirve para identificar un dummy cuando un idInventario no ha sido fijado
    }
    // Todo Modificar
    reset(){
        this.lista = []
        this.filtroFechas = []
        this.filtroCeco = []
        this.filtroRegiones = []
        this.filtroComunas = []
        this.idDummy = 1
    }
    // Todo Modificar: el listado de clientes
    addNuevo(inventario){
        // al agregar un elemento unico, se debe ubicar: DESPUES de los inventarios sin fecha, y ANTES que los inventarios con fecha
        // buscar el indice del primer inventario con fecha
        let indexConFecha = this.lista.findIndex(inv=>{
            let dia = inv.inv_fechaProgramada.split('-')[2]
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
        let indexConFecha = this.lista.findIndex(inv=>{
            let dia = inv.inv_fechaProgramada.split('-')[2]
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
            inv_idInventario: null,
            local_idLocal: idLocal,
            local_ceco: '-',
            local_nombre: '',
            inv_fechaProgramada: annoMesDia,
            inv_stockTeorico: '0',
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
        console.log(annoMesDia, numeroLocal, local, dummy)
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
            let dateA = new Date(inventario.inv_fechaProgramada)
            if(dateA=='Invalid Date'){
                let [annoA, mesA, diaA] = inventario.inv_fechaProgramada.split('-')
                // OJO: (new Date('2016-04')) - (new Date('2016-03-31')), resulta en 0, y los ordena mal, por eso se resta 1
                return new Date(`${annoA}-${mesA}`) - 1
            }else{
                return dateA
            }
        }
        let porCliente = (inventario)=> inventario.cliente_idCliente
        let porStockTeorico = (inventario)=> inventario.inv_stockTeorico

        // ordenar por fechaprogramada, por stock teorico
        this.lista = _.orderBy(this.lista, [porFechaProgramada, porCliente, porStockTeorico], ['asc', 'asc', 'desc'])
    }

    actualizarFiltros(){
        // ##### Filtro fechas
        this.filtroFechas = _.chain(this.lista)
            .map(inventario=>{
                let momentFecha = moment(inventario.inv_fechaProgramada)
                let valor = inventario.inv_fechaProgramada
                let texto = momentFecha.isValid()? momentFecha.format('dddd DD MMMM') : `-- ${inventario.inv_fechaProgramada} --`

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
            .uniqBy('valor')
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
                // Filtro por CECO
                .filter(inventario=>{
                    return _.find(this.filtroCeco, {'valor': ''+inventario.local_ceco, 'seleccionado': true})
                })
                // Filtro por Region
                .filter(inventario=>{
                    return _.find(this.filtroRegiones, {'valor': inventario.local_cutRegion, 'seleccionado': true})
                })
                // Filtro por Comuna
                .filter(inventario=>{
                    return _.find(this.filtroComunas, {'valor': inventario.local_cutComuna, 'seleccionado': true})
                })
                // Filtrar por Fecha (dejar de lo ultimo, ya que es la mas lenta -comparacion de strings- y la menos utilizada)
                .filter(inventario=>{
                    return _.find(this.filtroFechas, {'valor': inventario.inv_fechaProgramada, 'seleccionado': true})
                })
                .value(),
            filtros: {
                filtroFechas: this.filtroFechas,
                filtroCeco: this.filtroCeco,
                //filtroClientes: this.filtroClientes,
                filtroRegiones: this.filtroRegiones,
                filtroComunas: this.filtroComunas
            }
        }
    }
}