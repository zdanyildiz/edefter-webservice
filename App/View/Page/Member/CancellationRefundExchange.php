<?php
/**
 * @var Casper $casper
 * @var Session $session
 * @var string $query
 */

$visitor = $casper->getVisitor();
$talepler = $visitor['visitorIsMember']['memberCancellationRefundExchange'];
?>

<div class="cancel-refund-exchange-container">
    <h1>İptal / İade / Değişim Taleplerim</h1>

    <?php if (empty($talepler)): ?>
        <div class="no-requests">
            <p>Henüz iptal, iade veya değişim talebiniz bulunmamaktadır.</p>
        </div>
    <?php else: ?>
        <div class="requests-container">
            <?php foreach ($talepler as $talep): ?>
                <div class="request-card">
                    <div class="request-header">
                        <div class="request-type <?= strtolower($talep['degisimtur']); ?>">
                            <?= $talep['degisimtur']; ?>
                        </div>
                        <div class="request-date">
                            <?= date('d.m.Y H:i', strtotime($talep['tarih'])); ?>
                        </div>
                    </div>

                    <div class="request-body">
                        <div class="request-item">
                            <span class="item-label">Sipariş No:</span>
                            <span class="item-value"><?= $talep['siparisid']; ?></span>
                        </div>
                        <div class="request-item">
                            <span class="item-label">İade Nedeni:</span>
                            <span class="item-value"><?= $talep['iadenedeni']; ?></span>
                        </div>
                        <div class="request-item">
                            <span class="item-label">Açıklama:</span>
                            <span class="item-value description"><?= nl2br(htmlspecialchars($talep['iadeaciklama'])); ?></span>
                        </div>
                    </div>

                    <div class="request-footer">
                        <div class="request-status <?= empty($talep['answer']) ? 'pending' : 'answered'; ?>">
                            <?= empty($talep['answer']) ? 'Beklemede' : 'Yanıtlandı'; ?>
                        </div>

                        <?php if (!empty($talep['answer'])): ?>
                            <button class="view-answer-btn" data-request-id="<?= $talep['talepid']; ?>">Yanıtı Görüntüle</button>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($talep['answer'])): ?>
                        <div class="request-answer" id="answer-<?= $talep['talepid']; ?>">
                            <h4>Talebinize Verilen Yanıt</h4>
                            <div class="answer-content">
                                <?= nl2br(htmlspecialchars($talep['answer']['content'])); ?>
                            </div>
                            <div class="answer-date">
                                <?= date('d.m.Y H:i', strtotime($talep['answer']['date'])); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const answerButtons = document.querySelectorAll('.view-answer-btn');

    answerButtons.forEach(button => {
        button.addEventListener('click', function() {
            const requestId = this.getAttribute('data-request-id');
            const answerDiv = document.getElementById('answer-' + requestId);

            if (answerDiv.classList.contains('active')) {
                answerDiv.classList.remove('active');
                this.textContent = 'Yanıtı Görüntüle';
            } else {
                answerDiv.classList.add('active');
                this.textContent = 'Yanıtı Gizle';
            }
        });
    });
});
</script>
<style>
    .cancel-refund-exchange-container {
        max-width: 1200px;
        margin: 0 auto 30px;
        padding: 20px;
        font-family: var(--font-family, Arial, sans-serif);
    }

    .cancel-refund-exchange-container h1 {
        font-size: 24px;
        margin-bottom: 25px;
        color: var(--primary-text-color, #333);
        border-bottom: 2px solid var(--border-light-color, #eee);
        padding-bottom: 10px;
    }

    .no-requests {
        background-color: var(--light-bg-color, #f8f8f8);
        padding: 30px;
        text-align: center;
        border-radius: 8px;
        color: #666;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
    }

    .requests-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .request-card {
        border: 1px solid var(--border-color, #e0e0e0);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        background-color: #fff;
        position: relative;
    }

    .request-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .request-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background-color: var(--header-bg-color, #f5f5f5);
        border-bottom: 1px solid var(--border-color, #e0e0e0);
    }

    .request-type {
        font-weight: bold;
        padding: 6px 12px;
        border-radius: 4px;
        color: white;
        font-size: 14px;
        text-transform: capitalize;
    }

    .request-type.iade {
        background-color: var(--refund-color, #e74c3c);
    }

    .request-type.iptal {
        background-color: var(--cancel-color, #3498db);
    }

    .request-type.değişim {
        background-color: var(--exchange-color, #2ecc71);
    }

    .request-date {
        color: #777;
        font-size: 14px;
    }

    .request-body {
        padding: 15px;
        background-color: white;
    }

    .request-item {
        margin-bottom: 12px;
        display: flex;
        flex-wrap: wrap;
    }

    .request-item:last-child {
        margin-bottom: 0;
    }

    .item-label {
        font-weight: bold;
        min-width: 120px;
        color: var(--label-color, #555);
        margin-right: 10px;
    }

    .item-value {
        color: var(--value-color, #333);
    }

    .item-value.description {
        display: block;
        width: 100%;
        margin-top: 5px;
        background-color: var(--description-bg, #f9f9f9);
        padding: 10px;
        border-radius: 4px;
        white-space: pre-wrap;
        line-height: 1.6;
        border: 1px solid #eee;
    }

    .request-footer {
        padding: 15px;
        background-color: var(--footer-bg-color, #f5f5f5);
        border-top: 1px solid var(--border-color, #e0e0e0);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .request-status {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: bold;
    }

    .request-status.pending {
        background-color: var(--pending-color, #f39c12);
        color: white;
    }

    .request-status.answered {
        background-color: var(--answered-color, #27ae60);
        color: white;
    }

    .view-answer-btn {
        border: none;
        background-color: var(--button-color, #3498db);
        color: white;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.2s;
        font-weight: 500;
    }

    .view-answer-btn:hover {
        background-color: var(--button-hover-color, #2980b9);
    }

    .request-answer {
        padding: 15px;
        background-color: var(--answer-bg-color, #f0f8ff);
        border-top: 1px solid var(--answer-border-color, #c8e1ff);
        display: none;
    }

    .request-answer.active {
        display: block;
        animation: slideDown 0.3s ease forwards;
    }

    .request-answer h4 {
        margin-top: 0;
        color: var(--answer-title-color, #2c3e50);
        margin-bottom: 10px;
        font-size: 16px;
    }

    .answer-content {
        background-color: white;
        padding: 12px;
        border-radius: 4px;
        border: 1px solid var(--content-border-color, #ddd);
        margin-bottom: 10px;
        white-space: pre-wrap;
        line-height: 1.6;
        color: #333;
    }

    .answer-date {
        text-align: right;
        font-size: 12px;
        color: #777;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobil Responsive Tasarım */
    @media screen and (max-width: 768px) {
        .cancel-refund-exchange-container h1 {
            font-size: 20px;
        }

        .request-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .request-item {
            flex-direction: column;
        }

        .item-label {
            margin-bottom: 5px;
        }

        .request-footer {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        .view-answer-btn {
            width: 100%;
        }
    }
</style>