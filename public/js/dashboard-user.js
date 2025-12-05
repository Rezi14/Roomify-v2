document.addEventListener("DOMContentLoaded", function () {
    const checkboxes = document.querySelectorAll(".cek-fasilitas");
    const listSelected = document.getElementById("selectedFasilitas");

    function updateSelected() {
        listSelected.innerHTML = "";

        checkboxes.forEach((cb) => {
            if (cb.checked) {
                let badge = document.createElement("span");
                badge.className = "px-3 py-1 rounded-pill border text-primary";
                badge.style.fontSize = "13px";
                badge.style.background = "#eaf3ff";
                badge.textContent = cb.dataset.nama;
                listSelected.appendChild(badge);
            }
        });
    }

    checkboxes.forEach((cb) => cb.addEventListener("change", updateSelected));

    updateSelected();

    // RESET FILTER
    document
        .getElementById("btnResetJs")
        .addEventListener("click", function (e) {
            e.preventDefault();

            const form = this.closest("form");

            form.querySelectorAll("input[type='number']").forEach(
                (i) => (i.value = "")
            );
            form.querySelectorAll("input[type='checkbox']").forEach(
                (i) => (i.checked = false)
            );
            form.querySelector("select").selectedIndex = 0;

            form.submit();
        });
});
