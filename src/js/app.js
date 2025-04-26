const mobileBtnMenu = document.querySelector('#mobile-menu');
const sidebar = document.querySelector('.sidebar');
const cerrarBtnMenu = document.querySelector('#cerrar-menu');

if(mobileBtnMenu){
    mobileBtnMenu.addEventListener('click',function (){
        sidebar.classList.add('mostrar');
    })
}

if(cerrarBtnMenu){
    cerrarBtnMenu.addEventListener('click',function (){
        sidebar.classList.add('ocultar');
        setTimeout(() => {
            sidebar.classList.remove('mostrar');
            sidebar.classList.remove('ocultar');
            
        }, 1000);
    })
}

//elimina la clase de mostrar en tamaño tablet y mayores
const px= document.body.clientWidth;
window.addEventListener('resize', function(){
    const px= document.body.clientWidth;
    if (px > 768){
        sidebar.classList.remove('mostrar');
    }
})