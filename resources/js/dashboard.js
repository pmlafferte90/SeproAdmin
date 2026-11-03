import { Spanish } from "flatpickr/dist/l10n/es.js";

document.addEventListener("DOMContentLoaded", function() {
    if (window.TomSelect) {
        /*new TomSelect('#localidad', {
            placeholder: 'Seleccione una localidad',
            allowEmptyOption: true
        });*/ 
    }

    function capitalizarPrimeraLetra(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function formatearFechaYMes(fechaStr) {
        const [año, mes] = fechaStr.split('-');
        const fecha = new Date(año, parseInt(mes) - 1); // mes: 0-11
        const formato = fecha.toLocaleDateString('es-EC', { month: 'long', year: 'numeric' });
        return capitalizarPrimeraLetra(formato);
    }

    function renderTwoColTable(bodyId, items) {
        const tbody = document.getElementById(bodyId);
        tbody.innerHTML = '';
        if (!items || !items.length) {
            tbody.innerHTML = `
                <tr>
                <td colspan="2" class="text-center small text-muted">Sin datos</td>
                </tr>`;
            return;
        }
        items.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.cantidad}</td>
                <td>${item.descripcion}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    const btnBuscar    = document.getElementById('btnBuscar');
    const cuerpoTabla  = document.getElementById('extintorTableBody');

    btnBuscar.addEventListener('click', async function() {
        const localidad = document.getElementById('localidad').value;
        const revInput  = document.getElementById('fechaRevision');
        const recInput  = document.getElementById('fechaRecarga');
        const revInst   = revInput._flatpickr;
        const recInst   = recInput._flatpickr;
        const revDate   = revInst?.selectedDates[0] || null;
        const recDate   = recInst?.selectedDates[0] || null;

        const fechaRevision = revDate
        ? `${revDate.getFullYear()}-${String(revDate.getMonth()+1).padStart(2,'0')}-01`
        : '';
        const fechaRecarga = recDate
        ? `${recDate.getFullYear()}-${String(recDate.getMonth()+1).padStart(2,'0')}-01`
        : '';

        if (!localidad) {
            showToast('Selecciona una localidad', 'error');
            return;
        }

        if (!fechaRevision) {
            showToast('Selecciona la fecha de revisión', 'error');
            return;
        }

        const params = new URLSearchParams();
        params.append('fecha_revision', fechaRevision);
        if (fechaRecarga) params.append('fecha_prox_recarga', fechaRecarga);
        const url = `/api/extintorApis/${localidad}/detalle?${params.toString()}`;

        showLoading();
        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error(res.statusText);

        // Desestructuramos todo
        const {
            data: rows,
            novedades,
            accesorios_extintor,
            novedades_especiales,
            novedades_gabinete
        } = await res.json();

        cuerpoTabla.innerHTML = '';
        if (!rows.length) {
            // Update record count to 0
            document.getElementById('recordCount').textContent = '0';

            // Add the d-none class to hide the download button container
            document.getElementById('downloadButtonContainer').classList.add('d-none');
            document.getElementById('downloadButtonContainer').classList.remove('d-flex');

            cuerpoTabla.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center small text-muted">
                        No se encontraron registros
                    </td>
                </tr>`;

                ['resumenPQS', 'resumenCO2', 'resumenFOAM', 'resumenOTROS'].forEach(id => {
                    document.getElementById(id).innerHTML = '<li class="text-muted">Sin datos</li>';
                });

        } else {
            // Update record count with the number of rows
            document.getElementById('recordCount').textContent = rows.length;

            // Remove the d-none class and add the d-flex class to show the download button container
            document.getElementById('downloadButtonContainer').classList.remove('d-none');
            document.getElementById('downloadButtonContainer').classList.add('d-flex');

            rows.forEach((r, i) => {
                const fecha = formatearFechaYMes(r.fecha_revision);
                const fechaProx = formatearFechaYMes(r.fecha_prox_recarga);
                const pesoFormateado = parseFloat(r.peso).toString();

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${i + 1}</td>
                    <td>${r.codigo}</td>
                    <td>${r.nombre_ubicacion}</td>
                    <td>${fecha}</td>
                    <td>${fechaProx}</td>
                    <td>${r.clase}</td>
                    <td>${pesoFormateado} ${r.unidad || ''}</td>
                    <td>${r.observaciones}</td>
                `;
                cuerpoTabla.appendChild(tr);
            });

            ['resumenPQS', 'resumenCO2', 'resumenFOAM', 'resumenOTROS'].forEach(id => {
                document.getElementById(id).innerHTML = '';
            });

            const resumen = {};
            rows.forEach(r => {
                const clase = r.clase.trim();
                const pu = `${parseFloat(r.peso)} ${r.unidad || ''}`.trim();
                resumen[clase] = resumen[clase] || {};
                resumen[clase][pu] = (resumen[clase][pu] || 0) + 1;
            });

            // Mostrar resumen en cada tarjeta
            const resumenTargets = {
                'PQS': 'resumenPQS',
                'CO₂': 'resumenCO2',
                'CO2': 'resumenCO2',
                'FOAM': 'resumenFOAM',
                'H₂O': 'resumenFOAM',
                'H2O': 'resumenFOAM',
                'TIPO K': 'resumenFOAM'
            };

            const usados = {
                resumenPQS: false,
                resumenCO2: false,
                resumenFOAM: false,
                resumenOTROS: false
            };

            Object.entries(resumen).forEach(([clase, pesos]) => {
                const targetId = resumenTargets[clase.toUpperCase()] || 'resumenOTROS';
                const ul = document.getElementById(targetId);
                if (ul) {
                    ul.innerHTML = Object.entries(pesos)
                        .map(([pu, count]) => `<li>${count} ${clase} ${pu}</li>`)
                        .join('');
                    usados[targetId] = true;
                }
            });

            Object.entries(usados).forEach(([id, fueUsado]) => {
                if (!fueUsado) {
                    document.getElementById(id).innerHTML = '<li class="text-muted">Sin datos</li>';
                }
            });
        }

        renderTwoColTable('otrasNovedadesBody',    novedades);
        renderTwoColTable('accesoriosBody',        accesorios_extintor);
        renderTwoColTable('senalizacionBody',      novedades_especiales);
        renderTwoColTable('gabinetesBody',         novedades_gabinete);

        } catch (err) {
            console.error('Error al cargar datos:', err);
            showToast('Error al cargar datos', 'error');
        } finally {
            hideLoading();
        }
    });


    // Flatpickr para fechas
    if (window.flatpickr) {
        flatpickr("#fechaRevision", {
            dateFormat: "F Y",
            disableMobile: true,
            locale: Spanish,
            plugins: [
                new monthSelectPlugin({
                    shorthand: true, // Mostrar meses abreviados
                    dateFormat: "F Y", // Formato
                    altFormat: "F Y"
                })
            ]
        });

        flatpickr("#fechaRecarga", {
            dateFormat: "F Y",
            disableMobile: true,
            locale: Spanish,
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "F Y",
                    altFormat: "F Y"
                })
            ]
        });
    }

    window.exportarResumenExtintores = async function () {
        window.showLoading(); // Mostrar loading

        try {
            const localidad = document.getElementById('localidad').value;
            const revInput = document.getElementById('fechaRevision')._flatpickr;
            const recInput = document.getElementById('fechaRecarga')._flatpickr;

            const revDate = revInput?.selectedDates[0] || null;
            const recDate = recInput?.selectedDates[0] || null;

            if (!localidad) {
                throw new Error('Selecciona una localidad');
            }
            if (!revDate) {
                throw new Error('Selecciona la fecha de revisión');
            }
            if (!recDate) {
                throw new Error('Selecciona la fecha de recarga');
            }

            const fechaRevision = `${revDate.getFullYear()}-${String(revDate.getMonth()+1).padStart(2,'0')}-01`;
            const fechaRecarga = `${recDate.getFullYear()}-${String(recDate.getMonth()+1).padStart(2,'0')}-01`;

            // Construir URL con params
            const params = new URLSearchParams({
                id_localidad: localidad,
                fecha_revision: fechaRevision,
                fecha_prox_recarga: fechaRecarga
            });
            const url = `${window.location.origin}/api/reporte-resumen-extintores?${params}`;

            // Fetch como blob
            const resp = await fetch(url, { method: 'GET' });
            if (!resp.ok) {
                const errText = await resp.text();
                throw new Error(`Error ${resp.status}: ${errText}`);
            }
            const blob = await resp.blob();

            // 1) Intenta extraer el nombre del header Content-Disposition
            let filename = 'resumen-extintores.xlsx'; // fallback
            const disposition = resp.headers.get('Content-Disposition') || '';
            const filenameMatch = disposition.match(/filename\*?=(?:UTF-8'')?["']?([^;"']+)["']?/i);
            if (filenameMatch && filenameMatch[1]) {
                filename = decodeURIComponent(filenameMatch[1]);
            }

            // 2) Crea y dispara el download
            const blobUrl = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = blobUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(blobUrl);

        } catch (e) {
            showToast('Hubo un error al descargar el archivo', 'error');
        } finally {
            // Siempre oculta el loading
            window.hideLoading();
        }
    };

});
