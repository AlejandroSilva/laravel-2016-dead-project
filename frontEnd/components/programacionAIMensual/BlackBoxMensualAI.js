import _ from 'lodash'

export default class BlackBox{
    constructor(clientes){
        this.clientes = clientes
        this.lista = []
        this.filtroFechas = []
        this.filtroClientes = []
        this.filtroCeco = []
        this.filtroRegiones = []
        this.filtroComunas = []
        this.filtroAuditores = []
        this.idDummy = 1    // valor unico, sirve para identificar un dummy cuando un idAuditoria no ha sido fijado
    }
    // Todo Modificar
    reset(){
        this.lista = []
        this.filtroFechas = []
        this.filtroClientes = []
        this.filtroCeco = []
        this.filtroRegiones = []
        this.filtroComunas = []
        this.filtroAuditores = []
        this.idDummy = 1
    }
    // Todo Modificar: el listado de clientes
    addNuevo(auditoria){
        // al agregar un elemento unico, se debe ubicar: DESPUES de los inventarios sin fecha, y ANTES que los inventarios con fecha
        // buscar el indice del primer inventario con fecha
        let indexConFecha = this.lista.findIndex(auditoria=>{
            let dia = auditoria.aud_fechaProgramada.split('-')[2]
            return dia!=='00'
        })
        if(indexConFecha>=0){
            // se encontro el indice
            this.lista.splice(indexConFecha, 0, auditoria)
        }else{
            // no hay ninguno con fecha, agregar al final
            this.lista.push(auditoria)
        }
    }
    // igual que el anterior, pero con un arreglo
    addNuevos(auditorias){
        // al agregar un elemento unico, se debe ubicar: DESPUES de los auditorias sin fecha, y ANTES que los auditorias con fecha
        // buscar el indice del primer inventario con fecha
        let indexConFecha = this.lista.findIndex(aud=>{
            let dia = aud.aud_fechaProgramada.split('-')[2]
            return dia!=='00'
        })
        if(indexConFecha>=0){
            // se encontro el indice
            this.lista.splice(indexConFecha, 0, ...auditorias)
        }else{
            // no hay ninguno con fecha, agregar al final
            this.lista = this.lista.concat(auditorias)
        }
    }
    addInicio(auditoria){
        // unshift agrega un elemento al inicio del array
        this.lista.unshift(auditoria)
    }
    addFinal(auditoria){
        this.lista.push(auditoria)
    }
    // Todo Modificar: el listado de clientes
    remove(idDummy){
        let index = this.lista.findIndex(auditoria=>auditoria.idDummy===idDummy)
        if(index>=0) this.lista.splice(index, 1)
    }
    yaExiste(idLocal, annoMesDia){
        let auditoriaDelLocal = this.lista.filter(auditoria=>auditoria.local_idLocal===idLocal)
        // buscar si se tiene inventarios para este local
        if(auditoriaDelLocal.length===0){
            return false
        }
        // buscar si alguna fecha coincide
        let existe = false
        auditoriaDelLocal.forEach(auditoria=>{
            if(auditoria.aud_idAuditoria===annoMesDia){
                // se tiene el mismo local, con la misma fecha inventariado
                console.log('ya programado')
                existe = true
                return true
            }
        })
        return existe
    }
    // Metodos de alto nivel
    __crearDummy(annoMesDia, idLocal, auditor){
        return {
            idDummy: this.idDummy++,    // asignar e incrementar
            aud_idAuditoria: null,
            aud_fechaProgramada: annoMesDia,
            aud_fechaProgramadaFbreve: '',
            aud_fechaProgramadaDOW: '',
            aud_idAuditor: auditor,
            local_idLocal: idLocal,
            local_ceco: '-',
            local_direccion: '',
            local_comuna: '',
            local_region: '',
            local_nombre: '-',
            local_stock: '',
            local_horaApertura: '',
            local_horaCierre: '',
            local_fechaStock: '',
            cliente_nombreCorto: ''
        }
    }
    crearDummy(idCliente, numeroLocal, annoMesDia, auditor){
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
        let dummy = this.__crearDummy(annoMesDia, local.idLocal, auditor)
        return[ null, dummy]
    }

    // Todo modificar el listado de clientes
    actualizarDatosAuditoria(idDummy, auditoriaActualizada){
        this.lista = this.lista.map(auditoria=> {
            if (auditoria.idDummy == idDummy) {
                auditoria = Object.assign(auditoria, auditoriaActualizada)
            }
            return auditoria
        })
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
        let porAuditor = (auditoria)=>auditoria.aud_auditor
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

        // ##### Filtro Clientes (ordenado por codRegion)
        this.filtroClientes = _.chain(this.lista)
            .map(auditoria=>{
                let valor = auditoria.cliente_idCliente
                let texto = auditoria.cliente_nombreCorto

                // entrega la opcion si ya existe (para mantener el estado del campo 'seleccionado', o la crea si no existe
                let opcion = _.find(this.filtroClientes, {'valor': valor})
                return opcion? opcion : {valor, texto, seleccionado: true}
            })
            .uniqBy('valor')
            .sortBy('valor')
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
                // Filtrar por Cliente
                .filter(auditoria=>{
                    return _.find(this.filtroClientes, {'valor': auditoria.cliente_idCliente, 'seleccionado': true})
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
                // Filtrar por Fecha (dejar de lo ultimo, ya que es la mas lenta -comparacion de strings-)
                .filter(auditoria=>{
                    return _.find(this.filtroFechas, {'valor': auditoria.aud_fechaProgramada, 'seleccionado': true})
                })
                .value(),
            filtros: {
                filtroFechas: this.filtroFechas,
                filtroClientes: this.filtroClientes,
                filtroCeco: this.filtroCeco,
                filtroRegiones: this.filtroRegiones,
                filtroComunas: this.filtroComunas,
                filtroAuditores: this.filtroAuditores
            }
        }
    }
}