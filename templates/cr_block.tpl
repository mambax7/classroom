<div class="outer" style="margin-bottom: 5px;">
    <div class="head"><{$block.name}></div>
    <div class="foot"><{$block.content}></div>
</div>
<div class="outer" style="margin-bottom: 5px;">
    <div class="head"><{$smarty.const._CR_MA_BELONGSIN}></div>
    <div class="odd"><a href="classroom.php?cr=<{$classroom.classroomid}>"><{$classroom.name}></a></div>
</div>
<div class="outer" style="clear:both;">
    <div class="head"><{$smarty.const._CR_MA_VISIBLEIN}></div>
    <{foreach item=class from=$classes}>
        <div class="<{cycle values='odd, even'}>"><a href="class.php?c=<{$class.classid}>"><{$class.name}></a></div>
    <{/foreach}>
</div>
