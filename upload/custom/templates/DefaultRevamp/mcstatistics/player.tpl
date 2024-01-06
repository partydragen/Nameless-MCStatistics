{include file='header.tpl'}
{include file='navbar.tpl'}

<div class="ui stackable grid">
    <div class="ui row">
        <div class="ui six wide tablet four wide computer column">
            <div class="ui segment">
                <img src="{$AVATAR}" alt="{$USERNAME}" class="ui centered image rounded" />

                <center>
                    </br>
                    {if $IS_ONLINE}<span class="badge badge-success">{$ONLINE_ON_SERVER}</span>{else}<span class="badge badge-danger">{$OFFLINE}</span>{/if}
                </center>

                <div class="ui divider"></div>

                <div class="ui relaxed list">
                {foreach from=$ABOUT_FIELDS key=key item=field}
                    <div class="item">
                        <div class="header">{$field.title}</div>
                        <div class="description">{$field.value}</div>
                    </div>
                {/foreach}
                </div>
            </div>
        </div>

        <div class="ui ten wide tablet twelve wide computer column">
            <div class="ui segment">
                <h2>{if $USER_STYLE}<img src="{$USER_AVATAR}" class="ui avatar image" style="height:35px; width:35px;" alt="{$USERNAME}" /> <a style="{$USER_STYLE}" href="{$USER_PROFILE}" data-poload="{$USER_INFO_URL}{$USER_ID}">{$USERNAME}</a>{else}{$USERNAME}{/if}</h2>
                <div class="ui divider"></div>

                {foreach from=$SERVERS_FIELDS item=server}
                    <h3>{$server.name}</h3>
                    <hr>
                    <div class="ui grid">
                        {foreach from=$server.fields item=field}
                            <div class="four wide column">
                                <strong>{$field.title}</strong></br>
                                <p>{$field.value}</p>
                            </div>
                        {/foreach}
                    </div>
                    </br>
                {/foreach}

                <center>Statistics provided by <a href="https://mcstatistics.org/" target="_blank">MCStatistics</a></center>
            </div>
        </div>
    </div>
</div>

{include file='footer.tpl'}