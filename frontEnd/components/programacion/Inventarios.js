
export default class Inventarios{
    constructor(clientes){
        this.clientes = clientes
        this.lista = []
    }
    // Optimizar
    add(inventario){
        this.lista.push(inventario)
    }
    existe(idLocal){
        return this.lista.find(inventario=>inventario.idLocal===idLocal)!==undefined
    }
    getListaFiltrada(){
        return this.lista
    }

    // Metodos de alto nivel
    crearDummy(idCliente, numeroLocal, annoMes){
        let fechaProgramada = `${annoMes}-00`
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
        if( this.existe(local.idLocal) ) {
            return[{
                idCliente,
                numeroLocal,
                errorNumeroLocal: `El local ${numeroLocal} ya ha sido agendado.`
            }, null]
        }

        // ########### ok, se puede crear el inventario "vacio" ###########
        return[ null, {
            idInventario: null,
            idLocal: local.idLocal,
            idJornada: null,
            fechaProgramada: fechaProgramada,
            //horaLlegada: "00:00:00",
            stockTeorico: 0,
            dotacionAsignada: null,
            local: {
                idLocal: local.idLocal,
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
        }]
    }


    actualizarDatosLocal(local){
        this.lista = this.lista.map(inventario=>{
            if(inventario.local.idLocal===local.idLocal){
                inventario.local = Object.assign(inventario.local, local)
                // si no hay una dotacion asignada, ver la sugerida
                if(inventario.dotacionAsignada===null){
                    inventario.dotacionAsignada = local.dotacionSugerida
                }
            }
            return inventario
        })
    }

    /*
    let filtroActualizado = this.generarFiltro(inventariosActualizados)
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