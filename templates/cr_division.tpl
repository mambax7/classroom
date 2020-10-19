<div style="float:left; width:100%;">
    <a href='index.php'><{$smarty.const._CR_MA_HOME}></a>
    :: <a href="school.php?s=<{$school.0.schoolid}>"><{$school.0.name}></a>
    :: <{$division.name}>
</div>
<{if $edit_mode == 1}>
    <div style="float:right; width:30%; text-align: right;"><a href="manage.php?op=division&amp;d=<{$division.divisionid}>"><{$smarty.const._CR_MA_EDITDIVISION}></a></div>
    <div style="float:right; width:100%; text-align: right;"><a href="manage.php?op=classroom&amp;d=<{$division.divisionid}>"><{$smarty.const._CR_MA_ADDCLASSROOM}></a></div>
<{/if}>
<div style="clear:both; width: 100%;">
    <div class="outer">
        <div class="head">
            <{$division.name}> (<{$division.directorname}>) - <{$division.location}>
        </div>
        <div class="foot"><{$division.description}></div>
    </div>
</div>
<div style="clear:both; width: 100%; padding-top: 10px;">
    <{foreach item=classroom from=$classrooms}>
        <div class="outer" style="margin-bottom: 5px;">
            <div class="<{cycle values='odd, even'}>"><a href="classroom.php?cr=<{$classroom.classroomid}>"><{$classroom.name}></a> (<{$classroom.ownername}>) - <{$classroom.location}></div>
        </div>
    <{/foreach}>
</div>
