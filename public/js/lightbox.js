function initializeLightbox() {
    // Évite d'installer plusieurs fois les événements globaux
    if (document.body.dataset.lightboxReady === "true") return;
    document.body.dataset.lightboxReady = "true";

    let count = 0;

    function getPics() {
        return document.querySelectorAll(".container img");
    }

    function removeClassActive() {
       for (const pic of getPics()) {
            pic.classList.remove("show");
        };
    }

    function closeLightbox() {
        removeClassActive();
        document.querySelector(".navigate")?.classList.remove("shownav");
        document.querySelector(".imgname")?.classList.remove("imgname2");
    }

    function toggleImage(index) {
        const pics = getPics();
        const text = document.querySelector(".imgname");
        const navbis = document.querySelector(".navigate");

        if (!pics.length || !text || !navbis) return;
        if (index < 0 || index >= pics.length) return;

        const currentPic = pics[index];

        if (currentPic.classList.contains("show")) {
            closeLightbox();
            return;
        }

        removeClassActive();

        currentPic.classList.add("show");
        navbis.classList.add("shownav");
        text.classList.add("imgname2");
        text.textContent = currentPic.alt || "";

        count = index;
    }

    // Image suivante
    function nextImage() {
        const pics = getPics();
        if (!pics.length) return;

        count = (count + 1) % pics.length;
        toggleImage(count);
    }

    // Image précédente
    function prevImage() {
        const pics = getPics();
        if (!pics.length) return;

        count = (count - 1 + pics.length) % pics.length;
        toggleImage(count);
    }

    // Gestion des clics
    document.addEventListener("click", (e) => {
        const pic = e.target.closest(".container img");

        if (pic) {
            const pics = getPics();
            toggleImage(Array.from(pics).indexOf(pic));
            return;
        }

        if (e.target.closest(".next")) {
            nextImage();
            return;
        }

        if (e.target.closest(".prev")) {
            prevImage();
        }
    });

    // Gestion du clavier
    document.addEventListener("keydown", (e) => {
        // Espace ou Échap : ferme la lightbox
        if (e.code === "Space" || e.key === "Escape") {
            e.preventDefault();
            closeLightbox();
            return;
        }

        // Les flèches fonctionnent uniquement si une image est ouverte
        const activeImage = document.querySelector(".container img.show");
        if (!activeImage) return;

        // Flèche droite clavier
        if (e.key === "ArrowRight") {
            nextImage();
        }

        // Flèche gauche clavier
        if (e.key === "ArrowLeft") {
            prevImage();
        }
    });
}

initializeLightbox();