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
        }
    },
    inventario: {
        nuevo: (datos)=>
            axios.post(`/api/inventario/nuevo`, datos),
        actualizar: (idInventario, datos)=>
            axios.put(`/api/inventario/${idInventario}`, datos),
        eliminar: (idInventario)=>
            axios.delete(`/api/inventario/${idInventario}`),
        getPorMesYCliente: (annoMesDia, idCliente)=>
            axios.get(`/api/inventarios/buscar?mes=${annoMesDia}&idCliente=${idCliente}`),
        getPorRangoYCliente: (fechaInicio, fechaFin, idCliente)=>
            axios.get(`/api/inventarios/buscar?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&idCliente=${idCliente}`)
    },
    auditoria: {
        nuevo: (datos)=>
            axios.post(`/api/auditoria/nuevo`, datos),
        actualizar: (idAuditoria, datos)=>
            axios.put(`/api/auditoria/${idAuditoria}`, datos),
        eliminar: (idAuditoria)=>
            axios.delete(`/api/auditoria/${idAuditoria}`),
        getPorMesYCliente: (annoMesDia, idCliente)=>
            axios.get(`/api/auditoria/mes/${annoMesDia}/cliente/${idCliente}`),
        getPorRangoYCliente: (fechaInicio, fechaFin, idCliente)=>
            axios.get(`/api/auditoria/${fechaInicio}/al/${fechaFin}/cliente/${idCliente}`),
        estadoGeneral: (idCliente, dia)=>
            axios.get(`/api/auditoria/cliente/${idCliente}/dia/${dia}/estado-general`)
    },
    nomina: {
        // utilizada por programacion IG para actualizar lider
        actualizar: (idNomina, datos)=>
            axios.put(`/api/nomina/${idNomina}`, datos),
        agregarLider: (idNomina, usuarioRUN)=>
            axios.post(`/api/nomina/${idNomina}/lider/${usuarioRUN}`),
        quitarLider: (idNomina, usuarioRUN)=>
            axios.delete(`/api/nomina/${idNomina}/lider`),
        agregarSupervisor: (idNomina, usuarioRUN)=>
            axios.post(`/api/nomina/${idNomina}/supervisor/${usuarioRUN}`),
        quitarSupervisor: (idNomina, usuarioRUN)=>
            axios.delete(`/api/nomina/${idNomina}/supervisor`),
        agregarOperador: (idNomina, usuarioRUN, esTitular)=>
            axiosRaw.post(`/api/nomina/${idNomina}/operador/${usuarioRUN}`, {esTitular}),
        quitarOperador: (idNomina, usuarioRUN)=>
            axios.delete(`/api/nomina/${idNomina}/operador/${usuarioRUN}`),
        // modificarOperador: (idNomina, usuarioRUN, datos)=>
        //     axios.put(`/api/nomina/${idNomina}/operador/${usuarioRUN}`, datos),
        enviar: (idNomina)=>
            axios.post(`/api/nomina/${idNomina}/estado-enviar`),
        aprobar: (idNomina)=>
            axios.post(`/api/nomina/${idNomina}/estado-aprobar`),
        rechazar: (idNomina)=>
            axios.post(`/api/nomina/${idNomina}/estado-rechazar`),
        informar: (idNomina)=>
            axios.post(`/api/nomina/${idNomina}/estado-informar`),
        rectificar: (idNomina)=>
            axios.post(`/api/nomina/${idNomina}/estado-rectificar`)
    },
    geo: {
       comunas: ()=>
            axios.get(`/api/geo/comunas`)
    },
    usuario: {
        buscarRUN: (run)=>
            axios.get(`/api/usuarios/buscar?run=${run}`),
        nuevoOperador: (datos)=>
            axios.post(`/api/usuarios/nuevo-operador`, datos)
    }
}