
function SwalGame({ title, html, icon = 'success', confirmText = 'Aceptar', redirect = null }) {
  Swal.fire({
    title: title,
    titleColor: '#fff',
    html: `
      <div style="font-family: 'Orbitron', sans-serif; color:#fff; font-size:1.2rem; letter-spacing:1px;">
        ${html}
      </div>
    `,
    icon: icon,
    iconColor: '#a259ff',
    background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
    showConfirmButton: true,
    confirmButtonText: confirmText,
    confirmButtonColor: '#a259ff',
    confirmButtonColor: '#a259ff',
    customClass: {
      popup: 'swal-game1',
      confirmButton: 'swal-game-btn'
    },
    showClass: {
      popup: 'animate__animated animate__fadeInDown'
    },
    hideClass: {
      popup: 'animate__animated animate__fadeOutUp'
    }
  }).then(() => {
    if (redirect) window.location.href = redirect;
  });
}