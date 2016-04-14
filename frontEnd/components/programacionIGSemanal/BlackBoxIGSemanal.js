import R from 'ramda'

export default class BlackBoxIGSemanal{
    constructor(){
        this.lista = []
        // this.filtroClientes = []
        this.filtroRegiones = []
        this.filtroLideres = []
        this.filtroLocales = []
    }
    reset() {
        this.lista = []
        // this.filtroClientes = []
        this.filtroRegiones = []
        this.filtroLideres = []
        this.filtroLocales = []
    }
    // Todo: Optimizar
    // Todo Modificar: el listado de clientes
    add(inventario){
        this.lista.push(inventario)
    }
    
    getListaFiltrada(){
        this.actualizarFiltros()

        // Todo: filtrar por clientes

        // Filtrar por Regiones
        let regionesSeleccionadas = this.filtroRegiones.filter(opcion=>opcion.seleccionado).map(opcion=>opcion.texto)
        let listaFiltradaPorRegiones = R.filter(inventario=>{
            return R.contains(inventario.local.direccion.comuna.provincia.region.numero, regionesSeleccionadas)
        }, this.lista)

        // Filtrar por Lideres (hay lideres en dos nominas: nominaDia y nominaNoche
        let lideresSeleccionados = this.filtroLideres.filter(opcion=>opcion.seleccionado).map(opcion=>opcion.texto)
        let listaFiltradaPorLideres = R.filter(inventario=>{
            if(inventario.idJornada=='2' || inventario.idJornada=='4'){
                // si la jornada es de dia, revisar si el lider esta en la nomina_dia
                let liderDia = inventario.nomina_dia.lider
                let nombreLiderDia = liderDia? `${liderDia.nombre1} ${liderDia.apellidoPaterno}` : '-- NO FIJADO --'
                return R.contains(nombreLiderDia, lideresSeleccionados)
            }else if(inventario.idJornada=='2' || inventario.idJornada=='3'){
                // si la jornada es de noche, revisar si el lider esta en la nomina_noche
                let liderNoche = inventario.nomina_noche.lider
                let nombreLiderNoche = liderNoche? `${liderNoche.nombre1} ${liderNoche.apellidoPaterno}` : '-- NO FIJADO --'
                return R.contains(nombreLiderNoche, lideresSeleccionados)
            }else{
                // la jornada no ha sido seleccionada, no hay ninguna nomina activa, entonces no hay lider seleccionado
                return false
            }
        }, listaFiltradaPorRegiones)

        // Filtro por Local
        let localesSeleccionados = this.filtroLocales.filter(opcion=>opcion.seleccionado).map(opcion=>opcion.texto)
        let listaFiltradaPorLocales = R.filter(inventario=>{
            return R.contains(inventario.local.numero, localesSeleccionados)
        }, listaFiltradaPorLideres)

        return {
            inventariosFiltrados: listaFiltradaPorLocales,
            //filtroClientes: this.filtroClientes,
            filtroRegiones: this.filtroRegiones,
            filtroLideres: this.filtroLideres,
            filtroLocales: this.filtroLocales
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
        this.lista = R.sort(orderByFechaProgramadaStock, this.lista)
    }
    
    // Todo modificar el listado de clientes
    actualizarInventario(inventarioActualizado) {
        this.lista = this.lista.map(inventario=> {
            if (inventario.idInventario == inventarioActualizado.idInventario) {
                //inventario = Object.assign(inventario, inventarioActualizado)
                return inventarioActualizado
            }
            return inventario
        })
        this.actualizarFiltros()
    }

    /** ###################### FILTROS ###################### **/
    actualizarFiltros(){
        // Clientes
        // let clientes = this.lista.map(inventario=>inventario.local.cliente.nombre)
        // this.filtroClientes = R.uniq(clientes).map(textoUnico=>{
        //     // si no existe la opcion, se crea y se selecciona por defecto
        //     return this.filtroClientes.find(opc=>opc.texto===textoUnico)
        //         || { texto: textoUnico, seleccionado: true}
        // })

        // ##### Filtro Regiones
        let regiones = this.lista.map(inventario=>inventario.local.direccion.comuna.provincia.region.numero)
        this.filtroRegiones = R.uniq(regiones).map(textoUnico=>{
            // si no existe la opcion, se crea y se selecciona por defecto
            return this.filtroRegiones.find(opc=>opc.texto===textoUnico) || { texto: textoUnico, seleccionado: true}
        })

        // ##### Filtro Lideres, considerar el lider solo si la jornada de la nomina se encuentra activa
        let lideresDia = this.lista
            // considerar la nomina de dia solo cuando la jornada sea 'dia (2)' o 'dia y noche (4)'
            .filter( inventario=>(inventario.idJornada=='2' || inventario.idJornada=='4') )
            // obtener el nombre+apellido como el texto de la opcion
            .map(inventario=>{
                let lider = inventario.nomina_dia.lider
                return lider? `${lider.nombre1} ${lider.apellidoPaterno}` : '-- NO FIJADO --'
            })
        let lideresNoche = this.lista
            // considerar la nomina de noche solo cuando la jornada sea 'noche (3)' o 'dia y noche (4)'
            .filter( inventario=>(inventario.idJornada=='3' || inventario.idJornada=='4') )
            // obtener el nombre+apellido como el texto de la opcion
            .map(inventario=>{
                let lider = inventario.nomina_noche.lider
                return lider? `${lider.nombre1} ${lider.apellidoPaterno}` : '-- NO FIJADO --'
            })
        // unir lideres de dia + lideres de noche, ordenarlos alfabeticamente
        let lideresDiaNoche = ['-- NO FIJADO --'].concat(lideresDia, lideresNoche).sort((a,b)=>a>b)

        this.filtroLideres = R.uniq( lideresDiaNoche ).map(textoUnico=>{
            // si no existe la opcion, se crea y se selecciona por defecto
            return this.filtroLideres.find(opc=>opc.texto===textoUnico) || { texto: textoUnico, seleccionado: true}
        })

        // ##### Filtro Numero de Local
        let locales = this.lista
            .map(inventario=>inventario.local.numero)
            // convierte el texto a numero, y los ordena de menor a mayor
            .sort((a, b)=>{ return (isNaN(a*1) || isNaN(b*1))? (a>b) : (a*1>b*1) })
        this.filtroLocales = R.uniq(locales).map(textoUnico=>{
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