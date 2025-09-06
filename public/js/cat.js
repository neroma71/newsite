let category = document.querySelector('.category');

window.addEventListener('scroll',()=>{
    category.style.backgroundPositionY = -window.scrollY / 3 + "px";
});