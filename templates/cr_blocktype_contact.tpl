<{$block.contactform.javascript}>
<form name="<{$block.contactform.name}>" id="<{$block.contactform.name}>" action="<{$block.contactform.action}>" method="<{$block.contactform.method}>" <{$block.contactform.extra}> >
    <table id="contactform" cellspacing="0">
        <!-- start of form elements loop -->
        <{foreach item=element from=$block.contactform.elements}>
            <{if $element.hidden !== true}>
                <tr valign="top" class="<{cycle values='odd, even'}>">
                    <td><{$element.caption}></td>
                    <td><{$element.body}></td>
                </tr>
            <{else}>
                <{$element.body}>
            <{/if}>
        <{/foreach}>
        <!-- end of form elements loop -->
    </table>
</form>
