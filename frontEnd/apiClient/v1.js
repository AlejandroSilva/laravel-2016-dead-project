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
    locales: {
        get: (idLocal)=>
            axios.get(`/api/locales/${idLocal}`),
        getVerbose: (idLocal)=>
            axios.get(`/api/locales/${idLocal}/verbose`)
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
        agregarOperador: (idNomina, usuarioRUN, esTitular)=>
            axiosRaw.post(`/api/nomina/${idNomina}/operador/${usuarioRUN}`, {esTitular}),
        quitarOperador: (idNomina, usuarioRUN)=>
            axios.delete(`/api/nomina/${idNomina}/operador/${usuarioRUN}`)
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