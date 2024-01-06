{include file='header.tpl'}
{include file='navbar.tpl'}

<div class="ui stackable padded grid" id="forum-index">
    <div class="ui centered row">
        <div class="ui eleven wide tablet twelve wide computer column">
            <h2 class="ui header">
                {$PLAYERS}
            </h2>
        </div>

        <div class="ui five wide tablet four wide computer column">
            <form class="ui form" method="post" action="{$SEARCH_URL}" name="searchForm">
                <input type="hidden" name="token" value="{$TOKEN}">
                <div class="ui fluid action input">
                    <input type="text" name="player_search" placeholder="{$SEARCH}" minlength="0" maxlength="64">
                    <button type="submit" class="ui primary icon button"><i class="search icon"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

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

            <div class="ui segment">
                <table id="players" class="ui table dataTables-users" style="width:100%">
                    <thead>
                    <tr>
                        <th>{$PLAYER}</th>
                        <th>{$REGISTERED}</th>
                        <th>{$LAST_SEEN}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$PLAYERS_LIST item=player}
                        <tr>
                            <td><a href="{$player.link}"><img src="{$player.avatar}" class="ui avatar image" style="height:35px; width:35px;" alt="{$player.username}" />{if $player.user_style}<a style="{$player.user_style}" href="{$player.link}" data-poload="{$USER_INFO_URL}{$player.user_id}">{$player.username}</a>{else}{$player.username}{/if}</a></td>
                            <td>{$player.registered}</td>
                            <td>{$player.last_seen}</td>
                            <td><a class="ui primary icon tiny button right floated" href="{$player.link}">{$VIEW}</a></td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>

                {$PAGINATION}
            </div>

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