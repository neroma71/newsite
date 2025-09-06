let body = document.querySelector('body');
let catHeader = document.querySelector('.categories-header');
let header = document.querySelector('header');
let title = document.querySelector('h1');
const slidingTitles = document.querySelectorAll('.slidingTitle');
const image = document.querySelector('.zoom');
const nav = document.querySelectorAll('nav ul li a');

// smooth scroll pour les liens d'ancre
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();

    const targetId = this.getAttribute('href');
    const target = document.querySelector(targetId);

    if (target) {
      const targetY = target.offsetTop;
      smoothScrollTo(targetY, 1200); // 1500ms = 1.5s pour un scroll plus lent
    }
  });
});

function smoothScrollTo(targetY, duration = 1000) {
  const startY = window.scrollY;
  const distance = targetY - startY;
  const startTime = performance.now();

  function scroll(currentTime) {
    const timeElapsed = currentTime - startTime;
    const progress = Math.min(timeElapsed / duration, 1);

    const ease = progress < 0.5 
      ? 4 * progress * progress * progress 
      : 1 - Math.pow(-2 * progress + 2, 3) / 2;

    window.scrollTo(0, startY + distance * ease);

    if (timeElapsed < duration) {
      requestAnimationFrame(scroll);
    }
  }

  requestAnimationFrame(scroll);
}
// fin de smooth scroll

// Animation de l'image au scroll
image.style.transform = 'scale(1.7)'; 
function updateImageScale() {
    const scrollY = window.scrollY;
    const scale = Math.max(1.7 - scrollY / 1000, 1); 
    image.style.transform = `scale(${scale})`;
}
window.addEventListener('scroll', updateImageScale);
window.addEventListener('resize', updateImageScale);
updateImageScale();

window.addEventListener('scroll', ()=>{
      catHeader.style.backgroundPositionY = -window.scrollY / 3 + "px";
    });

const observer = new IntersectionObserver((entries) => {
  for (const entry of entries) {
    if (entry.isIntersecting) {
      entry.target.classList.add('active');
      observer.unobserve(entry.target);
    }
  }
}, {
  root: null,
  rootMargin: '0px',
  threshold: 0.25
});

for (const el of slidingTitles) {
  observer.observe(el);
}
// Gestion de la nav
for (const link of nav) {
  link.addEventListener("mouseenter", e => {
    const { left, width } = link.getBoundingClientRect();
    link.classList.toggle("hover-left", e.clientX - left >= width / 2);
    link.classList.toggle("hover-right", e.clientX - left < width / 2);
  });

  link.addEventListener("mouseleave", () => {
    link.classList.remove("hover-left", "hover-right");
  });
}
