NutZen
======

```
Disclaimer: el contenido de este repositorio es completamente subjetivo a mi
concepcion de los temas aqui tratados. Si bien trato de adecuarme a las buenas
practicas del mundillo del desarrollo web, esto no deberia ser tomado como LA
FORMA de hacer las cosas, sino simplemente como una guia para comprender mejor
que es lo que sucede en determinadas situaciones y porque optamos por resolver
de cierta forma y no de otra.
```

De la palabra germana `nutzen` (aprovechar, usar) o su descomposición en las
palabras (completamente inconexas) `nut` (loco) y `zen` (estado de paz).

Creando nuestro primer sitio web, el antes y despues de leer esto:

|  antes  |  después  |
|:-------:|:---------:|
|  :rage: | :relaxed: |
|   Nut   |    Zen    |

La idea de este proyecto es explicar de la forma mas sencilla posible el
funcionamiento de una aplicación web basada en estándares utilizados en
frameworks como [Rails][], [Django][] o [Laravel][].

Mi intención no es entrar demasiado en detalle ya que hay libros enteros
dedicados a cada instancia de este proceso, por lo cual el siguiente texto no
_deberia_ extenderse demasiado.


Tecnologías que usaremos:
-------------------------

- [PHP][]
- [CSS][]
- [Nginx][]
- [MySQL][]

Mas tecnologías utilizadas pero no explicadas (lo dejamos para el curioso):

- [Vagrant][]
- [Ansible][]

Temas a tocar
-------------

- Introducción:
  - Interacción entre clientes y servidores.
  - Expectativas y requisitos de nuestra aplicación

- Interacción entre el servidor web y PHP

- La aplicación y sus partes
  - Disposición y estructura de directorios
  - Objetos Núcleo y sus responsabilidades:
    - Application
    - Request
    - Router
    - Route
    - Controller
    - Model
    - View
    - Response

Introducción
------------

Para poder arrancar primero tenemos que comprender, aunque sea muy por arriba,
como funciona una pagina web desde el principio. ¿Como pedimos una pagina web?,
¿a quien se la estamos pidiendo? y ¿exactamente que estamos pidiendo?.


```
        _____
       /  _)))
      (___|''-
        ; _=                 REQUEST: 'http://farfanoi.de/index.html'      _______________
     ___//_   /_   ______                                                 |  ___________  |
    /)  \/ )  ))  |.     |_                   .-,(  ),-.                  | |           | |
   //| - -/\\/;   |.     |:|  --------->   .-(          )-.   --------->  | |   0   0   | |
  |/ |   /  \/    |.     |/               (    internet    )              | |     -     | |
  ;  :::::        |______|                 '-(          ).-'              | |   \___/   | |
_(/ //////\\\\\    __||__     <---------       '-.( ).-'      <---------  | |___     ___| |
/|_//////// / /___[______]_                                               |_____|\_/|_____|
          |/|/                                                                  |\|/|
          | |               RESPONSE: '<html> jellou worlds </html>'       / ************ \
         (|(|                                                             /  ************* \
        ,||||                                                            --------------------
         '='=
```

Dentro de todo, esto es bastante simple. La comunicación se da entre clientes y
servidores, siendo el cliente quien requiere una pagina (recurso) por por medio
de una URL y el servidor quien responde con dicha pagina la cual no es mas que
texto empaquetado en un http response que sera leído por el navegador del
cliente y representado en pantalla.

Tomemos una URL cualquiera como ejemplo:

```
http://farfanoi.de/index.html?offset=3
```

Lo primero que deberíamos hacer seria identificar las distintas partes dentro de
este string:

```
   http://farfanoi.de   /index.html    offset=3
  '-----------------'   '---------'   '--------'
          V                  V            V
      Dominio             Recurso       Parametros
```


Muy bien, ahora podemos asignarle una labor a cada uno de estos extractos. Si
bien la intervención de cada uno dentro del ciclo de vida de un request/response
puede verse entremezclada en ocasiones, vamos a simplificarnos y considerar que:

- El `Dominio` es de lo que se valdrá la gran y aterradora burbuja conocida como
  internet para hacer que el request (o pedido) llegue efectivamente a nuestro
  servidor.

- El `Recurso` sera lo que efectivamente le estamos pidiendo al servidor, podría
  ser un archivo HTML, CSS, JS, una imagen o un archivo PHP el cual debería ser
  ejecutado por el servidor para devolverme la información que realmente deseo ver.

- Los `Parametros` son una lista en el formato `clave=valor` de la cual nos
  valemos para pasarle información a nuestra aplicación

Entonces ya podemos introducir algunos conceptos nuevos y relacionarlos
directamente a las partes que decodificamos de la URL:

- Scary Internet: Se encarga de rutear el requerimiento/respuesta entre cliente
  y servidor

- Servidor web: Sera el encargado de buscar el recurso en caso de ser estático
  (CSS, JS, imagen) o invocara a nuestra aplicación para contenido dinámico.

- Aplicación web: Sera la encargada de procesar el request y devolver un
  response (respuesta) adecuado.

Resumiendo hasta aquí:

1. Un cliente inicializa un request
2. `The Scary Internet` se encarga de rutearlo hasta el servidor correspondiente
3. Un servidor web recibe el request y lo redirige a una aplicación (en caso
   que deba ser procesado) o directamente retorna el recurso requerido (en caso
   de ser estático)
4. Nuestra aplicación procesa el request y devuelve la respuesta al servidor web.
5. El servidor web reenvía la respuesta al cliente
6. `The Scary Internet` se encarga de rutearla hasta el cliente.
7. El navegador en el cliente parsea la respuesta (probablemente html) y lo
   muestra como una pagina web bonita con formatos imágenes y todas las cositas
   lindas que estamos acostumbrados a ver.


Interacción entre el servidor web y PHP
---------------------------------------


Todo nuestro codigo por mas modularizado y objetoso que parezca, en esencia no
es mas que un script, y es asi como el servidor lo maneja, lo invoca pasandole
informacion del request actual mediante variables de entorno las cuales nuestro
interprete se encargara de dejarnos disponibles.

Disposición de nuestra aplicación
----------------------------------

El primer paso al armar nuestra aplicación debería ser determinar cuales son
las expectativas que tenemos de la misma:

Nuevamente pecando de simplista me atrevo a enumerar lo siguiente:

- El código debe ser simple y legible con un solo punto de entrada para poder
  seguir mas fácilmente el flujo de ejecución de la aplicación.

- Debe respetar el patron [MVC][].

- Debe soportar [pretty urls][].


La aplicación y sus partes
--------------------------

Disposición y estructura de directorios:
----------------------------------------

```
nutzen/
|-- app/
|   |-- config/      # Archivos de configuracion
|   |-- controllers/ # Controladores
|   |-- core/        # Archivos con el nucleo de nuestra aplicacion
|   |-- models/      # Modelos
|   |-- views/       # Vistas
|   |-- init.php     # Script para unificar nuestra aplicacion
|-- public/          # Esta deberia ser la carpeta root para nuestro servidor web
|   |-- index.php    # Nuestro punto de entrada, incluye a init.php y corre nuestra app
|   |-- assets/      # Recursos estaticos (hojas de estilo, javascripts, imagenes, etc)
|   |   |-- css/
|   |   |-- img/
|   |   |-- js/
```

Objetos Núcleo y sus responsabilidades:
---------------------------------------

Antes de entrar en detalle en el funcionamiento particular de cada objeto
demarcaremos el flujo general y su interacción de la forma mas sencilla posible
siguiendo el `happy path` (ejecución correcta).

Arrancamos entonces por nuestro archivo `index.php` el cual hemos demarcado como
entry point a nuestra aplicación, este se encargara de requerir los archivos
necesarios para así disponer de nuestras clases y poder instanciar a nuestro
primer objeto real, nuestra `Application`.

Esta delega el `Request` actual al `Router` para ver si se condice con alguna de
las `Route` definidas.

De ser así un `Controller` ejecutara la lógica deseada, esto normalmente
involucra hacer uso de algún `Model` para buscar datos desde la BBDD y hacerlos
disponibles para su renderizado en una `View`.

Dicho renderizado se incorpora como el contenido de nuestro `Response` el cual
es retornado al servidor web para así volver hasta el cliente.

Así pues, siguiendo un formato de desarrollo 'outside-in' o 'afuera-adentro' ya
podríamos declarar la interfaz básica que popularía nuestro
[index.php](public/index.php):

```php

// -----------------------------------------------------------
// requerimos todas nuestras dependencias
// -----------------------------------------------------------

require_once dirname(__DIR__) . '/app/init.php';

// -----------------------------------------------------------
// Instanciamos nuestra aplicacion
// -----------------------------------------------------------

$app = new Application();

// -----------------------------------------------------------
// La ejecutamos con el request actual y recibimos un response
// -----------------------------------------------------------

$response = $app->run(Request::current());

// -----------------------------------------------------------
// Finalmente retornamos la respuesta al servidor web
// -----------------------------------------------------------

$response->send();
```

O dicho mas gráficamente:

```
,-----------.           ,------.        ,----------.        ,-----.    ,----.
|Application|           |Router|        |Controller|        |Model|    |View|
`-----+-----'           `--+---'        `----+-----'        `--+--'    `-+--'
     ,-. dispatch(request) ,-.               |                 |         |
     |X| ----------------->|X|               |                 |         |
     |X|                   |X|               |                 |         |
     |X|                   |X|  execute()    ,-.               |         |
     |X|                   |X| ------------->|X|               |         |
     |X|                   |X|               |X|               |         |
     |X|                   |X|               |X|    get()     ,-.        |
     |X|                   |X|               |X| ------------>|X|        |
     |X|                   |X|               |X|              |X|        |
     |X|                   |X|               |X|    Object    |X|        |
     |X|                   |X|               |X| <------------|X|        |
     |X|                   |X|               |X|              `-'        |
     |X|                   |X|               |X|    render(context)      ,-.
     |X|                   |X|               |X| ----------------------->|X|
     |X|                   |X|               |X|               |         |X|
     |X|                   |X|               |X|    HTML       |         |X|
     |X|                   |X|               |X| <-----------------------|X|
     |X|                   |X|               |X|               |         `-'
     |X|               Response              |X|               |         |
     |X| <-----------------------------------|X|               |         |
,----`-'----.           ,--`-'-.        ,----`-'---.        ,--+--.    ,-+--.
|Application|           |Router|        |Controller|        |Model|    |View|
`-----------'           `------'        `----------'        `-----'    `----'
```


> Nota: debido a la naturaleza efímera de una aplicación web la cual es
> instanciada una vez por cada request que llegue al servidor y en cuya
> ejecución solo tendremos una instancia de nuestra clase `Request`, una de
> `Application`, un `Router`, etc, utilizaremos el patron [singleton][] en
> varias ocasiones. Esto es simplemente una cuestión de diseño y se detallara el
> porque en cada caso que se utilice.


[Application](app/core/application.php)
-----------------------------------------

Este objeto se encarga de inicializar el recorrido del request así como también
controlar el flujo de ejecución y la respuesta en caso de errores.

Es importante remarcar que, para Nginx, una vez que logra despachar el request a
nuestro interprete de PHP el código de respuesta siempre sera un `200 OK` sin
importar lo que suceda dentro de nuestra aplicación, es por esto que nos
corresponde a nosotros responder con un codigo que represente adecuadamente el
estado de la respuesta.

Si bien la lista de códigos de estado es [extensa][http_status_codes] nosotros
nos limitaremos a unos pocos:

- `200 OK`: Respuesta correcta.
- `302 Redirect`: Redireccion a otra URL.
- `403 Unauthorized`: No se cuenta con los permisos necesarios para acceder a la
    URL requerida.
- `404 Not Found`: El recurso requerido no se encuentra en el servidor.
- `500 Internal Error`: Por alguna razón nuestra aplicación exploto(B-b-b-break!).



[Request](app/core/request.php)
---------------------------------

Para funcionar, nuestra aplicación depende de un `Request`, ahora bien, ¿que es
el request?, ¿que datos tiene?, ¿como se forma? Y ¿por que?

Cuando el servidor web invoca a nuestra aplicación pone a nuestra disposición
determinada información acerca de él mismo y del request actual, el problema es
que para accederla debemos hacerlo a través de variables
[superglobales][php_superglobals] de PHP lo cual hace a nuestro código poco
mantenible. Las variables que nos interesan en particular son las siguientes:

- `$_SERVER`: contiene información acerca del servidor.
- `$_GET`: contiene los parámetros pasados por `GET`.
- `$_POST`: contiene los parámetros pasados por `POST`.


Para facilitar entonces nuestro trabajo, crearemos la clase `Request` con la
cual encapsularemos el acceso a dichas variables.

Siguiendo la interfaz definida en `index.php` deberíamos crear el método de
clase `Request::current()`, el cual nos devolverá el request actual. Aplicaremos
el patron singleton aquí ya que no necesitamos mas que una instancia.

```php
  public static function current()
  {
    if (!isset(self::$current))
    {
      self::$current = self::fromSuperGlobals();
    }

    return self::$current;
  }
```

Esto nos lleva al método que realmente nos importa:
`Request::fromSuperGlobals()` el cual sera el que efectivamente cree la
instancia.

Es aqui donde nos encargaremos de sanitizar algunos datos como por ejemplo el
URI requerido. Este lo podemos encontrar en `$_SERVER`.

Al igual que todas las otras superglobales, `$_SERVER` es un arreglo asociativo
con lo cual podremos accederlo de la siguiente manera:

```php
$_SERVER['REQUEST_URI']
```

Supongamos que nos llega el request `http:nutzen.io/home?offset=3`. Si bien
dentro de `$_GET` dispondremos del parámetro `offset`, este también se encuentra
en el URI por lo cual simplemente lo partiremos en la primer aparicion del
caracter `?` (el cual actua como delimitador entre el URI y los parametros
recibidos por `GET`) y nos quedaremos unicamente con el primer elemento del
resultado.

A continuación le daremos soporte básico a nuestra aplicación para hacer [method
spoofing][method_spoofing] lo cual nos habilitara a hacer rutas restful mas
adelante.

Esta tarea comúnmente es realizada por algun middleware, pero la simplificaremos
y la incluiremos dentro del mismo objeto request comprobando haber recibido por
`POST` un parametro con nombre `_method` cuyo valor representara efectivamente
el metodo del request actual.


> Nota: no estamos corroborando que el metodo recibido sea soportado. Mas acerca
> de este tema en la seccion dedicada al ruteador.

Resta entonces solamente asegurarnos de tomar los parametros correspondientes:
En caso de ser un request `GET` utilizaremos aquellos alojados en la superglobal
`$_GET` caso contrario utilizaremos `$_POST`.


[Router](app/core/router.php)
-----------------------------

El `Router` o `Ruteador` es el encargado de conocer las rutas disponibles de la
aplicación y relacionarlas con la logica correspondiente para su correcta
atencion.

Veamos algunos ejemplos de configuraciones de rutas:

```php
Router::get('/', 'HomeController#index');
```

Esta es una tipicaconfiguracion para mostrar una pagina principal. Debemos
entender que estamos configurando nuestra aplicación para que sepa atender un
request por `GET` por el recurso `/` el cual sera atendido por una instancia del
`HomeController` a la cual se le enviara el mensaje `index`;

Un ejemplo de rutas para la gestion del CRUD de un recurso como usuarios se
podria ver asi:

```php
Router::get('/users', 'UsersController#index');
Router::get('/users/new', 'UsersController#new');
Router::post('/users', 'UsersController#create');
Router::get('/users/:id', 'UsersController#show');
Router::put('/users/:id/edit', 'UsersController#edit');
Router::put('/users/:id', 'UsersController#update');
Router::delete('/users/:id', 'UsersController#destroy');
```

Aqui podemos ver mas en detalle como podemos aprovechar los verbos HTTP para
agregar semantica a las rutas disponibles.

- `GET` para obtener recursos del servidor.
- `POST` para crear nuevos recursos en el servidor.
- `PUT` para modificar recursos ya existentes.
- `DELETE` para eliminarlos.

Lamentablemente los navegadores (ej: Chrome, Firefox, etc) otro methodo que no
sea `GET` o `POST` por lo cual nos vemos obligados a compensar por esta falencia
valiendonos de tecnicas como `method spoofing` para poder hacer uso de el resto.

Como ya vimos en la seccion del objeto `Request`, esta tecnica es bastante
simple y consta simplemente de mandar un parametro extra llamado `_method` en
nuestro request para asi reconocer en el servidor el verbo HTTP que se desea
utilizar.

Valiendonos de estos verbos, podemos entonces optimizar un poco la busqueda de
un match reduciendo la comparacion unicamente a las rutas determinadas
especificamente para un verbo en particular, lo cual nos brinda ademas una
pequenia capa extra de seguridad extra ya que no permitiriamos jamaz que se
ejecute codigo "destructivo" preparado para un request por `POST` cuando el
request atendido sea por `GET`.

__El orden en que las rutas son definidas es de suma importancia__



Router Extra:
-------------



[laravel]: http://laravel.com/
[rails]: http://rubyonrails.org/
[django]: https://www.djangoproject.com/
[php]: https://secure.php.net/
[css]: https://en.wikipedia.org/wiki/Cascading_Style_Sheets
[nginx]: https://www.nginx.com/
[mysql]: https://www.mysql.com/
[vagrant]: https://www.vagrantup.com/
[ansible]: http://www.ansible.com/
[sapi]: https://en.wikipedia.org/wiki/Server_Application_Programming_Interface
[php_vars]: https://github.com/php/php-src/blob/master/main/php_variables.c
[pretty urls]: https://en.wikipedia.org/wiki/Semantic_URL
[mvc]: https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller
[singleton]: https://en.wikipedia.org/wiki/Singleton_pattern
[http_status_codes]: http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
[php_superglobals]: http://php.net/manual/es/language.variables.superglobals.php
[uri]: https://es.wikipedia.org/wiki/Uniform_Resource_Identifier
[method_spoofing]: http://laravel.com/docs/master/routing#form-method-spoofing
