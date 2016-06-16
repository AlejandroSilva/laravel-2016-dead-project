import Immutable from 'immutable'
import moment from 'moment'
import momentRange from 'moment-range'  // no quitar
import _ from 'lodash'

export class BlackBox{
    constructor(){
        this.calendar = []
        this.immutableCalendar = {}
        this.nominas = []
        this.lideresUnicos = []
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
                            day: day.format('YYYY-MM-DD'),
                            sameMonth: day.month()==month && day.year()==year,
                            isWeekend: day.day()==6 || day.day()==0,
                            number: day.date(),
                            // estas variables se sobreescriben luego
                            lideres: [
                                //{id: 11, nombre: 'asd', nominas: []}
                            ]
                        }
                    })
                }
            })
        }
        this.immutableCalendar = Immutable.fromJS(calendar)
        this.calendar = calendar
    }
    
    set_inventarios(inventarios){
        // antes de cualquier cosa, se deben convertir "inventarios" a un arreglo de nominas (con informacion reducida)
        this.nominas = this.inventarios_to_nominas(inventarios)
        //console.log('nominas', this.nominas)

        // obtener la lista de lideres en el mes (solo los asignados a nominas validas)
        this.lideresUnicos = _.chain(this.nominas)
            .map(nom=>{ return {id: nom.idLider, nombre: nom.lider} })
            .uniqBy('id')
            .sort('id')
            .value()
        //console.log('lideres unicos', this.lideresUnicos)

        // "reiniciar" el calendar
        this.calendar = this.immutableCalendar.toJS()

        // paso 1: asignar los Lideres a las Semanas
        this.calendar.weeks.forEach(week=>{
            // Por cada dia de la semana, tambien se asigna un arrelo con lideres
            week.days.forEach(day=>{
                day.lideres = this.lideresUnicos.map(lider=>({
                    id: lider.id,
                    nombre: lider.nombre,
                    // se completan despues (en el paso 3)
                    nominas: []
                }))
            })
        })

        // paso 2: tomas la lista de nominas, y ASIGNAR cada nomina al DIA DEL CALENDARIO y al LIDER que le corresponde
        this.nominas.forEach(nom=>{
            // buscar el dia de la nomina en cada una de las semanas
            let momentFechaProgramada = moment(nom.fechaProgramada)
            this.calendar.weeks.forEach(week=>{
                let day = _.find(week.days, wDay=>momentFechaProgramada.isSame(wDay.day))
                // si encontramos el dia (y la semana de la nomina)
                if(day){
                    // agregar al nomina al lider de ese dia
                    let liderDia = _.find(day.lideres, liderD=>liderD.id==nom.idLider)
                    liderDia.nominas.push(nom)
                    return
                }
            })
        })
        console.log('calendar', this.calendar)
    }

    inventarios_to_nominas(inventarios){
        let nominas = []
        inventarios.forEach(inv=> {
            let ndia = inv.nomina_dia && inv.nomina_dia.habilitada == 1 ? inv.nomina_dia : null
            let nnoche = inv.nomina_noche && inv.nomina_noche.habilitada == 1 ? inv.nomina_noche : null
            let nomina = {
                fechaProgramada: inv.fechaProgramada,
                local: `${inv.local.cliente.nombreCorto} ${inv.local.numero}`,
                ciudad: inv.local.direccion.comuna.nombre,
                region: inv.local.direccion.comuna.provincia.region.nombreCorto,
            }

            if (ndia) {
                nominas.push(Object.assign({}, nomina, {
                    id: ndia.idNomina,
                    jornada: 'D',
                    dTotal: ndia.dotacionTotal,
                    dOperadores: ndia.dotacionOperadores,
                    lider: ndia.lider? (ndia.lider.nombre1 + ' ' + ndia.lider.apellidoPaterno) : 'no asignado',
                    idLider: ndia.lider? ndia.lider.id : 0
                }))
            }
            if (nnoche) {
                nominas.push(Object.assign({}, nomina, {
                    id: nnoche.idNomina,
                    jornada: 'N',
                    dTotal: nnoche.dotacionTotal,
                    dOperadores: nnoche.dotacionOperadores,
                    lider: nnoche.lider? (nnoche.lider.nombre1 + ' ' + nnoche.lider.apellidoPaterno) : 'no asignado',
                    idLider: nnoche.lider? nnoche.lider.id : 0
                }))
            }
        })
        return nominas
    }

    calcularSummaryLider_semana(week, lider){
        let maximoSemana = 0
        let totalSemana = 0

        // se recorre cada uno de los dias, revisando en cual de ellos el lider tiene mas nominas
        week.days.forEach(day=>{
            let liderDia = _.find(day.lideres, liderD=>liderD.id===lider.id)
            // maximo de semana
            let totalDia = liderDia.nominas.length
            maximoSemana = totalDia>maximoSemana? totalDia : maximoSemana
            // total semana
            totalSemana += totalDia
        })

        return {
            id: lider.id,
            nombre: lider.nombre,
            maximoSemana,
            totalSemana
        }
    }

    // se analizan los datos, se construye un summary y finalmente se genera un estado para react
    get_state(){
        let calendarState = {
            weeks: this.calendar.weeks.map(week=>{
                // todo: hacer en este punto el calculo del maximo de nominas por lider,
                let lideresConSummary = this.lideresUnicos.map(lider=> {
                    let liderConSummary = this.calcularSummaryLider_semana(week, lider)
                    console.log(liderConSummary)
                    return liderConSummary
                })

                
                let days = week.days.map(day=>{
                    // Construir los rows del dia
                    // se generan arrays con espacios en blanco por cada nomina que tenga el lider EN LA MISMA SEMANA
                    let rowsLideres = day.lideres.map(liderDia=>{
                        // buscar el liderDia, en el liderSemana
                        let liderSemana = _.find(lideresConSummary, liderS=>liderS.id===liderDia.id)

                        // se debe mostrar al menos una fila (vacia)
                        let totalRows = liderSemana.maximoSemana>0? liderSemana.maximoSemana : 1
                        // rowLider es un arreglo lleno con nulls
                        let rowLider = new Array(totalRows)
                        rowLider.fill(null)
                        // llenar los primeros valores con los valores de la nomina
                        liderDia.nominas.forEach((nomina, index)=>{
                            rowLider[index] = nomina
                        })
                        return rowLider
                    })
                    // concatenar todos los arrays en un unico array
                    let rows = []
                    rowsLideres.forEach(row=>{
                        rows = rows.concat(row)
                    })
                    
                    // la informacion del dia, para luego ser convertidas en "CARDS"
                    return {
                        idDay: week.weekNumber*1000+day.number,
                        sameMonth: day.sameMonth,
                        isWeekend: day.isWeekend,
                        number: day.number,
                        rows: rows
                    }
                })

                return {
                    idWeek: week.weekNumber,
                    summary: {
                        lideres: lideresConSummary
                    },
                    days: days
                }
            })
        }
        
        console.log('state ', calendarState)
        console.log('------------------------------------------------------------------------')
        return calendarState
    }
}