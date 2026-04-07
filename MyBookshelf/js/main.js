const btnFilter = document.getElementById("filter");

btnFilter.onclick(toggleFilters);

function toggleFilters() {
        const panel = document.getElementById('filter-panel');
        if (btnFilter.style.display == "block") {
            btnFilter.style.display = "none";
        } else {
            btnFilter.style.display = "block";
        }
}



