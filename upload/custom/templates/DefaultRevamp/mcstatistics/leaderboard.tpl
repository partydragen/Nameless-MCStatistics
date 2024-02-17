{include file='header.tpl'}
{include file='navbar.tpl'}

<h2 class="ui header">
    {$LEADERBOARD}
</h2>

<br />

<div class="ui stackable grid">
    <div class="ui row">

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

        <div class="ui six wide tablet four wide computer column">
            {if isset($LEADERBOARD_LIST)}
                {foreach from=$LEADERBOARD_LIST item=server}
                    <div class="ui fluid vertical pointing menu">
                        <a href="{$server.link}"><div class="header item" style="font-size:18px"><strong>{$server.server_name}</strong></div></a>

                         {foreach from=$server.placeholders item=placeholder}
                             <a class="item" href="{$placeholder.link}">{$placeholder.name}</a>
                         {/foreach}
                    </div>
                {/foreach}
            {else}
                <div class="ui orange message">{$NO_DATA_AVAILABLE}</div>
            {/if}

            {if count($WIDGETS_LEFT)}
                <div class="ui six wide tablet four wide computer column">
                    {foreach from=$WIDGETS_LEFT item=widget}
                        {$widget}
                    {/foreach}
                </div>
            {/if}
        </div>

        <div class="ui ten wide tablet twelve wide computer column">
            {if count($VIEWING_LEADERBOARDS)}
                <div class="ui stackable equal width left aligned three column grid segment" style="margin-top: 0">
                    {foreach from=$VIEWING_LEADERBOARDS item=list}
                        <div class="ui column">
                            <h3>{$list.server_name} &bull; {$list.friendly_name}</h3>
                            <div>
                                <ul id="leaderboard_list_{$list.server_name}_{$list.friendly_name}" class="ui list large selection" style="margin-left: -10px;">
                                </ul>
                                {if $VIEWING_LIST == "overview"}<a class="fluid ui grey basic button" href="{$list.link}">{$VIEW_ALL}</a>{/if}
                                {if isset($PAGINATION)}{$PAGINATION}{/if}
                            </div>
                        </div>
                    {/foreach}
                </div>
            {else}
                <div class="ui orange message">{$NO_DATA_AVAILABLE}</div>
            {/if}
        </div>

    </div>
</div>

<script type="text/javascript">
    const renderList = (server, leaderboard) => {
        const list = document.getElementById('leaderboard_list_' + server + '_' + leaderboard);
        list.innerHTML = '<div class="ui active centered inline loader"></div>';

        fetch(
            '{$QUERIES_URL}'
                .replace({literal}'{{server}}'{/literal}, server)
                .replace({literal}'{{leaderboard}}'{/literal}, leaderboard)
                .replace({literal}'{{page}}'{/literal}, new URLSearchParams(window.location.search).get('p') ?? 1)
        )
            .then(async response => {
                const data = await response.json();
                if (data.length === 0) {
                    list.parentElement.innerHTML = '<div class="ui orange message">{$NO_DATA_AVAILABLE}</div>';
                    return;
                }

                list.innerHTML = '';

                for (const player of data) {
                    const mainDiv = document.createElement('div');
                    mainDiv.classList.add('item');
                    mainDiv.onclick = () => window.location.href = player.profile_url;

                    const countDiv = document.createElement('div');
                    countDiv.classList.add('right', 'floated', 'content');

                    if (player.count !== null) {
                        const countHeader = document.createElement('h3');
                        countHeader.classList.add('ui', 'header');
                        countHeader.innerText = player.count;
                        countDiv.appendChild(countHeader);
                        mainDiv.appendChild(countDiv);
                    }

                    const contentDiv = document.createElement('div');
                    contentDiv.classList.add('middle', 'aligned', 'content');
                    contentDiv.style.whiteSpace = 'nowrap';
                    contentDiv.style.overflow = 'hidden';
                    contentDiv.style.textOverflow = 'ellipsis';

                    const avatarDiv = document.createElement('img');
                    avatarDiv.classList.add('ui', 'avatar', 'image');
                    avatarDiv.setAttribute('src', player.avatar_url);
                    {if $VIEWING_LIST == "overview"}
                    contentDiv.appendChild(avatarDiv);
                    {else}
                    mainDiv.appendChild(avatarDiv);
                    {/if}

                    const nameDiv = document.createElement('span');
                    nameDiv.style = player.group_style?.replace('&#039;', "'")?.replace('&quot;', '"');
                    {if $VIEWING_LIST != "overview"}
                    nameDiv.innerHTML = player.username + '&nbsp;' + player.group_html.join('');
                    {else}
                    nameDiv.innerText = player.username;
                    {/if}
                    contentDiv.appendChild(nameDiv);

                    {if $VIEWING_LIST != "overview"}
                    const metaDiv = document.createElement('div');
                    metaDiv.classList.add('description');

                    const metaSpan = document.createElement('span');
                    metaSpan.classList.add('ui', 'text', 'small');
                    const playerMeta = player.metadata;
                    metaSpan.innerHTML = Object.keys(playerMeta).map(key => key + ': ' + playerMeta[key]).join(' &middot; ');

                    metaDiv.appendChild(metaSpan);
                    contentDiv.appendChild(metaDiv);
                    {/if}
                    mainDiv.appendChild(contentDiv);
                    list.appendChild(mainDiv)
                }
            });
    }

    window.onload = () => {
        {foreach from=$VIEWING_LEADERBOARDS item=list}
            renderList('{$list.server_name}', '{$list.friendly_name}');
        {/foreach}
    }
</script>

{include file='footer.tpl'}