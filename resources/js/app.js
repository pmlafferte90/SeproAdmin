import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap; // Esto asegura que bootstrap esté disponible globalmente
import $ from 'jquery'; // Importa jQuery
window.$ = window.jQuery = $; // Asigna jQuery a window para que esté disponible globalmente
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
// Este es el plugin de selección de mes
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";
import "flatpickr/dist/plugins/monthSelect/style.css";
import 'bootstrap-icons/font/bootstrap-icons.css';

window.flatpickr = flatpickr;
window.monthSelectPlugin = monthSelectPlugin;

import './helpers/ui';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';
window.TomSelect = TomSelect;


document.querySelectorAll('.nav-group-toggle').forEach(toggle => {
    toggle.addEventListener('click', function (event) {
        event.preventDefault(); // Evitar la navegación

        const navGroup = this.closest('.nav-group');
        const groupItems = navGroup.querySelector('.nav-group-items');

        // Alternar expansión del menú actual
        navGroup.classList.toggle('nav-group-expanded');

        // Ajustar altura del menú actual
        if (navGroup.classList.contains('nav-group-expanded')) {
            groupItems.style.maxHeight = groupItems.scrollHeight + 'px';
        } else {
            groupItems.style.maxHeight = '0px';
        }
    });
});

//filtros en cada columna
document.addEventListener("DOMContentLoaded", function () {
    function setupSearchFilter(tableSelector) {
        document.querySelectorAll(`${tableSelector} .search_column-btn`).forEach(button => {
            button.addEventListener("click", function (event) {
                event.stopPropagation();
                let column = this.getAttribute("data-column");
                let searchId = `search_column_popup_${column}`;

                // Eliminar popups previos
                document.querySelectorAll(".search_column-popup").forEach(popup => popup.remove());

                // Crear el popup flotante
                let popup = document.createElement("div");
                popup.classList.add("search_column-popup", "show");
                popup.id = searchId;
                popup.style.position = "absolute";
                popup.style.top = `${this.offsetTop + this.offsetHeight + 5}px`;  // Coloca el popup debajo del botón
                popup.style.left = `${this.offsetLeft}px`;  // Alinea el popup con el botón
                popup.innerHTML = `
                    <input type="text" class="form-control search_column-input" id="search_column_input_${column}"
                        data-column="${column}" placeholder="Buscar...">
                `;

                // Insertar popup en el DOM
                this.parentElement.appendChild(popup);

                // Enfocar input
                let input = document.getElementById(`search_column_input_${column}`);
                input.focus();

                // Manejar evento de filtrado
                input.addEventListener("keyup", function () {
                    let value = this.value.toLowerCase();
                    let rows = document.querySelectorAll(`${tableSelector} tbody tr:not(.no-results-row)`);
                    let hasActiveFilter = value.trim() !== "";
                    let visibleCount = 0;

                    rows.forEach(row => {
                        let cell = row.cells[column];
                        if (cell) {
                            let text = cell.textContent.toLowerCase();
                            if (text.includes(value)) {
                                row.style.display = "";
                                visibleCount++;
                            } else {
                                row.style.display = "none";
                            }
                        }
                    });

                    // Manejar mensaje de "No existen registros"
                    let tbody = document.querySelector(`${tableSelector} tbody`);
                    let noResultsRow = document.querySelector(".no-results-row");

                    if (visibleCount === 0) {
                        if (!noResultsRow) {
                            noResultsRow = document.createElement("tr");
                            noResultsRow.classList.add("no-results-row");
                            noResultsRow.innerHTML = `<td colspan="${rows[0].cells.length}" class="text-center text-muted">No existen registros</td>`;
                            tbody.appendChild(noResultsRow);
                        }
                    } else {
                        if (noResultsRow) {
                            noResultsRow.remove();
                        }
                    }

                    // Cambiar color del botón de lupa si hay un filtro activo
                    let searchButton = document.getElementById(`search_column_btn_${column}`);
                    if (hasActiveFilter) {
                        searchButton.classList.add("active");
                    } else {
                        searchButton.classList.remove("active");
                    }
                });
            });
        });

        // Ocultar popup al hacer clic fuera
        document.addEventListener("click", function () {
            document.querySelectorAll(".search_column-popup").forEach(popup => popup.remove());
        });
    }

    // Inicializar la función en todas las tablas
    setupSearchFilter(".table");
});


window.addEventListener('load', () => {
    setTimeout(() => {
        hideLoading(); // se asume que el loader está visible al inicio
    }, 300); // pequeño delay para una mejor experiencia visual
});


function generarUsername(nombre, apellido, usuarioInput) {
    if (!nombre || !apellido) {
    console.error("Nombre y apellido son requeridos para generar el username.");
    return;
    }
  const baseUsername = `${nombre.charAt(0)}.${apellido}`.replace(/\s+/g, '').toLowerCase();

  fetch(`/usuario/check/username?username=${encodeURIComponent(baseUsername)}`)
    .then(res => res.json())
    .then(json => {
      if (json.success && json.username) {
        usuarioInput.value = json.username;
      } else {
        usuarioInput.value = baseUsername;
      }
    })
    .catch(error => {
      console.error('Error verificando username:', error);
      usuarioInput.value = baseUsername;
    });
}

window.generarUsername = generarUsername;


