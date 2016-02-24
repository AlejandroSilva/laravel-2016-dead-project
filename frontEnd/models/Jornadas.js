/**
 * se crea este modelo statico, que es un espejo de la informacion en la
 * tabla "Jornadas", para evitar que se realicen llamados a la BD, se asume
 * que estos datos no cambian, por lo que se evita tener que realizar consultas
 */

const NoSeleccionada = {
    idJornada: 1,
    nombre: "no definido",
    descripcion: "no se ha definido una jornada por defecto",
    dia: "0",
    noche: "0"
}
const Dia = {
    idJornada: 2,
    nombre: "dia",
    descripcion: "el inventario se realizara dentro del dia.",
    dia: "1",
    noche: "0"
}
const Noche= {
    idJornada: 3,
    nombre: "noche",
    descripcion: "el inventario se realizara en la tarde/noche.",
    dia: "0",
    noche: "1"
}
const DiaNoche= {
    idJornada: 4,
    nombre: "día y noche",
    descripcion: "el inventario se realizara en dos turnos.",
    dia: "1",
    noche: "1"
}

export default {
    NoSeleccionada,
    Dia,
    Noche,
    DiaNoche,
    // asArray se utiliza para buscar por indice, Ej. JORNADA.asArray[2] retorna el objeto 'Dia'
    asArray: [NoSeleccionada, NoSeleccionada, Dia, Noche, DiaNoche]
}