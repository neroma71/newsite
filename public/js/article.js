document.addEventListener("DOMContentLoaded", () => {

    async function loadArticle(url) {
        try {
            const res = await fetch(url);
            const html = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");

            const newContainer = doc.querySelector(".container");
            const newNav = doc.querySelector(".nav-btn");
            const newHeader = doc.querySelector("header h1");

            if (newContainer) document.querySelector(".container").outerHTML = newContainer.outerHTML;
            if (newNav) document.querySelector(".nav-btn").outerHTML = newNav.outerHTML;
            if (newHeader) document.querySelector("header h1").textContent = newHeader.textContent;

            window.scrollTo({ top: 0, behavior: "smooth" });
            history.pushState({}, "", url);

        } catch (err) {
            console.error("Erreur navigation article:", err);
        }
    }

    document.addEventListener("click", e => {
        const link = e.target.closest(".nav-btn a");
        if (!link) return;
        e.preventDefault();
        loadArticle(link.href);
    });

    window.addEventListener("popstate", () => {
        const url = location.href;
        if (document.querySelector(".container")) {
            loadArticle(url);
        }
    });

});
