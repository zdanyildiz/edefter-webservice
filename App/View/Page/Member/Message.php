<?php
/**
 * @var array $visitor
 * @var array $memberInfo
 * @var Helper $helper
 */

$messagess = $visitor['visitorIsMember']['memberMessages'];
$memberInfo = $visitor['visitorIsMember'];
$memberID = $memberInfo['memberID'];
//$helper->writeToArray($messagess);
?>
<div class="member-container">
    <div class="message-card-container">
        <h1><?=_uyelik_mesajlarim_baslik?></h1>
        <?php
        if(!empty($messagess)) {
            foreach ($messagess as $message) {?>
                <details>
                    <summary><?= $message['mesajkonusu'] ?></summary>
                    <?php $helper->printMessages($messagess);?>
                </details>

            <?php }
        }
        ?>
    </div>
    <div class="member-message-container">
        <div class="member-message-form-container">
            <h1><?=_uye_mesaj_yaz_baslik?></h1>
            <form action="/control/member/post/addMessage" method="post">
                <input type="hidden" name="action" value="addMessage">
                <input type="hidden" name="memberID" value="<?= $memberID ?>">
                <div class="form-group">
                    <label for="messageTitle"><?=_uye_mesaj_yaz_konu?>:</label>
                    <input type="text" class="form-control" id="messageTitle" name="messageTitle" required>
                    <small class="form-text text-muted">*<?=_uye_mesaj_yaz_konu?>.</small>
                </div>
                <div class="form-group">
                    <label for="messageContent"><?=_uye_mesaj_yaz_icerik?>:</label>
                    <textarea class="form-control" id="messageContent" name="messageContent" required></textarea>
                    <small class="form-text text-muted">*<?=_uye_mesaj_yaz_icerik?>.</small>
                </div>
                <button type="submit" class="btn btn-primary"><?=_uye_mesaj_yaz_buton?></button>
            </form>
        </div>
    </div>
</div>
