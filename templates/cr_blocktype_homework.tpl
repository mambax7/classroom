<table>
    <tr>
        <th><{$smarty.const._CR_MA_ASSIGNED}></th>
        <th><{$smarty.const._CR_MA_ASSIGNMENT}></th>
        <th><{$smarty.const._CR_MA_DUE}></th>
    </tr>
    <{foreach item=entry from=$block.entries}>
        <tr class="<{cycle values='odd, even'}>">
            <td><{$entry.assigned}></td>
            <td><{$entry.value}></td>
            <td><{$entry.due}></td>
        </tr>
    <{/foreach}>
</table>