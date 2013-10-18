{if isset($main_content_tpl)}
 {include $main_content_tpl}
{else}
 error (empty.tpl): main_content_tpl is not set
{/if}