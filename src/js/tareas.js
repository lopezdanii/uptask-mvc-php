//IIFE: Se protege con function para que no se pueda llamar desde otros archivos js, así no se mezclan
(function() {

    //Boton para mostrar el modal de Agregar Tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', mostrarFormulario);

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



            //const btnCerrarModal= document.querySelector('.cerrar-modal');
            //btnCerrarModal.addEventListener('click',function(){

            //});


        })

        document.querySelector('body').appendChild(modal);
    }


})();