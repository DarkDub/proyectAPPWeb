<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';
$user_id = $_SESSION['user_id'];

// Obtener puntos del usuario
$stmt = $conn->prepare("SELECT puntos FROM usuarios WHERE id_usuario = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener categorías
$stmt = $conn->prepare("SELECT * FROM categorias ORDER BY nombre ASC");
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener recompensas
// Obtener recompensas con info de canje
$stmt = $conn->prepare("
    SELECT r.*, c.nombre AS categoria_nombre, c.icono_fa,
           CASE WHEN ca.id_usuario IS NOT NULL THEN 1 ELSE 0 END AS ya_canjeada
    FROM recompensas r
    LEFT JOIN categorias c ON r.id_categoria = c.id_categoria
    LEFT JOIN canjes ca ON ca.id_recompensa = r.id AND ca.id_usuario = :user_id
    ORDER BY r.id DESC
");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$recompensas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tienda - BrainPlay</title>
<?php include __DIR__ . '/../includes/cdns.php'; ?>
<link rel="stylesheet" href="../public/css/user/shop.css">
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<style>
.category-card.active {
    border: 2px solid #ffb703;
    transform: scale(1.05);
}


       .swal-game {
            border: 2px solid #a259ff;
            box-shadow: 0 0 30px #a259ff80;
            border-radius: 20px;
        }

        @keyframes glow {
            from {
                box-shadow: 0 0 10px #a259ff80;
            }

            to {
                box-shadow: 0 0 30px #a259ff;
            }
        }

        .swal-game-btn {
            font-family: 'Orbitron', sans-serif !important;
            text-transform: uppercase;
            border-radius: 12px !important;
            padding: 10px 30px !important;
            box-shadow: 0 0 15px #a259ff;
            transition: 0.3s ease;
        }
        #swal2-title {
            color: white !important;
        }
        .swal-game-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 25px #a259ff;
        }
.shop-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            color: #a259ff;
        }
</style>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="shop-container">
    <header class="shop-header" style="background: url('../public/img/shop-bg.jpg') center/cover no-repeat;">
        <h1>Tienda de Recompensas</h1>
        <p>Canjea tus puntos por recompensas exclusivas y personaliza tu perfil</p>
        <p><strong>Puntos disponibles:</strong> <span class="user-points"><?= $user['puntos'] ?></span></p>
    </header>

    <section class="shop-categories">
        <?php foreach($categorias as $cat): ?>
            <div class="category-card" data-category="<?= htmlspecialchars($cat['id_categoria']) ?>">
                <i class="fas <?= htmlspecialchars($cat['icono_fa']) ?> fa-2x"></i>
                <span><?= htmlspecialchars($cat['nombre']) ?></span>
            </div>
        <?php endforeach; ?>
        <div class="category-card" data-category="all">
            <i class="fas fa-th-large fa-2x"></i>
            <span>Todas</span>
        </div>
    </section>

    <section class="shop-items">
    <?php foreach($recompensas as $item): ?>
        <div class="reward-card" data-category="<?= htmlspecialchars($item['id_categoria']) ?>">
            <div class="reward-header">
                <i class="fas <?= htmlspecialchars($item['icono_fa']) ?>"></i>
                <span class="reward-category"><?= htmlspecialchars($item['categoria_nombre'] ?? 'Extra') ?></span>
            </div>
            <h2 class="reward-title"><?= htmlspecialchars($item['nombre']) ?></h2>
            <p class="reward-desc"><?= htmlspecialchars($item['descripcion']) ?></p>
            <div class="reward-footer">
                <span class="reward-points"><i class="fas fa-star"></i> <?= $item['puntos_requeridos'] ?> pts</span>

                <?php if($item['ya_canjeada']): ?>
                    <button class="btn btn-disabled" disabled>Ya canjeada </button>
                <?php elseif($user['puntos'] >= $item['puntos_requeridos']): ?>
                    <button class="btn btn-redeem" data-reward-id="<?= $item['id'] ?>">Canjear</button>
                <?php else: ?>
                    <button class="btn btn-disabled" disabled>Puntos insuficientes</button>
                <?php endif; ?>

            </div>
        </div>
    <?php endforeach; ?>
</section>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
document.querySelectorAll('.btn-redeem').forEach(btn => {
    btn.addEventListener('click', () => {
        const rewardId = btn.dataset.rewardId;

        fetch('Game/shopRedeem.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: <?= $user_id ?>, reward_id: rewardId })
        })
        .then(async res => {
            // Comprobar si la respuesta es JSON
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch(e) {
                console.error("Respuesta inválida:", text);
                throw new Error("Error al procesar la respuesta del servidor.");
            }
        })
        .then(data => {
            if (data.success) {
                btn.textContent = 'Canjeado 🎉';
                btn.disabled = true;
                document.querySelector('.user-points').textContent = data.nuevo_puntaje;

                Swal.fire({
                    title: '¡Recompensa canjeada!',
                    titleColor: '#fff',
                    html: `<div style="
                        font-family: 'Orbitron', sans-serif;
                        color: #fff;
                        font-size: 1.2rem;
                        letter-spacing: 1px;
                        text-align: center;">
                        ${data.mensaje}
                       </div>`,
                    background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
                    icon: 'success',
                    iconColor: '#a259ff',
                    showConfirmButton: true,
                    confirmButtonText: '¡Genial!',
                    confirmButtonColor: '#a259ff',
                   customClass: {
                                            popup: 'swal-game',
                                            confirmButton: 'swal-game-btn'
                                        },
                                        showClass: {
                                            popup: 'animate__animated animate__fadeInDown'
                                        },
                                        hideClass: {
                                            popup: 'animate__animated animate__fadeOutUp'
                                        },
                });

            } else {
                Swal.fire({
                    title: 'Error',
                    titleColor: '#fff',
                    html: `<div style="
                        font-family: 'Orbitron', sans-serif;
                        color: #ff4f4f;
                        font-size: 1.2rem;
                        letter-spacing: 1px;
                        text-align: center;">
                        ${data.mensaje}
                       </div>`,
                    background: 'radial-gradient(circle at center, #2b0056 0%, #0e001a 100%)',
                    icon: 'error',
                    iconColor: '#ff4f4f',
                    showConfirmButton: true,
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#ff4f4f',
                    customClass: {
                        popup: 'swal-game',
                        confirmButton: 'swal-game-btn'
                    },
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                title: 'Error',
                html: `<div style="
                    font-family: 'Orbitron', sans-serif;
                    color: #ff4f4f;
                    text-align: center;">
                    Ocurrió un error al conectar con el servidor.
                   </div>`,
                icon: 'error'
            });
        });
    });
});

// Filtrar por categoría
const categoryCards = document.querySelectorAll('.category-card');
const rewardCards = document.querySelectorAll('.reward-card');

categoryCards.forEach(card => {
    card.addEventListener('click', () => {
        const cat = card.dataset.category;

        rewardCards.forEach(reward => {
            reward.style.display = (cat === 'all' || reward.dataset.category === cat) ? 'block' : 'none';
        });

        categoryCards.forEach(c => c.classList.remove('active'));
        card.classList.add('active');
    });
});

</script>
</body>
</html>
