//IIFE: Se protege con function para que no se pueda llamar desde otros archivos js, así no se mezclan
(function() {

    obtenerTareas();
    let tareas=[];


    //Boton para mostrar el modal de Agregar Tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', mostrarFormulario);

    //obtencion de las tareas por medio de la api de tareas
    async function obtenerTareas(){
        try {
            const id=obtenerProyecto();
            const url= `api/tareas?id=${id}`;
            const respuesta= await fetch(url);
            const resultado= await respuesta.json();

            //const { tareas } = resultado;
            tareas = resultado.tareas;
            mostrarTareas();

        } catch (error) {
            console.log(error);
        }
    }
    //mostrar las tareas
    function mostrarTareas(){
        limpiarTareas();

        if(tareas.length === 0){
        
            const contenedorTareas = document.querySelector('#listado-tareas');

            const textoNoTareas = document.createElement('LI');
            textoNoTareas.textContent= 'No hay tareas';
            textoNoTareas.classList.add('no-tareas');

            contenedorTareas.appendChild(textoNoTareas);
            return;
        }

        const estados = {
            0: 'Pendiente',
            1: 'Completa'
        };

        tareas.forEach(tarea => {
            const contenedorTarea= document.createElement('LI');
            contenedorTarea.dataset.tareaId= tarea.id;
            contenedorTarea.classList.add('tarea');
            
            const nombreTarea= document.createElement('P');
            nombreTarea.textContent= tarea.nombre;
            //nombreTarea.classList.add('tarea');
            
            const opcionesDiv= document.createElement('DIV');
            opcionesDiv.classList.add('opciones');
            
            //botones
            const btnEstadoTarea = document.createElement('BUTTON');
            btnEstadoTarea.classList.add('estado-tarea');
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`);
            btnEstadoTarea.textContent = estados[tarea.estado];
            btnEstadoTarea.dataset.estadoTarea = tarea.estado;
            btnEstadoTarea.ondblclick = function(){
                //se le pasa la copia del objeto para no modificarel objeto original en memoria
                cambiarEstadoTarea({...tarea});
            }
            
            const btnEliminarTarea = document.createElement('BUTTON');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.dataset.idTarea= tarea.id;
            btnEliminarTarea.textContent= "Eliminar";
            btnEliminarTarea.ondblclick = function (){
                popUpEliminarTarea({...tarea});
            }


            opcionesDiv.appendChild(btnEstadoTarea);
            opcionesDiv.appendChild(btnEliminarTarea);

            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(opcionesDiv);

            const listadoTareas= document.querySelector('#listado-tareas');
            listadoTareas.appendChild(contenedorTarea);

        });
    }

    function mostrarFormulario(){
        //Generamos elemento div y le añadimos class modal. Dentro formulario para añadir tarea
        const modal= document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML= `
            <form class="formulario nueva-tarea">
                <legend> Añade una nueva tarea</legend>
                <div class="campo">
                    <label>Tarea</label>
                    <input 
                        type="text" name="tarea" placeholder="Añadir tarea al proyecto actual" id="tarea"
                    />
                </div>
                <div class="opciones">
                    <input type="submit" class="submit-nueva-tarea" value="Añadir Tarea"/>
                    <button type="button" class="cerrar-modal">Cancelar</button>
                </div>
            </form>
        `;

        //Se añade clase para animar la entrada del modal
        setTimeout(() => {
            const formulario=document.querySelector('.formulario');
            formulario.classList.add('animar');
        }, 0);

        modal.addEventListener('click', function(e){
            e.preventDefault();
            if(e.target.classList.contains('cerrar-modal') ){
                //Se busca por click en clase cerrar-modal=boton cancelar dentro del modal
                
                const formulario=document.querySelector('.formulario');
                formulario.classList.add('cerrar');
                setTimeout(() => {
                    modal.remove();
                }, 500);
            }
            if(e.target.classList.contains('modal') ){
                //Si se clica fuera del formulario tambien se cierra la ventana dinamica
                const formulario=document.querySelector('.formulario');
                formulario.classList.add('cerrar');
                setTimeout(() => {
                    modal.remove();
                }, 500);
            }

            if(e.target.classList.contains('submit-nueva-tarea') ){
                //Si se clica en boton Añadir Tarea
                submitFormNewTask();
            }

        })

        document.querySelector('.dashboard').appendChild(modal);
    }

    function submitFormNewTask(){
        const tarea = document.querySelector('#tarea').value.trim();

        if(tarea === ''){
            //Si no se escribe tarea, se muestra alerta
            mostrarAlerta('error', 'El nombre de la tarea es obligatorio', document.querySelector('.formulario legend'));
            return;
        }

        agregarTarea(tarea);
    }

    //Mostrar alertas
    function mostrarAlerta(tipo, mensaje, referencia){
        //Prevenir creacion multiples alertas
        const alertaPrevia = document.querySelector('.alerta');
        if(alertaPrevia){
            alertaPrevia.remove();
        }

        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo); 
        alerta.textContent= mensaje;

        //Inserta la alerta antes del legend
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);

        //Eliminar la alerta despues de 5 segundos
        setTimeout(() => {
            alerta.remove();
        }, 3000);

    }

    //Consultar el servidor para añadir una nueva tarea al proyecto actual
    async function agregarTarea(tarea){
        //Construir la peticion
        const datos= new FormData();
        datos.append('nombre',tarea);
        datos.append('proyectoId',obtenerProyecto());

        try {
            const url= '/api/tarea';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();

            mostrarAlerta(resultado.tipo, resultado.mensaje, document.querySelector('.formulario legend'));

            if(resultado.tipo === 'exito'){
                const modal = document.querySelector('.modal');
                setTimeout(() => {
                    modal.remove();
                    //window.location.reload();
                }, 1500);

                //agregar el objeto de tarea al global de tareas
                const tareaObj = {
                    id: String(resultado.id),
                    nombre: tarea, 
                    estado: "0",
                    proyectoId: resultado.proyectoId
                }

                //se hace copia del anterior y se añade el nuevo objeto
                tareas = [...tareas, tareaObj];

                mostrarTareas();
            }

        } catch (error) {
            console.log(error);
        }
    }

    //Cambia el estado de una tarea en el objeto en memoria
    function cambiarEstadoTarea(tarea){
        const nuevoEstado = tarea.estado === "1" ? "0" : "1";
        tarea.estado = nuevoEstado;
        
        actualizarTarea(tarea);
    }

    async function actualizarTarea(tarea){
        const {estado, id, nombre, proyectoId} = tarea
        const datos= new FormData();

        datos.append('id',id);
        datos.append('nombre',nombre);
        datos.append('estado',estado);
        datos.append('proyectoId',obtenerProyecto());

        /* para probar los datos que se mandan al servidor 
        for( let valor of datos.values()){
            console.log(valor)
        }*/

        try {
            const url= 'api/tarea/actualizar'

            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado= await respuesta.json();

            if(resultado.respuesta.tipo === 'exito'){
                mostrarAlerta(resultado.respuesta.tipo, resultado.respuesta.mensaje,
                     document.querySelector('.contenedor-nueva-tarea'));
            }

            //crea nuevo array con la actualizacion de la nueva tarea, no se modifica el original mientras se hace la modificacion
            tareas = tareas.map(tareaMemoria => {
                if(tareaMemoria.id === id){
                    tareaMemoria.estado = estado;
                }
                return tareaMemoria;
            });

            mostrarTareas();

        } catch (error) {
            console.log(error);
        }

    }

    //Se confirma la eliminación de una tarea
    function popUpEliminarTarea(tarea){
        //Crea alerta pero no es muy visual
        //const respuesta = confirm('Eliminar');

        Swal.fire({
            title: "¿Seguro que quieres eliminar esta tarea?",
            showCancelButton: true,
            confirmButtonText: "Eliminar",
            cancelButtonText: `Cancelar`
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                eliminarTarea(tarea);
            }
          });

    }

    async function eliminarTarea(tarea){
        const {estado, id, nombre} = tarea;
        
        const datos= new FormData();
        datos.append('id',id);
        datos.append('nombre',nombre);
        datos.append('estado',estado);
        datos.append('proyectoId',obtenerProyecto());
        
        try {
            url= 'api/tarea/eliminar';
            const respuesta= await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado= await respuesta.json();
            console.log(resultado);

            if(resultado.respuesta.tipo === 'exito'){

                Swal.fire("Tarea eliminada correctamente", "", "success");

                //crea nuevo array con filter para excluir el eliminado. No se modifica el original mientras se hace la modificacion
                tareas = tareas.filter(tareaMemoria => tareaMemoria.id !== id);
                mostrarTareas();
            }
        } catch (error) {
            console.log(error)
        }
    }


    function obtenerProyecto(){
        const proyectoParams= new URLSearchParams(window.location.search);

        //fromEntries para acceder al objeto proyectoParams
        const proyecto= Object.fromEntries(proyectoParams.entries());
        return proyecto.id;
    }

    function limpiarTareas(){
        const listadoTareas = document.querySelector('#listado-tareas');
        //listadoTareas.innerHTML = '';

        while (listadoTareas.firstChild){
            listadoTareas.removeChild(listadoTareas.firstChild);
        }
    }
})();