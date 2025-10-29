function verLeccion(btn) {
  const info = `
        <p><strong>ID:</strong> ${btn.dataset.id}</p>
        <p><strong>Título:</strong> ${btn.dataset.titulo}</p>
        <p><strong>Contenido:</strong><br>${btn.dataset.contenido}</p>
        <p><strong>Ejemplo:</strong> ${btn.dataset.ejemplo}</p>
        <p><strong>Lección Relacionada:</strong> ${btn.dataset.leccion}</p>
    `;
  document.getElementById("infoLeccion").innerHTML = info;
  new bootstrap.Modal(document.getElementById("modalVer")).show();
}

function editarLeccion(btn) {
  document.getElementById("editId").value = btn.dataset.id;
  document.getElementById("editTitulo").value = btn.dataset.titulo;
  document.getElementById("editContenido").value = btn.dataset.contenido;
  document.getElementById("editEjemplo").value = btn.dataset.ejemplo;

  const select = document.getElementById("editEjercicioRecomendado");
  select.value = btn.dataset.ejercicio_recomendado || "";

  new bootstrap.Modal(document.getElementById("modalEditar")).show();
}

document.getElementById("formEditar").addEventListener("submit", (e) => {
  e.preventDefault();

  const data = new FormData();
  data.append("action", "editar");
  data.append("id", document.getElementById("editId").value);
  data.append("titulo", document.getElementById("editTitulo").value);
  data.append("contenido", document.getElementById("editContenido").value);
  data.append("ejemplo", document.getElementById("editEjemplo").value);
  data.append(
    "ejercicio_recomendado",
    document.getElementById("editEjercicioRecomendado").value
  );

  fetch("back/lecciones_acciones.php", {
    method: "POST",
    body: data,
  })
    .then((r) => r.json())
    .then((res) => {
      if (res.status === "success") {
        SwalGame({
          title: "¡Actualizado!",
          html: "<p>La lección se actualizó correctamente.</p>",
          icon: "success",
        });
        setTimeout(() => location.reload(), 1500);
      } else {
        SwalGame({
          title: "Error",
          html: `<p>${res.message}</p>`,
          icon: "error",
        });
      }
    });
});

function eliminarLeccion(btn) {
  const id = btn.dataset.id;
  Swal.fire({
    title: "¿Eliminar lección?",
    text: "Esta acción no se puede deshacer.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#a259ff",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, eliminar",
    background: "radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)",
    color: "#fff",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("back/lecciones_acciones.php", {
        method: "POST",
        body: new URLSearchParams({
          action: "eliminar",
          id,
        }),
      })
        .then((r) => r.json())
        .then((res) => {
          if (res.status === "success") {
            SwalGame({
              title: "¡Eliminada!",
              html: "<p>La lección ha sido eliminada correctamente.</p>",
              icon: "success",
            });
            setTimeout(() => location.reload(), 1500);
          } else {
            SwalGame({
              title: "Error",
              html: `<p>${res.message}</p>`,
              icon: "error",
            });
          }
        });
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const formCrearLeccion = document.getElementById('formCrearLeccion');
  if (!formCrearLeccion) {
    console.warn('⚠️ No se encontró el formulario con id="formCrearLeccion"');
    return;
  }

  formCrearLeccion.addEventListener('submit', e => {
    e.preventDefault();

    const data = new FormData();
    data.append('action', 'crear_leccion');
    data.append('titulo', document.getElementById('nuevaLeccionTitulo').value);
    data.append('descripcion', document.getElementById('nuevaLeccionDescripcion').value);

    fetch('back/crear_leccion.php', {
      method: 'POST',
      body: data
    })
    .then(async (r) => {
      const text = await r.text();
      try {
        return JSON.parse(text);
      } catch (e) {
        console.error('⚠️ Respuesta no es JSON válido:', text);
        throw new Error('El servidor no devolvió un JSON válido.');
      }
    })
    .then(res => {
      if (res.status === 'success') {
        SwalGame({
          title: '¡Lección creada!',
          html: `<p>${res.message}</p>`,
          icon: 'success'
        });
        setTimeout(() => location.reload(), 1500);
      } else {
        console.error('❌ Error en respuesta del servidor:', res);
        SwalGame({
          title: 'Error',
          html: `<p>${res.message}</p>`,
          icon: 'error'
        });
      }
    })
    .catch(err => {
      console.error('🚨 Error en el fetch:', err);
      SwalGame({
        title: 'Error de conexión',
        html: `<p>${err.message}</p>`,
        icon: 'error'
      });
    });
  });
});


document.getElementById('formCrearTeoria').addEventListener('submit', e => {
  e.preventDefault();

  const data = new FormData();
  data.append('id_leccion', document.getElementById('teoriaLeccion').value);
  data.append('titulo', document.getElementById('teoriaTitulo').value);
  data.append('contenido', document.getElementById('teoriaContenido').value);
  data.append('ejemplo', document.getElementById('teoriaEjemplo').value);
  data.append('ejercicio_recomendado', document.getElementById('teoriaEjercicioRecomendado').value);

  fetch('back/crear_leccion_teoria.php', {
    method: 'POST',
    body: data
  })
  .then(r => r.text())
  .then(text => {
    console.log('Respuesta del servidor:', text);
    try {
      const res = JSON.parse(text);
      if (res.status === 'success') {
        SwalGame({
        title: '¡ Teoria de Lección creada!',
        html: `<p>${res.message}</p>`,
        icon: 'success'
      });
        setTimeout(() => location.reload(), 1500);
      } else {
        SwalGame({
          title: 'Error',
          html: `<p>${res.message}</p>`,
          icon: 'error',
        });
      }
    } catch (err) {
      console.error('Error al parsear JSON:', err);
    }
  })
  .catch(err => console.error('Error en fetch:', err));
});
