<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<{$xoops_langcode}>" lang="<{$xoops_langcode}>">
<head>
    <meta http-equiv="content-type" content="text/html; charset=<{$xoops_charset}>">
    <meta http-equiv="content-language" content="<{$xoops_langcode}>">
    <meta name="robots" content="<{$xoops_meta_robots}>">
    <meta name="keywords" content="<{$xoops_meta_keywords}>">
    <meta name="description" content="<{$xoops_meta_description}>">
    <meta name="rating" content="<{$xoops_meta_rating}>">
    <meta name="author" content="<{$xoops_meta_author}>">
    <meta name="copyright" content="<{$xoops_meta_copyright}>">
    <meta name="generator" content="XOOPS">
    <title><{$xoops_sitename}> - <{$xoops_pagetitle}></title>
    <link href="<{$xoops_url}>/favicon.ico" rel="SHORTCUT ICON">
    <link rel="stylesheet" type="text/css" media="screen" href="<{$xoops_url}>/xoops.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<{$xoops_themecss}>">
    <!-- RMV: added module header -->
    <{$xoops_module_header}>
    <script type="text/javascript">
        <!--
        <{$xoops_js}>
        //-->
    </script>
</head>
<body>
<center>
    <div style='width: 600px; text-align: left; border: none; padding: 6px; font-family: Tahoma, Verdana, sans-serif; font-size: 10px; '>
        <{foreach item=result from=$results}>
            <div class="<{cycle values='odd, even'}>">
                <{$smarty.const._CR_MA_QUESTION}>: <{$result.question}>
                <br><{$smarty.const._CR_MA_YOURANSWER}>: <{$result.answer}>
                <{if $result.correct != -1}>
                    <br>
                    <{$smarty.const._CR_MA_ANSWER_IS}>:
                    <strong>
                        <{if $result.correct == 1}>
                            <{$smarty.const._CR_MA_ANSWER_CORRECT}>
                        <{else}>
                            <{$smarty.const._CR_MA_ANSWER_WRONG}>
                        <{/if}>
                    </strong>
                <{/if}>
                <{if $show_answer == 1 && $result.correct != 1}>
                    <br>
                    <{$smarty.const._CR_MA_CORRECT}>
                    <strong><{$result.correctanswer}></strong>
                <{/if}>
            </div>
        <{/foreach}>
        <div class="foot">
            <{$smarty.const._CR_MA_QUESTIONCOUNT}>: <{$questionno}><br>
            <{$smarty.const._CR_MA_CORRECT_ANSWERS}>: <{$correct}><br>
            <{$smarty.const._CR_MA_PERCENTAGE}>: <{$percentage}> %
        </div>
    </div>
</center>
</body>
</html>
