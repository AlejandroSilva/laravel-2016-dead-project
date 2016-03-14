import R from 'ramda'

export default class BlackBox{
    constructor(clientes){
        this.clientes = clientes
        this.lista = []
        this.filtroClientes = []
        this.filtroRegiones = []
        this.idDummy = 1    // valor unico, sirve para identificar un dummy cuando un idInventario no ha sido fijado
    }
    // Todo Modificar
    reset(){
        this.lista = []
        this.filtroClientes = []
        this.filtroRegiones = []
        this.idDummy = 1
    }
    // Todo: Optimizar
    // Todo Modificar: el listado de clientes
    add(inventario){
        this.lista.push(inventario)
    }
    // Todo Modificar: el listado de clientes
    remove(idDummy){
        let index = this.lista.findIndex(inventario=>inventario.idDummy===idDummy)
        if(index>0) this.lista.splice(index, 1)
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
    //getListaFiltrada(){
    getListaFiltrada(){
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

        let orderByFechaProgramadaStock = (a,b)=>{
            // si la fecha es la misma, ordenar por stock
            let dateA = new Date(a.fechaProgramada)
            let dateB = new Date(b.fechaProgramada)
            if(dateA-dateB===0){
                // stock, mayor a menor (B-A)
                return b.local.stock - a.local.stock
            }else{
                // fecha, de menor a mayor (A-B)
                return dateA - dateB
            }
        }
        let listaOrdenada = R.sort(orderByFechaProgramadaStock, listaFiltrada2)
        return listaOrdenada
    }

    // Metodos de alto nivel
    __crearDummy(annoMesDia, idLocal){
        return {
            idDummy: this.idDummy++,    // asignar e incrementar
            idInventario: null,
                idLocal: idLocal,
                idJornada: null,
            fechaProgramada: annoMesDia,
            horaLlegada: "00:00:00",
            stockTeorico: 0,
            dotacionAsignada: null,
            local: {
                idLocal: idLocal,
                idJornadaSugerida: 4, // no definida
                nombre: '-',
                numero: '-',
                stock: 0,
                fechaStock: 'YYYY-MM-DD',
                formato_local: {
                    produccionSugerida: 0
                },
                nombreCliente: '-',
                nombreComuna: '-',
                nombreProvincia: '-',
                nombreRegion: '-',
                dotacionSugerida: 0
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
        let clientes = this.lista.map(inventario=>inventario.local.nombreCliente)
        let clientesUnicos = R.uniq(clientes)
        this.filtroClientes = clientesUnicos.map(textoUnico=>{
            // si no existe la opcion, se crea y se selecciona por defecto
            return this.filtroClientes.find(opc=>opc.texto===textoUnico)
                || { texto: textoUnico, seleccionado: true}
        })

        // Regiones
        let regiones = this.lista.map(inventario=>inventario.local.nombreRegion)
        let regionesUnicas = R.uniq(regiones)
        this.filtroRegiones = regionesUnicas.map(textoUnico=>{
            // si no existe la opcion, se crea y se selecciona por defecto
            return this.filtroRegiones.find(opc=> opc.texto===textoUnico)
                || { texto: textoUnico, seleccionado: true}
        })
        return {
            filtroClientes: this.filtroClientes,
            filtroRegiones: this.filtroRegiones
        }
    }
    reemplazarFiltroClientes(filtro){
        this.filtroClientes = filtro
    }
    reemplazarFiltroRegiones(filtro){
        this.filtroRegiones = filtro
    }

    /*
    let     filtroActualizado = this.generarFiltro(inventariosActualizados)
    let programasFiltrados = this.filtrarInventarios(inventariosActualizados, filtroActualizado)

    generarFiltro(inventarios){
        // TODO: simplificar este metodo, hay mucho codigo repetido
        const filtrarSoloUnicos = (valor, index, self)=>self.indexOf(valor)===index

        // FILTRO CLIENTES
        // obtener una lista de clientes sin repetir
        const seleccionarNombreCliente = inventario=>inventario.nombreCliente || ''
        let clientesUnicos = inventarios.map(seleccionarNombreCliente).filter(filtrarSoloUnicos)

        // crear el filtro con los datos del filtro anterior
        let filtroClientes = clientesUnicos.map(textoUnico=>{
            let opcion = this.state.filtro.clientes.find(opc=> opc.texto===textoUnico)

            // si no existe la opcion, se crea y se selecciona por defecto
            return opcion || { texto: textoUnico, seleccionado: true}
        })

        // FILTRO REGIONES
        const seleccionarNombreRegion = inventario=>inventario.nombreRegion || ''
        let regionesUnicas = inventarios.map(seleccionarNombreRegion).filter(filtrarSoloUnicos)

        // crear el filtro con los datos del filtro anterior
        let filtroRegiones = regionesUnicas.map(textoUnico=>{
            let opcion = this.state.filtro.regiones.find(opc=> opc.texto===textoUnico)

            // si no existe la opcion, se crea y se selecciona por defecto
            return opcion || { texto: textoUnico, seleccionado: true}
        })

        return {
            clientes: filtroClientes,
            regiones: filtroRegiones
        }
    }

    filtrarInventarios(inventarios, filtros){
        //console.log('filtros actualizado: ', filtros.clientes.map(op=>op.seleccionado))
        //console.log('inventarios: ', inventarios.map(local=>local.nombreCliente))

        // por cliente: cumple el criterio si la opcion con su nombre esta seleccionada
        let programasFiltrados = inventarios.filter(inventario=>{
            let textoBuscado = inventario.nombreCliente || ''  // si es undefined, es tratado como ''
            return filtros.clientes.find( opcion=>(opcion.texto===textoBuscado && opcion.seleccionado===true) )
        })
        // por regiones
        programasFiltrados = programasFiltrados.filter(inventario=>{
            let textoBuscado = inventario.nombreRegion || ''  // si es undefined, es tratado como ''
            return filtros.regiones.find( opcion=>(opcion.texto===textoBuscado && opcion.seleccionado===true) )
        })
        return programasFiltrados
    }
*/
}