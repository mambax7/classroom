<{if $edit_mode == 1}>
    <div align="right"><a href="manage.php?op=school"><{$smarty.const._CR_MA_ADDSCHOOL}></a></div>
<{/if}>
<div style="clear:both; width: 100%;">
    <div class="outer">
        <div class="foot">
            <{$frontpagetext}>
        </div>
    </div>
</div>

<{foreach item=school from=$schools}>
    <div class="<{cycle values='odd, even'}>">
        <a href="school.php?s=<{$school.schoolid}>"><{$school.name}></a> (<{$school.headname}>) - <{$school.location}>
    </div>
<{/foreach}>
