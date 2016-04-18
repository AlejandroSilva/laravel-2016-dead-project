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
        getLocales: (idCliente)=>{
            return axios.get(`/api/cliente/${idCliente}/locales`)
        }
    },
    locales: {
        get: (idLocal)=>{
            return axios.get(`/api/locales/${idLocal}`)
        },
        getVerbose: (idLocal)=>{
            return axios.get(`/api/locales/${idLocal}/verbose`)
        }
    },
    inventario: {
        nuevo: (datos)=>{
            return axios.post(`/api/inventario/nuevo`, datos)
        },
        actualizar: (idInventario, datos)=>{
            return axios.put(`/api/inventario/${idInventario}`, datos)
        },
        eliminar: (idInventario)=>{
            return axios.delete(`/api/inventario/${idInventario}`)
        },
        getPorRango: (fechaInicio, fechaFin)=>{
            return axios.get(`/api/inventario/${fechaInicio}/al/${fechaFin}`)
        },
        getPorMesYCliente: (annoMesDia, idCliente)=> {
            return axios.get(`/api/inventario/${annoMesDia}/cliente/${idCliente}`)
        },
        getPorRangoYCliente: (fechaInicio, fechaFin, idCliente)=>{
            return axios.get(`/api/inventario/${fechaInicio}/al/${fechaFin}/cliente/${idCliente}`)
        }
    },
    auditoria: {
        nuevo: (datos)=>{
            return axios.post(`/api/auditoria/nuevo`, datos)
        },
        actualizar: (idAuditoria, datos)=>{
            return axios.put(`/api/auditoria/${idAuditoria}`, datos)
        },
        eliminar: (idAuditoria)=>{
            return axios.delete(`/api/auditoria/${idAuditoria}`)
        },
        getPorMesYCliente: (annoMesDia, idCliente)=> {
            return axios.get(`/api/auditoria/mes/${annoMesDia}/cliente/${idCliente}`)
        },
        getPorRangoYCliente: (fechaInicio, fechaFin, idCliente)=>{
            return axios.get(`/api/auditoria/${fechaInicio}/al/${fechaFin}/cliente/${idCliente}`)
        }
    },
    nomina: {
        actualizar: (idNomina, datos)=>{
            return axios.put(`/api/nomina/${idNomina}`, datos)
        }
    }
}