<form name="<{$block.quizform.name}>" id="<{$block.quizform.name}>" action="<{$block.quizform.action}>" method="<{$block.quizform.method}>" <{$block.quizform.extra}> >
    <table id="quizform" cellspacing="0">
        <!-- start of form elements loop -->
        <{counter start=1 print=false assign="number"}>
        <{foreach item=element from=$block.quizform.elements}>
            <{if $element.hidden !== true}>
                <tr valign="top" class="<{cycle values='odd, even'}>">
                    <td>
                        <{if $element.caption != ""}>
                            <{$number}>.
                        <{/if}>
                    </td>
                    <td><{$element.caption}></td>
                    <td><{$element.body}></td>
                </tr>
                <{counter}>
            <{else}>
                <{$element.body}>
            <{/if}>

        <{/foreach}>
        <!-- end of form elements loop -->
    </table>
</form>
