<div style="float:left; width:100%;">
    <a href='index.php'><{$smarty.const._CR_MA_HOME}></a>
    :: <a href="school.php?s=<{$school.0.schoolid}>"><{$school.0.name}></a>
    :: <a href="division.php?d=<{$division.divisionid}>"><{$division.name}></a>
    :: <{$classroom.name}>
</div>
<{if $edit_mode == 1}>
    <div style="float:right; width:30%; text-align: right;"><a href="manage.php?op=classroom&amp;cr=<{$classroom.classroomid}>"><{$smarty.const._CR_MA_EDITCLASSROOM}></a></div>
    <div style="float:right; width:100%; text-align: right;"><a href="manage.php?op=class&amp;cr=<{$classroom.classroomid}>"><{$smarty.const._CR_MA_ADDCLASS}></a></div>
    <div style="float:right; width:100%; text-align: right;"><a href="manage.php?op=block&amp;cr=<{$classroom.classroomid}>"><{$smarty.const._CR_MA_MANAGEBLOCKS}></a></div>
<{/if}>
<div style="clear:both; width: 100%;">
    <div class="outer">
        <div class="head">
            <{$classroom.name}> (<{$classroom.ownername}>) - <{$classroom.location}>
        </div>
        <div class="foot"><{$classroom.description}></div>
    </div>
</div>
<div style="clear:both; width: 100%; padding-top: 10px;">
    <{foreach item=class from=$classes}>
        <div class="outer" style="margin-bottom: 5px;">
            <div class="<{cycle values='odd, even'}>"><a href="class.php?c=<{$class.classid}>"><{$class.name}></a> (<{$class.time}>)</div>
        </div>
    <{/foreach}>
</div>
