


//const obtenerVerificador = (rutCompleto)=>{
var obtenerVerificador = function(rutCompleto){
    // limpiar los caracteres innecesarios
    var rut = rutCompleto.trim().split('.').join('')

    // tomar cada uno de los valores y ponerlos al revez
    var rutReverso = rut.split('').reverse()

    // multiplicar los numeros por la serie 2, 3, 4, 5, 6, 7, repetir si faltan numeros
    var serie = [2, 3, 4, 5, 6, 7]
    var totalSuma = rutReverso.reduce(function(acumulado, actual, index){
        var nroSerie = serie[index%serie.length]
        return acumulado +  nroSerie*actual
    }, 0)

    // calcular el modulo de 11, y con esto obtener el verificador
    var restoSuma = totalSuma%11
    var verificador = 11-restoSuma

    if(verificador==11)
        return '0'
    if(verificador==10)
        return 'K'
    return ''+verificador
}

const validarRUN = (rutCompleto, digitoVerificador)=>{
    if(!rutCompleto || rutCompleto.trim()=='')
        return false
    if(!digitoVerificador || digitoVerificador.trim()=='')
        return false
    if(digitoVerificador==='k')
        digitoVerificador = 'K'

    let dv = obtenerVerificador(rutCompleto)
    //console.log(`debug, el verificador es: '${rutCompleto}', '${digitoVerificador}'==${dv} : ${dv==digitoVerificador}`)
    return dv==digitoVerificador
}

export {
    obtenerVerificador,
    validarRUN
}