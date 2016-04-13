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
    locales: {
        get: (idLocal)=>{
            return axios.get(`/api/locales/${idLocal}`)
        },
        getVerbose: (idLocal)=>{
            return axios.get(`/api/locales/${idLocal}/verbose`)
        }
        // Demora artificial para probar los mensajes de carga
        //getVerbose: (idLocal)=>{
        //    return new Promise((resolve, reject)=>{
        //        axios.get(`/api/locales/${idLocal}/verbose`)
        //            .then(data=>{
        //                setTimeout(()=>{
        //                    resolve(data)
        //                }, Math.random()*2000)
        //            })
        //            .catch(err=>reject(err))
        //    })
        //}
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
        getPorMes: (annoMesDia)=> {
            return axios.get(`/api/inventario/mes/${annoMesDia}`)
        },
        getPorRango: (fechaInicio, fechaFin)=>{
            return axios.get(`/api/inventario/${fechaInicio}/al/${fechaFin}`)
        },
        getPorRangoYCliente: (fechaInicio, fechaFin, idCliente)=>{
            return axios.get(`/api/inventario/${fechaInicio}/al/${fechaFin}/cliente/${idCliente}`)
        }
    },
    auditoria: {
        nuevo: (datos)=>{
            return axios.post(`/api/auditoria/nuevo`, datos)
        },
        getPorMes: (annoMesDia)=> {
            return axios.get(`/api/auditoria/mes/${annoMesDia}`)
        },
        actualizar: (idAuditoria, datos)=>{
            return axios.put(`/api/auditoria/${idAuditoria}`, datos)
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