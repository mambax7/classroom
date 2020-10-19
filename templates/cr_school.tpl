<div style="float:left; width:100%;">
    <a href='index.php'><{$smarty.const._CR_MA_HOME}></a>
    :: <{$school.name}>
</div>
<{if $edit_mode == 1}>
    <div style="float:right; width:50%; text-align: right;"><a href="manage.php?op=school&amp;s=<{$school.schoolid}>"><{$smarty.const._CR_MA_EDITSCHOOL}></a></div>
    <div style="float:right; width:100%; text-align: right;"><a href="manage.php?op=division&amp;s=<{$school.schoolid}>"><{$smarty.const._CR_MA_ADDDIVISION}></a></div>
<{/if}>
<div style="clear:both; width: 100%;">
    <div class="outer">
        <div class="head"><{$school.name}> (<{$school.headname}>) - <{$school.location}></div>
        <div class="foot"><{$school.description}></div>
    </div>
</div>

<div style="clear:both; width: 100%; padding-top: 10px;">
    <{foreach item=division from=$divisions}>
        <div class="outer" style="margin-bottom: 5px;">
            <div class="<{cycle values='odd, even'}>"><a href="division.php?d=<{$division.divisionid}>"><{$division.name}></a> (<{$division.directorname}>) - <{$division.location}></div>
        </div>
    <{/foreach}>
</div>
