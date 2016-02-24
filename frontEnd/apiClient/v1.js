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
    }
}