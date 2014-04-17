<? use Studip\Button, Studip\LinkButton; ?>

<? if(!empty($dates)) :?>
<form action="<?= PluginEngine::getLink('opencast/course/bulkschedule/') ?>" method=post>
<table class="default">
    <tr>
        <th></th>
        <th>Termin</th>
        <th>Titel</th>
        <th>Status</th>
        <th>Aktionen</th>
    </tr>

    <? foreach($dates as $d) : ?>
    <tr>
        <? $date = new SingleDate($d['termin_id']); ?>
        <? $resource = $date->getResourceID(); ?>
        <td>
            <? if(isset($resource) && OCModel::checkResource($resource)) :?>
                <input name="dates[<?=$date->termin_id?>]" type="checkbox" value="<?=$resource?>"></input>
            <? else: ?>
                <input type="checkbox" disabled></input>
            <? endif;?>
        </td>
        <td> <?=$date->getDatesHTML()?> </td>
        <? $issues = $date->getIssueIDs(); ?>
        <? if(is_array($issues)) : ?>
        <? foreach($issues as $is) : ?>
            <? $issue = new Issue(array('issue_id' => $is));?>
            <? $topic = true; ?>
            <td> <?= my_substr($issue->getTitle(), 0 ,80 ); ?></td>
            <? endforeach; ?>
        <? else: ?>
        <? $topic = false; ?>
        <td> <?=_("Kein Titel eingetragen")?></td>
        <? endif; ?>
        <td>
            <? if(isset($resource) && OCModel::checkResource($resource)) :?>
            <? if(OCModel::checkScheduled($course_id, $resource, $date->termin_id)) :?>
                    <?= Assets::img('icons/16/blue/video.png', array('title' => _("Aufzeichnung ist bereits geplant. Klicken Sie hier um die Planung zu aufzuheben"))) ?>
                <?  else : ?>
                    <? if(date($d['date']) > time()) :?>
                        <?= Assets::img('icons/16/blue/date.png', array('title' => _("Aufzeichnung planen"))) ?>
                    <? else :?>
                        <?= Assets::img('icons/16/blue/exclaim-circle.png', array('title' =>  _("Dieses Datum liegt in der Vergangenheit. Sie k�nnen keine Aufzeichnung planen."))) ?>
                    <? endif;?>
                <? endif; ?>
            <?

            ?>
            <?// $date->getRoom() ?>
            <? elseif(false) : ?>
            <?= Assets::img('icons/16/blue/video.png') ?>
            <?
            /*  Wenn es eine Aufzeichnung gibt, optionen zum Unsichtbar machen anbieten
            *  Wenn keine Aufzeichnung aus OC gibt dann ersma nix machen
            *
            *
            */
            ?>
            <? else : ?>
            <?= Assets::img('icons/16/red/exclaim-circle.png', array('title' =>  _("Es wurde bislang kein Raum mit Aufzeichnungstechnik gebucht"))) ?>
            <? endif; ?>
        </td>
        <td>
            <? $resource = $date->getResourceID(); ?>
            <? if(isset($resource) && OCModel::checkResource($resource)) :?>
            <? if(OCModel::checkScheduled($course_id, $resource, $date->termin_id)) :?>
                <?= LinkButton::create(_('Aufzeichnung l�schen'), PluginEngine::getLink('opencast/course/unschedule/'.$resource .'/'. $date->termin_id ),array('title' => _("Aufzeichnung ist bereits geplant. Klicken Sie hier um die Planung zu aufzuheben"))); ?>
                <?  else : ?>
                    <? if(date($d['date']) > time()) :?>
                         <?= LinkButton::create(_('Aufzeichnung planen'), PluginEngine::getLink('opencast/course/schedule/'.$resource .'/'. $date->termin_id )); ?>
                    <? else :?>
                        <?= Button::create(_("Planung nicht m�glich "), array('disabled' => true, 'title' =>  _("Dieses Datum liegt in der Vergangenheit. Sie k�nnen keine Aufzeichnung planen.")));?>
                    <? endif;?>
                <? endif; ?>
            <? elseif(false) : ?>
            <?= Assets::img('icons/16/blue/video.png') ?>
            <? else : ?>
            <?= Button::create(_("Planung nicht m�glich "), array('disabled' => true, 'title' =>  _("Es wurde bislang kein Raum mit Aufzeichnungstechnik gebucht")));?>
            <? endif; ?>
        </td>
    </tr>
    <? endforeach; ?>
    <tfoot style="border-top: 1px solid #1e3e70; background-color: #e7ebf1;">
    <tr>
        <td class="thead"><input type="checkbox" data-proxyfor="[name^=dates]:checkbox" id="checkall"></td>
        <td class="thead">
            <select name="action">
                <option value="" disabled selected><?=_("Bitte w�hlen Sie eine Aktion.")?></option>
                <option value="create"><?=_("Aufzeichnungen planen")?></option>
                <option value="update"><?=_("Aufzeichnungen aktualisieren")?></option>
                <option value="delete"><?=_("Aufzeichnungen l�schen")?></option>
            </select>
        </td>
        <td></td>
        <td></td>
        <td></td>    
    </tr>
    </tfoot>        
</table>

<div>
            <?= Button::createAccept(_('�bernehmen'), array('title' => _("�nderungen �bernehmen"))); ?>
            <?= LinkButton::createCancel(_('Abbrechen'), PluginEngine::getLink('opencast/course/scheduler')); ?>
</div>
</form>
<? else: ?>
    <?= MessageBox::info(_('Es gibt keine passenden Termine'));?>
<? endif;?>

