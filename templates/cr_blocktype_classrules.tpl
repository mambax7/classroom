<ol type="1">
    <{foreach item=rule from=$block.rules}>
        <li><{$rule.value}></li>
    <{/foreach}>
</ol>