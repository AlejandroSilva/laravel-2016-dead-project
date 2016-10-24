import Immutable from 'immutable'
import moment from 'moment'
import momentRange from 'moment-range'  // no quitar
import _ from 'lodash'

export class StateMachine{
    constructor(){
        this.calendar = []
        this.immutableCalendar = {}
        this.nominas = []
        this.lideres = []
        this.auditores = []
        this.usuariosUnicos = []
    }
    
    // a partir de dos fechas construye el calendario completo
    build_calendar(year, month) {
        let startDate = moment([year, month]);
        let firstDay = moment(startDate).startOf('month');
        let endDay = moment(startDate).endOf('month');
        let monthRange = moment.range(firstDay, endDay);

        let weeksNumbers = [];
        // weeks = arreglo con el numero de cada una de las semanas que componen el mes
        // todo eliminar la necesidad de usar moment-range
        monthRange.by('days', function(moment) {
            var ref = moment.week()     // numero de la semana
            if (weeksNumbers.indexOf(ref)<0)
                return weeksNumbers.push(moment.week());
        });
        
        let calendar = {
            weeks: weeksNumbers.map((weekNumber, index)=>{
                let days = []
                // Todo, que pasa el primero de enero? cuando la semana anterior es mayor
                if(index>0 && weekNumber<weeksNumbers[index-1]){
                    // la proxima semana es mas menor que la actual: entones pasamos el año
                    days = [1,2,3,4,5,6,7].map(weekDay=> moment([year, month]).add(1, "year").week(weekNumber).day(weekDay) )
                }else{
                    days = [1,2,3,4,5,6,7].map(weekDay=> moment([year, month]).week(weekNumber).day(weekDay) )
                }
                return {
                    weekNumber: weekNumber,
                    days: days.map(day=>{
                        return {
                            date: day.format('YYYY-MM-DD'),
                            sameMonth: day.month()==month && day.year()==year,
                            isWeekend: day.day()==6 || day.day()==0,
                            dayOfWeek: day.day(),
                            dayOfMonth: day.date(),
                            // estas variables se sobreescriben luego
                            usuarios: []    //{id: 11, nombre: 'asd', nominas: []}
                        }
                    }),
                    usuarios: []    //{id: 11, nombre: 'asd', nominas: []}
                }
            })
        }
        this.immutableCalendar = Immutable.fromJS(calendar)
        this.calendar = calendar
    }
    clean_calendar(){
        this.calendar = this.immutableCalendar.toJS()
    }
    set_usuarios(lideres, auditores){
        this.lideres = _.sortBy(lideres, 'id')
        this.auditores = _.sortBy(auditores, 'id')
        let todos = lideres.concat(auditores)
        this.usuariosUnicos = _.chain( todos )
            .uniqBy('id')
            .sortBy('id')
            .value()
        // agregar un dato para las nominas/auditorias sin lider/auditor
        this.usuariosUnicos.push({
            id: null,
            nombre: 'SIN LIDER'
        })

        // paso 1: asignar los USUARIOS a los DIAS - Por cada dia de la semana, tambien se inicia un arrelo con todos los usuarios
        this.calendar.weeks.forEach(week=>{
            let usuariosSemana = this.usuariosUnicos.map(usuario=> {
                let nuevoUsuario = {
                    id: usuario.id,
                    isSelected: true,
                    nombre: usuario.nombre,
                    // se completan despues (en el paso 3)
                    nominasSemana: [],
                    nominasDia: [[], [], [], [], [], [], []],//(Array(7)).fill([])
                    auditoriasSemana: [],
                    auditoriasDia: [[], [], [], [], [], [], []], //(Array(7)).fill([])
                    maximoSemana: function maximoSemana() {
                        let maximoNominas = 0
                        let maximoAuditorias = 0
                        this.nominasDia.forEach(dia=> {
                            // cuenta las nominas SELECCIONADAS en un dia
                            //let maxDia = dia.filter(nom=>nom.isSelected).reduce((prev, actual)=>prev+1, 0)
                            let maxDia = dia.length
                            maximoNominas = maxDia>maximoNominas? maxDia : maximoNominas
                        })
                        this.auditoriasDia.forEach(dia=> {
                            // cuenta las nominas SELECCIONADAS en un dia
                            //let maxDia = dia.filter(aud=>aud.isSelected).reduce((prev, actual)=>prev+1, 0)
                            let maxDia = dia.length
                            maximoAuditorias = maxDia>maximoAuditorias? maxDia:maximoAuditorias
                        })
                        let maxTotal = maximoNominas + maximoAuditorias
                        return maxTotal == 0 ? 1 : maxTotal
                    },
                    totalNominas: function totalNominas() {
                        let total = 0
                        this.nominasDia.forEach(nominaDia=> {
                            total += nominaDia.length
                        })
                        return total
                    },
                    totalAuditorias: function totalAuditorias() {
                        let total = 0
                        this.auditoriasDia.forEach(dia=> {
                            total += dia.length
                        })
                        return total
                    }
                }
                return nuevoUsuario
            })
            // ambas listas de usuarios hacen referencia al mismo array
            week.usuarios = usuariosSemana
            week.days.forEach(day=>{
                day.usuarios = usuariosSemana
            })
        })
    }
    selectUsuario(idUsuario){
        // buscar el usuario en cada una de las semanas
        this.calendar.weeks.forEach(week=>{
            let usuarioW = _.find(week.usuarios, usuario=>usuario.id===idUsuario)
            usuarioW.isSelected = !usuarioW.isSelected
        })
    }
    filtrarLideres(mostrar){
        // buscar lideres en cada una de las semanas
        this.lideres.forEach(lider=>{
            this.calendar.weeks.forEach(week=>{
                let usuarioW = _.find(week.usuarios, usuario=>usuario.id===lider.id)
                usuarioW.isSelected = mostrar
            })
        })
    }
    filtrarAuditores(mostrar){
        // buscar auditores en cada una de las semanas
        this.auditores.forEach(lider=>{
            this.calendar.weeks.forEach(week=>{
                let usuarioW = _.find(week.usuarios, usuario=>usuario.id===lider.id)
                usuarioW.isSelected = mostrar
            })
        })
    }
    
    set_nominas(nominas){
        // paso 2: tomas la lista de nominas, y ASIGNAR cada nomina al DIA DEL CALENDARIO y al USUARIO que le corresponde
        // por cada nomina...
        this.nominas = nominas
        this.nominas.forEach(nom=>{
            nom.isSelected = true
            this.calendar.weeks.forEach(week=>{
                // ...se busca la SEMANA Y EL DIA que esta programada en el calendario...

                let day = _.find(week.days, wDay=>wDay.date===nom.fechaProgramada)
                if(day){
                    // ...cuando se encuentra el dia, se revisa a cada USUARIO UNICO si forma parte de la nomina,
                    // ya sea participando como LIDER, SUPERVISOR u OPERADOR
                    week.usuarios.forEach(usuarioW=>{
                        // el usuario es LIDER, SUPERVISOR, u OPERADOR de la nomina
                        let id = usuarioW.id
                        if(id==null){
                            // si la nomina NO TIENE LIDER asignado, se agrega al usuario "SIN LIDER"
                            if(id==nom.idLider) {
                                // se agrega la nomina al USUARIO DE LA SEMANA y al USUARIO DEL DIA
                                usuarioW.nominasSemana.push(nom)
                                usuarioW.nominasDia[day.dayOfWeek].push(nom)
                            }
                        }else{

                            if(id==nom.idLider || id==nom.idSupervisor || _.find(nom.operadores, idOperador=>idOperador==id) ){
                                // se agrega la nomina al USUARIO DE LA SEMANA y al USUARIO DEL DIA
                                usuarioW.nominasSemana.push(nom)
                                usuarioW.nominasDia[day.dayOfWeek].push(nom)
                            }
                        }
                    })
                }
            })
        })
        //console.log('calendar', this.calendar)
    }
    selectNominas(setSelect){
        this.nominas.forEach(nomina=>{
            nomina.isSelected = setSelect
        })
    }
    
    set_auditorias(auditorias){

        this.auditorias = auditorias
        this.auditorias.forEach(auditoria=>{
            auditoria.isSelected = true

            this.calendar.weeks.forEach(week=> {
                // ...se busca la SEMANA Y EL DIA que esta programada en el calendario...
                let day = _.find(week.days, wDay=>wDay.date === auditoria.fechaProgramada)
                if (day) {
                    week.usuarios.forEach(usuarioW=>{
                        let id = usuarioW.id
                        if(auditoria.idAuditor === id){
                            usuarioW.auditoriasSemana.push(auditoria)
                            usuarioW.auditoriasDia[day.dayOfWeek].push(auditoria)
                        }
                    })
                }
            })
        })
        console.log('calendar', this.calendar)
    }
    selectAuditorias(setSelect){
        this.auditorias.forEach(aud=>{
            aud.isSelected = setSelect
        })
    }
    
    // se analizan los datos, se construye un summary y finalmente se genera un estado para react
    get_state(){
        let calendarState = {
            weeks: this.calendar.weeks.map(week=>{

                let days = week.days.map(day=>{
                    // Construir los rows del dia
                    // se generan arrays con espacios en blanco por cada nomina que tenga el usuario EN LA MISMA SEMANA
                    let rowsUsuarios = day.usuarios.map(usuarioDia=>{
                        // rowUsuario es un arreglo lleno con valores por defecto
                        let rowsEventos = []//new Array(usuarioDia.maximoSemana())
                        for(let index=0; index<usuarioDia.maximoSemana(); index++){
                            rowsEventos[index] = {
                                id: 'user'+usuarioDia.id+'vacia'+index,
                                usuarioId: usuarioDia.id,
                                col1: '',
                                col2: '',
                                col3: '.',
                                selected: usuarioDia.isSelected
                            }
                        }

                        // llenar los primeros EVENTOS con los valores de la nominas
                        let index = 0
                        usuarioDia.nominasDia[day.dayOfWeek].forEach(nomina=>{
                            // convertir la nomina a un "evento"
                            rowsEventos[index] = {
                                id: 'user'+usuarioDia.id+'nom'+nomina.id,
                                usuarioId: usuarioDia.id,
                                col1: `${nomina.cliente} ${nomina.local}`,
                                col2: nomina.comuna, //nomina.ciudad,
                                col3: nomina.dotTotal,
                                selected: usuarioDia.isSelected==true && nomina.isSelected
                            }
                            index += 1
                        })
                        usuarioDia.auditoriasDia[day.dayOfWeek].forEach(auditoria=>{
                            // convertir la auditoria a un "evento"
                            rowsEventos[index] = {
                                id: 'user'+usuarioDia.id+'aud'+auditoria.id,
                                usuarioId: usuarioDia.id,
                                col1: `${auditoria.cliente} ${auditoria.local}`,
                                col2: auditoria.comuna, //nomina.ciudad,
                                col3: 'AUD',
                                selected: usuarioDia.isSelected==true && auditoria.isSelected
                            }
                            index += 1
                        })
                        return rowsEventos
                    })

                    // concatenar todos los arrays en un unico array
                    let rows = []
                    rowsUsuarios.forEach(row=>{
                        rows = rows.concat(row)
                    })
                    
                    // la informacion del dia, para luego ser convertidas en "CARDS"
                    return {
                        idDay: week.weekNumber*1000+day.dayOfMonth,
                        sameMonth: day.sameMonth,
                        isWeekend: day.isWeekend,
                        number: day.dayOfMonth,
                        rows: rows
                    }
                })

                let rowsSummaries = week.usuarios.map(usuario=>{
                    let rowsSummaryUser = []

                    rowsSummaryUser[0] = {
                        id: 'user'+usuario.id,
                        idUsuario: usuario.id,
                        nombre: usuario.nombre,
                        isUserSelected: usuario.isSelected,
                        totalNominas: usuario.totalNominas(),
                        totalAuditorias: usuario.totalAuditorias()
                    }
                    for(let index=1; index<usuario.maximoSemana(); index++){
                        rowsSummaryUser[index] = {
                            id: 'user'+usuario.id+'void'+index,
                            idUsuario: -1, //usuario.id,
                            nombre: ' ',
                            totalNominas: null,
                            totalNominas: null
                        }
                    }
                    return rowsSummaryUser
                })
                let summaryRows = []
                rowsSummaries.forEach(row=>{
                    summaryRows = summaryRows.concat(row)
                })
                
                return {
                    idWeek: week.weekNumber,
                    summary: summaryRows,
                    days: days
                }
            })
        }
        
        console.log('state ', calendarState)
        console.log('------------------------------------------------------------------------')
        return calendarState
    }
}