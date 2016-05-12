import axios from 'axios'

axios.interceptors.response.use((response)=>{
    // Si existe un problema de red, terminar la promesa
    if( response instanceof Error ){
        return Promise.reject(response.message)
    }else{
        return Promise.resolve(response.data)
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
        actualizar: (idNomina, datos)=>
            axios.put(`/api/nomina/${idNomina}`, datos)
    },
    geo: {
       comunas: ()=>
            axios.get(`/api/geo/comunas`)
    },
    usuario: {
        buscarRUN: (run)=>
            axios.get(`/api/usuario/buscar?run=${run}`),
        nuevoOperador: (datos)=>
            axios.post(`/api/usuario/nuevo-operador`, datos)
    }
}