let category = document.querySelector('.category');

window.addEventListener('scroll',()=>{
    category.style.backgroundPositionY = -window.scrollY / 3 + "px";
});
document.addEventListener("DOMContentLoaded", () => {

    async function loadPagination(url) {
        try {
            const res = await fetch(url);
            const html = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");

            const newArticles = doc.querySelector(".articles");
            const newPagination = doc.querySelector(".pagination");

            if (newArticles) document.querySelector(".articles").outerHTML = newArticles.outerHTML;

            if (newPagination) {
                document.querySelector(".pagination").outerHTML = newPagination.outerHTML;
            } else {
                const oldPagination = document.querySelector(".pagination");
                if (oldPagination) oldPagination.remove();
            }

            window.scrollTo({ top: 0, behavior: "smooth" });
            history.pushState({}, "", url);

        } catch (err) {
            console.error("Erreur pagination:", err);
        }
    }

    document.addEventListener("click", e => {
        const link = e.target.closest(".pagination a");
        if (!link) return;
        e.preventDefault();
        loadPagination(link.href);
    });

    window.addEventListener("popstate", () => {
        const url = location.href;
        if (document.querySelector(".articles")) {
            loadPagination(url);
        }
    });

});
