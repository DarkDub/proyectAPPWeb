function verPregunta(btn) {
    const info = `
        <p><strong>ID:</strong> ${btn.dataset.id}</p>
        <p><strong>Pregunta:</strong><br>${btn.dataset.pregunta.replace(/\n/g,'<br>')}</p>
        <p><strong>Opción A:</strong> ${btn.dataset.opcion_a}</p>
        <p><strong>Opción B:</strong> ${btn.dataset.opcion_b}</p>
        <p><strong>Opción C:</strong> ${btn.dataset.opcion_c}</p>
        <p><strong>Opción D:</strong> ${btn.dataset.opcion_d}</p>
        <p><strong>Correcta:</strong> ${btn.dataset.correcta.toUpperCase()}</p>
        <p><strong>Lección:</strong> ${btn.dataset.leccion}</p>
    `;
    document.getElementById("infoPregunta").innerHTML = info;
    new bootstrap.Modal(document.getElementById("modalVerPregunta")).show();
}

function editarPregunta(btn) {
    document.getElementById("editPreguntaId").value = btn.dataset.id;
    document.getElementById("editPreguntaTexto").value = btn.dataset.pregunta;
    document.getElementById("editOpcionA").value = btn.dataset.opcion_a;
    document.getElementById("editOpcionB").value = btn.dataset.opcion_b;
    document.getElementById("editOpcionC").value = btn.dataset.opcion_c;
    document.getElementById("editOpcionD").value = btn.dataset.opcion_d;
    document.getElementById("editCorrecta").value = btn.dataset.correcta;
    document.getElementById("editLeccion").value = btn.dataset.leccion;
    new bootstrap.Modal(document.getElementById("modalEditarPregunta")).show();
}
