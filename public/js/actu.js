document.addEventListener("DOMContentLoaded", () => {

    const container = document.querySelector("#actus");

    // crée un loader simple
    const loader = document.createElement("div");
    loader.className = "loader";
    loader.style.display = "none"; // caché par défaut
    loader.textContent = "Chargement...";
    container.parentNode.insertBefore(loader, container);

    function setLoading(on) {
        loader.style.display = on ? "block" : "none";
        container.style.opacity = on ? "0.5" : "1"; // effet visuel
    }

    async function loadPage(url) {
        try {
            setLoading(true);
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await res.text();
            const doc = new DOMParser().parseFromString(html, "text/html");

            // remplacer actus
            const newActus = doc.querySelector("#actus");
            if (newActus) container.innerHTML = newActus.innerHTML;

            // remplacer pagination
            const newPagination = doc.querySelector(".pagination");
            const paginationContainer = document.querySelector(".pagination");
            if (newPagination && paginationContainer) {
                paginationContainer.innerHTML = newPagination.innerHTML;
            }

            window.scrollTo({ top: 0, behavior: "smooth" });
            history.pushState({}, "", url);

        } catch (err) {
            console.error("Erreur chargement page:", err);
        } finally {
            setLoading(false);
        }
    }

    // clic sur liens de pagination
    document.addEventListener("click", e => {
        const link = e.target.closest(".pagination a");
        if (!link) return;
        e.preventDefault();
        loadPage(link.href);
    });

    // support back/forward
    window.addEventListener("popstate", () => {
        loadPage(location.href);
    });

});
