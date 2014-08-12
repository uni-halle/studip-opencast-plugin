<?

use Studip\Button,
    Studip\LinkButton;

if ($success = $flash['success']) {
    echo MessageBox::success($success);
}
if ($error = $flash['error']) {
    echo MessageBox::error($error);
}
if ($flash['question']) {
    echo $flash['question'];
}


$infobox_content = array(array(
        'kategorie' => _('Hinweise:'),
        'eintrag' => array(array(
                'icon' => 'icons/16/black/info.png',
                'text' => _("Hier k�nnen Sie AV-Medien direkt in das angebunde OpenCast Matterhorn laden.")
        ))
        ));
$infobox = array('picture' => 'infobox/administration.jpg', 'content' => $infobox_content);
?>

<script language="javascript">
    OC.initUpload(<?= OC_UPLOAD_CHUNK_SIZE ?>);
</script>

<form id="upload_fom" action="<?= PluginEngine::getLink('opencast/upload/upload_file/') ?>" enctype="multipart/form-data" method="post">
    <fieldset class="oc_mediaupload">
        <legend class="oc_mediaupload_legend"><?= _("Medienupload") ?></legend>
    
        <label id="title" for="titleField">
            <?= _('Titel') ?>:
            <span style="color: red; font-size: 1.6em">* </span>
        </label>
        <br>
        <input type="text" maxlength="255" name="title" id="titleField" required>
        <br>
        <label id="creatorLabel" for="creator">
            <span><?= _("Vortragende") ?></span>:
        </label>
        <br>
        <input type="text" maxlength="255" name="creator" id="creator" value="<?=get_fullname_from_uname($GLOBALS['auth']->auth['uname']) ?>">
        <br>
        <label id="recordingDateLabel" class="scheduler-label" for="recordDate">
            <span><?= _('Aufnahmedatum') ?></span>:
            <span style="color: red; font-size: 1.6em">* </span>
        </label>
        <br>
        <input type="text" name="recordDate" value="<?= $this->date ?>" id="recordDate" size="10" required>
        <br>
        <label id="startTimeLabel" for="startTimeHour">
            <span><?= _('Startzeit') ?></span>:
        </label>
        <span style="color: red; font-size: 1.6em">* </span><br>
        <select id="startTimeHour" name="startTimeHour">
            <?php for ($i = 0; $i <= 23; $i++): ?>
                <?php if ($i < 10) {
                    $in = '0' . $i;
                } else {
                    $in = (string) $i;
                } ?>
                <?php if ($in == $this->hour): ?>
            <option value="<?= $i ?>" selected="selected"><?= $in ?></option>
                <?php else: ?>
            <option value="<?= $i ?>"><?= $in ?></option>
                <?php endif; ?>
            <?php endfor; ?>
        </select>
        :
        <select id="startTimeMin" name="startTimeMin">
            <?php for ($i = 0; $i <= 60; $i++): ?>
                <?php if ($i < 10) {
                    $in = '0' . $i;
                } else {
                    $in = (string) $i;
                } ?>
                <?php if ($in == $this->minute): ?>
                    <option value="<?= $i ?>" selected="selected"><?= $in ?></option>
                <?php else: ?>
                    <option value="<?= $i ?>"><?= $in ?></option>
                <?php endif; ?>
            <?php endfor; ?>
        </select>
        <br>
        <label id="contributorLabel" for="contributor">
            <span><?= _('Mitwirkende') ?></span>:
        </label>
        <br>
        <input type="text" maxlength="255" id="contributor" name="contributor">
        <br>
        <label id="subjectLabel" for="subject">
            <span><?= _('Thema') ?></span>:
        </label>
        <br>
        <input type="text" maxlength="255" id="subject" name="subject">
        <br>
        <label id="languageLabel" for="language">
            <span><?= _('Sprache') ?></span>:
        </label>
        <br>
        <input type="text" maxlength="255" id="language" name="language">
        <br>
        <label id="descriptionLabel" for="description">
            <span><?= _('Beschreibung') ?></span>:
        </label>
        <br>
        <textarea cols="10" rows="5" id="description" name="description"></textarea>
        <br>
        <label for="video_upload">Datei:</label>
        <br>
        <div id="file_wrapper">
                <?= LinkButton::create(_('Datei ausw�hlen'), null, array('id' => 'video-chooser', 'onClick' => "$('input[type=file]').trigger('click');return false;")); ?>
                <input name="video" type="file" id="video_upload" required>
        </div>
        <div id="upload_info">
        </div>
        <div id="progressbarholder" style="overflow: hidden; padding-top: 5px; height: 30px; width:50%; margin: 0 auto;">
            <div id="progressbar"></div>
        </div>
        <input type="hidden" value="" name="total_file_size" id="total_file_size" />
        <input type="hidden" value="" name="file_name" id="file_name" />
        <div class="form_submit">
            <?= Button::createAccept(_('�bernehmen'), null, array('id' => 'btn_accept')) ?>
            <?= LinkButton::createCancel(_('Abbrechen'), PluginEngine::getLink('opencast/course/upload')) ?>
        </div>
    </fieldset>
</form>

  