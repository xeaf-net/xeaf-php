{if $pluginModel->type eq 'css'}
    {foreach $pluginModel->data as $link}
        <link rel="stylesheet" href="{$link}">
    {/foreach}
{/if}
{if $pluginModel->type eq 'js'}
    {foreach $pluginModel->data as $link}
        <script type="text/javascript" src="{$link}"></script>
    {/foreach}
{/if}
