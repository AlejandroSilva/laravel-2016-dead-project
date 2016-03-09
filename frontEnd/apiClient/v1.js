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
            return axios.get(`/locales/${idLocal}`)
        },
        getVerbose: (idLocal)=>{
            return axios.get(`/locales/${idLocal}/verbose`)
        }
        // Demora artificial para probar los mensajes de carga
        //getVerbose: (idLocal)=>{
        //    return new Promise((resolve, reject)=>{
        //        axios.get(`/locales/${idLocal}/verbose`)
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
            return axios.post(`/inventario/nuevo`, datos)
        },
        actualizar: (idInventario, datos)=>{
            return axios.put(`/inventario/${idInventario}`, datos)
        }
    }
}