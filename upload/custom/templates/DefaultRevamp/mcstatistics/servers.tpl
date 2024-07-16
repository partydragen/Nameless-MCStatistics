{include file='header.tpl'}
{include file='navbar.tpl'}

<h2 class="ui header">
    {$SERVERS}
</h2>

<br />

<div class="ui stackable grid">
    <div class="ui row">

        {if count($WIDGETS_LEFT)}
            <div class="ui six wide tablet four wide computer column">
                {foreach from=$WIDGETS_LEFT item=widget}
                    {$widget}
                {/foreach}
            </div>
        {/if}

        <div class="ui {if count($WIDGETS_LEFT) && count($WIDGETS_RIGHT) }four wide tablet eight wide computer{elseif count($WIDGETS_LEFT) || count($WIDGETS_RIGHT)}ten wide tablet twelve wide computer{else}sixteen wide{/if} column">
            {if isset($ERRORS)}
                <div class="ui error icon message">
                    <i class="x icon"></i>
                    <div class="content">
                        <div class="header">{$ERRORS_TITLE}</div>
                        <ul class="list">
                            {foreach from=$ERRORS item=error}
                                <li>{$error}</li>
                            {/foreach}
                        </ul>
                    </div>
                </div>
            {/if}

            {if isset($SERVERS_LIST)}
                <div class="ui centered three stackable cards" id="servers">
                    {foreach from=$SERVERS_LIST item=server}
                        <div class="ui fluid card center aligned {if $server.online}green{else}red{/if} server" style="height: 100%;">
                            <div class="content">
                                <div class="header">
                                    {$server.name}
                                </div>
                                <div class="description" id="server-status">
                                    {if $server.online}{$server.x_players_online}{else}{$SERVER_OFFLINE}{/if}
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
            {else}
                <div class="ui orange message">{$NO_DATA_AVAILABLE}</div>
            {/if}

        </div>

        {if count($WIDGETS_RIGHT)}
            <div class="ui six wide tablet four wide computer column">
                {foreach from=$WIDGETS_RIGHT item=widget}
                    {$widget}
                {/foreach}
            </div>
        {/if}

    </div>
</div>

{include file='footer.tpl'}