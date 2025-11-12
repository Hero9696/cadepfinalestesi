# Informe de Vistas de Configuración (Settings)

Este documento detalla las vistas de Vue.js que se encuentran en el directorio `resources/js/pages/settings`, su propósito y la relación que guardan con sus controladores de Laravel correspondientes.

---

## 1. Gestión de Roles

### a. `RoleIndex.vue`

*   **Archivo:** `resources/js/pages/settings/RoleIndex.vue`
*   **Propósito:** Esta vista es la página principal para la gestión de roles. Muestra una tabla con todos los roles existentes, incluyendo su nombre, descripción y estado. Proporciona botones para navegar a la creación de un nuevo rol, editar uno existente o eliminarlo.
*   **Controlador Asociado:** `app/Http/Controllers/RoleController.php`

#### Relación y Flujo de Datos:

1.  **Renderizado Inicial:** El método `indexPage()` del `RoleController` es invocado por la ruta `role.index`. Este método renderiza la vista `settings/RoleIndex` a través de Inertia, mostrando la estructura de la página.
2.  **Obtención de Datos:** Una vez que el componente `RoleIndex.vue` se monta en el navegador, utiliza `axios` para hacer una petición GET a la ruta `role.json`. Esta petición es manejada por el método `index()` del controlador, que devuelve una respuesta JSON con la lista completa de roles. La vista usa esta lista para poblar la tabla.
3.  **Crear Rol:** Al hacer clic en "Nuevo Rol", la vista utiliza `router.visit()` de Inertia para navegar a la ruta `role.create`. El método `create()` del controlador renderiza la vista `RoleForm.vue` (ver siguiente sección).
4.  **Editar Rol:** El botón "Editar" de cada fila navega a la ruta `role.edit`, pasando el ID del rol. El método `edit()` del controlador (usando Route Model Binding) encuentra el rol y renderiza `RoleForm.vue`, pasándole los datos del rol a editar como `props`.
5.  **Eliminar Rol:** El botón "Eliminar" ejecuta `router.delete()` hacia la ruta `role.destroy`. El método `destroy()` en el controlador elimina el registro y, como la vista carga los datos por `axios`, se vuelve a llamar a `fetchRoles()` para refrescar la tabla tras una eliminación exitosa.

### b. `RoleForm.vue`

*   **Archivo:** `resources/js/pages/settings/RoleForm.vue` (No proporcionado, pero inferido de los controladores y otras vistas).
*   **Propósito:** Es un formulario único que sirve tanto para crear un nuevo rol como para editar uno existente. Contiene campos para el nombre del rol, su descripción y un selector para su estado.
*   **Controlador Asociado:** `app/Http/Controllers/RoleController.php`

#### Relación y Flujo de Datos:

*   **Modo Creación:** El método `create()` del controlador renderiza esta vista y le pasa la lista de `states` para poblar el selector de estado.
*   **Modo Edición:** El método `edit()` renderiza esta misma vista, pero además de los `states`, le pasa el objeto `role` que se está editando. La vista utiliza esta `prop` para rellenar los campos del formulario.
*   **Guardado (Crear):** Al enviar el formulario en modo creación, se hace una petición POST a la ruta `roles.store`. El método `store()` del controlador valida los datos, crea el nuevo rol en la base de datos y redirige al usuario a la página de índice (`role.index`) con un mensaje de éxito.
*   **Guardado (Editar):** Al enviar el formulario en modo edición, se hace una petición PUT/PATCH a la ruta `roles.update`. El método `update()` valida los datos, actualiza el rol existente y redirige al índice con un mensaje de éxito.

---

## 2. Gestión de Municipios

### a. `MunicipalityIndex.vue`

*   **Archivo:** `resources/js/pages/settings/MunicipalityIndex.vue`
*   **Propósito:** Muestra una tabla con la lista de todos los municipios, su ID, nombre y el departamento al que pertenecen. Permite la creación, edición y eliminación de municipios.
*   **Controlador Asociado:** `app/Http/Controllers/MunicipalityController.php` (No proporcionado, pero inferido).

#### Relación y Flujo de Datos:

1.  **Renderizado y Datos:** A diferencia de `RoleIndex`, esta vista sigue un enfoque más "puro" de Inertia. El método `index()` del `MunicipalityController` es el responsable tanto de renderizar la vista como de pasarle la lista de `municipalities` como `prop` inicial. Inertia se encarga de mantener esta `prop` actualizada.
2.  **Crear Municipio:** El botón "Nuevo Municipio" navega a la ruta `municipalities.create`, que renderiza la vista `MunicipalityForm.vue`.
3.  **Editar Municipio:** El botón "Editar" navega a la ruta `municipalities.edit`, pasando el ID del municipio. El controlador renderiza `MunicipalityForm.vue` con los datos del municipio a editar.
4im.  **Eliminar Municipio:** El botón "Eliminar" utiliza `router.delete()` para enviar una petición a la ruta `municipalities.destroy`. El controlador elimina el registro y redirige de vuelta al índice. Inertia recibe la nueva lista de municipios y el mensaje de éxito/error, y actualiza la página automáticamente.

### b. `MunicipalityForm.vue`

*   **Archivo:** `resources/js/pages/settings/MunicipalityForm.vue` (No proporcionado, pero inferido).
*   **Propósito:** Formulario para crear o editar un municipio. Contendría campos para el nombre del municipio y un selector para el departamento al que pertenece.
*   **Controlador Asociado:** `app/Http/Controllers/MunicipalityController.php`

#### Relación y Flujo de Datos:

*   **Carga:** Los métodos `create()` y `edit()` del controlador renderizan esta vista, pasándole la lista de `departments` para el selector y, en el caso de la edición, el objeto `municipality`.
*   **Envío:** El formulario se envía con `form.post()` (para `store`) o `form.put()` (para `update`) a las rutas correspondientes. El controlador procesa la petición y redirige al índice.

---

## 3. Gestión de Citas

### a. `AppointmentIndex.vue`

*   **Archivo:** `resources/js/pages/settings/AppointmentIndex.vue` (No proporcionado, pero inferido).
*   **Propósito:** Mostraría una lista o calendario de las citas agendadas. Permitiría ver detalles, editar, cancelar o crear nuevas citas.
*   **Controlador Asociado:** `app/Http/Controllers/AppointmentController.php` (No proporcionado, pero inferido).

#### Relación y Flujo de Datos:

*   El método `index()` del controlador renderizaría esta vista, pasándole una colección de `appointments` con sus relaciones (empleado, paciente, estado) para ser mostradas.

### b. `AppointmentForm.vue`

*   **Archivo:** `resources/js/pages/settings/AppointmentForm.vue`
*   **Propósito:** Un formulario para agendar una nueva cita o modificar una existente. Incluye selectores para el terapeuta y el estado de la cita, y un campo de fecha/hora.
*   **Controlador Asociado:** `app/Http/Controllers/AppointmentController.php` (No proporcionado, pero inferido).

#### Relación y Flujo de Datos:

1.  **Carga del Formulario:** Los métodos `create()` y `edit()` del controlador son los responsables de renderizar esta vista. Pasan como `props`:
    *   `employees`: Una lista de todos los empleados/terapeutas para el selector.
    *   `states`: Una lista de los posibles estados de una cita.
    *   `appointment` (solo en edición): El objeto de la cita que se está modificando.
2.  **Envío del Formulario:** La vista utiliza el hook `useForm` de Inertia.
    *   **Creación:** Se envía una petición POST a la ruta `appointments.store`. El método `store()` en el controlador valida los datos, crea el registro y redirige al índice.
    *   **Edición:** Se envía una petición PUT a la ruta `appointments.update`. El método `update()` valida, actualiza el registro y redirige.

---

## 4. Gestión de Donantes

### a. `DonorIndex.vue`

*   **Archivo:** `resources/js/pages/settings/DonorIndex.vue` (No proporcionado, pero inferido del controlador `DonorController`).
*   **Propósito:** Vista principal para la gestión de donantes. Muestra una tabla paginada con la información de los donantes y un campo de búsqueda para filtrar los resultados.
*   **Controlador Asociado:** `app/Http/Controllers/DonorController.php`

#### Relación y Flujo de Datos:

1.  **Carga y Paginación:** El método `index()` del controlador se encarga de todo. Recibe los parámetros de búsqueda (`search`) de la URL, filtra la consulta a la base de datos, pagina los resultados y renderiza la vista `DonorIndex`, pasándole los `donors` paginados y los `filters` actuales.
2.  **Búsqueda:** Cuando el usuario escribe en el campo de búsqueda, la vista vuelve a hacer una petición a la misma ruta `donors.index` pero añadiendo el parámetro `search`. El controlador vuelve a ejecutar la lógica de filtrado y devuelve la vista con los resultados actualizados. Inertia maneja esto de forma eficiente.
3im.  **Navegación:** Los botones de "Crear", "Editar" y "Ver" navegan a las rutas `donors.create`, `donors.edit` y `donors.show` respectivamente, las cuales son manejadas por sus métodos correspondientes en el `DonorController`.

### b. `DonorForm.vue` y `DonorShow.vue`

*   **Archivos:** `resources/js/pages/settings/DonorForm.vue` y `resources/js/pages/settings/DonorShow.vue` (No proporcionados, pero inferidos).
*   **Propósito:** `DonorForm` es el formulario de creación/edición, y `DonorShow` es una vista de solo lectura para ver los detalles de un donante y sus donaciones asociadas.
*   **Controlador Asociado:** `app/Http/Controllers/DonorController.php`

#### Relación y Flujo de Datos:

*   Los métodos `create`, `store`, `edit`, `update`, `show` y `destroy` del `DonorController` siguen el patrón estándar de Inertia:
    *   `create` y `edit` renderizan `DonorForm.vue`.
    *   `show` renderiza `DonorShow.vue`, pasándole el donante con sus donaciones (`$donor->load('donations')`).
    *   `store`, `update` y `destroy` procesan la petición y redirigen con `Redirect::route()` o `Redirect::back()`, llevando consigo mensajes de éxito (`->with('success', '...')`). Inertia se encarga de mostrar estos mensajes en la siguiente vista.

---

## 5. Gestión de Usuarios

*   **Archivos:** `UserIndex.vue` y `UserForm.vue` (No proporcionados, pero inferidos del `UserController`).
*   **Propósito:** Siguen la misma lógica que la gestión de roles. `UserIndex` muestra la lista de usuarios y `UserForm` es el formulario para crearlos o editarlos.
*   **Controlador Asociado:** `app/Http/Controllers/UserController.php`

#### Relación y Flujo de Datos:

*   El `UserController` está configurado para funcionar de manera muy similar al `RoleController`.
*   `indexPage()` renderiza `settings/UserIndex`.
*   `index()` sirve como endpoint de API para que `UserIndex` obtenga los datos con `axios`.
*   `create()` y `edit()` renderizan `settings/UserForm`, pasándole los `roles` y `states` necesarios para los selectores.
*   `store()` y `update()` manejan la lógica de guardado y redirigen a `users.index` o a la página anterior.

```

