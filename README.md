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


## solucion de errores en archivos autogenerados de laravel:

```
   php artisan cache:clear
   php artisan config:cache
   php artisan clear-compiled
   composer dump-autoload
```