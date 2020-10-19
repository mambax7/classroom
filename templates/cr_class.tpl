<div style="float:left; width:100%;">
    <a href='index.php'><{$smarty.const._CR_MA_HOME}></a>
    :: <a href="school.php?s=<{$school.0.schoolid}>"><{$school.0.name}></a>
    :: <a href="division.php?d=<{$division.0.divisionid}>"><{$division.0.name}></a>
    :: <a href="classroom.php?cr=<{$classroom.0.classroomid}>"><{$classroom.0.name}></a>
    :: <{$class.name}>
</div>
<{if $edit_mode == 1}>
    <div style="float:right; width:30%; text-align: right;"><a href="manage.php?op=class&amp;c=<{$class.classid}>"><{$smarty.const._CR_MA_EDITCLASS}></a></div>
<{/if}>
<div style="clear:both; width: 100%;">
    <div class="outer">
        <div class="head"><{$class.name}> (<{$class.time}>)</div>
        <div class="foot"><{$class.description}></div>
    </div>
</div>
<br style="clear:both;">
<div style="clear:both;">
    <div style="float: left; width: 49%;">
        <{foreach item=block from=$cr_tlblocks}>
            <div class="outer" style="margin-bottom: 5px;">
                <div class="head"><{$block.name}></div>
                <div class="foot"><{$block.content}></div>
            </div>
        <{/foreach}>
    </div>
    <div style="float:right; width: 50%; padding-left: 2px;">
        <{foreach item=block from=$cr_trblocks}>
            <div class="outer" style="margin-bottom: 5px;">
                <div class="head"><{$block.name}></div>
                <div class="foot"><{$block.content}></div>
            </div>
        <{/foreach}>
    </div>
</div>
<div style="float:left; width: 100%; clear: both;">
    <{foreach item=block from=$cr_ccblocks}>
        <div class="outer" style="margin-bottom: 5px;">
            <div class="head"><{$block.name}></div>
            <div class="foot"><{$block.content}></div>
        </div>
    <{/foreach}>
</div>
<div style="clear: both;">
    <div style="float:left; width: 49%;">
        <{foreach item=block from=$cr_blblocks}>
            <div class="outer" style="margin-bottom: 5px;">
                <div class="head"><{$block.name}></div>
                <div class="foot"><{$block.content}></div>
            </div>
        <{/foreach}>
    </div>
    <div style="float:right; width: 50%; padding-left: 2px;">
        <{foreach item=block from=$cr_brblocks}>
            <div class="outer" style="margin-bottom: 5px;">
                <div class="head"><{$block.name}></div>
                <div class="foot"><{$block.content}></div>
            </div>
        <{/foreach}>
    </div>
</div>
