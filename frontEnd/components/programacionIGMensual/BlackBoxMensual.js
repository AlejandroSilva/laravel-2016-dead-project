import R from 'ramda'

export default class BlackBox{
    constructor(clientes){
        this.clientes = clientes
        this.lista = []
        this.filtroClientes = []
        this.filtroRegiones = []
        this.filtroLocales = []
        this.idDummy = 1    // valor unico, sirve para identificar un dummy cuando un idInventario no ha sido fijado
    }
    // Todo Modificar
    reset(){
        this.lista = []
        this.filtroClientes = []
        this.filtroRegiones = []
        this.filtroLocales = []
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
    getListaFiltrada(){
        this.actualizarFiltros()

        // filtrar por clientes
        // let clientesSeleccionados = this.filtroRegiones.filter(opcion=>opcion.seleccionado).map(opcion=>opcion.texto)
        // let listaFiltradaPorRegiones = R.filter(inventario=>{
        //     return R.contains(inventario.local.direccion.comuna.provincia.region.numero, clientesSeleccionados)
        // }, this.lista)

        // Filtrar por Regiones
        let regionesSeleccionadas = this.filtroRegiones.filter(opcion=>opcion.seleccionado).map(opcion=>opcion.texto)
        let listaFiltradaPorRegiones = R.filter(inventario=>{
            return R.contains(inventario.local.direccion.comuna.provincia.region.numero, regionesSeleccionadas)
        }, this.lista)

        // Filtro por Local
        let localesSeleccionados = this.filtroLocales.filter(opcion=>opcion.seleccionado).map(opcion=>opcion.texto)
        let listaFiltradaPorLocales = R.filter(inventario=>{
            return R.contains(inventario.local.numero, localesSeleccionados)
        }, listaFiltradaPorRegiones)

        return {
            inventariosFiltrados: listaFiltradaPorLocales,
            filtroClientes: this.filtroClientes,
            filtroRegiones: this.filtroRegiones,
            filtroLocales: this.filtroLocales
        }
    }
    __getListaFiltradaOrdenada__por_ahora_no_se_ocupa(){
        this.actualizarFiltros()

        // filtrar por clientes
        let clientesSeleccionados = this.filtroClientes.filter(opcion=>opcion.seleccionado).map(opcion=>opcion.texto)
        let listaFiltrada1 = R.filter(inventario=>{
            return R.contains(inventario.local.nombreCliente, clientesSeleccionados)
        }, this.lista)

        // filtrar por regiones
        let regionesSeleccionadas = this.filtroRegiones.filter(opcion=>opcion.seleccionado).map(opcion=>opcion.texto)
        let listaFiltrada2 = R.filter(inventario=>{
            return R.contains(inventario.local.nombreRegion, regionesSeleccionadas)
        }, listaFiltrada1)

        // Hack: para ordenar por fechas, se van a separar las que estan "fijadas", de las que "no estan fijadas"
        // se ordenaran las "fijadas", y las "no fijadas" quedan igual
        let listaDiaFijado = listaFiltrada2.filter(inventario=>inventario.fechaProgramada.indexOf('-00')===-1)
        let listaDiaNoFijado = listaFiltrada2.filter(inventario=>inventario.fechaProgramada.indexOf('-00')!==-1)

        let orderByFechaProgramadaStock = (a,b)=>{
            let dateA = new Date(a.fechaProgramada)
            let dateB = new Date(b.fechaProgramada)

            if((dateA-dateB)===0){
                // stock ordenado de mayor a menor (B-A)
                return b.stockTeorico - a.stockTeorico
            }else{
                // fecha ordenada de de menor a mayor (A-B)
                return dateA - dateB
            }
        }
        let listaDiaFIjadoOrdenado = R.sort(orderByFechaProgramadaStock, listaDiaFijado)

        return {
            inventariosFiltrados: [
                ...listaDiaNoFijado,
                ...listaDiaFIjadoOrdenado
            ],
            filtroClientes: this.filtroClientes,
            filtroRegiones: this.filtroRegiones,
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
        this.lista  = R.sort(orderByFechaProgramadaStock, this.lista)
    }
    // Metodos de alto nivel
    __crearDummy(annoMesDia, idLocal){
        return {
            idDummy: this.idDummy++,    // asignar e incrementar
            idInventario: null,
            idLocal: idLocal,
            idJornada: null,
            fechaProgramada: annoMesDia,
            horaLlegada: null,
            stockTeorico: 0,
            dotacionAsignada: null,
            local: {
                idLocal: idLocal,
                idJornadaSugerida: 4, // no definida
                nombre: '-',
                numero: '-',
                stock: 0,
                fechaStock: 'YYYY-MM-DD',
                nombreCliente: '-',
                nombreComuna: '-',
                nombreProvincia: '-',
                nombreRegion: '-',
                dotacionSugerida: 0,
                formato_local: {
                    produccionSugerida: 0
                },
                direccion: {
                    direccion: '-',
                    comuna: {
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
    actualizarDatosInventario(formInventario, inventarioActualizado){
        this.lista = this.lista.map(inventario=> {
            if (inventario.idDummy == formInventario.idDummy) {
                inventario = Object.assign(inventario, inventarioActualizado)
            }
            return inventario
        })
    }

    actualizarFiltros(){
        // Clientes
        // let clientes = this.lista.map(inventario=>inventario.local.cliente.nombre)
        // let clientes = this.lista.map(inventario=>inventario.local.XASDASDASDASDASDASDASDASD)
        // let clientesUnicos = R.uniq(clientes)
        // this.filtroClientes = clientesUnicos.map(textoUnico=>{
        //     // si no existe la opcion, se crea y se selecciona por defecto
        //     return this.filtroClientes.find(opc=>opc.texto===textoUnico)
        //         || { texto: textoUnico, seleccionado: true}
        // })

        // ##### Filtro Regiones
        let regiones = this.lista.map(inventario=>inventario.local.direccion.comuna.provincia.region.numero)
        let regionesOrdenadas = R.uniq(regiones).sort((a, b)=>a>b)
        this.filtroRegiones = regionesOrdenadas.map(textoUnico=>{
            // si no existe la opcion, se crea y se selecciona por defecto
            return this.filtroRegiones.find(opc=>opc.texto===textoUnico) || { texto: textoUnico, seleccionado: true}
        })

        // ##### Filtro Numero de Local
        let locales = this.lista.map(inventario=>inventario.local.numero)
        // convierte el texto a numero, y los ordena de menor a mayor
        let localesOrdenados = R.uniq(locales).sort((a, b)=>{ return (isNaN(a*1) || isNaN(b*1))? (a>b) : (a*1>b*1) })
/** */  console.log("inventario semanal: ", locales, localesOrdenados)
        this.filtroLocales = localesOrdenados.map(textoUnico=>{
            // si no existe la opcion, se crea y se selecciona por defecto
            return this.filtroLocales.find(opc=>opc.texto===textoUnico) || { texto: textoUnico, seleccionado: true}
        })
    }
    reemplazarFiltro(nombreFiltro, filtroActualizado) {
        if(this[nombreFiltro]) {
            this[nombreFiltro] = filtroActualizado
        }else{
            console.error(`Filtro ${nombreFiltro} no existe.`)
        }
    }
}