# SIG ..

## API's publicas disponibles:

#### Informar la recepcion de archivos finales de nominas:
```
metodo:
    POST
```
```
URL:
    api/inventario/informar-archivo-final
```
```
form-data:
    'idCliente' => 'required|numeric',
    'ceco' => 'required|numeric',
    'fechaProgramada' => 'required|date',
    'unidadesReal' => 'required|numeric',
    'unidadesTeorico' => 'required|numeric'
```
```
Respuesta:
    400: al existir un error, el cuerpo contiene la descripcion del error
    200: al guardar correctamente, el cuerpo contiene un json con el inventario actualizado
```

#### Informar la url para la descarga de nominas de pago para los inventarios
```
metodo:
    POST
```
```
URL:
    http://sig.seiconsultores.cl/api/nomina/cliente/{idCliente}/ceco/{ceco}/dia/{YYYY-MM-DD}/informar-nomina-pago
```
```
form-data:
    'urlNominaPago' => 'required|string',
```
```
Respuesta:
    400: al existir un error, el cuerpo contiene la descripcion del error
    200: al guardar correctamente, el cuerpo contiene un json con el inventario actualizado
```
```
Ejemplo:
    METHOD
        POST
    URL
        http://sig.seiconsultores.cl/api/nomina/cliente/2/ceco/492/dia/2016-07-06/informar-nomina-pago
    FORM-DATA
        urlNominaPago = http://inventario.seiconsultores.cl/fcvnominafinal/descarga/551 
```

## solucion de errores en archivos autogenerados de laravel:

```
   php artisan cache:clear
   php artisan config:cache
   php artisan clear-compiled
   composer dump-autoload
```