NutZen
======

De la palabra germana `nutzen` (aprovechar, usar) o su descomposición en las
palabras, completamente inconexas, `nut` (loco) y `zen` (estado de paz).

El antes y depuse de leer esto:

 antes |  después
-------|---------
:rage: | :relaxed:
 Nut   |   Zen

La idea de este proyecto es explicar de la forma mas sencilla posible el
funcionamiento de una aplicación web basada en estándares utilizados en
frameworks como [Rails][], [Django][] o [Laravel][].

Mi intención no es entrar demasiado en detalle ya que hay materias enteras
dedicadas a cada instancia de este proceso, por lo cual el siguiente texto no
_deberia_ extenderse demasiado.


Tecnologías que usaremos:
-------------------------

- [PHP][]
- [CSS][]
- [Nginx][]
- [MySQL][]

[laravel]: http://laravel.com/
[rails]: http://rubyonrails.org/
[django]: https://www.djangoproject.com/
[php]: https://secure.php.net/
[css]: https://en.wikipedia.org/wiki/Cascading_Style_Sheets
[Nginx]: https://www.nginx.com/
[MySQL]: https://www.mysql.com/

Mas tecnologías utilizadas pero no explicadas (lo dejamos para el curioso):

- [Vagrant][]
- [Ansible][]

[Vagrant]: https://www.vagrantup.com/
[Ansible]: http://www.ansible.com/



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
        ; _=                                                                    _______________
     ___//_   /_    _________    REQUEST: 'http://farfanoi.de/index.html'      |  ___________  |
    /)  \/ )  ))   |.        |_                   .-,(  ),-.                   | |           | |
   //| - -/\\/;    |.        |:|  --------->   .-(          )-.   --------->   | |   0   0   | |
  |/ |   /  \/     |.        |/               (    internet    )               | |     -     | |
  ;  :::::         |_________|                 '-(          ).-'               | |   \___/   | |
_(/ //////\\\\\     __|___|__     <---------       '-.( ).-'      <---------   | |___     ___| |
/|_//////// / /____[_________]_                                                |_____|\_/|_____|
          |/|/                                                                       |\|/|
          | |                   RESPONSE: '<html> jellou worlds </html>'        / ************ \
         (|(|                                                                  /  ************* \
        ,||||                                                                  -------------------
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


Disposición de nuestra aplicación
----------------------------------

El primer paso al armar nuestra aplicación debería ser determinar cuales son
las expectativas que tenemos de la misma:

Nuevamente pecando de simplista me atrevo a enumerar lo siguiente:

- El código debe ser simple y legible con un solo punto de entrada para poder
  seguir mas fácilmente el flujo de ejecución de la aplicación.

- Debe respetar el patron [MVC][].

- Debe soportar [pretty urls][].


[pretty urls]: https://en.wikipedia.org/wiki/Semantic_URL
[mvc]: https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller




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
podríamos declarar la interfaz básica que popularía nuestro `index.php`:

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

echo $response->send();
```

O dicho mas graficamente:

![Alt text](http://g.gravizo.com/g?@startuml;
  participant Application;
  participant Router;
  participant Controller;
  participant Model;
  participant View;
  activate Application;
  activate Router;
  Application -> Router : dispatch(request);
  Router      -> Controller : execute();
  activate Controller;
    Controller  -> Model : get();
    activate Model;
    Model      --> Controller : Object;
    deactivate Model;
    Controller  -> View : render();
    activate View;
    View       --> Controller : HTML;
    deactivate View;
    Controller ->> Application : Response;
  deactivate Controller;
  deactivate Router;
  deactivate Application;
@enduml
)
