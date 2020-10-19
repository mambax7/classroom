<div>
    <form action="<{$xoops_url}>/modules/classroom/mode.php" method="POST">
        <input type="hidden" name="url" value="<{$block.url}>">
        <{if $block.mode == 1}>
            <{$smarty.const._CR_BL_EDITMODEON}>
            <br>
            <input type="hidden" name="mode" value="0">
            <input type="submit" value="<{$smarty.const._CR_BL_DISABLEEDITMODE}>">
        <{else}>
            <{$smarty.const._CR_BL_EDITMODEOFF}>
            <br>
            <input type="hidden" name="mode" value="1">
            <input type="submit" value="<{$smarty.const._CR_BL_ENABLEEDITMODE}>">
        <{/if}>
    </form>
</div>
