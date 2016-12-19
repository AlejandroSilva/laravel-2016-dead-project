import Axios from 'axios'
let axios = Axios.create()
let axiosRaw = Axios.create()

axios.interceptors.response.use((response)=>{
    // Si existe un problema de red, terminar la promesa
    if( response instanceof Error ){
        return Promise.reject(response.message)
    }else{
        return Promise.resolve(response.data)
    }
})
axiosRaw.interceptors.response.use((response)=>{
    // Si existe un problema de red, terminar la promesa
    if( response instanceof Error ){
        return Promise.reject(response)
    }else{
        return Promise.resolve(response)
    }
})

export default {
    activoFijo: {
        barra: (barra)=>({
            eliminar: ()=>
                axios.delete(`/api/activo-fijo/barra/${barra}`),
        }),
        barras: ({
            nuevo: (datos)=>
                axios.post(`/api/activo-fijo/barras/nuevo`, datos),
        }),
        articulo: (idArticuloAF)=>({
            actualizar: (datos)=>
                axios.put(`/api/activo-fijo/articulo/${idArticuloAF}`, datos),
            eliminar: ()=>
                axios.delete(`/api/activo-fijo/articulo/${idArticuloAF}`),
        }),
        articulos : {
            buscarBarra: (barra)=>
                axios.get(`/api/activo-fijo/articulos/buscar?barra=${barra}&conExistencias=true`),
            transferir: (articulos)=>
                axios.post(`/api/activo-fijo/articulos/transferir`, articulos),
            entregar: (datos)=>
                axios.post(`/api/activo-fijo/articulos/entregar`, datos),
            nuevo: (datos)=>
                axios.post(`/api/activo-fijo/articulos/nuevo`, datos)
        },
        almacenes: {
            buscar: ()=>
                axios.get(`/api/activo-fijo/almacenes/buscar`),
            nuevo: (datos)=>
                axios.post(`/api/activo-fijo/almacen/nuevo`, datos)
        },
        almacen: (idAlmacen)=>({
            productos: ()=>
                axios.get(`/api/activo-fijo/productos/buscar?almacen=${idAlmacen}`),
            articulos: ()=>
                axios.get(`/api/activo-fijo/almacen/${idAlmacen}/articulos`),
            preguias: ()=> 
                axios.get(`/api/activo-fijo/preguias/buscar?almacen=${idAlmacen}`)
        }),
        responsables: {
            buscar: ()=>
                axios.get(`/api/activo-fijo/responsables/buscar`)
        },
        preguia: (idPreguia)=>({
            fetch: ()=>
                axios.get(`/api/activo-fijo/preguia/${idPreguia}`),
            devolver: (datos)=>
                axios.post(`/api/activo-fijo/preguia/${idPreguia}/devolver`, datos)
        }),
        producto: (sku)=>({
            eliminar: ()=>
                axios.delete(`api/activo-fijo/producto/${sku}`),
            actualizar: (datos)=>
                axios.put(`/api/activo-fijo/producto/${sku}`, datos),
            articulos: ()=>
                axios.get(`/api/activo-fijo/articulos/buscar?sku=${sku}`),
        }),
        productos: {
            nuevo: (datos)=>
                axios.post(`api/activo-fijo/productos/nuevo`, datos),
            fetch: ()=>
                axios.get(`api/activo-fijo/productos/buscar`),
        }
    },
    cliente: {
        getLocales: (idCliente)=>
            axios.get(`/api/cliente/${idCliente}/locales`)
    },
    local: {
        // get: (idLocal)=>
        //     axios.get(`/api/locales/${idLocal}`),
        nuevo: (datos)=>
            axios.post(`/api/locales`, datos),
        actualizar: (idLocal, datos) =>
            axios.put(`/api/local/${idLocal}`, datos),
        enviarArchivoStock: (idCliente, archivo)=>{
            let datos = new FormData();
            datos.append('idCliente', idCliente)
            datos.append('stockExcel', archivo)

            return axios.post('/api/stock/upload', datos)
        },
        enviarPegarStock: datos=>
            axios.post('/api/stock/pegar', datos)
    },
    inventario: {
        nuevo: (datos)=>
            axios.post(`/api/inventario/nuevo`, datos),
        actualizar: (idInventario, datos)=>
            axios.put(`/api/inventario/${idInventario}`, datos),
        eliminar: (idInventario)=>
            axios.delete(`/api/inventario/${idInventario}`),
        // mes y cliente se usa en IG Mensual
        getPorMesYCliente: (annoMesDia, idCliente)=>
            axios.get(`/api/inventarios/buscar-2?mes=${annoMesDia}&idCliente=${idCliente}&incluirConFechaPendiente=true`),
        buscar2: (fechaInicio, fechaFin, idCliente)=>
            axios.get(`/api/inventarios/buscar-2?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&idCliente=${idCliente}`),
        acta: {
            get: idInventario=>
                axios.get(`/api/inventario/${idInventario}/acta-fcv`),
            actualizar: (idInventario, datos)=>
                axios.post(`/api/inventario/${idInventario}/acta-fcv`, datos),
            publicar: (idInventario)=>
                axios.post(`/api/inventario/${idInventario}/publicar-acta-fcv`),
            despublicar: (idInventario)=>
                axios.post(`/api/inventario/${idInventario}/despublicar-acta-fcv`),
            reprocesar: (idArchivo)=>
                axios.post(`/api/inventario/archivo-final/${idArchivo}/reprocesar-fcv`),
        }
    },
    auditoria: {
        nuevo: (datos)=>
            axios.post(`/api/auditoria/nuevo`, datos),
        actualizar: (idAuditoria, datos)=>
            axios.put(`/api/auditoria/${idAuditoria}`, datos),
        eliminar: (idAuditoria)=>
            axios.delete(`/api/auditoria/${idAuditoria}`),
        getPorMesYCliente: (annoMesDia, idCliente)=>
            axios.get(`/api/auditoria/buscar?idCliente=${idCliente}&mes=${annoMesDia}&incluirConFechaPendiente=true`),
        getPorRangoYCliente: (fechaInicio, fechaFin, idCliente)=>
            axios.get(`/api/auditoria/buscar?idCliente=${idCliente}&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`)
    },
    nomina: (idNomina)=>({
        // utilizada por programacion IG para actualizar lider
        actualizar: (datos)=>
            axios.put(`/api/nomina/${idNomina}`, datos),
        actualizarEmailContacto: (emailContacto)=>
            axios.post(`/api/nomina/${idNomina}/actualizar-email`, {emailContacto}),
        agregarLider: (usuarioRUN)=>
            axios.post(`/api/nomina/${idNomina}/lider/${usuarioRUN}`),
        quitarLider: ()=>
            axios.delete(`/api/nomina/${idNomina}/lider`),
        agregarCaptador: (idUsuario)=>
            axios.post(`/api/nomina/${idNomina}/captador/${idUsuario}`),
        quitarCaptador: (idUsuario)=>
            axios.delete(`/api/nomina/${idNomina}/captador/${idUsuario}`),
        cambiarAsignadosCaptador: (idCaptador, payload)=>
            axios.put(`/api/nomina/${idNomina}/captador/${idCaptador}`, payload),
        agregarSupervisor: (usuarioRUN)=>
            axios.post(`/api/nomina/${idNomina}/supervisor/${usuarioRUN}`),
        quitarSupervisor: ()=>
            axios.delete(`/api/nomina/${idNomina}/supervisor`),
        agregarOperador: (usuarioRUN, esTitular, idCaptador)=>
            axiosRaw.post(`/api/nomina/${idNomina}/operador/${usuarioRUN}`, {esTitular, idCaptador}),
        quitarOperador: (usuarioRUN)=>
            axios.delete(`/api/nomina/${idNomina}/operador/${usuarioRUN}`),
        lideresDisponibles: ()=>
            axios.get(`/api/nomina/${idNomina}/lideres-disponibles`),
        enviar: ()=>
            axios.post(`/api/nomina/${idNomina}/estado-enviar`),
        aprobar: ()=>
            axios.post(`/api/nomina/${idNomina}/estado-aprobar`),
        rechazar: ()=>
            axios.post(`/api/nomina/${idNomina}/estado-rechazar`),
        informar: ()=>
            axios.post(`/api/nomina/${idNomina}/estado-informar`),
        completarSinCorreo: ()=>
            axios.post(`/api/nomina/${idNomina}/estado-informar`, {omitirCorreo: true}),
        rectificar: ()=>
            axios.post(`/api/nomina/${idNomina}/estado-rectificar`)
    }),
    geo: {
       comunas: ()=>
            axios.get(`/api/geo/comunas`)
    },
    usuarios: {
        fetch: ()=>
            axios.get(`/api/usuarios/buscar`)
        ,
        // buscarRUN: (run)=>
        //     axios.get(`/api/usuarios/buscar?run=${run}`),
        nuevoUsuario: (datos)=>
            axios.post(`/api/usuarios/nuevo-usuario`, datos),
        nuevoOperador: (datos)=>
            axios.post(`/api/usuarios/nuevo-operador`, datos),
    },
    usuario: (idUsuario)=>({
        get: ()=>
            axios.get(`/api/usuario/${idUsuario}`),
        nominasAsignadas: (fechaHoy)=>
            axios.get(`/api/nominas/buscar?idCaptador1=${idUsuario}&fechaInicio=${fechaHoy}`),
        actualizar: (datos)=>
            axios.put(`/api/usuario/${idUsuario}`, datos),
        bloquear: ()=>
            axios.post(`/api/usuario/${idUsuario}/bloquear`, {}),
        cambiarContrasena: (contrasena)=>
            axios.post(`/api/usuario/${idUsuario}/cambiar-contrasena`, {contrasena}),
        historial: ()=>
            axios.get(`/api/usuario/${idUsuario}/historial-nominas`)
    }),
    vistaGeneral: {
        fetch: (annoMesDia, idCliente=0)=>
            axios.get(`/api/vista-general/nominas-inventarios?idCliente=${idCliente}&annoMesDia=${annoMesDia}`),
    },
    otros: {
        comunas: ()=>
            axios.get(`/api/comunas`)
    }
}