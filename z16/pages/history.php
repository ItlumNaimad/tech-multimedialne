<?php
require_once 'db_connect.php';

$id_cms = $id_cms ?? 1; // zdefiniowane w index.php

$stmt_hist = $pdo->prepare("SELECT * FROM chatbot WHERE id_cms = ? ORDER BY id DESC");
$stmt_hist->execute([$id_cms]);
$history = $stmt_hist->fetchAll();
?>

<h1>Historia Chatbota</h1>
<div class="list-group">
    <?php if (empty($history)): ?>
        <p>Brak historii rozmów.</p>
    <?php else: ?>
        <?php foreach($history as $row): ?>
            <div class="list-group-item mb-3 shadow-sm border-start border-primary border-4">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1 text-primary">Zapytanie: <?php echo htmlspecialchars($row['question']); ?></h6>
                    <small class="text-muted"><?php echo $row['datetime']; ?></small>
                </div>
                <p class="mb-1 bg-light p-2 rounded">
                    <strong>Odpowiedź bota:</strong> <?php echo htmlspecialchars($row['answer']); ?>
                </p>
                <small class="text-muted">IP: <?php echo htmlspecialchars($row['question_ip']); ?></small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
